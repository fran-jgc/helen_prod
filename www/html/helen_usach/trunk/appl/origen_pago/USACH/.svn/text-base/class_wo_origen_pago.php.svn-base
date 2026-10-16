<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_origen_pago extends wo_origen_pago_base
{
   function __construct()
   {
      $sql = sql_origen_pago::get_sql('OUTPUT');
			
      parent::__construct($_REQUEST['cod_item_menu']);
      $this->b_add_visible = false;
      
      // headers
      $COD_ORIGEN_PAGO = new header_num('COD_ORIGEN_PAGO', 'COD_ORIGEN_PAGO', 'Código');
      $NOM_ORIGEN_PAGO = new header_text('NOM_ORIGEN_PAGO', 'NOM_ORIGEN_PAGO', 'Nombre Carrera');
      
      $this->add_header($COD_ORIGEN_PAGO);
      $this->add_header($NOM_ORIGEN_PAGO);
      
   }
}
?>