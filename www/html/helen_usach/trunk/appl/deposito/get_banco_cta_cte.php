<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_cuenta_corriente = $_REQUEST['cod_cuenta_corriente'];

	$sql = sql_deposito::get_sql('CUENTA_CORRIENTE',$cod_cuenta_corriente);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql);
$row_count = $db->count_rows();
$respuesta = ''; 
if ($row_count == 0)
	$respuesta = "0|";
else{
	$respuesta = $result[0]['COD_CUENTA_CORRIENTE']."|";
	$respuesta =  $respuesta.$result[0]['NOM_BANCO']."|";
	$respuesta =  $respuesta.$result[0]['NRO_CUENTA_CORRIENTE']."|";
	$respuesta =  $respuesta.$result[0]['COD_FAMILIA_DEUDA']."|";
	$respuesta =  $respuesta.$result[0]['NOM_FAMILIA_DEUDA']."|";
}	
	
print $respuesta;
?>