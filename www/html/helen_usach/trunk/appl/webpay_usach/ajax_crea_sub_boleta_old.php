<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../web_service/class_ws_helen.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	
//include("valida_session.php");

$cod_plan_pago 		= $_REQUEST['cod_plan_pago'];
$monto_abono_pesos	= $_REQUEST['monto_abono_pesos'];
$cod_tipo_descuento = "NULL";
$tipo_pago_web		= $_REQUEST['tipo_pago_web'];
//		= "NULL";

$ws_helen = new wakeUp_helen();
//ws consulta deuda de cod_plan_pago
$result = $ws_helen->pago_online_1($cod_plan_pago);
$cod_deuda = $result[0]['COD_DEUDA'];

//ws boleta calculo
$result_c = $ws_helen->pago_online_2($cod_deuda, $cod_plan_pago, $cod_tipo_descuento, $monto_abono_pesos); 

if (count($result_c) != 0) {
	$tipo_pago	= 'PAGO_ABONO';
	$cod_deudor	= session::get('COD_DEUDOR');
	$cod_sub_boletas	= 'NULL';
	///$cod_plan_pago	= lo trae por request 
	$monto_capital	= $result_c[0]['D_MONTO_CAPITAL'];
	$monto_interes	= $result_c[0]['D_MONTO_INTERES'];
	$monto_multa	= $result_c[0]['D_MONTO_MULTA'];
	$monto_moneda	= $result_c[0]['D_MONTO_CAPITAL'];
	$monto_pesos	= $result_c[0]['D_MONTO_PESOS'];
	$cod_tipo_descuento = 'NULL';
	$dscto_utm		= 0;
	$dscto_pesos	= 0;

	$param_wp = "'$tipo_pago'
				 ,$cod_deudor
				 ,'$cod_sub_boletas'
				 ,$cod_plan_pago
				 ,$monto_capital
				 ,$monto_interes
				 ,$monto_multa
				 ,$monto_moneda
				 ,$monto_pesos
				 ,$cod_tipo_descuento
				 ,$dscto_utm
				 ,$dscto_pesos
				 ,'$tipo_pago_web'";

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
		//$fp=fopen("web_pay/publica.pem","r"); 
		$fp=fopen("/var/www/cgi-helen/maestros/publica.pem","r"); //se descomenta para produccion
		$pub_key=fread($fp,8192);
		fclose($fp);
		
		$datos = $usuario.'&'.$password.'&'.$rut_deudor.'&'.$monto_abono_pesos.'&'.$codigo_trans;
		$error = 3; //sin errores
		if(!openssl_get_publickey($pub_key)) {
			$error = 1;	//echo"Error abriendo clave";
			$datosEncriptados = "ERROR_CLAVE";
		}else if(!openssl_public_encrypt($datos, &$datosEncriptados, openssl_get_publickey($pub_key))) {
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