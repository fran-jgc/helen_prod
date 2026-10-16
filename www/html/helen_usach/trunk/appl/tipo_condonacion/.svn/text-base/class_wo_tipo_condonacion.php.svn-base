<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_tipo_condonacion extends w_parametrica
{
   function __construct()
   {   	
		//////////////// CLASS_SQL = OUTPUT ///////////////
   		$sql = sql_tipo_condonacion::get_sql("OUTPUT");
			
   		parent::__construct('tipo_condonacion', $sql, $_REQUEST['cod_item_menu'], '0515');
      
      // headers
   		$COD_TIPO_CONDONACION = new header_num('COD_TIPO_CONDONACION', 'COD_TIPO_CONDONACION', 'Código');
   		$NOM_TIPO_CONDONACION = new header_text('NOM_TIPO_CONDONACION', 'NOM_TIPO_CONDONACION', 'Tipo Condonación');
   		$this->add_header($COD_TIPO_CONDONACION);
   		$this->add_header($NOM_TIPO_CONDONACION);
   }
}
?>