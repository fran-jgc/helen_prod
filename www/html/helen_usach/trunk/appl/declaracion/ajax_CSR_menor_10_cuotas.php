<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$rut 		= $_REQUEST['rut'];
//$ano_declaracion	= $_REQUEST['ano_declaracion'];

$K_TIPO_DEUDA_CSR = 'CSR';
/*$K_DECL_SOLICITADA = 1;
$K_DECL_APROBADA = 2;
$K_PARAM_CARTERA = 2;*/

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql =sql_declaracion::get_sql('AJAX_TIPO_DEUDA', $rut);
$result = $db->build_results($sql);

if ($result[0]['COD_TIPO_DEUDA']== $K_TIPO_DEUDA_CSR){
	$sql =sql_declaracion::get_sql('AJAX_CANT_CUOTAS', $rut);
	$result = $db->build_results($sql);
	
	if($result[0]['CANT_CUOTA_DEUDA'] < $result[0]['CANT_CUOTA']){
		print urlencode('CANT_CUOTAS_MENOR');
		return;
		
	}
	
}
?>