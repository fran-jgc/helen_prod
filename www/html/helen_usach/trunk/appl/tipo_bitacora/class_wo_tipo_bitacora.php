<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_tipo_bitacora extends w_parametrica
{
   function __construct()
   {   	
      $sql = sql_tipo_bitacora::get_sql("OUTPUT");
			
      parent::__construct('tipo_bitacora', $sql, $_REQUEST['cod_item_menu'], '0515');
      
      // headers
      $COD_TIPO_BITACORA = new header_num('COD_TIPO_BITACORA', 'COD_TIPO_BITACORA', 'Código') ;
      $NOM_TIPO_BITACORA = new header_text('NOM_TIPO_BITACORA', 'NOM_TIPO_BITACORA', 'Tipo Bitácora');
      $this->add_header($COD_TIPO_BITACORA);
      $this->add_header($NOM_TIPO_BITACORA);
   }
}
?>