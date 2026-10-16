<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_facultad extends w_parametrica
{
   function __construct()
   {
   		//////////////// CLASS_SQL = OUTPUT ///////////////
		$sql = sql_facultad::get_sql('OUTPUT');
		parent::__construct('facultad', $sql, $_REQUEST['cod_item_menu'], '0515');
		
		$COD_FACULTAD= new header_num('COD_FACULTAD', 'COD_FACULTAD', 'Código');
		$NOM_FACULTAD = new header_text('NOM_FACULTAD', 'NOM_FACULTAD', 'Nombre');
      
		$this->add_header($COD_FACULTAD);
		$this->add_header($NOM_FACULTAD);        
   }
}
?>