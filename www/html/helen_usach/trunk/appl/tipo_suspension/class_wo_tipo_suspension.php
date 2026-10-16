<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_tipo_suspension extends w_parametrica
{
    function __construct()
   {   	
   		//////////////// CLASS_SQL = OUTPUT ///////////////
      $sql = sql_tipo_suspension::get_sql('OUTPUT');
			
      parent::__construct('tipo_suspension', $sql, $_REQUEST['cod_item_menu'], '0515');
      
      // headers
      $COD_TIPO_SUSPENSION = new header_num('COD_TIPO_SUSPENSION', 'COD_TIPO_SUSPENSION', 'Código');
      $NOM_TIPO_SUSPENSION = new header_text('NOM_TIPO_SUSPENSION', 'NOM_TIPO_SUSPENSION', 'Tipo Suspensión');
      
      $this->add_header($COD_TIPO_SUSPENSION);
      $this->add_header($NOM_TIPO_SUSPENSION);
   }
}
?>