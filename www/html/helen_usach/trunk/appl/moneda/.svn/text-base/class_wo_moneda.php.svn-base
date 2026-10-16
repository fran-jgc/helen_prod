<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_moneda extends w_parametrica
{
    function __construct()
   {   	
      $sql = sql_moneda::get_sql('OUTPUT');
			
      parent::__construct('moneda', $sql, $_REQUEST['cod_item_menu'], '102010');
      
      // headers
      $COD_MONEDA = new header_num('COD_MONEDA', 'COD_MONEDA', 'Código');
      $NOM_MONEDA = new header_text('NOM_MONEDA', 'NOM_MONEDA', 'Moneda');
      $SIMBOLO = new header_text('SIMBOLO', 'SIMBOLO', 'Simbolo');
      
      $this->add_header($COD_MONEDA);
      $this->add_header($NOM_MONEDA);
      $this->add_header($SIMBOLO);
   }
}
?>