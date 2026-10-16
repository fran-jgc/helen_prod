<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class dw_cuenta_tipo_familia_deuda extends datawindow {
	function __construct() {		
		$sql = sql_tipo_documento_pago::get_sql('DW_CUENTA_TIPO_FAMILIA_DEUDA');			
		parent::__construct($sql, 'CUENTA_TIPO_FAMILIA_DEUDA', false, false);	
		
		$this->add_control(new edit_text('COD_CUENTA_TIPO_DOCUMENTO_PAGO', 10,10, 'hidden'));
		
		$sql = sql_tipo_documento_pago::get_sql('DD_FAMILIA_DEUDA');
		$this->add_control(new drop_down_dw('COD_FAMILIA_DEUDA', $sql, 140));
		$this->set_entrable('COD_FAMILIA_DEUDA', false);
		
		$this->add_control(new edit_check_box('USAR_CUENTA_CTACTE', 'S', 'N'));
		
		$sql = sql_familia_deuda::get_sql('DD_CUENTA_CONTABLE') ;
		$this->add_control(new drop_down_dw('COD_CUENTA_CONTABLE', $sql , 250));

	}
	
	function update($db, $COD_TIPO_DOCUMENTO_PAGO)	{

		$sp = 'spu_cuenta_tipo_documento_pago';		
		for ($i = 0; $i < $this->row_count(); $i++)		{
			$statuts = $this->get_status_row($i);
			if ($statuts == K_ROW_NOT_MODIFIED || $statuts == K_ROW_NEW)
				continue;
			
			$COD_CUENTA_TIPO_DOCUMENTO_PAGO	= $this->get_item($i, 'COD_CUENTA_TIPO_DOCUMENTO_PAGO');
			$COD_CUENTA_CONTABLE			= $this->get_item($i, 'COD_CUENTA_CONTABLE');
			$COD_FAMILIA_DEUDA				= $this->get_item($i, 'COD_FAMILIA_DEUDA');
			$USAR_CUENTA_CTACTE				= $this->get_item($i, 'USAR_CUENTA_CTACTE');
			
			$COD_CUENTA_TIPO_DOCUMENTO_PAGO = ($COD_CUENTA_TIPO_DOCUMENTO_PAGO=='') ? "null" : $COD_CUENTA_TIPO_DOCUMENTO_PAGO;
			$COD_CUENTA_CONTABLE = ($COD_CUENTA_CONTABLE=='') ? "null" : $COD_CUENTA_CONTABLE;
				
			if ($statuts == K_ROW_NEW_MODIFIED)
				$operacion = 'INSERT';
			elseif ($statuts == K_ROW_MODIFIED)
				$operacion = 'UPDATE';
			
			$param = "'$operacion'
					,$COD_CUENTA_TIPO_DOCUMENTO_PAGO
					,$COD_FAMILIA_DEUDA
					,$COD_CUENTA_CONTABLE
					,$COD_TIPO_DOCUMENTO_PAGO
					,'$USAR_CUENTA_CTACTE'";
								
			if (!$db->EXECUTE_SP($sp, $param))
				return false;
			}
		return true;
	}
}
/*
Clase : WI_TIPO_documento_pago
*/
class wi_tipo_documento_pago extends w_input {
    function __construct($cod_item_menu) {
        parent::__construct('tipo_documento_pago', $cod_item_menu);
		$this->valida_llave = true;		// valida que la PK sea unica
		
		$sql = sql_tipo_documento_pago::get_sql('INPUT');
		
		$this->dws['dw_tipo_documento_pago'] = new datawindow($sql);
		
		// asigna los formatos		
		$this->dws['dw_tipo_documento_pago']->add_control($control = new edit_num('COD_TIPO_DOCUMENTO_PAGO', 10, 10));
		$control->set_onChange("valida_cod_tipo_documento_pago();");	
		$this->dws['dw_tipo_documento_pago']->add_control($control = new edit_text_upper('NOM_TIPO_DOCUMENTO_PAGO', 80, 100));	

		// asigna los mandatorys
		$this->dws['dw_tipo_documento_pago']->set_mandatory('COD_TIPO_DOCUMENTO_PAGO', 'Código');
		$this->dws['dw_tipo_documento_pago']->set_mandatory('NOM_TIPO_DOCUMENTO_PAGO', 'Nombre del Descuento');
	
		$this->dws['dw_cuenta_tipo_familia_deuda'] = new dw_cuenta_tipo_familia_deuda();	
		
	}
	function new_record() {
		$this->dws['dw_tipo_documento_pago']->insert_row();
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = sql_tipo_documento_pago::get_sql('NEW_RECORD_FAMILIA_DEUDA');
		$result = $db->build_results($sql);
		for ($i=0; $i<count($result); $i++) {
			$row = $this->dws['dw_cuenta_tipo_familia_deuda']->insert_row();
			$this->dws['dw_cuenta_tipo_familia_deuda']->set_item($row, 'COD_FAMILIA_DEUDA', $result[$i]['COD_FAMILIA_DEUDA']);
			$this->dws['dw_cuenta_tipo_familia_deuda']->set_item($row, 'USAR_CUENTA_CTACTE', 'N');
		}
		
	}
	function load_record() {
		
		$cod_tipo_documento_pago = $this->get_item_wo($this->current_record, 'COD_TIPO_DOCUMENTO_PAGO');
		$this->dws['dw_tipo_documento_pago']->retrieve($cod_tipo_documento_pago);
		//$this->dws['dw_tipo_documento_pago']->set_entrable('COD_TIPO_DOCUMENTO_PAGO', false);
		
		if($cod_tipo_documento_pago == 1 || $cod_tipo_documento_pago == 2 || $cod_tipo_documento_pago == 3
		    || $cod_tipo_documento_pago == 4 || $cod_tipo_documento_pago == 5 || $cod_tipo_documento_pago == 6){	
			$this->b_delete_visible = false;
			$this->b_save_visible = false;
			$this->b_no_save_visible = false;
			$this->b_modify_visible = false;
		}	
		else {
			$this->b_delete_visible = true;
			$this->b_save_visible = true;
			$this->b_no_save_visible = true;
			$this->b_modify_visible = true;
		}
		$this->dws['dw_cuenta_tipo_familia_deuda']->retrieve($cod_tipo_documento_pago);
	}
	function get_key() {
		return $this->dws['dw_tipo_documento_pago']->get_item(0, 'COD_TIPO_DOCUMENTO_PAGO');
	}
	
	function save_record($db) {
		$COD_TIPO_DOCUMENTO_PAGO = $this->get_key();
		$NOM_TIPO_DOCUMENTO_PAGO = $this->dws['dw_tipo_documento_pago']->get_item(0, 'NOM_TIPO_DOCUMENTO_PAGO');
		
		
		$COD_TIPO_DOCUMENTO_PAGO = ($COD_TIPO_DOCUMENTO_PAGO=='') ? "null" : $COD_TIPO_DOCUMENTO_PAGO;		
    
		$sp = 'spu_tipo_documento_pago';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    		    
	    $param	= "'$operacion', $COD_TIPO_DOCUMENTO_PAGO, '$NOM_TIPO_DOCUMENTO_PAGO'";
	   	
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$COD_TIPO_DOCUMENTO_PAGO = $this->get_key();
				$this->dws['dw_tipo_documento_pago']->set_item(0, 'COD_TIPO_DOCUMENTO_PAGO', $COD_TIPO_DOCUMENTO_PAGO);
			}
			
			if (!$this->dws['dw_cuenta_tipo_familia_deuda']->update($db,$COD_TIPO_DOCUMENTO_PAGO))
				return false;
				
			return true;
		}
		return false;		
				
	}
	
}

?>