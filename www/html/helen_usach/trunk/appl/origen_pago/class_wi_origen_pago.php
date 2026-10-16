<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/*
Clase : WI_ORIGEN_PAGO
*/
class wi_origen_pago_base extends w_input {
	function __construct($cod_item_menu) {
	    parent::__construct('origen_pago', $cod_item_menu);
		$this->valida_llave = true;		// valida que la PK sea unica
		$this->b_save_visible = false;
		$this->b_no_save_visible = false;
		$this->b_modify_visible = false;
		$sql = sql_origen_pago::get_sql('INPUT');
		
		$this->dws['dw_origen_pago'] = new datawindow($sql);

		// asigna los formatos
		$this->dws['dw_origen_pago']->add_control($control = new edit_num('COD_ORIGEN_PAGO', 12, 10));
		$control->con_separador_miles = false;		
		$control->set_onChange("valida_cod_origen_pago();");
		$this->dws['dw_origen_pago']->add_control($control = new edit_text_upper('NOM_ORIGEN_PAGO', 80, 100));	
		$control->set_onChange("valida_nom_origen_pago();");	
		
		
		// asigna los mandatorys
		$this->dws['dw_origen_pago']->set_mandatory('COD_ORIGEN_PAGO', 'Código');
		$this->dws['dw_origen_pago']->set_mandatory('NOM_ORIGEN_PAGO', 'Nombre Origen Pago');
	}
	function new_record() {
		$this->dws['dw_origen_pago']->insert_row();
	}
	function load_record() {
		$cod_origen_pago = $this->get_item_wo($this->current_record, 'COD_ORIGEN_PAGO');
		$this->dws['dw_origen_pago']->retrieve($cod_origen_pago);
	}
	function get_key() {
		return $this->dws['dw_origen_pago']->get_item(0, 'COD_ORIGEN_PAGO');
	}
	
	function save_record($db) {
		$COD_ORIGEN_PAGO = $this->get_key();
		$NOM_ORIGEN_PAGO = $this->dws['dw_origen_pago']->get_item(0, 'NOM_ORIGEN_PAGO');
		

		$COD_ORIGEN_PAGO = ($COD_ORIGEN_PAGO=='') ? "null" : $COD_ORIGEN_PAGO;		
    
		$sp = 'spu_origen_pago';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
			    		    
	    $param	= "'$operacion', $COD_ORIGEN_PAGO, '$NOM_ORIGEN_PAGO'";
	    
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$COD_ORIGEN_PAGO = $this->get_key();
				$this->dws['dw_origen_pago']->set_item(0, 'COD_ORIGEN_PAGO', $COD_ORIGEN_PAGO);
			}
			return true;
		}
		return false;		
	}
}
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wi_origen_pago.php";
if (file_exists($file_name)) 
	require_once($file_name);
else {
	class wi_origen_pago extends wi_origen_pago_base {
	    function __construct($cod_item_menu) {
	        parent::__construct($cod_item_menu); 
		}
	}
}
?>