<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_origen_carga_banco extends w_parametrica
{
   function __construct()
   {
   		//////////////// CLASS_SQL = OUTPUT ///////////////
		$sql = sql_origen_carga_banco::get_sql('OUTPUT');
		parent::__construct('origen_carga_banco', $sql, $_REQUEST['cod_item_menu'], '0515');
		
		$COD_ORIGEN_CARGA_BANCO = new header_num('COD_ORIGEN_CARGA_BANCO', 'COD_ORIGEN_CARGA_BANCO', 'Código');
		$NOM_ORIGEN_CARGA_BANCO = new header_text('NOM_ORIGEN_CARGA_BANCO', 'NOM_ORIGEN_CARGA_BANCO', 'Nombre');
      
		$this->add_header($COD_ORIGEN_CARGA_BANCO);
		$this->add_header($NOM_ORIGEN_CARGA_BANCO);
        
      	$sql_cta_corriente = sql_origen_carga_banco::get_sql('DD_CUENTA_CORRIENTE_OUTPUT');
      	$NOM_CUENTA_CORRIENTE = new header_drop_down_string('NOM_CUENTA_CORRIENTE', 'CC.COD_CUENTA_CORRIENTE', 'Cuenta Corriente', $sql_cta_corriente);
      	$this->add_header($NOM_CUENTA_CORRIENTE);
   }
}
?>