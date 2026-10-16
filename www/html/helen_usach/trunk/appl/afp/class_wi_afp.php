<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/*
Clase : WI_AFP
*/
class wi_afp extends w_input {
	function __construct($cod_item_menu) {
	    parent::__construct('afp', $cod_item_menu);
		
		$sql = sql_afp::get_sql('INPUT');
		
		$this->dws['dw_afp'] = new datawindow($sql);

		// asigna los formatos			
		$this->dws['dw_afp']->add_control($control = new edit_text_upper('NOM_AFP', 80, 100));
		$control->set_onChange("valida_nom_afp();");			
		
		// asigna los mandatorys		
		$this->dws['dw_afp']->set_mandatory('NOM_AFP', 'Nombre de la AFP');
	}
	function new_record() {	
		$this->dws['dw_afp']->insert_row();
	}
	function load_record() {
		$COD_AFP = $this->get_item_wo($this->current_record, 'COD_AFP');
		$this->dws['dw_afp']->retrieve($COD_AFP);
	}
	function get_key() {
		return $this->dws['dw_afp']->get_item(0, 'COD_AFP');
	}
	
	function save_record($db) {
		$COD_AFP = $this->get_key();
		$NOM_AFP = $this->dws['dw_afp']->get_item(0, 'NOM_AFP');		

		$COD_AFP = ($COD_AFP=='') ? "null" : $COD_AFP;		
    
		$sp = 'spu_afp';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    
	    $param	= "'$operacion', $COD_AFP, '$NOM_AFP'"; 
		
	    if ($db->EXECUTE_SP($sp, $param)){
	    	if ($this->is_new_record()) {				
        		$COD_AFP = $db->GET_IDENTITY("afp");
        		$this->dws['dw_afp']->set_item(0, 'COD_AFP', $COD_AFP);
			}
			return true;
		}
		return false;				
	}
}
?>