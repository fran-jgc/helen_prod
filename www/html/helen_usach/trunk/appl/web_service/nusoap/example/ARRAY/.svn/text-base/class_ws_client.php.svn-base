<?php
	require_once("../../lib/nusoap.php");

	$client = new soapclient('http://192.168.2.140/desarrolladores/jvenegas/simulacion_RCS/trunk/appl/web_service/nusoap/example/ARRAY/class_ws_server.php?wsdl');

    $result = $client->GetWalk(1);

    echo "<pre>";
    print_r($result);
    echo "</pre>";
?>