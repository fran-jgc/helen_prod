<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_deuda	= $_REQUEST['cod_deuda'];

$sql = sql_boleta::get_sql('AJAX_COD_DEUDA',$cod_deuda);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql);
$row_count = $db->count_rows();
$row_deuda = '';
$datos_deuda = '';

if ($row_count == 0){
	$result  = 0;
}
else{
	$datos_deuda = $cod_deuda."|";
	$datos_deuda = $datos_deuda.$result[0]['SIMBOLO']."|";
	$datos_deuda = $datos_deuda.$result[0]['TASA_INTERES_PENAL'];
	print $datos_deuda;
}
?>