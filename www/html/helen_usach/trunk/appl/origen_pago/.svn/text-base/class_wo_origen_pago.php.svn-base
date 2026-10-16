<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../appl.ini");

class wo_origen_pago_base extends w_parametrica
{
   function __construct()
   {
      $sql = sql_origen_pago::get_sql('OUTPUT');
			
      parent::__construct('origen_pago', $sql, $_REQUEST['cod_item_menu'], '051562');
      
      
      // headers
      $COD_ORIGEN_PAGO = new header_num('COD_ORIGEN_PAGO', 'COD_ORIGEN_PAGO', 'Código');
      $NOM_ORIGEN_PAGO = new header_text('NOM_ORIGEN_PAGO', 'NOM_ORIGEN_PAGO', 'Nombre Carrera');
      
      $this->add_header($COD_ORIGEN_PAGO);
      $this->add_header($NOM_ORIGEN_PAGO);
      
   }
	
    
}
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wo_origen_pago.php";
if (file_exists($file_name)) {
	require_once($file_name);
}else {
	class wo_origen_pago extends wo_origen_pago_base {
	    function __construct() {
	        
	        parent::__construct('origen_pago', $sql, $_REQUEST['cod_item_menu'], '051562'); 
		}
	}
}
?>