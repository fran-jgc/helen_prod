<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");

$K_PARAM_CARTERA	   = 2;

$rut = $_REQUEST['rut'];

$cartera_defecto = base::get_parametro($K_PARAM_CARTERA);
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

///////////////// CLASS_SQL = AJAX_DEUDA_CARTERA_DIF_USACH ///////////////
$sql = "SELECT 	COUNT(*) CUANTO
		FROM  	DEUDOR DR, DEUDA DA 
		WHERE 	DR.COD_DEUDOR = DA.COD_DEUDOR 
		AND   	DA.COD_CARTERA  <> $cartera_defecto
		AND   	DR.RUT = $rut";
$result = $db->build_results($sql);

if ($result[0]['CUANTO']!= 0){
	print urlencode('DEUDA_OTRA_CARTERA');
	return;
} 
else {
	$result = $db->build_results($sql);
	print urlencode(json_encode($result));				
}	
?>