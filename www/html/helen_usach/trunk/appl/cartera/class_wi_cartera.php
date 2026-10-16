<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/*
Clase : WI_CARTERA
*/
class wi_cartera extends w_input {
	function __construct($cod_item_menu) {
	    parent::__construct('cartera', $cod_item_menu);

		$sql = sql_cartera::get_sql('INPUT');
		
		$this->dws['dw_cartera'] = new datawindow($sql);

		// asigna los formatos			
		$this->dws['dw_cartera']->add_control($control = new edit_text_upper('NOM_CARTERA', 80, 100));
		$control->set_onChange("valida_nom_cartera();");	
		$this->dws['dw_cartera']->add_control(new edit_text_upper('ENCARGADO', 80, 100));		
		
		// asigna los mandatorys		
		$this->dws['dw_cartera']->set_mandatory('NOM_CARTERA', 'Nombre Cartera');
	}
	function new_record() {	
		$this->dws['dw_cartera']->insert_row();
	}
	function load_record() {
		$cod_cartera = $this->get_item_wo($this->current_record, 'COD_CARTERA');
		$this->dws['dw_cartera']->retrieve($cod_cartera);
	}
	function get_key() {
		return $this->dws['dw_cartera']->get_item(0, 'COD_CARTERA');
	}
	
	function save_record($db) {
		$COD_CARTERA = $this->get_key();
		$NOM_CARTERA = $this->dws['dw_cartera']->get_item(0, 'NOM_CARTERA');
		$ENCARGADO = $this->dws['dw_cartera']->get_item(0, 'ENCARGADO');		

		$COD_CARTERA = ($COD_CARTERA=='') ? "null" : $COD_CARTERA;		
    
		$sp = 'spu_cartera';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    
	    $param	= "'$operacion', $COD_CARTERA, '$NOM_CARTERA','$ENCARGADO'"; 
		
	    if ($db->EXECUTE_SP($sp, $param)){
	    	if ($this->is_new_record()) {				
        		$cod_cartera = $db->GET_IDENTITY("cartera");
        		$this->dws['dw_cartera']->set_item(0, 'COD_CARTERA', $cod_cartera);
			}
			return true;
		}
		return false;				
	}
}
?>