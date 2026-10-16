<?php
///////////////////////
// php llamado con ajax en el javascript
////////////////////////

require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");

$rut = $_REQUEST['rut'];
$sql_deudor = sql_condonacion::get_sql('AJAX_NOMBRE',$rut);
$K_PARAM_CARTERA = 2;

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql_deudor);
$row_count = $db->count_rows();

If ($row_count == 0){
	$result  = 0;
	print $result;
}
else{
	$ed_no_exigible 		= 1;
	$ed_cobranza_normal		= 2;
	$estado_cond_solicitada	= 1;
	
	$sql_count_deuda = sql_condonacion::get_sql('AJAX_DEUDA',$rut,$ed_no_exigible,$ed_cobranza_normal);
	
	$result = $db->build_results($sql_count_deuda);
	$row_count_cant_deuda = $db->count_rows();
	If ($row_count_cant_deuda == 0){
		$result  = 'NO_DEUDAS';
		print $result;
	}
	else{
		
		$sql_est_cond = sql_condonacion::get_sql('AJAX_ESTADO_CONDONACION',$rut);
		
		$result 	= $db->build_results($sql_est_cond);
		$row_count 	= $db->count_rows();
		if ($row_count > 0){
			if ($result[0]['COD_ESTADO_CONDONACION']==$estado_cond_solicitada){
				$result  = 'CONDONA_SOLICITADA';
				print $result;
				return;
			}
		}
		
			$sql_deuda = sql_condonacion::get_sql('AJAX_NOMBRE_1',$rut);
			$result = $db->build_results($sql_deuda);
			$row_count_deuda = $db->count_rows();

			If ($row_count_deuda == 0){
				$result  = 1;
				print $result;
			}
			else{
				$sql_deuda = sql_condonacion::get_sql('AJAX_NOMBRE_1',$rut);
				$result = $db->build_results($sql_deuda);
				$row_count_deuda = $db->count_rows();
					
				$result = $db->build_results($sql_deuda);
				$NOMBRE = $result[0]['NOMBRE'];
				$DIG_VERIF = $result[0]['DIG_VERIF'];
				$COD_DEUDOR = $result[0]['COD_DEUDOR'];
				print $NOMBRE."|".$DIG_VERIF."|".$COD_DEUDOR;
				
			}
	}
}
?>