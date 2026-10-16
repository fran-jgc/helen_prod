<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_estado_deuda extends w_parametrica
{
    function __construct()
   {   	
      $sql = sql_estado_deuda::get_sql('OUTPUT');
			
      parent::__construct('estado_deuda', $sql, $_REQUEST['cod_item_menu'], '0515');
      
      // headers
      $COD_ESTADO_DEUDA = new header_num('COD_ESTADO_DEUDA', 'COD_ESTADO_DEUDA', 'Código');
      $NOM_ESTADO_DEUDA = new header_text('NOM_ESTADO_DEUDA', 'NOM_ESTADO_DEUDA', 'Estado Deuda');
      $this->add_header($COD_ESTADO_DEUDA);
      $this->add_header($NOM_ESTADO_DEUDA);      
   }	
}
?>