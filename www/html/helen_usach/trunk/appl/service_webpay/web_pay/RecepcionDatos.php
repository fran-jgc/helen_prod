<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
require_once ("../../../appl.ini");
require_once ("../funciones.php");
/*
$bd_type = K_TIPO_BD;
$bd_host = K_SERVER;
$bd_name = K_BD;
$bd_user = K_USER;
$bd_pass = K_PASS;
$bd_data = $bd_type.'&'.$bd_host.'&'.$bd_name.'&'.$bd_user.'&'.$bd_pass;
//$fp=fopen("clave_public.pem","r");
//$pub_key=fread($fp,8192);
//fclose($fp);
//$error = 3;// SIN ERRORES
/*if(!openssl_get_publickey($pub_key)) {
	$error = 1;	//echo"Error abriendo clave";
	$param_descripter = "ERROR_CLAVE";
}
$llave_pub = openssl_get_publickey($pub_key);
if(!openssl_public_encrypt($bd_data, $param_descripter, $llave_pub) ) {
	$error = 2; //echo "Error encriptando datos";
	$param_descripter = "ERROR_ENCRIPTADO";
}


$TBK_BD = base64url_encode($bd_data);*/

$parametros = $_REQUEST['parametros'];
//$usuario.'&'.$password.'&'.$rut_deudor.'&'.$monto_abono_pesos.'&'.$codigo_trans
//fondoCreditoWebPay&WebPay&910131419&22153&13
$usuario = "fondoCreditoWebPay";  ///usuario de conexion
$password = "WebPay";	///password de conexion

$datos = base64_decode($parametros);

/*
//$fp=fopen("/var/www/cgi-helen/maestros/privada.pem","r");
$fp=fopen("privada.pem","r");
$pub_key=fread ($fp,8192);

if(!openssl_get_privatekey($pub_key)) {
	$error = 1;	//echo"Error abriendo clave";
	$param_descripter = "ERROR_CLAVE";
}else if(!openssl_private_decrypt($datos, &$param_descripter, openssl_get_privatekey($pub_key))) {
	$error = 2; //echo "Error encriptando datos";
	$param_descripter = "ERROR_ENCRIPTADO";
}*/

$dato = explode('&', $datos);
$usuario_webpay = $dato[0]; //rut+dig sin gion
$pass_webpay = $dato[1];
$rut_deudor = $dato[2];
$monto_abono_pesos = $dato[3];
$codigo_trans = $dato[4];
/***falta de descriptacion de datos**/

if($usuario_webpay == $usuario && $pass_webpay == $password){
	//Monto. Sin separadores de miles ni de decimales. Se asume los dos últimos dígitos como valores decimales. LARGO = 10  TIPO = N
	$TBK_MONTO = $monto_abono_pesos;
	//Orden de compra, generado por el comercio. LARGO = 26 TIPO = A
	$TBK_ORDEN_COMPRA = $codigo_trans;
	$TBK_CODIGO_TIENDA = '597032909078'; //codigo tienda 1 
	/******se cambia y apunta al servidor que corresponde usach_pro******************/
	//$TBK_URL_EXITO = 'http://201.238.213.60/wan/helen/trunk/appl/webpay_usach/web_pay/exito.php';
	//$TBK_URL_FRACASO = 'http://201.238.213.60/wan/helen/trunk/appl/webpay_usach/web_pay/fracaso.php';

	$TBK_URL_EXITO = 'https://fondocredito.usach.cl/new_helen_pro/helen/trunk/appl/webpay_usach/web_pay/exito.php';
	$TBK_URL_FRACASO = 'https://fondocredito.usach.cl/new_helen_pro/helen/trunk/appl/webpay_usach/web_pay/fracaso.php';

	
	//$url  = 'https://webpay.integrasystem.cl/webpay_ws_usach/transaccion_completa_mall.php?';
	
	$url  = 'http://192.168.2.139/desarrolladores/desarrolladores/evergara_usach/helen_usach/trunk/webpay_ws_usach/transaccion_completa_mall.php?';
	//$url  = 'https://fondocredito.usach.cl/cgi-helen/tbk_bp_pago.cgi?TBK_TIPO_TRANSACCION='.$TBK_TIPO_TRANSACCION;
	$url .= 'TBK_MONTO='.$TBK_MONTO;
	$url .= '&TBK_ORDEN_COMPRA='.$TBK_ORDEN_COMPRA;
	//$url .= '&TBK_BD='.$TBK_BD;
	$url .= '&TBK_CODIGO_TIENDA='.$TBK_CODIGO_TIENDA;
	
	header ('Location: '.$url);
}else{
	$error = 1;
	echo "error al encriptar";
}
?>
