<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

$cod_deuda 			= $_REQUEST['cod_deuda'];
$cod_plan_pago 		= $_REQUEST['cod_plan_pago'];
$cod_tipo_descuento = $_REQUEST['cod_tipo_descuento'];
$monto_pago_pesos	= $_REQUEST['monto_pago_pesos'];

$cod_tipo_descuento		= ($cod_tipo_descuento=='') ? "NULL" : $cod_tipo_descuento;
$monto_pago_pesos		= ($monto_pago_pesos=='') ? "NULL" : $monto_pago_pesos;	
$cod_plan_pago	= ($cod_plan_pago=='') ? "NULL" : $cod_plan_pago;
	
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

if (K_TIPO_BD=="oci"){
	$sp = 'sp_boleta_calculo';
	$param	= "$cod_deuda, $cod_plan_pago, $cod_tipo_descuento, $monto_pago_pesos"; 

	$db->EXECUTE_SP($sp, $param);
	
	$sql = "SELECT	 D_MONTO_CAPITAL	
					,D_MONTO_INTERES
					,D_MONTO_MULTA
					,D_MONTO_PESOS
					,D_DSCTO_CAPITAL
					,D_DSCTO_INTERES
					,D_DSCTO_MULTA
					,D_DSCTO_PESOS
			FROM	TEMP_TOTAL_BOLETA";
	$db_2 = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	$result = $db_2->build_results($sql);
	$db->ROLLBACK_TRANSACTION();

	$monto_capital	=	$result[0]['D_MONTO_CAPITAL'];
	$monto_interes	=	$result[0]['D_MONTO_INTERES']; 
	$monto_multa	=	$result[0]['D_MONTO_MULTA'];
	$monto_pesos	=	$result[0]['D_MONTO_PESOS'];
	$dscto_capital	=	$result[0]['D_DSCTO_CAPITAL'];
	$dscto_interes	=	$result[0]['D_DSCTO_INTERES'];
	$dscto_multa	=	$result[0]['D_DSCTO_MULTA'];
	$dscto_pesos	=	$result[0]['D_DSCTO_PESOS'];

	$datos = '';
	$datos = $datos.$monto_capital."|";
	$datos = $datos.$monto_interes."|";
	$datos = $datos.$monto_multa."|";
	$datos = $datos.$monto_pesos."|";
	$datos = $datos.$dscto_capital."|";
	$datos = $datos.$dscto_interes."|";
	$datos = $datos.$dscto_multa."|";
	$datos = $datos.$dscto_pesos;
	
	print $datos;
}
else if (K_TIPO_BD=="mssql"){
	$sql = "exec sp_boleta_calculo $cod_deuda, $cod_plan_pago, $cod_tipo_descuento, $monto_pago_pesos";
	
	$result 			= $db->build_results($sql);
	$monto_capital 		= $result[0]['D_MONTO_CAPITAL'];
	$monto_interes	 	= $result[0]['D_MONTO_INTERES'];
	$monto_multa	 	= $result[0]['D_MONTO_MULTA'];
	$monto_pesos	 	= $result[0]['D_MONTO_PESOS'];
	$dscto_capital		= $result[0]['D_DSCTO_CAPITAL'];
	$dscto_interes		= $result[0]['D_DSCTO_INTERES'];
	$dscto_multa		= $result[0]['D_DSCTO_MULTA'];
	$dscto_pesos		= $result[0]['D_DSCTO_PESOS'];
	
	print $monto_capital."|".$monto_interes."|".$monto_multa."|".$monto_pesos."|".$dscto_capital."|".$dscto_interes."|".$dscto_multa."|".$dscto_pesos;
}
?>