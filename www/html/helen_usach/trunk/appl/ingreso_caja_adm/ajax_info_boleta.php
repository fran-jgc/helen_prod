<?php
///////////////////////
// php llamado con ajax en el javascript function f_get_info_boleta()
////////////////////////
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_boleta = $_REQUEST['cod_boleta'];

$sql = sql_ingreso_caja_adm::get_sql('AJAX_EXISTE_BOLETA', $cod_boleta);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql);
$row_count = $db->count_rows();
$respuesta = '';
	
if ($row_count == 0){
	$respuesta = "NO|";
}
else
{	
	$sql = sql_ingreso_caja_adm::get_sql('AJAX_EXISTE_SUB_BOLETA', $cod_boleta);
	
	$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	$result = $db->build_results($sql);
	$row_count = $db->count_rows();
	$respuesta = '';
		
	if ($row_count == 0){
		$respuesta = "NO_SB|";
	}
	else{
		$sql = sql_ingreso_caja_adm::get_sql('AJAX_INFO_BOLETA', $cod_boleta);
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result = $db->build_results($sql);
		$row_count = $db->count_rows();
		$respuesta = '';
		
		if ($row_count == 0){
			$respuesta = "0|";
		}
		else{
			$respuesta =  $result[0]['RUT']."|";
			$respuesta =  $respuesta.$result[0]['DIG_VERIF']."|";
			$respuesta =  $respuesta.$result[0]['NOMBRE']."|";
			$respuesta =  $respuesta.$result[0]['COD_DEUDA']."|";
			$respuesta =  $respuesta.$result[0]['COD_TIPO_DEUDA']."|";
			$respuesta =  $respuesta.$result[0]['ANO_COBRO']."|";
			$respuesta =  $respuesta.$result[0]['SIMBOLO'];
		}
	}
}
print urlencode($respuesta);
?>