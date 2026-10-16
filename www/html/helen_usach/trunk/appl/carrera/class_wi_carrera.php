<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/*
Clase : WI_CARRERA
*/
class wi_carrera extends w_input {
	function __construct($cod_item_menu) {
	    parent::__construct('carrera', $cod_item_menu);
		$this->valida_llave = true;		// valida que la PK sea unica

		$sql = sql_carrera::get_sql('INPUT');
		
		$this->dws['dw_carrera'] = new datawindow($sql);

		// asigna los formatos
		$this->dws['dw_carrera']->add_control($control = new edit_num('COD_CARRERA', 12, 10));
		$control->con_separador_miles = false;		
		$control->set_onChange("valida_cod_carrera();");
		$this->dws['dw_carrera']->add_control($control = new edit_text_upper('NOM_CARRERA', 80, 100));	
		$control->set_onChange("valida_nom_carrera();");	
		$sql_facultad = sql_carrera::get_sql('DD_FACULTAD');	
		$this->dws['dw_carrera']->add_control($control = new drop_down_dw('COD_FACULTAD',$sql_facultad,350));
		
		// asigna los mandatorys
		$this->dws['dw_carrera']->set_mandatory('COD_CARRERA', 'Código');
		$this->dws['dw_carrera']->set_mandatory('NOM_CARRERA', 'Nombre de la Carrera');
	}
	function new_record() {
		$this->dws['dw_carrera']->insert_row();	
	}
	function load_record() {
		$cod_carrera = $this->get_item_wo($this->current_record, 'COD_CARRERA');
		$this->dws['dw_carrera']->retrieve($cod_carrera);
		$this->dws['dw_carrera']->set_entrable('COD_CARRERA', false);
	}
	function get_key() {
		return $this->dws['dw_carrera']->get_item(0, 'COD_CARRERA');
	}
	
	function save_record($db) {
		$COD_CARRERA = $this->get_key();
		$NOM_CARRERA = $this->dws['dw_carrera']->get_item(0, 'NOM_CARRERA');
		$COD_FACULTAD = $this->dws['dw_carrera']->get_item(0, 'COD_FACULTAD');

		$COD_CARRERA = ($COD_CARRERA=='') ? "null" : $COD_CARRERA;
		$COD_FACULTAD = ($COD_FACULTAD=='') ? "null" : $COD_FACULTAD;				
    
		$sp = 'spu_carrera';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
			    		    
	    $param	= "'$operacion', $COD_CARRERA, '$NOM_CARRERA', $COD_FACULTAD";
	    
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$COD_CARRERA = $this->get_key();
				$this->dws['dw_carrera']->set_item(0, 'COD_CARRERA', $COD_CARRERA);
			}
			return true;
		}
		return false;		
				
	}
	
}

?>