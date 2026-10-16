<?php
///////////////////////////////////////////
// php llamado con ajax en el javascript //
///////////////////////////////////////////
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");

$rut = $_REQUEST['rut'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

///////////////// CLASS_SQL = AJAX_DEUDA_CARTERA_DIF_USACH ///////////////
$sql = "SELECT 	to_date(add_months(FECHA_INICIO_COBRO, -1),'dd/mm/yyyy') FECHA_INI
				,to_date(sysdate,'dd/mm/yyyy') FECHA_ACTUAL
		FROM  	DEUDOR DR, DEUDA DA 
		WHERE 	DR.COD_DEUDOR = DA.COD_DEUDOR 
		AND   	DR.RUT = $rut";
$result = $db->build_results($sql);

$fecha_actual = strtotime($result[0]['FECHA_ACTUAL']);
$fecha_ini = strtotime($result[0]['FECHA_INI']); 

if ($fecha_actual < $fecha_ini){
	print urlencode('NO_PUEDE_HACER_NOVACION');
	return;
}
?>