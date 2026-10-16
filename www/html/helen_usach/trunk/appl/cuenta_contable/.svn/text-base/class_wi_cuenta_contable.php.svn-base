<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");


class wi_cuenta_contable extends w_input 
{
    function __construct($cod_item_menu) 
	{
	    parent::__construct('cuenta_contable', $cod_item_menu);
		$this->valida_llave = true;		// valida que la PK sea unica
		
		$sql = sql_cuenta_contable::get_sql('INPUT');

		$this->dws['dw_cuenta_contable'] = new datawindow($sql);

		// asigna los formatos
		$this->dws['dw_cuenta_contable']->add_control($control = new edit_cuenta_contable('COD_CUENTA_CONTABLE', array(2,2,3)));
		
		
		$this->dws['dw_cuenta_contable']->set_entrable('COD_CUENTA_CONTABLE', false);
		
		$this->dws['dw_cuenta_contable']->add_control($control = new edit_text_upper('NOM_CUENTA_CONTABLE', 80, 100));
		$control->set_onChange("valida_nom_cta_contable();");			
		$sql= sql_cuenta_contable::get_sql('DD_COD_TIPO_CUENTA_CONTABLE');
		$this->dws['dw_cuenta_contable']->add_control(new drop_down_dw('COD_TIPO_CUENTA_CONTABLE', $sql, 140));	
		
		// asigna los mandatorys
		$this->dws['dw_cuenta_contable']->set_mandatory('COD_CUENTA_CONTABLE', 'Código');
		$this->dws['dw_cuenta_contable']->set_mandatory('NOM_CUENTA_CONTABLE', 'Nombre Cuenta Contable');	
		$this->dws['dw_cuenta_contable']->set_mandatory('COD_TIPO_CUENTA_CONTABLE', 'Tipo Cuenta Contable');

		// focus 
		$this->set_first_focus('NOM_CUENTA_CONTABLE');		
	}
	function new_record() {
		$this->dws['dw_cuenta_contable']->insert_row();
		$this->dws['dw_cuenta_contable']->set_entrable('COD_CUENTA_CONTABLE', true);
		$this->set_first_focus('COD_CUENTA_CONTABLE');		
	}
	function load_record() {
		$COD_CUENTA_CONTABLE = $this->get_item_wo($this->current_record, 'COD_CUENTA_CONTABLE');
		$this->dws['dw_cuenta_contable']->retrieve($COD_CUENTA_CONTABLE);
	}
	function get_key() {
		return $this->dws['dw_cuenta_contable']->get_item(0, 'COD_CUENTA_CONTABLE');
	}
	
	
	function save_record($db) {
		$COD_CUENTA_CONTABLE = $this->get_key();
		$NOM_CUENTA_CONTABLE = $this->dws['dw_cuenta_contable']->get_item(0, 'NOM_CUENTA_CONTABLE');	
		$COD_TIPO_CUENTA_CONTABLE= $this->dws['dw_cuenta_contable']->get_item(0, 'COD_TIPO_CUENTA_CONTABLE');
		
		$COD_CUENTA_CONTABLE = ($COD_CUENTA_CONTABLE=='') ? "null" : $COD_CUENTA_CONTABLE;		
    
		$sp = 'spu_cuenta_contable';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    
	    $param	= "'$operacion', $COD_CUENTA_CONTABLE, $COD_TIPO_CUENTA_CONTABLE, '$NOM_CUENTA_CONTABLE'"; 	

	    //$db->debug=true;
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {				
				$COD_CUENTA_CONTABLE = $this->dws['dw_cuenta_contable']->get_item(0, 'COD_CUENTA_CONTABLE');
				$this->dws['dw_cuenta_contable']->set_item(0, 'COD_CUENTA_CONTABLE', $COD_CUENTA_CONTABLE);			
			}
			return true;
		}
		return false;		
				
	}
	
}
////////////////////////

?>