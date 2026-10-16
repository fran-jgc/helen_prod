<?php
    require_once("../lib/nusoap.php");
      
    function getProd($categoria) {
        $contenido="<?xml version='1.0' encoding='UTF-8'?> 
					 <Message>
        			    <Fecha>01/04/2011</Fecha>
				        <Hora>16:57</Hora>
				        <Ds_SecurePayment>1</Ds_SecurePayment>
				        <Ds_Amount>345</Ds_Amount>
				        <Ds_Currency>978</Ds_Currency>
				        <Ds_Order>165446</Ds_Order>
				        <Ds_MerchantCode>123456</Ds_MerchantCode>
				        <Ds_Terminal>001</Ds_Terminal>
				        <Ds_Card_Country>724</Ds_Card_Country>
				        <Ds_Response>0000</Ds_Response>
				        <Ds_MerchantData>Alfombrilla para raton</Ds_MerchantData>
				        <Ds_Card_Type>C</Ds_Card_Type>
				        <Ds_TransactionType>1</Ds_TransactionType>
				        <Ds_ConsumerLanguage>1</Ds_ConsumerLanguage>
					</Message>"; 
		$contenido=base64_encode($contenido);
		return $contenido;
    }
      
    $server = new soap_server();
    $server->configureWSDL("producto", "urn:producto");
      
    $server->register("getProd",
        array("categoria" => "xsd:string"),
        array("return" => "xsd:string"),
        "urn:producto",
        "urn:producto#getProd",
        "rpc",
        "encoded",
        "Nos da una lista de productos de cada categoría");
    
    $server->service($HTTP_RAW_POST_DATA);
?>