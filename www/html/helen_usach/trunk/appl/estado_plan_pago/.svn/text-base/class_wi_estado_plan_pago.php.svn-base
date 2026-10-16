<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/*
Clase : WI_ESTADO_PLAN_PAGO
*/
class wi_estado_plan_pago extends w_input {
	function __construct($cod_item_menu) {
	    parent::__construct('estado_plan_pago', $cod_item_menu);
		$this->b_delete_visible = false;
		$this->b_save_visible = false;
		$this->b_no_save_visible = false;
		$this->b_modify_visible = false;
		
		$sql = sql_estado_plan_pago::get_sql('INPUT');
		
		$this->dws['dw_estado_plan_pago'] = new datawindow($sql);

		// asigna los formatos		
		$this->dws['dw_estado_plan_pago']->add_control(new edit_text_upper('NOM_ESTADO_PLAN_PAGO', 80, 100));		
		
		// asigna los mandatorys
		$this->dws['dw_estado_plan_pago']->set_mandatory('NOM_ESTADO_PLAN_PAGO', 'Estado Pagaré');
	}
	function new_record() {
		$this->dws['dw_estado_plan_pago']->insert_row();
	}
	function load_record() {
		$COD_ESTADO_PLAN_PAGO = $this->get_item_wo($this->current_record, 'COD_ESTADO_PLAN_PAGO');
		$this->dws['dw_estado_plan_pago']->retrieve($COD_ESTADO_PLAN_PAGO);
	}
	function get_key() {
		return $this->dws['dw_estado_plan_pago']->get_item(0, 'COD_ESTADO_PLAN_PAGO');
	}
	
	function save_record($db) {
		$COD_ESTADO_PLAN_PAGO = $this->get_key();
		$NOM_ESTADO_PLAN_PAGO = $this->dws['dw_estado_plan_pago']->get_item(0, 'NOM_ESTADO_PLAN_PAGO');

		
		$COD_ESTADO_PLAN_PAGO = ($COD_ESTADO_PLAN_PAGO=='') ? "null" : $COD_ESTADO_PLAN_PAGO;		
    
		$sp = 'spu_estado_plan_pago';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    		    
	    $param	= "'$operacion', $COD_ESTADO_PLAN_PAGO, '$NOM_ESTADO_PLAN_PAGO'";
	    	
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$COD_ESTADO_PLAN_PAGO = $db->GET_IDENTITY("estado_plan_pago");
				$this->dws['dw_estado_plan_pago']->set_item(0, 'COD_ESTADO_PLAN_PAGO', $COD_ESTADO_PLAN_PAGO);
			}
			return true;
		}
		return false;		
				
	}
	
}

?>