<?php
ini_set('display_errors', 'On');
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
require_once ("../../../appl.ini");
session::set('K_ROOT_DIR', K_ROOT_DIR);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);


$K_TIPO_BD = "oci";
			$K_SERVER = "(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=bdprdlegacy.usach.cl)(PORT = 1521)))(CONNECT_DATA=(SERVICE_NAME=FINA)))";
			$K_BD = "FINA";
			$K_USER ="CONS_INTERNET_EX";
			$K_PASS = "consulta2019";
			
			
			

$db_segic = new database($K_TIPO_BD, $K_SERVER, $K_BD, $K_USER, $K_PASS);

$sql = "SELECT COD_WP_TRANSACCION
				,TOKEN
				,MONTO
				,TO_CHAR(DR.RUT)||DR.DIG_VERIF RUT
                ,EXITO
		FROM WP_TRANSACCION WP, DEUDOR DR
        WHERE TIPO_PAGO_WEB = 'PAGO_SEGIC'
        AND WP.COD_DEUDOR = DR.COD_DEUDOR
        AND EXITO = 'S'
        AND TOKEN IS NOT NULL
        AND FECHA > TO_DATE('01/01/2023', 'DD/MM/YYYY')
        ORDER BY COD_WP_TRANSACCION ASC";

$result = $db->build_results($sql);

$count = 0;

if(count($result)>0){

	for ($i = 0; $i < count($result); $i++) {

		$RUT = $result[$i]['RUT'];
		$TOKEN = $result[$i]['TOKEN'];
		$MONTO = $result[$i]['MONTO'];
		$COD_WP_TRANSACCION = $result[$i]['COD_WP_TRANSACCION'];
		$sum_tr_legacy = 0;

		$sql = "Paq_FondoCredito.pro_ConsPagoWebFC($RUT,$TOKEN,{RESULT})";
					//$sql = "SELECT 'Hola' FROM dual";
					
					$result_consulta = $db_segic->build_results2($sql);
			
			
		if(count($result_consulta)>0){
			

			$NUM_TRANS_CONS = $result_consulta[0]['NUM_TRANS'];
						$FECPAGO_CONS = $result_consulta[0]['FEC_PAGO'];
						$RUT_CONS = $result_consulta[0]['RUT_ALUMNO'];
						$CARR_CONS = $result_consulta[0]['CARRERA'];
						$COD_COMERCIO_CONS = $result_consulta[0]['COD_COMERCIO'];
						$CAPITAL_CONS = $result_consulta[0]['CAPITAL'];
						$INTERES_CONS = $result_consulta[0]['INTERES'];
						$MULTA_CONS = $result_consulta[0]['MULTA'];
						$ESTADO_CONS = $result_consulta[0]['ESTADO'];

				$sum_tr_legacy = $CAPITAL_CONS + $INTERES_CONS + $MULTA_CONS;


			echo $sum_tr_legacy. ' - '. $MONTO;;
			echo '<br>';
			


			if( ($sum_tr_legacy > ($MONTO + 10)) || ($sum_tr_legacy < ($MONTO - 10)) ) {
				$str_Resp = $str_Resp.$COD_WP_TRANSACCION.',';
				$count ++;

				echo 'entra';
				$db->EXECUTE_SP("spx_error_transaccion","$COD_WP_TRANSACCION, $sum_tr_legacy");

			}



		}

	}

	
}





?>