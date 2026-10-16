<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../appl.ini");

class wo_pais_base extends w_parametrica
{
    function __construct()
	{
		//////////////// CLASS_SQL = OUTPUT ///////////////
		$sql = sql_pais::get_sql('OUTPUT');
		
		parent::__construct('pais', $sql, $_REQUEST['cod_item_menu'], '0515');
		
		$this->b_add_visible = true;
		// headers
		$COD_PAIS = new header_num('COD_PAIS', 'COD_PAIS', 'Código');
		$NOM_PAIS = new header_text('NOM_PAIS', 'NOM_PAIS', 'País');
		$this->add_header($COD_PAIS);
		$this->add_header($NOM_PAIS);
	}
}

// Se separa para UCT
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wo_pais.php";
if (file_exists($file_name)) 
	require_once($file_name);
else {
	class wo_pais extends wo_pais_base {
	    function __construct() {
		    parent::__construct();
		}
	}
}
?>