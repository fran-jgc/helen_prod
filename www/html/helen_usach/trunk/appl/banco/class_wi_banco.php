<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/*
Clase : WI_BANCO
*/
class wi_banco extends w_input {
    function __construct($cod_item_menu) {
        parent::__construct('banco', $cod_item_menu);
		$this->valida_llave = true;		// valida que la PK sea unica
		
		$sql = sql_banco::get_sql('INPUT');
		
		$this->dws['dw_banco'] = new datawindow($sql);

		// asigna los formatos		
		$this->dws['dw_banco']->add_control($control = new edit_num('COD_BANCO', 10, 10));
		$control->set_onChange("valida_cod_banco();");			
		$this->dws['dw_banco']->add_control($control = new edit_text_upper('NOM_BANCO', 80, 100));	
		$control->set_onChange("valida_nom_banco();");		
		
		// asigna los mandatorys
		$this->dws['dw_banco']->set_mandatory('COD_BANCO', 'Código');
		$this->dws['dw_banco']->set_mandatory('NOM_BANCO', 'Nombre del Banco');
	}
	function new_record() {
		$this->dws['dw_banco']->insert_row();
	}
	function load_record() {
		$cod_banco = $this->get_item_wo($this->current_record, 'COD_BANCO');
		$this->dws['dw_banco']->retrieve($cod_banco);
		$this->dws['dw_banco']->set_entrable('COD_BANCO', false);
	}
	function get_key() {
		return $this->dws['dw_banco']->get_item(0, 'COD_BANCO');
	}
	
	function save_record($db) {
		$COD_BANCO = $this->get_key();
		$NOM_BANCO = $this->dws['dw_banco']->get_item(0, 'NOM_BANCO');

		
		$COD_BANCO = ($COD_BANCO=='') ? "null" : $COD_BANCO;		
    
		$sp = 'spu_banco';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    		    
	    $param	= "'$operacion', $COD_BANCO, '$NOM_BANCO'";
	    	
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$COD_BANCO = $this->get_key();
				$this->dws['dw_banco']->set_item(0, 'COD_BANCO', $COD_BANCO);
			}
			return true;
		}
		return false;		
				
	}
	
}

?>