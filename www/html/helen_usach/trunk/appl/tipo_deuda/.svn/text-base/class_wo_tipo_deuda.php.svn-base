<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_tipo_deuda extends w_parametrica
{
    function __construct()
   {
	  //////////////// CLASS_SQL = OUTPUT ///////////////
   	  $sql = sql_tipo_deuda::get_sql('OUTPUT');
		if(K_CLIENTE == 'UCM'){
			$sql = sql_tipo_deuda::get_sql('OUTPUT_UCM');
		}else if(K_CLIENTE == 'UCM2'){
			$sql = sql_tipo_deuda::get_sql('OUTPUT_UCM2');
		}

		parent::__construct('tipo_deuda', $sql, $_REQUEST['cod_item_menu'], '0515');
      
      // headers
		$COD_TIPO_DEUDA = new header_text('COD_TIPO_DEUDA', 'COD_TIPO_DEUDA', 'Código');
		$this->add_header($COD_TIPO_DEUDA);
		$NOM_TIPO_DEUDA = new header_text('NOM_TIPO_DEUDA', 'NOM_TIPO_DEUDA', 'Tipo');
		$this->add_header($NOM_TIPO_DEUDA);
      $sql_familia_deuda = sql_tipo_deuda::get_sql('DD_FAMILIA_DEUDA');
      $NOM_FAMILIA_DEUDA = new header_drop_down_string('NOM_FAMILIA_DEUDA', 'FD.COD_FAMILIA_DEUDA', 'Familia Deuda', $sql_familia_deuda);
      $this->add_header($NOM_FAMILIA_DEUDA);
	  $sql_moneda = sql_tipo_deuda::get_sql('DD_MONEDA');
	  $NOM_MONEDA = new header_drop_down_string('NOM_MONEDA', 'M.COD_MONEDA', 'Moneda', $sql_moneda);
	  $this->add_header($NOM_MONEDA);	
          
   }
}
?>