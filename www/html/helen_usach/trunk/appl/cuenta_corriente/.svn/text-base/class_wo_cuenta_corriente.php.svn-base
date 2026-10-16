<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_cuenta_corriente extends w_parametrica
{
    function __construct()
   {   	
      $sql = sql_cuenta_corriente::get_sql('OUTPUT');
			
      parent::__construct('cuenta_corriente', $sql, $_REQUEST['cod_item_menu'], '0515');
      
      // headers      
      $COD_CUENTA_CORRIENTE = new header_num('COD_CUENTA_CORRIENTE', 'COD_CUENTA_CORRIENTE', 'Código');
      $NOM_CUENTA_CORRIENTE = new header_text('NOM_CUENTA_CORRIENTE', 'NOM_CUENTA_CORRIENTE', 'Nombre');
      $NRO_CUENTA_CORRIENTE = new header_text('NRO_CUENTA_CORRIENTE', 'NRO_CUENTA_CORRIENTE', 'Número');
      
      $this->add_header($COD_CUENTA_CORRIENTE);
      $this->add_header($NOM_CUENTA_CORRIENTE);
      $this->add_header($NRO_CUENTA_CORRIENTE);   
   }
}
?>