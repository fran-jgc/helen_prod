<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_banco extends w_parametrica
{
    function __construct()
   {
      
   	  $sql = sql_banco::get_sql('OUTPUT');
			
   	  parent::__construct('banco', $sql, $_REQUEST['cod_item_menu'], '0515');
      
      // headers
   	  $COD_BANCO = new header_num('COD_BANCO', 'COD_BANCO', 'Código');
   	  $NOM_BANCO = new header_text('NOM_BANCO', 'NOM_BANCO', 'Nombre Banco');
   	  
   	  $this->add_header($COD_BANCO);
   	  $this->add_header($NOM_BANCO);
   }
	
    
}
?>