<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/*
Clase : WI_FACULTAD
*/
class wi_facultad extends w_input {
	function __construct($cod_item_menu) {
	    parent::__construct('facultad', $cod_item_menu);
		//////////////// CLASS_SQL = INPUT ///////////////
		$sql = sql_facultad::get_sql('INPUT');
		
		
		$this->dws['dw_facultad'] = new datawindow($sql);

		// asigna los formatos			
		$this->dws['dw_facultad']->add_control($control = new edit_text_upper('NOM_FACULTAD', 80, 100));
		$control->set_onChange("valida_nom_facultad(this);");
		
		// asigna los mandatorys		
		$this->dws['dw_facultad']->set_mandatory('NOM_FACULTAD', 'Nombre Facultad');
	}
	function new_record() {	
		$this->dws['dw_facultad']->insert_row();
	}
	function load_record() {
		$COD_FACULTAD = $this->get_item_wo($this->current_record, 'COD_FACULTAD');
		$this->dws['dw_facultad']->retrieve($COD_FACULTAD);
	}
	function get_key() {
		return $this->dws['dw_facultad']->get_item(0, 'COD_FACULTAD');
	}
	
	function save_record($db) {
		$COD_FACULTAD = $this->get_key();
		$NOM_FACULTAD = $this->dws['dw_facultad']->get_item(0, 'NOM_FACULTAD');		
		
		$COD_FACULTAD = ($COD_FACULTAD=='') ? "null" : $COD_FACULTAD;		
    
		$sp = 'spu_facultad';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    
	    $param	= "'$operacion', 
	    		 	$COD_FACULTAD,
	    		 	'$NOM_FACULTAD'";

	    if ($db->EXECUTE_SP($sp, $param)){
	    	if ($this->is_new_record()) {				
        		$COD_FACULTAD = $db->GET_IDENTITY("facultad");
        		$this->dws['dw_facultad']->set_item(0, 'COD_FACULTAD', $COD_FACULTAD);
			}
			return true;
		}
		return false;		
	}
}
?>