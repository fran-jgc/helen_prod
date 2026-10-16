<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
 
$rut 				= $_REQUEST['rut'];
$cod_deuda_dd		= $_REQUEST['cod_deuda_dd'];
$cod_plan_pago_pp	= $_REQUEST['cod_plan_pago_pp'];

$wi = unserialize(session::get('wi_anula_abono'));
$sql_original = $wi->dws['dw_abono']->get_sql();

if($cod_plan_pago_pp == 9999) ///busca abonos para deudas sin plan de pago
	$sql = sql_anula_abono::get_sql('PAGO_DEUDA_NO_EXIGIBLE',$rut,$cod_deuda_dd);
else
	$sql = sql_anula_abono::get_sql('PAGO_VIGENTE',$rut,$cod_deuda_dd,$cod_plan_pago_pp);
		
$wi->dws['dw_abono']->set_sql($sql);
$wi->dws['dw_abono']->make_tabla_htm($wi->nom_template);
$wi->dws['dw_abono']->set_sql($sql_original);
$wi->save_SESSION();
?>