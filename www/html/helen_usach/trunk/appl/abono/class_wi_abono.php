<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wi_abono extends w_input {
	function __construct($cod_item_menu) {
	    parent::__construct('abono', $cod_item_menu);
		$this->b_delete_visible  = false;

		$sql = sql_abono::get_sql('INPUT');
		$this->dws['dw_abono'] = new datawindow($sql);
		
		$this->dws['dw_abono']->add_control(new edit_rut('RUT'));
		$this->dws['dw_abono']->set_entrable('RUT', false);
		$this->dws['dw_abono']->add_control(new static_num('MONTO_CAPITAL',4));
		$this->dws['dw_abono']->add_control(new static_num('MONTO_INTERES',4));
		$this->dws['dw_abono']->add_control(new static_num('MONTO_MULTA',4));
		$this->dws['dw_abono']->add_control(new static_num('MONTO_MONEDA',4));
		$this->dws['dw_abono']->add_control(new static_num('MONTO_PESOS',0));
		$this->dws['dw_abono']->add_control(new static_num('DIFERENCIA_PESOS',0));
		$this->dws['dw_abono']->add_control(new static_text('FOLIO_CARGA'));
		
		$this->dws['dw_abono']->add_control(new static_link('COD_BOLETA', '../../../../commonlib_seven/trunk/php/link_wi.php?modulo_origen=abono&modulo_destino=boleta&cod_modulo_destino=[COD_BOLETA]&cod_item_menu=1505'));
		
	}

	function new_record() {
		$this->dws['dw_abono']->insert_row();
	}
	
	function load_record() {
		$cod_abono	= $this->get_item_wo($this->current_record, 'COD_ABONO');
		$this->dws['dw_abono']->retrieve($cod_abono);
	}
	function get_key() {
		return $this->dws['dw_abono']->get_item(0, 'COD_ABONO');
	}
}
?>