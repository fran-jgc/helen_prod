<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/*
 Clase : WI_CONCEPTO_COBRO
 */
class wi_concepto_cobro extends w_input {
	function __construct($cod_item_menu) {
	    parent::__construct('concepto_cobro', $cod_item_menu);
		$this->b_delete_visible = false;
		$this->b_save_visible = false;
		$this->b_no_save_visible = false;
		$this->b_modify_visible = false;
		
		$sql = sql_concepto_cobro::get_sql('INPUT');
		
		$this->dws['dw_concepto_cobro'] = new datawindow($sql);

		// asigna los formatos
		$this->dws['dw_concepto_cobro']->add_control(new edit_text_upper('NOM_CONCEPTO_COBRO', 80, 100));

		// asigna los mandatorys
		$this->dws['dw_concepto_cobro']->set_mandatory('NOM_CONCEPTO_COBRO', 'Nombre Concepto Cobro');
	}
	function new_record() {
		$this->dws['dw_concepto_cobro']->insert_row();
	}
	function load_record() {
		$cod_concepto_cobro = $this->get_item_wo($this->current_record, 'COD_CONCEPTO_COBRO');
		$this->dws['dw_concepto_cobro']->retrieve($cod_concepto_cobro);
	}
	function get_key() {
		return $this->dws['dw_concepto_cobro']->get_item(0, 'COD_CONCEPTO_COBRO');
	}

	function save_record($db) {
		$COD_CONCEPTO_COBRO = $this->get_key();
		$NOM_CONCEPTO_COBRO = $this->dws['dw_concepto_cobro']->get_item(0, 'NOM_CONCEPTO_COBRO');

		$COD_CONCEPTO_COBRO = ($COD_CONCEPTO_COBRO=='') ? "null" : $COD_CONCEPTO_COBRO;

		$sp = 'spu_concepto_cobro';
		if ($this->is_new_record())
		$operacion = 'INSERT';
		else
		$operacion = 'UPDATE';
			
		$param	= "'$operacion', $COD_CONCEPTO_COBRO, '$NOM_CONCEPTO_COBRO'";

		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$COD_CONCEPTO_COBRO = $db->GET_IDENTITY("concepto_cobro");
				$this->dws['dw_concepto_cobro']->set_item(0, 'COD_CONCEPTO_COBRO', $COD_CONCEPTO_COBRO);
			}
			return true;
		}
		return false;
	}
}
?>