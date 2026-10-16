<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/*
Clase : WI_EMPRESA_COBRANZA
*/
class wi_empresa_cobranza extends w_input {
	function __construct($cod_item_menu) {
	    parent::__construct('empresa_cobranza', $cod_item_menu);
		$this->valida_llave = true;		// valida que la PK sea unica
		
		$sql = sql_empresa_cobranza::get_sql('INPUT');
		
		$this->dws['dw_empresa_cobranza'] = new datawindow($sql);

		// asigna los formatos		
		$this->dws['dw_empresa_cobranza']->add_control($control = new edit_num('COD_EMPRESA_COBRANZA', 10, 10));
		$control->set_onChange("valida_cod_empresa_cobranza();");	
		$this->dws['dw_empresa_cobranza']->add_control($control = new edit_text_upper('NOM_EMPRESA_COBRANZA', 80, 100));	
		$control->set_onChange("valida_nom_empresa_cobranza();");	
		
		// asigna los mandatorys
		$this->dws['dw_empresa_cobranza']->set_mandatory('COD_EMPRESA_COBRANZA', 'Código');
		$this->dws['dw_empresa_cobranza']->set_mandatory('NOM_EMPRESA_COBRANZA', 'Nombre de la Empresa');
	}
	function new_record() {
		$this->dws['dw_empresa_cobranza']->insert_row();
	}
	function load_record() {
		$cod_empresa_cobranza = $this->get_item_wo($this->current_record, 'COD_EMPRESA_COBRANZA');
		$this->dws['dw_empresa_cobranza']->retrieve($cod_empresa_cobranza);
		$this->dws['dw_empresa_cobranza']->set_entrable('COD_EMPRESA_COBRANZA', false);
	}
	function get_key() {
		return $this->dws['dw_empresa_cobranza']->get_item(0, 'COD_EMPRESA_COBRANZA');
	}
	
	function save_record($db) {
		$COD_EMPRESA_COBRANZA = $this->get_key();
		$NOM_EMPRESA_COBRANZA = $this->dws['dw_empresa_cobranza']->get_item(0, 'NOM_EMPRESA_COBRANZA');

		
		$COD_EMPRESA_COBRANZA = ($COD_EMPRESA_COBRANZA=='') ? "null" : $COD_EMPRESA_COBRANZA;		
    
		$sp = 'spu_empresa_cobranza';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    		    
	    $param	= "'$operacion', $COD_EMPRESA_COBRANZA, '$NOM_EMPRESA_COBRANZA'";
	    	
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$COD_EMPRESA_COBRANZA = $this->get_key();
				$this->dws['dw_empresa_cobranza']->set_item(0, 'COD_EMPRESA_COBRANZA', $COD_EMPRESA_COBRANZA);
			}
			return true;
		}
		return false;		
				
	}
	
}

?>