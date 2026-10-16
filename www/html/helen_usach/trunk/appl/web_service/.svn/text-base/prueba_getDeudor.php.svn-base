<?php
require_once(dirname(__FILE__)."/../../appl.ini");
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/nusoap-0.7.3/lib/nusoap.php");

/* se ocupa cuando es WS de mineduc 
demo => "fscu.demo.exec.cl" 
oficial => reprogramar.creditosolidario.cl*/
//$serverURL = "http://fscu.demo.exec.cl/ws/reprogramar.php";
$serverURL = "http://reprogramar.creditosolidario.cl/ws/getDeudor.php";

$serverScript = 'getDeudor.php';
$metodoALlamar = 'getDeudor';
/***primero creo la WS  luego depiego el mensaje exito � fracaso ***/
/*$user = "mulloa";
$serverURL = "http://192.168.2.13/desarrolladores/".$user."/helen/trunk/appl/web_service/WS_server.php";
$serverScript = 'WS_server.php';
$metodoALlamar = 'getDeudor';*/


$cliente = new nusoap_client("$serverURL/$serverScript?wsdl", 'wsdl');
// Se pudo conectar?
$error = $cliente->getError();
if ($error) {
	echo '<pre style="color: red">' . $error . '</pre>';
    echo '<p style="color:red;'>htmlspecialchars($cliente->getDebug(), ENT_QUOTES).'</p>';
    die();
}

// 1. Llamar a la funcion getRespuesta del servidor
/*
$K_PARAM_RUT_UNI = 4;
$K_PARAM_MINEDUC = 33;
$rut_universidad = str_replace('.', '', base::get_parametro($K_PARAM_RUT_UNI));
//$rut_universidad = str_replace('-', '', $rut_universidad);
$array = explode('-', $rut_universidad);
$rut_universidad = $array[0];
$div_universidad = $array[1];
$clave_ws_mineduc = base::get_parametro($K_PARAM_MINEDUC);*/

//$param=array('Universidad'=>60910000, 'clave'=>'123', 'rutDeudor'=>700653, 'idPagare'=>'');
//$param=array('Universidad'=>$rut_universidad, 'clave'=>"'$clave_ws_mineduc'", 'rutDeudor'=>21107062, 'idPagare'=>'');

$param=array('Universidad'=>81380500, 'clave'=>'9gckx3fg', 'rutDeudor'=>10710071, 'idPagare'=>'070100001113');

$result = $cliente->call(
    "$metodoALlamar",                     // Funcion a llamar
    $param,    // Parametros pasados a la funcion
    "uri:$serverURL/$serverScript",                   // namespace
    "uri:$serverURL/$serverScript/$metodoALlamar"       // SOAPAction
);
// Verificacion que los parametros estan ok, y si lo estan. mostrar rta.

if ($cliente->fault) {
	echo '<b>Error: ';
	print_r($result);
    echo '</b>';
} else {
    $error = $cliente->getError();
	if ($error) {
    	echo '<b style="color: red">-Error: ' . $error . '</b>';
    } else {
    	//se ocupa cuando se WS de mineduc
    	//echo $result.'<br>MU';	
    	//se ocupa localhost
    	require("xml2array.php");
		//$xml_result = base64_decode($result);
		$xml_result = $result; //base64_decode($result);
	//print_r($result);

	echo '<br><br>';
	echo $result;
    echo '<br><br>';
		$resultado = xml2array("<?xml version='1.0' encoding='iso-8859-1'?>".$result);
		print_r($resultado);
		
		$error = $resultado['RESPUESTA']['MENSAJE'];
		if($error == 'Datos de autenticaci'){
			echo '<br><br>';
			echo '<br>1';
		}else if($error == 'Datos de deudor incorrecto'){
			echo '<br>2';
		}else{
			echo '<br>3';
		}
		
    	/*echo '<br><br>';
		$resultado = xml2array($xml_result);
    	print_r($resultado);
    	echo '<br><br>';
    	echo print_r($resultado['RESPUESTA']['CODIGO']);
    	echo '<br><br>';
    	echo print_r($resultado['RESPUESTA']['MENSAJE']);*/

	}
}


?>