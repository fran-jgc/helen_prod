<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$fecha_vencto	= $_REQUEST['fecha_vencto'];
$cuotas			= $_REQUEST['cuotas'];
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$fechas='';
$temp='';

for ($i = 0; $i < $cuotas; $i++) {
	$sql = sql_boleta::get_sql('AJAX_FECHAS',$fecha_vencto,$i);
	$result = $db->build_results($sql);
	$fechas = $fechas.$result[0]['FECHA']."|";
}
$fechas = $temp.$fechas;
print $fechas;
?>