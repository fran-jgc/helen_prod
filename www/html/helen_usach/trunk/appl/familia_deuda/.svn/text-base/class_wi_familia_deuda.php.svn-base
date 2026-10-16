<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
	
class dw_cuenta_tipo_documento_pago extends datawindow {
	function __construct() {		
		$sql = sql_familia_deuda::get_sql('DW_CUENTA_TIPO_DOCUMENTO_PAGO');			
		parent::__construct($sql, 'CUENTA_TIPO_DOCUMENTO_PAGO', false, false);	
		
		$this->add_control(new edit_text('COD_CUENTA_TIPO_DOCUMENTO_PAGO', 10,10, 'hidden'));
		
		$sql = sql_familia_deuda::get_sql('DD_TIPO_DOCUMENTO_PAGO');
		$this->add_control(new drop_down_dw('COD_TIPO_DOCUMENTO_PAGO', $sql, 140));
		$this->set_entrable('COD_TIPO_DOCUMENTO_PAGO', false);
		
		$this->add_control(new edit_check_box('USAR_CUENTA_CTACTE', 'S', 'N'));
		
		$sql = sql_familia_deuda::get_sql('DD_CUENTA_CONTABLE') ;
		$this->add_control(new drop_down_dw('COD_CUENTA_CONTABLE', $sql , 250));

		$this->set_mandatory('COD_TIPO_DOCUMENTO_PAGO', 'Tipo Documento');
	}
	
	function update($db, $COD_FAMILIA_DEUDA)	{

		$sp = 'spu_cuenta_tipo_documento_pago';		
		for ($i = 0; $i < $this->row_count(); $i++)		{
			$statuts = $this->get_status_row($i);
			if ($statuts == K_ROW_NOT_MODIFIED || $statuts == K_ROW_NEW)
				continue;
			
			$COD_CUENTA_TIPO_DOCUMENTO_PAGO	= $this->get_item($i, 'COD_CUENTA_TIPO_DOCUMENTO_PAGO');
			$COD_CUENTA_CONTABLE			= $this->get_item($i, 'COD_CUENTA_CONTABLE');
			$COD_TIPO_DOCUMENTO_PAGO		= $this->get_item($i, 'COD_TIPO_DOCUMENTO_PAGO');
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

class wi_familia_deuda extends w_input 
{
    function __construct($cod_item_menu) {
        parent::__construct('familia_deuda', $cod_item_menu);

		$sql =sql_familia_deuda::get_sql('INPUT');

		$this->dws['dw_familia_deuda'] = new datawindow($sql);

		// asigna los formatos				
		$this->dws['dw_familia_deuda']->add_control($control = new edit_text_upper('NOM_FAMILIA_DEUDA', 70, 100));
		$control->set_onChange("valida_nom_familia_deuda();");		

		$sql = sql_familia_deuda::get_sql('DD_CUENTA_CONTABLE') ;
		$this->dws['dw_familia_deuda']->add_control(new drop_down_dw('COD_CUENTA_CH_FECHA', $sql , 250));
		$this->dws['dw_familia_deuda']->add_control(new drop_down_dw('COD_CUENTA_PROTESTO', $sql , 250));
		$this->dws['dw_familia_deuda']->add_control(new drop_down_dw('COD_CUENTA_COBRANZA', $sql , 250));
		
		// asigna los mandatorys
		$this->dws['dw_familia_deuda']->set_mandatory('NOM_FAMILIA_DEUDA', 'Nombre Familia Deuda');	
		$this->dws['dw_cuenta_tipo_documento_pago'] = new dw_cuenta_tipo_documento_pago();
	}
	function new_record() {
		$this->dws['dw_familia_deuda']->insert_row();
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);		
		$sql = sql_familia_deuda::get_sql('NEW_RECORD_TIPO_DOCUMENTO_PAGO');
		$result = $db->build_results($sql);
		for ($i=0; $i<count($result); $i++) {
			$row = $this->dws['dw_cuenta_tipo_documento_pago']->insert_row();
			$this->dws['dw_cuenta_tipo_documento_pago']->set_item($row, 'COD_TIPO_DOCUMENTO_PAGO', $result[$i]['COD_TIPO_DOCUMENTO_PAGO']);
			$this->dws['dw_cuenta_tipo_documento_pago']->set_item($row, 'USAR_CUENTA_CTACTE', 'N');
		}
	}
	function load_record() {
		$COD_FAMILIA_DEUDA = $this->get_item_wo($this->current_record, 'COD_FAMILIA_DEUDA');
		$this->dws['dw_familia_deuda']->retrieve($COD_FAMILIA_DEUDA);
		$this->dws['dw_cuenta_tipo_documento_pago']->retrieve($COD_FAMILIA_DEUDA);
	}
	function get_key() {
		return $this->dws['dw_familia_deuda']->get_item(0, 'COD_FAMILIA_DEUDA');
	}
	function save_record($db) {
		$COD_FAMILIA_DEUDA = $this->get_key();
		$NOM_FAMILIA_DEUDA = $this->dws['dw_familia_deuda']->get_item(0, 'NOM_FAMILIA_DEUDA');	
		$COD_CUENTA_CH_FECHA = $this->dws['dw_familia_deuda']->get_item(0, 'COD_CUENTA_CH_FECHA');
		$COD_CUENTA_PROTESTO = $this->dws['dw_familia_deuda']->get_item(0, 'COD_CUENTA_PROTESTO');
		$COD_CUENTA_COBRANZA = $this->dws['dw_familia_deuda']->get_item(0, 'COD_CUENTA_COBRANZA');	
		
		$COD_FAMILIA_DEUDA = ($COD_FAMILIA_DEUDA=='') ? "null" : $COD_FAMILIA_DEUDA;		
		$COD_CUENTA_CH_FECHA = ($COD_CUENTA_CH_FECHA=='') ? "null" : $COD_CUENTA_CH_FECHA;		
		$COD_CUENTA_PROTESTO = ($COD_CUENTA_PROTESTO=='') ? "null" : $COD_CUENTA_PROTESTO;		
		$COD_CUENTA_COBRANZA = ($COD_CUENTA_COBRANZA=='') ? "null" : $COD_CUENTA_COBRANZA;
		
		$sp = 'spu_familia_deuda';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    
	    $param	= "'$operacion', $COD_FAMILIA_DEUDA, '$NOM_FAMILIA_DEUDA', $COD_CUENTA_CH_FECHA,$COD_CUENTA_PROTESTO,$COD_CUENTA_COBRANZA"; 	

		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$COD_FAMILIA_DEUDA = $db->GET_IDENTITY("familia_deuda");
				$this->dws['dw_familia_deuda']->set_item(0, 'COD_FAMILIA_DEUDA', $COD_FAMILIA_DEUDA);
			}
			
			if (!$this->dws['dw_cuenta_tipo_documento_pago']->update($db,$COD_FAMILIA_DEUDA))
				return false;
			
			return true;
		}
		return false;
				
	}
}
?>