<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/*
Clase : WI_CUENTA_CORRIENTE
*/
class wi_cuenta_corriente extends w_input {
	function __construct($cod_item_menu) {
	    parent::__construct('cuenta_corriente', $cod_item_menu);
		
		$sql = sql_cuenta_corriente::get_sql('INPUT');
		
		$this->dws['dw_cuenta_corriente'] = new datawindow($sql);

		// asigna los formatos		
		$this->dws['dw_cuenta_corriente']->add_control($control = new edit_text_upper('NOM_CUENTA_CORRIENTE', 80, 100));
		$control->set_onChange("valida_nom_cta_corriente();");
		$sql_familia_deuda = sql_cuenta_corriente::get_sql('DD_COD_FAMILIA_DEUDA');
		
		$this->dws['dw_cuenta_corriente']->add_control(new drop_down_dw('COD_FAMILIA_DEUDA',$sql_familia_deuda, 380));
		$sql_cuenta_contable = sql_cuenta_corriente::get_sql('DD_COD_CUENTA_CONTABLE');
		$this->dws['dw_cuenta_corriente']->add_control(new drop_down_dw('COD_CUENTA_CONTABLE',$sql_cuenta_contable, 380));
		$sql_banco = sql_cuenta_corriente::get_sql('DD_COD_BANCO');
		$this->dws['dw_cuenta_corriente']->add_control(new drop_down_dw('COD_BANCO',$sql_banco, 380));
		$this->dws['dw_cuenta_corriente']->add_control(new edit_text_upper('NRO_CUENTA_CORRIENTE', 80, 80));
		
					
		
		// asigna los mandatorys
		$this->dws['dw_cuenta_corriente']->set_mandatory('NOM_CUENTA_CORRIENTE', 'Nombre Cuenta Corriente');
		$this->dws['dw_cuenta_corriente']->set_mandatory('NRO_CUENTA_CORRIENTE', 'Número Cta. Cte.');
		$this->dws['dw_cuenta_corriente']->set_mandatory('COD_FAMILIA_DEUDA', 'Familia Deuda');
		$this->dws['dw_cuenta_corriente']->set_mandatory('COD_CUENTA_CONTABLE', 'Cuenta Contable');
		$this->dws['dw_cuenta_corriente']->set_mandatory('COD_BANCO', 'Banco');
	}
	function new_record() {
		$this->dws['dw_cuenta_corriente']->insert_row();
	}
	function load_record() {
		$cod_cuenta_corriente = $this->get_item_wo($this->current_record, 'COD_CUENTA_CORRIENTE');
		$this->dws['dw_cuenta_corriente']->retrieve($cod_cuenta_corriente);
	}
	function get_key() {
		return $this->dws['dw_cuenta_corriente']->get_item(0, 'COD_CUENTA_CORRIENTE');
	}
	
	function save_record($db) {
		$COD_CUENTA_CORRIENTE = $this->get_key();
		$NOM_CUENTA_CORRIENTE = $this->dws['dw_cuenta_corriente']->get_item(0, 'NOM_CUENTA_CORRIENTE');
		$COD_FAMILIA_DEUDA 	  = $this->dws['dw_cuenta_corriente']->get_item(0, 'COD_FAMILIA_DEUDA');
		$COD_CUENTA_CONTABLE  = $this->dws['dw_cuenta_corriente']->get_item(0, 'COD_CUENTA_CONTABLE');
		$COD_BANCO  = $this->dws['dw_cuenta_corriente']->get_item(0, 'COD_BANCO');
		$NRO_CUENTA_CORRIENTE = $this->dws['dw_cuenta_corriente']->get_item(0, 'NRO_CUENTA_CORRIENTE');
		
		$COD_CUENTA_CORRIENTE = ($COD_CUENTA_CORRIENTE=='') ? "null" : $COD_CUENTA_CORRIENTE;		
    
		$sp = 'spu_cuenta_corriente';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    		    
	    $param	= "'$operacion', $COD_CUENTA_CORRIENTE, '$NOM_CUENTA_CORRIENTE', $COD_FAMILIA_DEUDA, $COD_CUENTA_CONTABLE, $COD_BANCO, '$NRO_CUENTA_CORRIENTE'";
	    	
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$COD_CUENTA_CORRIENTE =$db->GET_IDENTITY("cuenta_corriente");
				$this->dws['dw_cuenta_corriente']->set_item(0, 'COD_CUENTA_CORRIENTE', $COD_CUENTA_CORRIENTE);
			}
			return true;
		}
		return false;		
				
	}
	
}

?>