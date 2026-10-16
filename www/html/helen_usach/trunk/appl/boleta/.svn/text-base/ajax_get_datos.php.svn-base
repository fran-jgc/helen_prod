<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$rut_deudor	= $_REQUEST['rut_deudor'];

$sql = sql_boleta::get_sql('AJAX_DATOS',$rut_deudor);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql);
$row_deuda = '';
$datos_deudor = 0;

if ($result[0]['CUENTA'] > 0){
	$datos_deudor  = 1;
}

print $datos_deudor;	
?>