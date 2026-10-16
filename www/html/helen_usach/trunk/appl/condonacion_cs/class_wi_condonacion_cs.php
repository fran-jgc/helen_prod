<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/*
Clase : WI_CONDONACION_CS
*/
class wi_condonacion_cs extends w_input {
    
	const K_EMITIDA		= 1;
	const K_ACEPTADA 	= 2;
	const K_RECHAZADA	= 3;
	
	function __construct($cod_item_menu) {
	    parent::__construct('condonacion_cs', $cod_item_menu);
		
		$this->b_delete_visible = false;

		$sql = sql_condonacion_cs::get_sql('INPUT');
		
		$this->dws['dw_condonacion_cs'] = new datawindow($sql);

	
		// asigna los formatos
		$this->dws['dw_condonacion_cs']->add_control(new static_text('COD_CONDONACION_CS'));
		$this->dws['dw_condonacion_cs']->add_control(new static_text('FECHA_CONDONACION_CS'));			
		$this->dws['dw_condonacion_cs']->add_control(new static_link('COD_DEUDA', '../../../../commonlib_seven/trunk/php/link_wi.php?modulo_origen=condonacion_cs&modulo_destino=deuda&cod_modulo_destino=[COD_DEUDA]&cod_item_menu=1010'));
		$this->dws['dw_condonacion_cs']->add_control(new static_text('RUT'));
		$this->dws['dw_condonacion_cs']->add_control(new static_text('NOMBRE'));
		$sql_cs = sql_condonacion_cs::get_sql('DD_COD_ESTADO_CONDONACION_CS');
		$this->dws['dw_condonacion_cs']->add_control(new drop_down_dw('COD_ESTADO_CONDONACION_CS', $sql_cs, 100));
		//$control->set_onChange("f_get_anulado($this->cod_usuario);");
		$this->dws['dw_condonacion_cs']->add_control(new static_num('MONTO',4));
		$this->dws['dw_condonacion_cs']->add_control(new edit_text_multiline('OBS', 60, 5));
		
		//historial de cambio de estado
		$this->add_auditoria('COD_ESTADO_CONDONACION_CS');
	}
	function new_record() {	
		$this->dws['dw_condonacion_cs']->insert_row();		
	}
	function load_record() {
		$COD_CONDONACION_CS = $this->get_item_wo($this->current_record, 'COD_CONDONACION_CS');
		$this->dws['dw_condonacion_cs']->retrieve($COD_CONDONACION_CS);
		
		$ESTADO_CONDONACION_CS = $this->dws['dw_condonacion_cs']->get_item(0, 'COD_ESTADO_CONDONACION_CS');
		
		$this->b_print_visible = false;
		IF ($ESTADO_CONDONACION_CS == self::K_EMITIDA) {					
			$this->b_save_visible = true;
			$this->b_no_save_visible = true;
			$this->b_modify_visible = true;
		}
		else if ($ESTADO_CONDONACION_CS == self::K_ACEPTADA || $ESTADO_CONDONACION_CS == self::K_RECHAZADA) {
			$this->b_save_visible = false;
			$this->b_no_save_visible = false;
			$this->b_modify_visible = false;
		}
	} 
	function get_key() {
		return $this->dws['dw_condonacion_cs']->get_item(0, 'COD_CONDONACION_CS');
	}
	
	function save_record($db) {
		$COD_CONDONACION_CS			= $this->get_key();
		$FECHA_CONDONACION_CS		= $this->dws['dw_condonacion_cs']->get_item(0, 'FECHA_CONDONACION_CS');
		$COD_ESTADO_CONDONACION_CS	= $this->dws['dw_condonacion_cs']->get_item(0, 'COD_ESTADO_CONDONACION_CS');
		$COD_DEUDA	 	      		= $this->dws['dw_condonacion_cs']->get_item(0, 'COD_DEUDA');
		$OBS		 	      		= $this->dws['dw_condonacion_cs']->get_item(0, 'OBS');
		 		
		$COD_CONDONACION_CS = ($COD_CONDONACION_CS=='') ? "null" : $COD_CONDONACION_CS;
		$OBS = ($OBS=='') ? "null" : "'$OBS'";
		
		if(K_TIPO_BD == 'oci'){
			$FECHA_CONDONACION_CS = "to_date('$FECHA_CONDONACION_CS','dd/mm/yyyy')";
		}
		else if(K_TIPO_BD == 'mssql'){
			$FECHA_CONDONACION_CS = $this->str2date($FECHA_CONDONACION_CS);
		}
		
		$sp = 'spu_condonacion_cs';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	 		    
		$param	= "'$operacion'
					, $COD_CONDONACION_CS
		    		, $FECHA_CONDONACION_CS
		    		, $COD_ESTADO_CONDONACION_CS
		    		, $COD_DEUDA
		    		, $OBS
		    		, $this->cod_usuario"; 

	    if ($db->EXECUTE_SP($sp, $param)){
	    	if ($this->is_new_record()) {				
        		$COD_CONDONACION_CS = $db->GET_IDENTITY("condonacion_cs");
        		$this->dws['dw_condonacion_cs']->set_item(0, 'COD_CONDONACION_CS', $COD_CONDONACION_CS);
			}
			return true;
		}
		return false;				
	}
}
?>