<?php
require_once(dirname(__FILE__)."/../../appl.ini");
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/ajax_get_deudas.php";
if (file_exists($file_name)) { 
	require_once($file_name);
	return;
}
$cod_deuda		= $_REQUEST['cod_deuda'];

$sql_deuda = sql_boleta::get_sql('AJAX_DEUDAS',$cod_deuda);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql_deuda);

$estado_deuda 	= $result[0]['COD_ESTADO_DEUDA'];
$cod_tipo_deuda = $result[0]['COD_TIPO_DEUDA'];

$result = $db->build_results(sql_boleta::get_sql('AJAX_SALDO_DEUDA',$cod_deuda));
$saldo_deuda = $result[0]['SALDO_DEUDA'];

$result = $db->build_results(sql_boleta::get_sql('AJAX_SALDO_CAPITAL',$saldo_deuda,$cod_deuda)); 
$capital = $result[0]['SALDO_CAPITAL'];

$interes =  $saldo_deuda - $capital;

$multa = 0; 
$total = $capital+$interes+$multa;

$result = $db->build_results(sql_boleta::get_sql('AJAX_VALOR_MONEDA',$cod_tipo_deuda)); 
$valor_moneda = $result[0]['VALOR_MONEDA'];

$wi = unserialize(session::get('wi_boleta'));
$sql_original = $wi->dws['dw_detalle_plan_pago']->get_sql();

if($estado_deuda == 1) {	
	$sql = sql_boleta::get_sql('AJAX_DEUDAS_1',$capital,$saldo_deuda,$multa,$total,$valor_moneda,$cod_deuda);
}
else{
	$sql = sql_boleta::get_sql('AJAX_DEUDAS_2',$cod_deuda);
}

$wi->dws['dw_detalle_plan_pago']->set_sql($sql);
$wi->dws['dw_detalle_plan_pago']->make_tabla_htm($wi->nom_template);
$wi->dws['dw_detalle_plan_pago']->set_sql($sql_original);
?>