<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_concepto_cobro extends w_parametrica
{
   function __construct()
   {
      $sql = sql_concepto_cobro::get_sql('OUTPUT');
			
      parent::__construct('concepto_cobro', $sql, $_REQUEST['cod_item_menu'], '0515');
      
      // headers
      $COD_CONCEPTO_COBRO = new header_num('COD_CONCEPTO_COBRO', 'COD_CONCEPTO_COBRO', 'Código');
      $NOM_CONCEPTO_COBRO = new header_text('NOM_CONCEPTO_COBRO', 'NOM_CONCEPTO_COBRO', 'Concepto Cobro');
      $this->add_header($COD_CONCEPTO_COBRO);
      $this->add_header($NOM_CONCEPTO_COBRO);
   }
	
    
}
?>