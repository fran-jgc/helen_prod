<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_tipo_descuento extends w_parametrica
{
    function __construct()
   {
      
   	  $sql = sql_tipo_descuento::get_sql('OUTPUT');
			
   	  parent::__construct('tipo_descuento', $sql, $_REQUEST['cod_item_menu'], '051556');
      
      // headers
   	  $COD_TIPO_DESCUENTO = new header_num('COD_TIPO_DESCUENTO', 'COD_TIPO_DESCUENTO', 'Código');
   	  $NOM_TIPO_DESCUENTO = new header_text('NOM_TIPO_DESCUENTO', 'NOM_TIPO_DESCUENTO', 'Nombre Tipo Descuento');
   	  $PORC_DESCUENTO = new header_text('PORC_DESCUENTO', 'PORC_DESCUENTO', '% Descuento');
   	  $this->add_header($COD_TIPO_DESCUENTO);
   	  $this->add_header($NOM_TIPO_DESCUENTO);
   	  $this->add_header($PORC_DESCUENTO);
   }    
}
?>