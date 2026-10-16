<?php
	require_once(dirname(__FILE__)."/../../../../../../../commonlib_seven2/trunk/php/auto_load.php");
	require_once(dirname(__FILE__)."/../../../../../appl.ini");
	$namespace = "http://192.168.2.140/desarrolladores/jvenegas/simulacion_RCS/trunk/appl/web_service/nusoap/example/ARRAY/class_ws_server.php?wsdl";
	 // call library
	require_once("../../lib/nusoap.php");
	
	$server = new soap_server();
	$server->debug_flag = false;
	$server->configureWSDL("WackyWsdl", $namespace);
	$server->wsdl->schemaTargetNamespace = $namespace;
	
	$server->wsdl->addComplexType(
	  'Walk',
	  'complexType',
	  'struct',
	  'all',
	  '',
	  array(
	    'WalkId' 			=> array('name' => 'WalkId'			,'type' => 'xsd:int'),
	    'WalkTitle' 		=> array('name' => 'WalkTitle'		,'type' => 'xsd:string'),
	    'WalkDate' 			=> array('name' => 'WalkDate'		,'type' => 'xsd:int'),
	    'WalkDescription' 	=> array('name' => 'WalkDescription','type' => 'xsd:string')
	  )
	);
	
	$server->wsdl->addComplexType(
	  'Walks',
	  'complexType',
	  'array',
	  '',
	  'SOAP-ENC:Array',
	  array(),
	  array(
	    array('ref' => 'SOAP-ENC:arrayType',
	         'wsdl:arrayType' => 'tns:Walk[]')
	  ),
	  'tns:Walk'
	);
	$server->register('GetWalk',                    // method name
	  array('WalkId' => 'xsd:int'),          // input parameters
	  array('return' => 'tns:Walks'),    // output parameters
	  $namespace,                         // namespace
	  $namespace . '#GetWalk',                   // soapaction
	  'rpc',                                    // style
	  'encoded',                                // use
	  'Get Specific Walk'        // documentation
	);
	function GetWalk($walkid)
	{
		/*
	        $test[0]['WalkId']  = $walkid;
	        $test[0]['WalkTitle'] = "Title of my long walk";
	        $test[0]['WalkDate'] =  date("Y-m-d", time());
	        $test[0]['WalkDescription'] = "Really long walk description";
	        
	        $test[1]['WalkId']  = $walkid;
	        $test[1]['WalkTitle'] = "Title of my long walk";
	        $test[1]['WalkDate'] =  date("Y-m-d", time());
	        $test[1]['WalkDescription'] = "Really long walk description";
	        
	       return $test; 
	      */ 
		//This is how to send a normal array!
		  //return array(
		  //  "WalkId" => $walkid,
		  //  "WalkTitle" => "Title of my long walk",
		  //  "WalkDate" => date("Y-m-d", time()),
		  //  "WalkDescription" => "Really long walk description"
		  ///);
		  
	    $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
        $sql_cod_deudor = "select TOP 4 NOMBRE,RUT from LOG_DEUDOR";
		$result = $db->build_results($sql_cod_deudor);
		
		for($i = 0;$i < count($result);$i++){
			$datos[$i]['WalkId']	= $result[$i]['RUT'];
			$datos[$i]['WalkTitle']	= $result[$i]['NOMBRE'];
			$datos[$i]['WalkDate']	= $result[$i]['RUT'];
			$datos[$i]['WalkDescription']	= $result[$i]['NOMBRE'];
		}	
		return $datos; 
	}
	
	 $HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
	 $server->service($HTTP_RAW_POST_DATA);

?>