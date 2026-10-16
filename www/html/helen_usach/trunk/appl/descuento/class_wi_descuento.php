<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
/*
 Clase : WI_DESCUENTO
 */
class wi_descuento extends w_input {
	function __construct($cod_item_menu) {
	    parent::__construct('descuento', $cod_item_menu);
		$this->b_delete_visible  = false;

		$sql = sql_descuento::get_sql('INPUT');
		
		$this->dws['dw_descuento'] = new datawindow($sql);
	
		$this->dws['dw_descuento']->add_control(new edit_rut('RUT'));
		$this->dws['dw_descuento']->set_entrable('RUT', false);
		$this->dws['dw_descuento']->add_control(new static_num('MONTO_CAPITAL',4));
		$this->dws['dw_descuento']->add_control(new static_num('MONTO_INTERES',4));
		$this->dws['dw_descuento']->add_control(new static_num('MONTO_MULTA',4));
		$this->dws['dw_descuento']->add_control(new static_num('MONTO_MONEDA',4));
		$this->dws['dw_descuento']->add_control(new static_num('MONTO_PESOS',0));
	}

	function new_record() {
		$this->dws['dw_descuento']->insert_row();
	}
	
	function load_record() {
		$cod_descuento	= $this->get_item_wo($this->current_record, 'COD_DESCUENTO');
		$this->dws['dw_descuento']->retrieve($cod_descuento);
	}
	function get_key() {
		return $this->dws['dw_descuento']->get_item(0, 'COD_DESCUENTO');
	}
}
?>