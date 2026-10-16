<?php
ini_set('max_execution_time', 0);
require_once(dirname(__FILE__) . "/../../../../../commonlib_seven/trunk/php/auto_load.php");
require_once("../../../../../helen_usach/trunk/appl.ini");
header('Content-Type: application/json');

session::set('K_ROOT_URL', K_ROOT_URL);
session::set('K_ROOT_DIR', K_ROOT_DIR);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

// Función para guardar log en base de datos
function insertar_log_bd($db, $estado, $token, $monto = null, $codigo_autorizacion = null, $detalle_json = null) {
    $monto_sql = ($monto !== null) ? $monto : 'NULL';
    $cod_auth_sql = ($codigo_autorizacion !== null) ? "'$codigo_autorizacion'" : 'NULL';
    
    $detalle_sql = ($detalle_json !== null) ? $detalle_json : 'NULL';
    $token_sql = $token;
    $estado_sql = $estado;

    $sp_log = 'SP_LOG_TRANSACCION';
    $param = "'$estado_sql','$token_sql',$monto_sql,$cod_auth_sql,'$detalle_sql'";
    $db->EXECUTE_SP($sp_log,$param);
}

$sql = "SELECT COD_WP_TRANSACCION
             ,TOKEN
             ,MONTO
             ,TO_CHAR(DR.RUT) || DR.DIG_VERIF AS RUT
             ,EXITO
             ,INGRESO_LEGACY
        FROM WP_TRANSACCION WP
             JOIN DEUDOR DR ON WP.COD_DEUDOR = DR.COD_DEUDOR
        WHERE TIPO_PAGO_WEB = 'PAGO_SEGIC'
          AND cod_wp_transaccion <= 392677
          AND TOKEN IS NOT NULL
        ORDER BY cod_wp_transaccion DESC";

$transacciones = $db->build_results($sql);

$url = "https://webpay-server-test.usach.cl/api/transaction/detail";
$username = 'api.helen';
$password = '4feoh4G9Zx2u';

$cant_si_legacy = 0;
$cant_no_legacy = 0;
$cant_no_boleta = 0;
$cant_si_abono = 0;
$cant_no_abono = 0;

$resultados = [];
$errores = [];

foreach ($transacciones as $trx) {
    $numTransaction = trim($trx['TOKEN']);
    $COD_WP_TRANSACCION = trim($trx['COD_WP_TRANSACCION']);
    $RUT = $trx['RUT'];
    
    if (!$numTransaction) {
        $errores[] = ["token" => null, "error" => "TOKEN vacío", "fila" => $trx];
        insertar_log_bd($db, "ERROR", null, null, null, "TOKEN vacío - COD_TRANSACCION: $COD_WP_TRANSACCION");
        continue;
    }

    $data = json_encode(["numTransaction" => $numTransaction]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Accept: application/json",
        "Content-Length: " . strlen($data)
    ]);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($error || $httpCode != 200) {
        $errores[] = [
            "token" => $numTransaction,
            "error" => $error ?: "HTTP $httpCode",
            "response" => $response
        ];
        insertar_log_bd($db, "ERROR", $numTransaction, null, null, $error . " / " . $response);
        continue;
    }

    $data = json_decode($response, true);

    if (
        isset($data['success']) && $data['success'] === true &&
        isset($data['detalle']['details'][0]['responseCode']) &&
        $data['detalle']['details'][0]['responseCode'] == 0
    ) {
        $monto = $data['monto'];
        $codigoAutorizacion = $data['detalle']['details'][0]['authorizationCode'];

        // Buscar info de boleta y datos financieros
        $sql = "SELECT f_get_matricula(D.COD_DEUDOR, 3000) COD_CARRERA,
                       SB.MONTO_CAPITAL * f_mon_get_valor_moneda(TD.COD_MONEDA,WP.FECHA) MONTO_CAPITAL,
                       SB.MONTO_INTERES * f_mon_get_valor_moneda(TD.COD_MONEDA,WP.FECHA) MONTO_INTERES,
                       SB.MONTO_MULTA * f_mon_get_valor_moneda(TD.COD_MONEDA,WP.FECHA) MONTO_MULTA,
                       To_Char(WP.FECHA,'DD/MM/YYYY') FECHA_TRANSACCION,
                       To_Char(WP.FECHA,'HH24:MI:SS') HORA_TRANSACCION,
                       SB.COD_SUB_BOLETA
                  FROM WP_TRANSACCION WP, BOLETA B, WP_SUB_BOLETA WPS, SUB_BOLETA SB, PLAN_PAGO PP,
                       DEUDOR D, DEUDA DEU, TIPO_DEUDA TD
                 WHERE WP.COD_WP_TRANSACCION = $COD_WP_TRANSACCION
                   AND WPS.COD_WP_TRANSACCION = WP.COD_WP_TRANSACCION
                   AND SB.COD_SUB_BOLETA = WPS.COD_SUB_BOLETA
                   AND B.COD_BOLETA = SB.COD_BOLETA
                   AND PP.COD_PLAN_PAGO = B.COD_PLAN_PAGO
                   AND DEU.COD_DEUDA = B.COD_DEUDA
                   AND D.COD_DEUDOR = DEU.COD_DEUDOR
                   AND TD.COD_TIPO_DEUDA = DEU.COD_TIPO_DEUDA";

        $result_monto = $db->build_results($sql);
        if (count($result_monto) > 0) {
            $COD_SUB_BOLETA = $result_monto[0]['COD_SUB_BOLETA'];

            $sql_ABONO = "SELECT count(*) as TOTAL from abono where cod_sub_boleta = $COD_SUB_BOLETA";
            $result_abono = $db->build_results($sql_ABONO);

            if ($result_abono[0]['TOTAL'] > 0) {
                $cant_si_abono++;
            } else {
                $cant_no_abono++;
                insertar_log_bd($db, "APROBADO SIN ABONO", $numTransaction, $monto, $codigoAutorizacion);
            }

            // Conexión a SEGIC
            $db_segic = new database(
                "oci",
                "(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=bdprdlegacy.usach.cl)(PORT = 1521)))(CONNECT_DATA=(SERVICE_NAME=FINA)))",
                "FINA",
                "CONS_INTERNET_EX",
                "consulta2019"
            );

            $sql = "Paq_FondoCredito.pro_ConsPagoWebFC('$RUT','$numTransaction',{RESULT})";
            $result_consulta = $db_segic->build_results2($sql);

            if (count($result_consulta) > 0) {
                $cant_si_legacy++;
                insertar_log_bd($db, "EXITO", $numTransaction, $monto, $codigoAutorizacion);
            } else {
                $cant_no_legacy++;
                insertar_log_bd($db, "APROBADO NO LEGACY", $numTransaction, $monto, $codigoAutorizacion);
            }
        } else {
            $cant_no_boleta++;
            insertar_log_bd($db, "APROBADO NO BOLETA", $numTransaction, $monto, $codigoAutorizacion);
        }

        $resultados[] = [
            "token" => $numTransaction,
            "estado" => "APROBADO",
            "monto" => $monto,
            "codigo_autorizacion" => $codigoAutorizacion
        ];
    } else {
        insertar_log_bd($db, "RECHAZADO", $numTransaction, null, null, json_encode($data));
        $resultados[] = [
            "token" => $numTransaction,
            "estado" => "RECHAZADO o NO AUTORIZADO",
            "detalle" => $data
        ];
    }
}

echo json_encode([
    "success" => true,
    "procesadas" => count($transacciones),
    "aprobadas" => count(array_filter($resultados, fn($r) => $r['estado'] === "APROBADO")),
    "rechazadas" => count(array_filter($resultados, fn($r) => $r['estado'] !== "APROBADO")),
    "si_legacy" => $cant_si_legacy,
    "no_legacy" => $cant_no_legacy,
    "no_boleta" => $cant_no_boleta,
    "no_abono" => $cant_no_abono,
    "si_abono" => $cant_si_abono,
    "errores" => $errores,
    "resultados" => $resultados
], JSON_PRETTY_PRINT);