<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../appl.ini");

class wo_ciudad_base extends w_parametrica
{
    function __construct()
   {   	
      	$sql = sql_ciudad::get_sql('OUTPUT');
      	parent::__construct('ciudad', $sql, $_REQUEST['cod_item_menu'], '0515');
      
		// headers
      	$COD_CIUDAD = new header_num('COD_CIUDAD', 'COD_CIUDAD', 'Código');
      	$NOM_CIUDAD = new header_text('NOM_CIUDAD', 'NOM_CIUDAD', 'Ciudad');
      	
      	$this->add_header($COD_CIUDAD);
      	$this->add_header($NOM_CIUDAD);      
   }	
}

// Se separa para UCT
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wo_ciudad.php";
if(file_exists($file_name)){ 
	require_once($file_name);
}
else {
	class wo_ciudad extends wo_ciudad_base {
	    function __construct() {
	        parent::__construct();
		}
	}
}
?>