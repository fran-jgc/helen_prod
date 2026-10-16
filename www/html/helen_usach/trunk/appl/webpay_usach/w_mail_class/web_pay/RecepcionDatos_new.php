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
$fp=fopen("clave_public.pem","r");
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
$usuario = "fondoCreditoWebPay";  ///usuario de conexion
$password = "WebPay";	///password de conexion

$datos = base64_decode($parametros);

$fp=fopen("privada.pem","r");
$pub_key=fread ($fp,8192);

if(!openssl_get_privatekey($pub_key)) {
	$param_descripter = "ERROR_CLAVE";
}else if(!openssl_private_decrypt($datos, $param_descripter, openssl_get_privatekey($pub_key))) {
	$param_descripter = "ERROR_ENCRIPTADO";
}

$dato = explode('&', $param_descripter);
$usuario_webpay = $dato[0]; //rut+dig sin gion  
echo $usuario_webpay.'$usuario_webpay';
$pass_webpay = $dato[1];
$monto_abono_pesos = $dato[3];
$codigo_trans = $dato[4];
/***falta de descriptacion de datos**/

if($usuario_webpay == $usuario && $pass_webpay == $password){
	//Monto. Sin separadores de miles ni de decimales. Se asume los dos �ltimos d�gitos como valores decimales. LARGO = 10  TIPO = N
	$TBK_MONTO = $monto_abono_pesos;
	//Orden de compra, generado por el comercio. LARGO = 26 TIPO = A
	$TBK_ORDEN_COMPRA = $codigo_trans;
	$TBK_CODIGO_TIENDA = '597032909078'; //codigo tienda 1 
	/******se cambia y apunta al servidor que corresponde usach_pro******************/
    //$url  = 'http://sishelen.usach.cl/new_helen_pro/helen/trunk/webpay_ws_usach/transaccion_completa_mall.php?';
    $url  = 'https://webpay.integrasystem.cl/webpay_ws_usach/transaccion_completa_mall.php?';
	$url .= 'TBK_MONTO='.$TBK_MONTO;
	$url .= '&TBK_ORDEN_COMPRA='.$TBK_ORDEN_COMPRA;
	$url .= '&TBK_BD='.$TBK_BD;
	$url .= '&TBK_CODIGO_TIENDA='.$TBK_CODIGO_TIENDA;
	
	header ('Location: '.$url);
}else{
	echo "error al encriptar";
}
?>
