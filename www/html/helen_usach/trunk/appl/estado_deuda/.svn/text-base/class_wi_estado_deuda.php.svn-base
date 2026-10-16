<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/*
Clase : WI_ESTADO_DEUDA
*/
class wi_estado_deuda extends w_input {
	function __construct($cod_item_menu) {
	    parent::__construct('estado_deuda', $cod_item_menu);
		$this->b_delete_visible = false;
		$this->b_save_visible = false;
		$this->b_no_save_visible = false;
		$this->b_modify_visible = false;
		
		$sql = sql_estado_deuda::get_sql('INPUT');
		
		$this->dws['dw_estado_deuda'] = new datawindow($sql);

		// asigna los formatos		
		$this->dws['dw_estado_deuda']->add_control(new edit_text_upper('NOM_ESTADO_DEUDA', 80, 100));		
		
		// asigna los mandatorys
		$this->dws['dw_estado_deuda']->set_mandatory('NOM_ESTADO_DEUDA', 'Estado Deuda');
	}
	function new_record() {
		$this->dws['dw_estado_deuda']->insert_row();
		$this->dws['dw_estado_deuda']->add_control(new edit_num('COD_ESTADO_DEUDA',5,3));
	}
	function load_record() {
		$cod_estado_deuda = $this->get_item_wo($this->current_record, 'COD_ESTADO_DEUDA');
		$this->dws['dw_estado_deuda']->retrieve($cod_estado_deuda);
	}
	function get_key() {
		return $this->dws['dw_estado_deuda']->get_item(0, 'COD_ESTADO_DEUDA');
	}
	
	function save_record($db) {
		$COD_ESTADO_DEUDA = $this->get_key();
		$NOM_ESTADO_DEUDA = $this->dws['dw_estado_deuda']->get_item(0, 'NOM_ESTADO_DEUDA');

		
		$COD_ESTADO_DEUDA = ($COD_ESTADO_DEUDA=='') ? "null" : $COD_ESTADO_DEUDA;		
    
		$sp = 'spu_estado_dueda';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    		    
	    $param	= "'$operacion', $COD_ESTADO_DEUDA, '$NOM_ESTADO_DEUDA'";
	    	
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$COD_ESTADO_DEUDA = $this->get_key();
				$this->dws['dw_estado_deuda']->set_item(0, 'COD_ESTADO_DEUDA', $COD_ESTADO_DEUDA);
			}
			return true;
		}
		return false;		
				
	}
	
}

?>