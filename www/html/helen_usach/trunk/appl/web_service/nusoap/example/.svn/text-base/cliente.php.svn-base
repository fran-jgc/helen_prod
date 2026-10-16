<?php
    require_once("../lib/nusoap.php");
    
    $cliente = new nusoap_client("http://192.168.2.140/desarrolladores/jvenegas/simulacion_RCS/trunk/appl/web_service/nusoap/example/libros.wsdl", true);
	//$cliente = new nusoap_client("http://192.168.2.140/desarrolladores/jvenegas/simulacion_RCS/trunk/appl/web_service/producto.php");
      
    $error = $cliente->getError();
    if ($error) {
        echo "<h2>Constructor error</h2><pre>" . $error . "</pre>";
    }
      
    $result = $cliente->call("getProd", array("categoria" => "libros"));
      
    if ($cliente->fault) {
        echo "<h2>Fault</h2><pre>";
        print_r($result);
        echo "</pre>";
    }
    else {
        $error = $cliente->getError();
        if ($error) {
            echo "<h2>Error</h2><pre>" . $error . "</pre>";
        }
        else {
            echo "<h2>Libros</h2><pre>";
            
            echo base64_decode($result);
			
            $xml = simplexml_load_string(base64_decode($result));

			print_r($xml);
			echo $xml->Fecha;
            
            echo "</pre>";
        }
    }
?>