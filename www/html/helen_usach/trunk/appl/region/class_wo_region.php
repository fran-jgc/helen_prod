<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../appl.ini");

class wo_region_base extends w_parametrica
{
   function __construct()
   {
   	//////////////// CLASS_SQL = OUTPUT ///////////////
   	$sql = sql_region::get_sql("OUTPUT");
			
   	parent::__construct('region', $sql, $_REQUEST['cod_item_menu'], '0515');
		// headers
   	$COD_REGION = new header_num('COD_REGION', 'COD_REGION', 'Código');
   	$NOM_REGION = new header_text('NOM_REGION', 'NOM_REGION', 'Región');
   	
   	$this->add_header($COD_REGION);
   	$this->add_header($NOM_REGION);
   }
}
// Se separa para UCT
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wo_region.php";
if(file_exists($file_name)) 
	require_once($file_name);
else {
	class wo_region extends wo_region_base {
	    function __construct() {
	        parent::__construct();
		}
	}
}
?>