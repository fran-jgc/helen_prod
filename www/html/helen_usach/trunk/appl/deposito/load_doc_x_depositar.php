<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_cuenta_corriente = $_REQUEST['cod_cuenta_corriente'];
$fecha_deposito = $_REQUEST['fecha_deposito'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

if ($cod_cuenta_corriente=='') 
	$cod_cuenta_corriente = 0;
$fecha_deposito = base::str2date($fecha_deposito, "23:59:59");
$sql= sql_deposito::get_sql('DOCUMENTO_DEPOSITO',$cod_cuenta_corriente, $fecha_deposito);
$result = $db->build_results($sql);
for($i=0; $i < count($result); $i++) {
	$result[$i]['NOM_BANCO_DP'] = urlencode($result[$i]['NOM_BANCO_DP']); 
	$result[$i]['NOM_PLAZA'] = urlencode($result[$i]['NOM_PLAZA']); 
	$result[$i]['NOM_TIPO_DOCUMENTO_PAGO'] = urlencode($result[$i]['NOM_TIPO_DOCUMENTO_PAGO']); 
}
print urlencode(json_encode($result));
?>