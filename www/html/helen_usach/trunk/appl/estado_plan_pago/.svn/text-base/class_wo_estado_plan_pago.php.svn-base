<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_estado_plan_pago extends w_parametrica
{
    function __construct()
   {   	
      $sql = sql_estado_plan_pago::get_sql('OUTPUT');
			
      parent::__construct('estado_plan_pago', $sql, $_REQUEST['cod_item_menu'], '0515');
      
      // headers
      $COD_ESTADO_PLAN_PAGO = new header_num('COD_ESTADO_PLAN_PAGO', 'COD_ESTADO_PLAN_PAGO', 'Código');
      $NOM_ESTADO_PLAN_PAGO = new header_text('NOM_ESTADO_PLAN_PAGO', 'NOM_ESTADO_PLAN_PAGO', 'Estado Plan Pago');
      $this->add_header($COD_ESTADO_PLAN_PAGO);
      $this->add_header($NOM_ESTADO_PLAN_PAGO);      
   }	
}
?>