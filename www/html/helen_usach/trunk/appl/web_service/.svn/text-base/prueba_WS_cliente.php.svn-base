<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/nusoap-0.7.3/lib/nusoap.php");

$user = "vmelo";

$serverURL = "http://192.168.2.13/desarrolladores/".$user."/helen/trunk/appl/web_service/WS_server.php";
$serverScript = 'WS_server.php';
$metodoALlamar = 'getXML';

$cliente = new nusoap_client("$serverURL/$serverScript?wsdl", 'wsdl');
// Se pudo conectar?
$error = $cliente->getError();
if ($error) {
	echo '<pre style="color: red">' . $error . '</pre>';
    echo '<p style="color:red;'>htmlspecialchars($cliente->getDebug(), ENT_QUOTES).'</p>';
    die();
}

// 1. Llamar a la funcion getRespuesta del servidor
$param=array('id_cod'=>'60.00',);
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
        $xml_result = '';
    } else {
        $xml_result = base64_decode($result);

    }
} 

if($xml_result != ''){
	require("xml2array.php");
	
	$xml = xml2array($xml_result);
	print_r($xml);
}
?>