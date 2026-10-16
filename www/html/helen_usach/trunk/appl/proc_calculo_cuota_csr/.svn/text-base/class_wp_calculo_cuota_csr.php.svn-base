<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wp_calculo_cuota_csr_base extends w_proceso {
	const K_CALCULO_CUOTA_CSR = 10;
	 
	function __construct() {
	    parent::__construct('calculo_cuota_csr', $_REQUEST['cod_item_menu'], self::K_CALCULO_CUOTA_CSR);
   	}
}
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wp_calculo_cuota_csr.php";
if (file_exists($file_name)) 
	require_once($file_name);
else {
	class wp_calculo_cuota_csr extends wp_calculo_cuota_csr_base {
	    function __construct() {
	        parent::__construct('calculo_cuota_csr', $_REQUEST['cod_item_menu'], self::K_CALCULO_CUOTA_CSR); 
		}
	}
}
?>