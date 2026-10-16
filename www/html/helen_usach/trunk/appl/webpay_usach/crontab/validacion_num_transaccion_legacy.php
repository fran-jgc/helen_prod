<?php
ini_set('display_errors', 'On');
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/clases/class_PHPMailer.php");
require_once ("../../../appl.ini");

session::set('K_ROOT_URL', K_ROOT_URL);
session::set('K_ROOT_DIR', K_ROOT_DIR);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$k_ruta_peticion = "https://webpay-server.usach.cl/api/default/get-transaction-detail";
$k_store_id = 12; //parametro fijo que identifica a los pagos institucionales
$cant_pagados = 0;
$sum_monto_total = 0;

$sp_bitacora = 'spw_bitacora_transaccion';
$param = "NULL,'CRONTAB REVISION NUM TRANSACCION SEGIC','LLAMADO'";
$db->EXECUTE_SP($sp_bitacora,$param);

$sql = "SELECT COD_WP_TRANSACCION
				,TOKEN
				,MONTO
				,TO_CHAR(DR.RUT)||DR.DIG_VERIF RUT
                ,EXITO
                ,TO_CHAR(WP.FECHA, 'DD/MM/YYYY') AS FECHA
		FROM WP_TRANSACCION WP, DEUDOR DR
        WHERE TIPO_PAGO_WEB = 'PAGO_SEGIC'
        AND WP.COD_DEUDOR = DR.COD_DEUDOR
        AND EXITO = 'S'
        AND TOKEN IS NOT NULL
        --AND WP.FECHA >= TRUNC(SYSDATE) - 1
        --AND WP.FECHA <  TRUNC(SYSDATE)
        AND COD_WP_TRANSACCION = 405759
        ORDER BY COD_WP_TRANSACCION DESC";

$result = $db->build_results($sql);
if(count($result)>0){
    for ($i = 0; $i < count($result); $i++){
        $K_TIPO_BD = "oci";
        $K_SERVER = "(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=bdprdlegacy.usach.cl)(PORT = 1521)))(CONNECT_DATA=(SERVICE_NAME=FINA)))";
        $K_BD = "FINA";
        $K_USER ="CONS_INTERNET_EX";
        $K_PASS = "consulta2019";
        
        $db_segic = new database($K_TIPO_BD, $K_SERVER, $K_BD, $K_USER, $K_PASS);
        
        $cont_intentos = 0;
        $respuesta_segic = false;
        $error_segic = false;
        
        $numTransaction = $result[$i]['TOKEN'];
        $COD_WP_TRANSACCION = $result[$i]['COD_WP_TRANSACCION'];
        $MONTO_BD = $result[$i]['MONTO'];
        $RUT = $result[$i]['RUT'];
        $TOKEN = $result[$i]['TOKEN'];
        $FECHA = $result[$i]['FECHA'];

            $sql = "SELECT num_trans NUM_TRANS
                    FROM Credycob.T_trans_hel_pagweb   
                    WHERE trim(rut_alumno) = trim($RUT)
                    AND TRUNC(FEC_PAGO) = TO_DATE('$FECHA', 'DD/MM/YYYY')
                    ";
            $result_consulta = $db_segic->build_results($sql);

            if(count($result_consulta)==1){
                echo 'vienen registros de legacy';
                echo '<br>';
                $NUM_TRANS_CONS = $result_consulta[0]['NUM_TRANS'];
                $FECPAGO_CONS = $result_consulta[0]['FEC_PAGO'];
                $RUT_CONS = $result_consulta[0]['RUT_ALUMNO'];
                $CARR_CONS = $result_consulta[0]['CARRERA'];
                $COD_COMERCIO_CONS = $result_consulta[0]['COD_COMERCIO'];
                $CAPITAL_CONS = $result_consulta[0]['CAPITAL'];
                $INTERES_CONS = $result_consulta[0]['INTERES'];
                $MULTA_CONS = $result_consulta[0]['MULTA'];
                $ESTADO_CONS = $result_consulta[0]['ESTADO'];

                if($NUM_TRANS_CONS != $TOKEN){
                    echo 'ACTUALIZA!!';
                    echo '<br>';
                    $sp_aux = 'spw_wp_aux';
                    $param = "'ACTUALIZA_TOKEN_LEGACY',$COD_WP_TRANSACCION,'$NUM_TRANS_CONS'";
                    $db->EXECUTE_SP($sp_aux,$param);
                }
            }
        
    }
}
