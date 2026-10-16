<?php 
	require_once('nusoap.php'); 
	$miURL = 'http://localhost/nusoap'; 
	$server = new nusoap_server(); 
	$server->configureWSDL('ws_orlando', $miURL); 
	$server->wsdl->schemaTargetNamespace=$miURL; 
	
	$server->register('getXML', // Nombre de la funcion 
	array('id' => 'xsd:int','cpar' => 'xsd:int') , // Parametros de entrada 
	array('return' => 'xsd:string') , // Parametros de salida 
	$miURL); 
	
	function getXML($id,$cpar)
	{ 
		$link=mysql_connect("localhost", "usuario", "password" ) ; 
		mysql_select_db("basededatos",$link) OR DIE ("Error: Imposible Conectar" ) ; 
		$sql="SELECT afil_nombre,plan_descripcion,pare_descripcion FROM afiliados INNER JOIN planes ON afil_idplan=plan_idplan INNER JOIN parentescos ON afil_codparentesco=pare_codparentesco WHERE afil_nrojub=".$id." AND afil_codparentesco=".$cpar; 
		$rsAfiliado=mysql_query($sql,$link); 
		$contenido='<?xml version="1.0" encoding="UTF-8"?> 
		<afiliado>'; 
		while($rowAfiliado=mysql_fetch_array($rsAfiliado)) 
		{ 
			$contenido.=' <miembro>'; 
			$contenido.='<nombre>'.$rowAfiliado["afil_nombre"].'</nombre>'; 
			$contenido.='<plan>'.$rowAfiliado["plan_descripcion"].'</plan>'; 
			$contenido.='<parentesco>'.$rowAfiliado["pare_descripcion"].'</parentesco>'; 
			$contenido.=' </miembro>'; 
		} 
		$contenido.=' </afiliado>'; 
		$contenido=base64_encode($contenido); 
		$rta= new soapval('return', 'xsd:string',$contenido ); 
		
		return $rta; 
	} 
	
	$server->service($HTTP_RAW_POST_DATA); 
?> 