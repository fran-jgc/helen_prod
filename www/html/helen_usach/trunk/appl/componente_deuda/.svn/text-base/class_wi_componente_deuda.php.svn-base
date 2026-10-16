<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/*
Clase : WI_COMPONENTE_DEUDA
*/
class wi_componente_deuda extends w_input {
	function __construct($cod_item_menu) {
	    parent::__construct('componente_deuda', $cod_item_menu);
		$this->b_delete_visible = false;
		$this->b_save_visible = false;
		$this->b_no_save_visible = false;
		$this->b_modify_visible = false;
		
		$sql = sql_componente_deuda::get_sql('INPUT');
		
		$this->dws['dw_componente_deuda'] = new datawindow($sql);

		// asigna los formatos
		$this->dws['dw_componente_deuda']->add_control(new static_text('COD_COMPONENTE_DEUDA'));		
		$this->dws['dw_componente_deuda']->add_control(new edit_text_upper('NOM_COMPONENTE_DEUDA', 80, 100));		
		
		// asigna los mandatorys
		$this->dws['dw_componente_deuda']->set_mandatory('NOM_COMPONENTE_DEUDA', 'Nombre Componente Deuda');
	}
	function new_record() {
		$this->dws['dw_componente_deuda']->insert_row();
	}
	function load_record() {
		$cod_componente_deuda = $this->get_item_wo($this->current_record, 'COD_COMPONENTE_DEUDA');
		$this->dws['dw_componente_deuda']->retrieve($cod_componente_deuda);
	}
	function get_key() {
		return $this->dws['dw_componente_deuda']->get_item(0, 'COD_COMPONENTE_DEUDA');
	}
	
	function save_record($db) {
		$COD_COMPONENTE_DEUDA = $this->get_key();
		$NOM_COMPONENTE_DEUDA = $this->dws['dw_componente_deuda']->get_item(0, 'NOM_COMPONENTE_DEUDA');

		
		$COD_COMPONENTE_DEUDA = ($COD_COMPONENTE_DEUDA=='') ? "null" : $COD_COMPONENTE_DEUDA;		
    
		$sp = 'spu_componente_deuda';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    		    
	    $param	= "'$operacion', $COD_COMPONENTE_DEUDA, '$NOM_COMPONENTE_DEUDA'";
	    	
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$COD_COMPONENTE_DEUDA = $db->GET_IDENTITY("componente_deuda");
				$this->dws['dw_componente_deuda']->set_item(0, 'COD_COMPONENTE_DEUDA', $COD_COMPONENTE_DEUDA);
			}
			return true;
		}
		return false;		
				
	}
	
}

?>