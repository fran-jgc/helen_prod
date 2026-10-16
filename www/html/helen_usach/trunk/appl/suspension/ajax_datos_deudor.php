<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");

$rut = urldecode($_REQUEST['rut']);

$sql = "SELECT COD_DEUDOR 
		FROM DEUDOR
		WHERE RUT = $rut";
$db 		= new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result 	= $db->build_results($sql);
$row_count	= $db->count_rows();
if ($row_count == 0) {
	print urlencode('NO_RUT');
}
else{
	$sql = "SELECT Count(*) COUNT
			FROM DEUDA
			WHERE COD_DEUDOR = ".$result[0]['COD_DEUDOR']
			." AND COD_TIPO_DEUDA IN ('CS', 'CSR', 'CR12', 'CRI','CAME')
			  AND COD_ESTADO_DEUDA = 2"; //Estado cobranza normal.
	$db 		= new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	$result 	= $db->build_results($sql);
	if ($result[0]['COUNT'] == 0) {
		print urlencode('DEUDA_NO_CS_CSR');
	}else{
		print urlencode('RUT_OK');
	}
}
?>