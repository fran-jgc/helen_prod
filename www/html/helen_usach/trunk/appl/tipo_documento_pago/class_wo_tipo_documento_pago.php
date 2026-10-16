<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_tipo_documento_pago extends w_parametrica
{
    function __construct()
   {
      
   	  $sql = sql_tipo_documento_pago::get_sql('OUTPUT');
			
   	  parent::__construct('tipo_documento_pago', $sql, $_REQUEST['cod_item_menu'], '051560');
      
      // headers
   	  $COD_TIPO_DOCUMENTO_PAGO = new header_num('COD_TIPO_DOCUMENTO_PAGO', 'COD_TIPO_DOCUMENTO_PAGO', 'Código');
   	  $NOM_TIPO_DOCUMENTO_PAGO = new header_text('NOM_TIPO_DOCUMENTO_PAGO', 'NOM_TIPO_DOCUMENTO_PAGO', 'Nombre Tipo Documento');
   	  $this->add_header($COD_TIPO_DOCUMENTO_PAGO);
   	  $this->add_header($NOM_TIPO_DOCUMENTO_PAGO);
   }    
}
?>