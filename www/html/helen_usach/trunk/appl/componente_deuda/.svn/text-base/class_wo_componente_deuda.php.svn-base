<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_componente_deuda extends w_parametrica
{
   function __construct()
   {
      $sql = sql_componente_deuda::get_sql('OUTPUT');
			
      parent::__construct('componente_deuda', $sql, $_REQUEST['cod_item_menu'], '0515');
      
      // headers
      $COD_COMPONENTE_DEUDA = new header_num('COD_COMPONENTE_DEUDA', 'COD_COMPONENTE_DEUDA', 'Código');
      $this->add_header($COD_COMPONENTE_DEUDA);
      
      $sql_componente_deuda = sql_componente_deuda::get_sql('DD_NOM_COMPONENTE_DEUDA');
      $NOM_COMPONENTE_DEUDA = new header_drop_down('NOM_COMPONENTE_DEUDA', 'COD_COMPONENTE_DEUDA', 'Componente Deuda', $sql_componente_deuda);
      $this->add_header($NOM_COMPONENTE_DEUDA);
   }
	
    
}
?>