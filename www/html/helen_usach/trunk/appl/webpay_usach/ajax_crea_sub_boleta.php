<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	
//include("valida_session.php");

$cod_plan_pago 		= $_REQUEST['cod_plan_pago'];
$cod_deuda 			= $_REQUEST['cod_deuda'];
$monto_abono_pesos	= $_REQUEST['monto_abono_pesos'];
$cod_tipo_descuento = "NULL";
$tipo_pago_web		= $_REQUEST['tipo_pago_web'];
//		= "NULL";

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
if($cod_plan_pago == "NULL"){
	$sql = "SELECT	 COD_DEUDA
					,COD_ESTADO_DEUDA
			FROM DEUDA
			WHERE COD_DEUDA = $cod_deuda";
}
else{
	$sql = "SELECT	 COD_DEUDA
					,2 COD_ESTADO_DEUDA
			FROM PLAN_PAGO
			WHERE COD_PLAN_PAGO = $cod_plan_pago";
}
$result = $db->build_results($sql);
$cod_deuda = $result[0]['COD_DEUDA'];
$cod_estado_deuda = $result[0]['COD_ESTADO_DEUDA'];

$db_b = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sp = 'sp_boleta_calculo_web';
$param	= "$cod_deuda, $cod_plan_pago, $cod_tipo_descuento, $monto_abono_pesos"; 
$db_b->EXECUTE_SP($sp, $param);

$sql = "SELECT	 D_MONTO_CAPITAL	
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
//$result_c->ROLLBACK_TRANSACTION();

if (count($result_c) != 0) {
	$tipo_pago	= 'PAGO_ABONO';
	$cod_deudor	= session::get('COD_DEUDOR');
	$cod_sub_boletas	= 'NULL';
	///$cod_plan_pago	= lo trae por request 
	$monto_capital	= $result_c[0]['D_MONTO_CAPITAL'];
	$monto_interes	= $result_c[0]['D_MONTO_INTERES'];
	$monto_multa	= $result_c[0]['D_MONTO_MULTA'];
	$monto_moneda	= $monto_capital+$monto_interes+$monto_multa;
	$monto_pesos	= $result_c[0]['D_MONTO_PESOS'];
	$cod_tipo_descuento = 'NULL';
	
	if($cod_estado_deuda == 1){
		$dscto_utm		= $result_c[0]['D_DSCTO_CAPITAL'];
		$dscto_pesos	= $result_c[0]['D_DSCTO_PESOS'];
	}else{
		$dscto_utm		= 0;
		$dscto_pesos	= 0;
	}
	$monto_total_abono = $monto_abono_pesos -  $dscto_pesos;
	$monto_capital = $monto_capital - $dscto_utm;
	$monto_moneda = $monto_capital  + $monto_interes;
	$monto_pesos = $monto_pesos -  $dscto_pesos;
	
	$ds = new datos_server();
	
	$ip						= $ds->getRealIP();
	$navegador				= $ds->browser();
	$sistema_operativo		= $ds->so();

	$param_wp = "'$tipo_pago'
				 ,$cod_deudor
				 ,'$cod_sub_boletas'
				 ,$cod_plan_pago
				 ,$cod_deuda
				 ,$monto_capital
				 ,$monto_interes
				 ,$monto_multa
				 ,$monto_moneda
				 ,$monto_pesos
				 ,$cod_tipo_descuento
				 ,$dscto_utm
				 ,$dscto_pesos
				 ,'$tipo_pago_web'
				 ,'$ip'
				 ,'$navegador'
				 ,'$sistema_operativo'";

	$db_sp = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	$sql_sp = 'spw_wp_transaccion';

	if ($db_sp->EXECUTE_SP($sql_sp, $param_wp)){
		//identity de WP_SUB_BOLETA => sub_boleta
		$cod_wp_sub_boleta = $db_sp->GET_IDENTITY("WP_SUB_BOLETA");

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql_deudor = "SELECT to_char(RUT) || DIG_VERIF RUT
						FROM	DEUDOR
						WHERE	COD_DEUDOR = $cod_deudor";
		$result_deudor = $db->build_results($sql_deudor);
		$rut = $result_deudor[0]['RUT'];

		$sql_trans = 	"select COD_WP_TRANSACCION
						 from	WP_SUB_BOLETA
						 where	COD_WP_SUB_BOLETA = $cod_wp_sub_boleta";
		$result_trans = $db->build_results($sql_trans);
		$cod_wp_transaccion = $result_trans[0]['COD_WP_TRANSACCION'];
		
		///SE ENCRIPTA LOS DATOS Y SE ENVIA AL FORMULARIO
		$usuario = "fondoCreditoWebPay";  ///usuario de conexion
		$password = "WebPay";	///password de conexion
		$rut_deudor = $rut;
		$monto_abono_pesos = number_format($monto_pesos, 0, '', '');
		$codigo_trans = $cod_wp_transaccion;
		
		//estas llaves se van ocupar tanto para servipag y transbank
		//es de uso entre servidor usach y cliente-web
		//deben estar fuera de la carpeta publicadora y borrar en produccion estos archivos texto plano
		$fp=fopen("../../../../../llaves_portal/webpay_rest/key_public.pem","r"); 
		//$fp=fopen("web_pay_rest/key_public.pem","r"); 
		//$fp=fopen("/var/www/cgi-helen/maestros/publica.pem","r");  //se descomenta para produccion
		$pub_key=fread($fp,8192);
		fclose($fp);
		
		$datos = $usuario.'&'.$password.'&'.$rut_deudor.'&'.$monto_abono_pesos.'&'.$codigo_trans;
		$error = 3; //sin errores
		if(!openssl_get_publickey($pub_key)) {
			$error = 1;	//echo"Error abriendo clave";
			$datosEncriptados = "ERROR_CLAVE";
		}else if(!openssl_public_encrypt($datos, $datosEncriptados, openssl_get_publickey($pub_key))) {
			$error = 2; //echo "Error encriptando datos";
			$datosEncriptados = "ERROR_ENCRIPTADO";
		}
		
		if($error == 3){
			//SE ENCRIPTA LOS DATOS Y SE ENVIA AL FORMULARIO		
			print urlencode(base64_encode($datosEncriptados));
		}else{
			print urlencode($datosEncriptados);
		}
		
	}else{
		$res = "SIN_EXITO";
		print urlencode($res);
	}	
}
?>
