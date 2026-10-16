<?php
///////////////////////////////////////////
// php llamado con ajax en el javascript //
///////////////////////////////////////////
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$K_EXIGIBLE  	 	   = 1;
$K_NO_EXIGIBLE 		   = 2;
$K_PRORROGA_SOLICITADA = 1;
$K_PARAM_CARTERA	   = 2;

$rut = $_REQUEST['rut'];

if($rut == null){
	print urlencode('VACIO');
	return;
}
	
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

///////////////// CLASS_SQL = AJAX_DEUDOR ///////////////
$sql = sql_prorroga::get_sql('AJAX_DEUDOR', $rut);		  
$result = $db->build_results($sql);

if ($result[0]['CUANTO']== 0){
	print urlencode('NO');
	return;
} else {
	///////////////// CLASS_SQL = AJAX_EXIGIBLE_NO_EXIGIBLE ///////////////
	$sql =	sql_prorroga::get_sql('AJAX_EXIGIBLE_NO_EXIGIBLE', $rut, $K_EXIGIBLE, $K_NO_EXIGIBLE);
	$result = $db->build_results($sql);
	if ($result[0]['CANT']== 0){
		print urlencode('NO_EXIGIBLE');
		return;
	} else{
		$cartera_defecto = base::get_parametro($K_PARAM_CARTERA);
		$sql = sql_prorroga::get_sql('DEUDA_CARTERA_DIFERENTE', $cartera_defecto,$rut);
		$result = $db->build_results($sql);
		
		if($result[0]['CANT'] > 0) {
			print urlencode('CARTERA_DIFERENTE');
			return;
		}
		else{
			///////////////// CLASS_SQL = AJAX_SOLICITUD ///////////////
			$sql =	sql_prorroga::get_sql('AJAX_SOLICITUD', $rut, $K_EXIGIBLE, $K_NO_EXIGIBLE, $K_PRORROGA_SOLICITADA);
			$result = $db->build_results($sql);
			if ($result[0]['CANT'] > 0){
				print urlencode('PENDIENTE');
				return;
			} else {
				///////////////// CLASS_SQL = AJAX_NOMBRE ///////////////
				$sql = sql_prorroga::get_sql('AJAX_NOMBRE', $rut, $K_EXIGIBLE, $K_NO_EXIGIBLE);
						  			
				$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
				$result = $db->build_results($sql);
				$row_count = $db->count_rows();
				
				if ($row_count == 0){
					$result  = 0;
				} else {
					$result = $db->build_results($sql);
					print urlencode(json_encode($result));				
				}
			}
		}
	}
}	
?>