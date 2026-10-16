<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once("../../appl.ini");

ini_set('display_errors', 'Off');

// Rechazar rutas adicionales no válidas.
if (!empty($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] !== '/') {
    header('HTTP/1.1 404 Not Found');
    exit;
}

session::set('K_ROOT_URL', K_ROOT_URL);
session::set('K_ROOT_DIR', K_ROOT_DIR);
session::set('K_APPL', K_APPL);

// Datos utilizados por commonlib.
session::set("COD_USUARIO", "2");
session::set("NOM_USUARIO", "USUARIO WEB");

class w_login extends w_base {
    var $dws = array();

    function __construct() {
        // Mantener la inicialización utilizada por el portal.
        base::__construct();
        $this->work_directory = getcwd();

        session::un_set("COD_DEUDOR");
        session::un_set("cod_deudor");
        session::un_set("NOM_DEUDOR");
		session::un_set("METODO_AUTENTICACION");
		
    }

    function login($login, $password) {
        $this->crea_dws();

        // No incorporar las credenciales a la consulta del formulario.
        $this->dws['dw_param']->retrieve('null', 'null');

        $t = new Template_appl(
            $this->root_dir."appl/webpay_usach/inicio_sesion.html"
        );

        $this->dws['dw_param']->habilitar($t, true);
        print $t->toString();

        print '<script type="text/javascript">
            var username = document.getElementById("RUT");
            if (username) {
                username.focus();
            }
        </script>';
    }

    function crea_dws() {
        $sql = "SELECT {KEY1} RUT,
                       {KEY2} CLAVE,
                       TO_CHAR(SYSDATE, 'dd/mm/yyyy') FECHA_ACTUAL
                FROM dual";

        $this->dws['dw_param'] = new datawindow($sql);
        $this->dws['dw_param']->add_control(
            new edit_num('RUT', 20, 20)
        );
        $this->dws['dw_param']->add_control(
            new edit_password('CLAVE', 20, 20)
        );
    }

    function validate_login() {
        $login = (
            isset($_POST['rut']) && is_string($_POST['rut'])
        ) ? trim($_POST['rut']) : '';

        $password = (
            isset($_POST['pwd']) && is_string($_POST['pwd'])
        ) ? $_POST['pwd'] : '';

        // El acceso tradicional recibe únicamente el cuerpo numérico del RUT.
        if (!preg_match('/^[0-9]{1,8}$/D', $login)) {
            $this->login('', '');
            $this->message(
                'Ingrese un RUT válido, sin puntos, guion ni dígito verificador.'
            );
            return;
        }

        if ($password === '') {
            $this->login('', '');
            $this->message('Ingrese su contraseña.');
            $this->enfocar_clave();
            return;
        }

        $db = new database(
            K_TIPO_BD,
            K_SERVER,
            K_BD,
            K_USER,
            K_PASS
        );

        // $login contiene exclusivamente dígitos, validados previamente.
        if (K_TIPO_BD == 'oci') {
            $sql = "SELECT PASSWORD,
                           COD_DEUDOR,
                           NOMBRE || ' ' || APELLIDO_PATERNO || ' ' ||
                           APELLIDO_MATERNO NOM_DEUDOR
                    FROM DEUDOR
                    WHERE RUT = $login";
        } elseif (K_TIPO_BD == 'mssql') {
            $sql = "SELECT PASSWORD,
                           COD_DEUDOR,
                           NOMBRE + ' ' + APELLIDO_PATERNO + ' ' +
                           APELLIDO_MATERNO NOM_DEUDOR
                    FROM DEUDOR
                    WHERE RUT = $login";
        } else {
            $this->login('', '');
            $this->message(
                'No fue posible iniciar sesión. Contacte al administrador.'
            );
            return;
        }

        $result = $db->build_results($sql);

        if (!is_array($result)) {
            $this->login('', '');
            $this->message(
                'No fue posible iniciar sesión. Intente nuevamente.'
            );
            return;
        }

        if (count($result) == 0) {
            $this->login('', '');
            $this->message('RUT o contraseña incorrectos.');
            return;
        }

        $passwd_bd = $result[0]['PASSWORD'];
        $cod_deudor = $result[0]['COD_DEUDOR'];
        $nom_deudor = $result[0]['NOM_DEUDOR'];

        // Conservar la comparación con la contraseña del sistema tradicional,
        // evitando conversiones implícitas entre números y cadenas.
        if ($passwd_bd === null || $password !== (string) $passwd_bd) {
            $this->login('', '');
            $this->message('RUT o contraseña incorrectos.');
            $this->enfocar_clave();
            return;
        }

        // Renovar el identificador si commonlib mantiene una sesión PHP activa.
        if (session_id() !== '') {
            session_regenerate_id(true);
        }

        // Ingreso correcto.
        session::set("cod_deudor", $cod_deudor);
        session::set("COD_DEUDOR", $cod_deudor);
        session::set("NOM_DEUDOR", $nom_deudor);
		session::set('METODO_AUTENTICACION', 'TRADICIONAL');

        $db->BEGIN_TRANSACTION();
        $db->EXECUTE_SP(
            "sp_log_cambio",
            "'LOGIN', '$login', 2, 'L'"
        );
        $db->COMMIT_TRANSACTION();

        header('Location: consulta_deuda.php');
        exit;
    }

    function enfocar_clave() {
        print '<script type="text/javascript">
            var passwd = document.getElementById("pwd");
            if (passwd) {
                passwd.focus();
            }
        </script>';
    }

    function procesa_event() {
    }
}

$w = new w_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $w->validate_login();
} else {
    $w->login('', '');
}