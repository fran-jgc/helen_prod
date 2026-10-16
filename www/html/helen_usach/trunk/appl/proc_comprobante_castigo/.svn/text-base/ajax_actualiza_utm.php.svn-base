<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$fecha_vencto = $_REQUEST['fecha_vencto'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$fecha_vencto = base::str2date($fecha_vencto);
if (K_TIPO_BD=="oci")
	$sql = "SELECT monto_valor_moneda VALOR
			FROM valor_moneda
			WHERE fecha_valor_moneda = add_months($fecha_vencto, 3)";
else if (K_TIPO_BD=="mssql")
	$sql = "SELECT monto_valor_moneda VALOR
			FROM valor_moneda
			WHERE fecha_valor_moneda = dateadd(month, 3, $fecha_vencto)";
$result = $db->build_results($sql);
$count = count($result);
$resultado = '';
if($count > 0){
	$resultado=number_format($result[0]['VALOR'], 0, ',', '.');
}
else{
	$resultado = 'no_utm';
}
print $resultado;
?>