<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

ini_set('memory_limit', '750M');
ini_set('max_execution_time', 1200); //1200 seconds = 40 minutes 
ini_set('mssql.connect_timeout','400');
ini_set('mssql.timeout','400');

class wp_calculo_cuota_cs_base extends w_proceso {
	const K_CALCULO_CUOTA_CS = 9;
	 
	function __construct() {
	    parent::__construct('calculo_cuota_cs', $_REQUEST['cod_item_menu'], self::K_CALCULO_CUOTA_CS);
   	}
	function make_cmd_sp($cod_ejecucion_proceso) {
		if (K_TIPO_BD=="oci")
			return "spp_cuota_cs($cod_ejecucion_proceso)";
		else if (K_TIPO_BD=="mssql")	
			return "spp_cuota_cs $cod_ejecucion_proceso";
	}
}
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wp_calculo_cuota_cs.php";
if (file_exists($file_name)) 
	require_once($file_name);
else {
	class wp_calculo_cuota_cs extends wp_calculo_cuota_cs_base {
	    function __construct() {
	        parent::__construct('calculo_cuota_cs', $_REQUEST['cod_item_menu'], self::K_CALCULO_CUOTA_CS); 
		}
	}
}
?>