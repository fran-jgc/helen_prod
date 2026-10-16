<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");


$cod_deudor = $_REQUEST['cod_deudor'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = sql_deuda::get_sql('VALIDA_DEVOLUCION', $cod_deudor);
$result = $db->build_results($sql);
$respuesta = '';
if($result[0]['CANT'] > 0) {
	$respuesta = 'TIENE_CHEQUES';
}

print $respuesta;
?>