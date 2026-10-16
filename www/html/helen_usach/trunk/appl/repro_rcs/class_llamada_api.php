<?php
	require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
	require_once(dirname(__FILE__)."/../../appl.ini");
	session::set('K_ROOT_URL', K_ROOT_URL);
	session::set('K_ROOT_DIR', K_ROOT_DIR);
	session::set('K_CLIENTE', K_CLIENTE);
	session::set('K_APPL', K_APPL);
	
	class llamada_api
	{
	    function __construct(){
	        
	    }
	    
	    function envia_api($datos) {
			$url="https://fscu.helen.cl/simulacion_rcs_seven/trunk/appl/repro_rcs/class_api_repro_rcs.php";
	        //$url="https://simulacion.prod.helen.cl/simulacion_rcs_seven/trunk/appl/repro_rcs/class_api_repro_rcs.php";
	       // $url="https://simulacionrcs.helen.cl/simulacion_rcs_seven/trunk/appl/repro_rcs/class_api_repro_rcs.php";
	        $ch = curl_init($url);
	        $datos_json = json_encode($datos);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $datos_json);
	        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        $result = curl_exec($ch);
	        curl_close($ch);
	        $arreglo = json_decode($result, true);
	        return $arreglo;
	       // return $arreglo;
	    }
	}
?>