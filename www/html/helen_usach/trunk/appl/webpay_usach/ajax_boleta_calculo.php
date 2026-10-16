<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

$cod_deuda 			= $_REQUEST['cod_deuda'];
$monto_abono_pesos	= $_REQUEST['monto_abono_pesos'];
$cod_plan_pago	= $_REQUEST['cod_plan_pago'];
$cod_tipo_descuento = "NULL";

$db_b = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sp = 'sp_boleta_calculo_web';
$param	= "$cod_deuda, $cod_plan_pago, $cod_tipo_descuento, $monto_abono_pesos"; 
$db_b->EXECUTE_SP($sp, $param);

$sql = "SELECT D_MONTO_CAPITAL	
			,D_MONTO_INTERES
			,D_MONTO_MULTA
			,D_MONTO_PESOS
			,D_DSCTO_CAPITAL
			,D_DSCTO_INTERES
			,D_DSCTO_MULTA
			,D_DSCTO_PESOS
	FROM	TEMP_TOTAL_BOLETA";
$db_c = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result_c = $db_c->build_results($sql);

if (count($result_c) != 0) {
	$monto_capital	= $result_c[0]['D_MONTO_CAPITAL'];
	$monto_interes	= $result_c[0]['D_MONTO_INTERES'];
	$monto_multa	= $result_c[0]['D_MONTO_MULTA'];
	$monto_moneda	= $result_c[0]['D_MONTO_CAPITAL'];
	$monto_pesos	= $result_c[0]['D_MONTO_PESOS'];
	$dscto_utm		= $result_c[0]['D_DSCTO_CAPITAL'];
	$dscto_pesos	= $result_c[0]['D_DSCTO_PESOS'];
	
	print urlencode(json_encode($result_c));
}else{
	$res = "SIN_EXITO";
	print urlencode(json_encode($res));
}	
?>