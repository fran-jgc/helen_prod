<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_cuenta_contable extends w_parametrica
{
   function __construct()
   {  
   	  
      $sql = sql_cuenta_contable::get_sql('OUTPUT');
			
      parent::__construct('cuenta_contable', $sql, $_REQUEST['cod_item_menu'], '0515');
      
	  $this->dw->add_control(new edit_cuenta_contable('COD_CUENTA_CONTABLE', array(2,2,3)));
	  
      // headers
	  $COD_CUENTA_CONTABLE = new header_num('COD_CUENTA_CONTABLE', 'COD_CUENTA_CONTABLE', 'Código');
	  $NOM_CUENTA_CONTABLE = new header_text('NOM_CUENTA_CONTABLE', 'NOM_CUENTA_CONTABLE', 'Cuenta Contable');
	  
	  $this->add_header($COD_CUENTA_CONTABLE);
	  $this->add_header($NOM_CUENTA_CONTABLE);
          
   }
}
?>