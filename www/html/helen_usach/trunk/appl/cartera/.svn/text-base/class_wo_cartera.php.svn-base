<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_cartera extends w_parametrica
{
   function __construct()
   {   	
      $sql = sql_cartera::get_sql('OUTPUT');
			
      parent::__construct('cartera', $sql, $_REQUEST['cod_item_menu'], '0515');
      
      // headers
      $COD_CARTERA = new header_num('COD_CARTERA', 'COD_CARTERA', 'Código');
      $NOM_CARTERA = new header_text('NOM_CARTERA', 'NOM_CARTERA', 'Cartera');
      $ENCARGADO = new header_text('ENCARGADO', 'ENCARGADO', 'Encargado');
      
      $this->add_header($COD_CARTERA);
      $this->add_header($NOM_CARTERA);
      $this->add_header($ENCARGADO);
      
   }
}
?>