<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/nusoap-0.7.3/lib/nusoap.php");

//Reprogramacion
$user = "mulloa";

$ns="http://192.168.2.13/desarrolladores/".$user."/commonlib_seven/trunk/php/nusoap-0.7.3/lib/nusoap";
$_wsdl = "Reprogramacion";
$server = new soap_server();
$server->configureWSDL($_wsdl,$ns);
/*$server->wsdl->schemaTargetNamespace=$ns;

$server->wsdl->addComplexType(
    'StatusType',
    'complexType',
    'struct',
    'all',
    '',
    array(
	'codigo' => array('name' => 'codigo', 'type' => 'xsd:string'),
	'mensaje' => array('name' => 'mensaje', 'type' => 'xsd:string')
    )
);

$server->wsdl->addComplexType(
    'RequestConfirmacionType',
    'complexType',
    'struct',
    'all',
    '',
    array(
	'token' => array('name' => 'token', 'type' => 'xsd:string'),
	'idVenta' => array('name' => 'idVenta', 'type' => 'xsd:string')
    )
);

$server->register('confirmarVenta',       
	array('requestConfirmarVenta' => 'tns:RequestConfirmacionType'),
	array('responseConfirmarVenta' => 'tns:StatusType'),	
	$ns,             
	false,                                                  
	'rpc',                                                  
	false,                                                  
	'Confirmacion de la venta de Ipad'                           
	);

function confirmarVenta($requestConfirmarVenta) {
	$_status = array(	'codigo' => '81764751',
                		'mensaje' => 'Este es un mensaje de Anexo' );
			
	return new soapval("return", "tns:StatusType", $_status);
}
*/
//$server->service($HTTP_RAW_POST_DATA); => ok
/*
$server->register('getXML', // Nombre de la funcion
                   array('id_cod' => 'xsd:string'), // Parametros de entrada
                   array('return' => 'xsd:string'), // Parametros de salida
                   $ns);


function getXML($id_cod){
	require_once('XML_resultado.php');

    return new soapval('return', 'xsd:string', base64_encode($xml));
} */

$server->register('getDeudor', // Nombre de la funcion
	//"idPagare" type="xsd:string"
                   array('Universidad' => 'xsd:integer', 'Clave' => 'xsd:string', 'rutDeudor' => 'xsd:integer', 'idPagare' => 'xsd:string'), // Parametros de entrada
                   array('getDeudorReturn' => 'xsd:string'), // Parametros de salida
                   $ns);

function getDeudor($Universidad, $Clave, $rutDeudor, $idPagare){
	require_once('XML_resultado.php');

    return new soapval('getDeudorReturn', 'xsd:string', base64_encode($xml));
} 

$server->register('reprogramar', // Nombre de la funcion
                   array('Universidad' => 'xsd:integer', 'Clave' => 'xsd:string', 'RutDeudor' => 'xsd:integer', 'xmlReprogramacion' => 'xsd:string'), // Parametros de entrada
                   array('reprogramarReturn' => 'xsd:string'), // Parametros de salida
                   $ns);

function reprogramar($Universidad, $Clave, $RutDeudor, $xmlReprogramacion){
	require_once('XML_resultado.php');

    return new soapval('reprogramarReturn', 'xsd:string', base64_encode($xml));
} 

$POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA'])
? $GLOBALS['HTTP_RAW_POST_DATA'] : '';
$server->service($POST_DATA);

?>