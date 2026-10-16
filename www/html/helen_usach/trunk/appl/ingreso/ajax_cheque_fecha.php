<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$cant_cheque = $_REQUEST['cant_cheque'];
$monto_a_pagar = $_REQUEST['monto_a_pagar'];
$fecha_1er_cheque = $_REQUEST['fecha_1er_cheque'];
$tasa = $_REQUEST['tasa'];
$tasa = str_replace(",", ".", $tasa);

// valida que esten todos los datos para calcular, en caso contrario no hace NADA
if ($cant_cheque==0 || $cant_cheque=='')
	return;
if ($monto_a_pagar==0 || $monto_a_pagar=='')
	return;
if ($fecha_1er_cheque=='')
	return;
if ($tasa=='')
	return;
	
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

$tasa = $tasa/100;
if ($tasa==0)
	$monto_cheque = round($monto_a_pagar / $cant_cheque, 0);
else {
	// Si cambiaron al fecha del 1er cheque de debe actualizar el moonto_pagar
	if (K_TIPO_BD=="oci")
		$sql = "select to_char(Add_Months(sysdate, 1), 'dd/mm/yyyy') FECHA
						,(to_Date('$fecha_1er_cheque','dd/mm/yyyy') - trunc(Add_Months(sysdate, 1))) / 30 DIF_MES
				from DUAL";
	else if (K_TIPO_BD=="mssql")
		$sql = "select convert(varchar, dateadd(month, 1, getdate()), 103) FECHA
						,datediff(day, dateadd(month, 1, getdate()), dbo.to_date('$fecha_1er_cheque')) / 30.0000 DIF_MES";
						
	$res = $db->build_results($sql);
	if ($res[0]['FECHA']!=$fecha_1er_cheque) {
		$monto_a_pagar = $monto_a_pagar * pow(1+$tasa, $res[0]['DIF_MES']);
	}
			
	$monto_cheque = round($monto_a_pagar * ((pow(1+$tasa, $cant_cheque)) * $tasa)/((pow(1+$tasa, $cant_cheque))-1), 0);
}
$result = array();
for ($i=0; $i < $cant_cheque; $i++) {
			
	if (K_TIPO_BD=="oci")
		$sql = "SELECT	to_char(Add_Months(to_Date('$fecha_1er_cheque','dd/mm/yyyy'), $i), 'dd/mm/yyyy') FECHA_DOCUMENTO_PAGO
			from DUAL";
	else if (K_TIPO_BD=="mssql")
		$sql = "select convert(varchar(20), dateadd(month, $i, dbo.to_date('$fecha_1er_cheque')), 103) FECHA_DOCUMENTO_PAGO";
						
	$res_fecha = $db->build_results($sql);
	$result[$i]['FECHA_DOCUMENTO_PAGO'] = $res_fecha[0]['FECHA_DOCUMENTO_PAGO'];
	$result[$i]['MONTO'] = $monto_cheque;
}

print urlencode(json_encode($result));
?>