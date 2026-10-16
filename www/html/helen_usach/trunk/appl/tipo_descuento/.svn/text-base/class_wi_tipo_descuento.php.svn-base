<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/*
Clase : WI_TIPO_DESCUENTO
*/
class wi_tipo_descuento extends w_input {
	function __construct($cod_item_menu) {
	    parent::__construct('tipo_descuento', $cod_item_menu);
		$this->valida_llave = true;		// valida que la PK sea unica
		
		$sql = sql_tipo_descuento::get_sql('INPUT');
		
		$this->dws['dw_tipo_descuento'] = new datawindow($sql);
		
		// asigna los formatos		
		$this->dws['dw_tipo_descuento']->add_control($control = new edit_num('COD_TIPO_DESCUENTO', 10, 10));
		$control->set_onChange("valida_cod_tipo_descuento();");	
		$this->dws['dw_tipo_descuento']->add_control($control = new edit_text_upper('NOM_TIPO_DESCUENTO', 80, 100));	
		$this->dws['dw_tipo_descuento']->add_control($control = new edit_porcentaje('PORC_DESCUENTO', 10, 10,4));	
		
		// asigna los mandatorys
		$this->dws['dw_tipo_descuento']->set_mandatory('COD_TIPO_DESCUENTO', 'Código');
		$this->dws['dw_tipo_descuento']->set_mandatory('NOM_TIPO_DESCUENTO', 'Nombre del Descuento');
		$this->dws['dw_tipo_descuento']->set_mandatory('PORC_DESCUENTO', '% del Descuento');
		
	}
	function new_record() {
		$this->dws['dw_tipo_descuento']->insert_row();
	}
	function load_record() {
		
		$cod_tipo_descuento = $this->get_item_wo($this->current_record, 'COD_TIPO_DESCUENTO');
		$this->dws['dw_tipo_descuento']->retrieve($cod_tipo_descuento);
		$this->dws['dw_tipo_descuento']->set_entrable('COD_TIPO_DESCUENTO', false);
		
	}
	function get_key() {
		return $this->dws['dw_tipo_descuento']->get_item(0, 'COD_TIPO_DESCUENTO');
	}
	
	function save_record($db) {
		$COD_TIPO_DESCUENTO = $this->get_key();
		$NOM_TIPO_DESCUENTO = $this->dws['dw_tipo_descuento']->get_item(0, 'NOM_TIPO_DESCUENTO');
		$PORC_DESCUENTO = $this->dws['dw_tipo_descuento']->get_item(0, 'PORC_DESCUENTO');
		
		$COD_TIPO_DESCUENTO = ($COD_TIPO_DESCUENTO=='') ? "null" : $COD_TIPO_DESCUENTO;		
    
		$sp = 'spu_tipo_descuento';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    		    
	    $param	= "'$operacion', $COD_TIPO_DESCUENTO, '$NOM_TIPO_DESCUENTO', $PORC_DESCUENTO";
	    	
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$COD_TIPO_DESCUENTO = $this->get_key();
				$this->dws['dw_tipo_descuento']->set_item(0, 'COD_TIPO_DESCUENTO', $COD_TIPO_DESCUENTO);
			}
			return true;
		}
		return false;		
				
	}
	
}

?>