<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/*
Clase : WI_ORIGEN_CARGA_BANCO
*/
class wi_origen_carga_banco extends w_input {
    function __construct($cod_item_menu) {
        parent::__construct('origen_carga_banco', $cod_item_menu);
		//////////////// CLASS_SQL = INPUT ///////////////
		$sql = sql_origen_carga_banco::get_sql('INPUT');
		
		
		$this->dws['dw_origen_carga_banco'] = new datawindow($sql);

		// asigna los formatos			
		$this->dws['dw_origen_carga_banco']->add_control($control = new edit_text_upper('NOM_ORIGEN_CARGA_BANCO', 80, 100));
		$control->set_onChange("valida_nom_origen_carga_banco();");
		$sql_cuenta = sql_origen_carga_banco::get_sql('DD_CUENTA_CORRIENTE');	
		$this->dws['dw_origen_carga_banco']->add_control($control = new drop_down_dw('COD_CUENTA_CORRIENTE',$sql_cuenta,380));
		$control->set_onChange("valida_cod_cuenta_corriente();");
		
		// asigna los mandatorys		
		$this->dws['dw_origen_carga_banco']->set_mandatory('NOM_ORIGEN_CARGA_BANCO', 'Nombre Origen Carga Banco');
		$this->dws['dw_origen_carga_banco']->set_mandatory('COD_CUENTA_CORRIENTE', 'Cuenta Corriente');
	}
	function new_record() {	
		
		$this->dws['dw_origen_carga_banco']->insert_row();
	}
	function load_record() {
		$COD_ORIGEN_CARGA_BANCO = $this->get_item_wo($this->current_record, 'COD_ORIGEN_CARGA_BANCO');
		$this->dws['dw_origen_carga_banco']->retrieve($COD_ORIGEN_CARGA_BANCO);
	}
	function get_key() {
		return $this->dws['dw_origen_carga_banco']->get_item(0, 'COD_ORIGEN_CARGA_BANCO');
	}
	
	function save_record($db) {
		$COD_ORIGEN_CARGA_BANCO = $this->get_key();
		$NOM_ORIGEN_CARGA_BANCO = $this->dws['dw_origen_carga_banco']->get_item(0, 'NOM_ORIGEN_CARGA_BANCO');		
		$COD_CUENTA_CORRIENTE 	= $this->dws['dw_origen_carga_banco']->get_item(0, 'COD_CUENTA_CORRIENTE');	
		
		$COD_ORIGEN_CARGA_BANCO = ($COD_ORIGEN_CARGA_BANCO=='') ? "null" : $COD_ORIGEN_CARGA_BANCO;		
    
		$sp = 'spu_origen_carga_banco';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    
	    $param	= "'$operacion', 
	    		 	$COD_ORIGEN_CARGA_BANCO,
	    		 	'$NOM_ORIGEN_CARGA_BANCO',
	    		 	$COD_CUENTA_CORRIENTE"; 
	    if ($db->EXECUTE_SP($sp, $param)){
	    	if ($this->is_new_record()) {				
        		$COD_ORIGEN_CARGA_BANCO = $db->GET_IDENTITY("origen_carga_banco");
        		$this->dws['dw_origen_carga_banco']->set_item(0, 'COD_ORIGEN_CARGA_BANCO', $COD_ORIGEN_CARGA_BANCO);
			}
			return true;
		}
		return false;		
	}
}
?>