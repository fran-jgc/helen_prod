<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/nusoap-0.7.3/lib/nusoap.php");

// se ocupa cuando se WS de mineduc
$serverURL = "http://fscu.demo.exec.cl/ws/reprogramar.php";
$serverScript = 'reprogramar.php';
$metodoALlamar = 'reprogramar';

$cliente = new nusoap_client("$serverURL/$serverScript?wsdl", 'wsdl');
// Se pudo conectar?
$error = $cliente->getError();
if ($error) {
	echo '<pre style="color: red">' . $error . '</pre>';
    echo '<p style="color:red;'>htmlspecialchars($cliente->getDebug(), ENT_QUOTES).'</p>';
    die();
}

$xmlReprogramacion ="<EntradaConfirmaReprogramacion>
						 <Pagares>
							<Pagare>
								<IdPagare>020100000299</IdPagare>
								<FechaPagare>20/09/2012</FechaPagare>
							</Pagare>
						 </Pagares>
						 <FechaReprogramacion>20/09/2012</FechaReprogramacion>
					 </EntradaConfirmaReprogramacion>";

// 1. Llamar a la funcion getRespuesta del servidor
//$param=array('Universidad'=>76823444, 'Clave'=>'password123', 'RutDeudor'=>12220606, 'xmlReprogramacion'=>$xmlReprogramacion);
$param=array('Universidad'=>81698900, 'clave'=>'x7pmm6t2', 'rutDeudor'=>13049544, 'xmlReprogramacion'=>$xmlReprogramacion);

//print_r($param);
//return;
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
    	//echo $result.'<br>';
    	
    	//se ocupa localhost
    	require("xml2array.php");
		//$xml_result = base64_decode($result);
		print_r($result);
		echo '<br><br>';
    	//$resultado = xml2array($xml_result);
        //print_r($resultado);
    }
}


?>