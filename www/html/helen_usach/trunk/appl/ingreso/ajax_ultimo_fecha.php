<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$datos = $_REQUEST['datos'];
$tasa =  $_REQUEST['tasa'];
$monto_a_pagar = $_REQUEST['monto_a_pagar'];
$tasa = str_replace(",", ".", $tasa);

$datos = explode("|", $datos);
$result = array();
for($i=0, $j=0; $i < count($datos) - 1; $i += 2, $j++) {
	$result[$j] = array('FECHA_DOCUMENTO_PAGO' => $datos[$i], 'MONTO' => $datos[$i + 1]);
}

$actual = 0;
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
for($i=0; $i < count($result); $i++) {
	$fecha = $result[$i]['FECHA_DOCUMENTO_PAGO'];
	$monto = $result[$i]['MONTO'];
	if (K_TIPO_BD=="oci")
		$sql = "select to_date('$fecha', 'dd/mm/yyyy') - trunc(sysdate) DIF_DIAS from DUAL";
	else if (K_TIPO_BD=="mssql")
		$sql = "select datediff(day, getdate(), dbo.to_date('$fecha')) DIF_DIAS";
	
	
	$res_dif = $db->build_results($sql);
	$periodo = $res_dif[0]['DIF_DIAS'] / 30; 
    $actual += $monto  / pow(1 + $tasa/100, $periodo);
}
$deltaHoy = $monto_a_pagar - $actual;
$delta = $deltaHoy * pow(1 + $tasa/100, $periodo);

$monto_ultimo = round($result[count($result) - 1]['MONTO'] + $delta, 0);
print $monto_ultimo;
?>