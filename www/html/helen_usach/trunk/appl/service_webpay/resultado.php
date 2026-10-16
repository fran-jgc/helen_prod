<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

session::set('K_ROOT_URL', K_ROOT_URL);
session::set('K_ROOT_DIR', K_ROOT_DIR);
session::set('K_CLIENTE', K_CLIENTE);
session::set('K_APPL', K_APPL);

$cod_estado_transaccion 		= $_REQUEST['TBK_ESTADO'];

if ($cod_estado_transaccion == 'APROBADO') {
	$tbk_codigo_autorizacion = $_REQUEST['TBK_CODIGO_AUTORIZACION'];
	$tbk_fecha_transaccion  = $_REQUEST['TBK_FECHA_TRANSACCION'];
	$tbk_tarjeta_final = $_REQUEST['TBK_TARJETA_FINAL'];
	$tbk_nro_cuota = $_REQUEST['TBK_NUMERO_CUOTAS'];
	$tbk_tipo_cuota =  $_REQUEST['TBK_TIPO_CUOTA'];
	$tbk_orden_compra =  $_REQUEST['TBK_ORDEN_COMPRA'];  //(rut.'/'.monto.'/'.cod_transaccion)
	
	$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	//$fecha_declaracion = base::str2date($db->current_date(), $db->current_time()); fechas
	$dato = explode('/', $tbk_orden_compra);
	$rut = $dato[0]; //rut+dig sin gion
	$monto_pago = $dato[1];
	$cod_transaccion = $dato[2];
	$tipo_respuesta = '1'; //EXITO
	
	$sp = 'spw_web_pay_pago';
	$param	= "$cod_transaccion
				,'$tipo_respuesta'";
	//echo $sp.' - '.$param;
	//return true;
	$db->BEGIN_TRANSACTION();
	if ($db->EXECUTE_SP($sp, $param)){
		$data = $cod_estado_transaccion."|".$tbk_codigo_autorizacion."|".$tbk_fecha_transaccion."|".$tbk_tarjeta_final."|".$tbk_nro_cuota."|".$tbk_tipo_cuota."|".$tbk_orden_compra;
		$db->COMMIT_TRANSACTION();
		header ('Location:'."webpay_cupon.php?data=$data");
		return true;
	}
	$db->ROLLBACK_TRANSACTION();
	header ('Location:'."error.php?error=7");
	return false;
	
}else if ($cod_estado_transaccion == 'RECHAZADO') {
	$tbk_orden_compra =  $_REQUEST['TBK_ORDEN_COMPRA'];  //(rut.'/'.monto.'/'.cod_transaccion)
	//rechazado
	$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	$dato = explode('/', $tbk_orden_compra);
	$rut = $dato[0]; //rut+dig sin gion
	$monto_pago = $dato[1];
	$cod_transaccion = $dato[2];
	$tipo_respuesta = '0'; //FRACASO
	
	$sp = 'spw_web_pay_pago';
	$param	= "$cod_transaccion
				,'$tipo_respuesta'";

	$db->BEGIN_TRANSACTION();
	if ($db->EXECUTE_SP($sp, $param)){
		$data = $cod_estado_transaccion."|".$tbk_orden_compra;
		$db->COMMIT_TRANSACTION();
		header ('Location:'."webpay_cupon.php?data=$data");
		return true;
	}
	$db->ROLLBACK_TRANSACTION();
	header ('Location:'."error.php?error=7");
	return false;
}
?>