<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_familia_deuda extends w_parametrica {
    function __construct() {
		$sql = sql_familia_deuda::get_sql('OUTPUT'); 

		parent::__construct('familia_deuda', $sql, $_REQUEST['cod_item_menu'], '0515');
		// headers
		$COD_FAMILIA_DEUDA = new header_num('COD_FAMILIA_DEUDA', 'COD_FAMILIA_DEUDA', 'Código');
		$NOM_FAMILIA_DEUDA = new header_text('NOM_FAMILIA_DEUDA', 'NOM_FAMILIA_DEUDA', 'Familia');
		$NOM_CUENTA_CORRIENTE = new header_text('NOM_CUENTA_CORRIENTE', 'NOM_CUENTA_CORRIENTE', 'Cuenta Corriente');
		
		$this->add_header($COD_FAMILIA_DEUDA);
		$this->add_header($NOM_FAMILIA_DEUDA);
		$this->add_header($NOM_CUENTA_CORRIENTE);
	}
}
?>