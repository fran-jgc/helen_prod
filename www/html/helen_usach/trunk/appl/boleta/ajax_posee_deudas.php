<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$rut_deudor	= $_REQUEST['rut_deudor'];

$sql = sql_boleta::get_sql('AJAX_POSEE_DEUDAS',$rut_deudor);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql);
$row_count = $db->count_rows();
$row_deuda = '';
$datos_deudor = '';

if ($row_count == 0){
	$result  = 0;
}
else{
	$datos_deudor = $datos_deudor.$result[0]['TOTAL']."|";
	$datos_deudor = $datos_deudor.$result[0]['DIG_VERIF']."|";
	$datos_deudor = $datos_deudor.$result[0]['NOMBRE']."|";
	$datos_deudor = $datos_deudor.$result[0]['COD_TIPO_DEUDA'];
	print $datos_deudor;
}
?>