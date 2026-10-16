<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_afp extends w_parametrica
{
   function __construct()
   {
      $sql = sql_afp::get_sql('OUTPUT');
			
      parent::__construct('afp', $sql, $_REQUEST['cod_item_menu'], '0515');
      
      // headers
      $COD_AFP = new header_num('COD_AFP', 'COD_AFP', 'Código');
      $NOM_AFP = new header_text('NOM_AFP', 'NOM_AFP', 'Nombre AFP');
      $this->add_header($COD_AFP);
      $this->add_header($NOM_AFP);
   }
	

}
?>