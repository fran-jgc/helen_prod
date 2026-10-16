<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
require_once ("../../../appl.ini");
require_once ("../funciones.php");


	$bd_type = K_TIPO_BD;
	$bd_host = K_SERVER;
	$bd_name = K_BD;
	$bd_user = K_USER;
	$bd_pass = K_PASS;
	$bd_data = $bd_type.'&'.$bd_host.'&'.$bd_name.'&'.$bd_user.'&'.$bd_pass;
	$fp=fopen("key_public.pem","r");
	$pub_key=fread($fp,8192);
	fclose($fp);
	
	if(!openssl_get_publickey($pub_key)) {
	    $param_descripter = "ERROR_CLAVE";
	}
	$llave_pub = openssl_get_publickey($pub_key);
	if(!openssl_public_encrypt($bd_data, $param_descripter, $llave_pub) ) {
	    $param_descripter = "ERROR_ENCRIPTADO";
	}
	$TBK_BD = base64url_encode($param_descripter);
	
	$parametros = $_REQUEST['parametros'];
	
	print_r($parametros);
	$usuario = "fondoCreditoWebPay";  ///usuario de conexion
	$password = "WebPay";	///password de conexion
	
	$datos = base64_decode($parametros);
	
	//deben estar fuera de la carpeta publicadora y borrar en produccion estos archivos texto plano
	$fp=fopen("../../../../../../llaves_portal/webpay_rest/key_privada.pem","r"); 
	//$fp=fopen("key_privada.pem","r");
	$pub_key=fread ($fp,8192);
	
	if(!openssl_get_privatekey($pub_key)) {
	    $param_descripter = "ERROR_CLAVE";
	}else if(!openssl_private_decrypt($datos, $param_descripter, openssl_get_privatekey($pub_key))) {
	    $param_descripter = "ERROR_ENCRIPTADO";
	}

	$dato = explode('&', $param_descripter);
	$usuario_webpay = $dato[0]; //rut+dig sin gion
	$rut_webpay = $dato[2]; //rut+dig sin gion
	
	/*if($rut_webpay != 183414631){
	 echo 'Servicio no disponible, favor intente mas tarde.';
	 die;
	 }
	 */
	
	$pass_webpay = $dato[1];
	$monto_abono_pesos = $dato[3];
	$codigo_trans = $dato[4];
	/***falta de descriptacion de datos**/
	
	if($usuario_webpay == $usuario && $pass_webpay == $password){

		//$url  = 'http://192.168.2.92/desarrolladores/ecastillo_casa/helen_usach/trunk/webpay_rest_usach/webpay-plus/index.php?action=create';
		$url  = 'https://sishelen.usach.cl/helen_usach/trunk/webpay_rest_usach/webpay-plus/index.php?action=create'; //OFICIAL
		$url .= '&TBK_MONTO='.$monto_abono_pesos;
		$url .= '&TBK_ORDEN_COMPRA='.$codigo_trans; 

		$dbc = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sp_bitacora = 'spw_bitacora_transaccion';
		$param = "'$codigo_trans','Recepción Datos','LLAMADO DE INICIO TRANSACCION'";
		$dbc->EXECUTE_SP($sp_bitacora,$param);
		
		header ('Location: '.$url);
		return;
	
		
	}else{
		echo "En estos momentos no es posible efectuar la Transacción";
		return;
	}
?>