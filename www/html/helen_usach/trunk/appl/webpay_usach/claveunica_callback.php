<?php

require_once(
    dirname(__FILE__)
    . '/../../../../commonlib_seven/trunk/php/auto_load.php'
);

require_once(dirname(__FILE__) . '/../../appl.ini');

require_once(
    dirname(__FILE__)
    . '/class_claveunica_client.php'
);

class w_claveunica_callback extends w_base
{
    public function __construct()
    {
        base::__construct();

        session::set('K_ROOT_URL', K_ROOT_URL);
        session::set('K_ROOT_DIR', K_ROOT_DIR);
        session::set('K_APPL', K_APPL);

        session::set('COD_USUARIO', '2');
        session::set('NOM_USUARIO', 'USUARIO WEB');
    }

    public function procesar()
    {
        if (isset($_GET['error'])) {
            throw new RuntimeException(
                'ClaveUnica rechazo la autenticacion.'
            );
        }

        $code = isset($_GET['code'])
            ? trim($_GET['code'])
            : '';

        $receivedState = isset($_GET['state'])
            ? trim($_GET['state'])
            : '';

        $expectedState = session::get('CLAVEUNICA_STATE');

        $createdAt = session::get(
            'CLAVEUNICA_STATE_CREATED_AT'
        );

        if (
            $code === ''
            || $receivedState === ''
            || !$expectedState
            || !hash_equals($expectedState, $receivedState)
        ) {
            throw new RuntimeException(
                'La respuesta de ClaveUnica no es valida.'
            );
        }

        if (
            !$createdAt
            || (time() - (int) $createdAt) > 600
        ) {
            throw new RuntimeException(
                'La autenticacion de ClaveUnica expiro.'
            );
        }

        session::un_set('CLAVEUNICA_STATE');
        session::un_set('CLAVEUNICA_STATE_CREATED_AT');

        $client = new ClaveUnicaClient();

        $accessToken = $client->exchangeCode(
            $code,
            $receivedState
        );

        $userInfo = $client->getUserInfo($accessToken);

        if (
            !isset($userInfo['RolUnico'])
            || !isset($userInfo['RolUnico']['numero'])
            || !isset($userInfo['RolUnico']['DV'])
        ) {
            throw new RuntimeException(
                'ClaveUnica no entrego el RUN del ciudadano.'
            );
        }

        $run = preg_replace(
            '/[^0-9]/',
            '',
            (string) $userInfo['RolUnico']['numero']
        );

        $dv = strtoupper(
            trim((string) $userInfo['RolUnico']['DV'])
        );

        if (
            $run === ''
            || !preg_match('/^[0-9K]$/', $dv)
        ) {
            throw new RuntimeException(
                'ClaveUnica entrego un RUN invalido.'
            );
        }

        $db = new database(
            K_TIPO_BD,
            K_SERVER,
            K_BD,
            K_USER,
            K_PASS
        );

        $ambiente = isset($_SERVER['CLAVEUNICA_AMBIENTE'])
            ? strtolower(
                trim($_SERVER['CLAVEUNICA_AMBIENTE'])
            )
            : 'produccion';

        /*
         * En sandbox se relaciona el RUN de prueba de
         * ClaveUnica con un deudor de prueba del sistema.
         *
         * En produccion se busca directamente el RUN real
         * entregado por ClaveUnica.
         */
        if ($ambiente === 'sandbox') {
            if (K_TIPO_BD !== 'oci') {
                throw new RuntimeException(
                    'El mapeo sandbox solo esta configurado para Oracle.'
                );
            }

            $sql = "
                SELECT D.COD_DEUDOR,
                       D.NOMBRE || ' ' ||
                       D.APELLIDO_PATERNO || ' ' ||
                       D.APELLIDO_MATERNO AS NOM_DEUDOR
                FROM DEUDOR D
                INNER JOIN CLAVEUNICA_DEUDOR_SANDBOX M
                    ON M.COD_DEUDOR = D.COD_DEUDOR
                WHERE M.RUT_CLAVEUNICA = "
                . (int) $run;
        } else {
            if (K_TIPO_BD === 'oci') {
                $sql = "
                    SELECT COD_DEUDOR,
                           NOMBRE || ' ' ||
                           APELLIDO_PATERNO || ' ' ||
                           APELLIDO_MATERNO AS NOM_DEUDOR
                    FROM DEUDOR
                    WHERE RUT = " . (int) $run . "
                    AND UPPER(DIG_VERIF) = '" . $dv . "'";
            } else {
                $sql = "
                    SELECT COD_DEUDOR,
                           NOMBRE + ' ' +
                           APELLIDO_PATERNO + ' ' +
                           APELLIDO_MATERNO AS NOM_DEUDOR
                    FROM DEUDOR
                    WHERE RUT = " . (int) $run . "
                    AND UPPER(DIG_VERIF) = '" . $dv . "'";
            }
        }

        $result = $db->build_results($sql);

        if (count($result) !== 1) {
            http_response_code(403);

            throw new RuntimeException(
                'El RUN autenticado no corresponde '
                . 'a un deudor habilitado.'
            );
        }

        $codDeudor = $result[0]['COD_DEUDOR'];
        $nomDeudor = $result[0]['NOM_DEUDOR'];

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        session::set('cod_deudor', $codDeudor);
        session::set('COD_DEUDOR', $codDeudor);
        session::set('NOM_DEUDOR', $nomDeudor);
		session::set('METODO_AUTENTICACION', 'CLAVEUNICA');

        $db->BEGIN_TRANSACTION();

        $db->EXECUTE_SP(
            'sp_log_cambio',
            "'LOGIN_CLAVEUNICA', '$run', 2, 'L'"
        );

        $db->COMMIT_TRANSACTION();

        header('Cache-Control: no-store');
        header('Location: consulta_deuda.php');
        exit;
    }
}

try {
    $window = new w_claveunica_callback();
    $window->procesar();
} catch (Throwable $exception) {
    error_log(
        'Error callback ClaveUnica: '
        . $exception->getMessage()
    );

    if (http_response_code() < 400) {
        http_response_code(400);
    }

    header('Cache-Control: no-store');

    echo 'No fue posible completar el ingreso con ClaveÚnica.';
    echo '<br><br>';
    echo '<a href="inicio_sesion.php">'
        . 'Volver al inicio de sesión'
        . '</a>';
}