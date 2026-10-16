<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");
/*
 Clase : WI_COMPROBANTE
 */

class dw_analitico_comprobante extends datawindow{
    
	function __construct() {
	    
		$sql = sql_comprobante::get_sql('DW_ANALITICO_COMPROBANTE');
		parent::__construct($sql, 'ANALITICO_COMPROBANTE', true, true);
		
		$this->add_control(new edit_text('COD_ANALITICO_COMPROBANTE', 10, 10, 'hidden'));
		$this->add_control(new static_num('RUT'));
		$this->add_control(new static_text('DIG_VERIF'));
		$this->add_control(new static_text('COD_DEUDA'));
		//$this->add_control(new static_num('MONTO_DEBE'));
		//$this->add_control(new static_num('MONTO_HABER'));
		
		$this->add_control(new edit_num('MONTO_DEBE'));
		$this->add_control(new edit_num('MONTO_HABER'));
		
		$sql_estado=sql_comprobante::get_sql('DD_CUENTA_CONTABLE');
		$this->add_control(new drop_down_dw('COD_CUENTA_CONTABLE_CC', $sql_estado, 325));
		
		$this->set_computed('MONTO_DEBE_H', '[MONTO_DEBE]');
		$this->controls['MONTO_DEBE_H']->type = 'hidden';
		$this->accumulate('MONTO_DEBE_H');
		
		$this->set_computed('MONTO_HABER_H', '[MONTO_HABER]');
		$this->controls['MONTO_HABER_H']->type = 'hidden';
		$this->accumulate('MONTO_HABER_H');
	}
	function retrieve($cod_comprobante) {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT COUNT(*) AS CANT_ANALITICO FROM ANALITICO_COMPROBANTE WHERE COD_COMPROBANTE = $cod_comprobante";		 	
		$result = $db->build_results($sql);
		$cant_analitico = $result[0]['CANT_ANALITICO'];		
				
		if ($cant_analitico < 100)
			parent::retrieve($cod_comprobante);
	}
	
	function fill_template(&$temp) {
		parent::fill_template($temp);
	}
	
	function update($db,$COD_COMPROBANTE){
		
		$sp = 'spu_analitico';
		
		for ($i = 0; $i < $this->row_count(); $i++){	
			$statuts = $this->get_status_row($i);

			if ($statuts == K_ROW_NOT_MODIFIED || $statuts == K_ROW_NEW)
				continue;
					
			//TOMO VALORES
			$COD_DEUDA=$this->get_item($i, 'COD_DEUDA');
			$COD_ANALITICO_COMPROBANTE=$this->get_item($i, 'COD_ANALITICO_COMPROBANTE');
			$COD_CUENTA_CONTABLE= $this->get_item($i, 'COD_CUENTA_CONTABLE_CC');
			$MONTO_DEBE=$this->get_item($i, 'MONTO_DEBE');
			$MONTO_HABER=$this->get_item($i, 'MONTO_HABER');
			
			if ($statuts == K_ROW_NEW_MODIFIED)$operacion = 'INSERT';
			elseif ($statuts == K_ROW_MODIFIED)$operacion = 'UPDATE';
			
			//SETEAR NULOS
			$COD_ANALITICO_COMPROBANTE		= ($COD_ANALITICO_COMPROBANTE=='') ? "null" : $COD_ANALITICO_COMPROBANTE;
			$MONTO_DEBE= ($MONTO_DEBE=='') ? "0" : $MONTO_DEBE;
			$MONTO_HABER= ($MONTO_HABER=='') ? "0" : $MONTO_HABER;
			$COD_DEUDA	= ($COD_DEUDA=='') ? "null" : $COD_DEUDA;
			$COD_CUENTA_CONTABLE = ($COD_CUENTA_CONTABLE=='') ? "null" : $COD_CUENTA_CONTABLE;
			
			$param = "'$operacion'
						, $COD_ANALITICO_COMPROBANTE
						, $COD_COMPROBANTE
						, $COD_DEUDA
						, $COD_CUENTA_CONTABLE
						, $MONTO_DEBE
						, $MONTO_HABER";

						
			if (!$db->EXECUTE_SP($sp, $param)) 
				return false;
			else {//--------
				if ($statuts == K_ROW_NEW_MODIFIED){
					$COD_ANALITICO_COMPROBANTE = $db->GET_IDENTITY('analitico_comprobante');
					$this->set_item($i, 'COD_ANALITICO_COMPROBANTE', $COD_ANALITICO_COMPROBANTE);		
				}
			}
		}
		for ($i = 0; $i < $this->row_count('delete'); $i++) {
			$statuts = $this->get_status_row($i, 'delete');			
			if ($statuts == K_ROW_NEW || $statuts == K_ROW_NEW_MODIFIED)
				continue;
				
			$COD_ANALITICO_DELETE = $this->get_item($i, 'COD_ANALITICO_COMPROBANTE', 'delete');
			if (!$db->EXECUTE_SP($sp, "'DELETE',$COD_ANALITICO_DELETE")){			
				return false;				
			}			
		}
		return true;
	}
}

class dw_detalle_cuenta extends datawindow{
    
	const K_ESTADO_COMPROBANTE_APROBADO=2;
	const K_ESTADO_COMPROBANTE_ANULADO=3;
	
	function __construct() {
		$sql = sql_comprobante::get_sql('DW_DETALLE_CUENTA');
		parent::__construct($sql, 'DETALLE_CUENTA', true, true);
		//CONTROLES
		$sql_estado = sql_comprobante::get_sql('DD_COD_CUENTA_CONTABLE');
		$this->add_control(new drop_down_dw('COD_CUENTA_CONTABLE', $sql_estado, 415));
		
		//DEBE****************************************************
		$this->add_control($control = new edit_num('DEBE', 20, 20));//CAMPO VISIBLE, PASAR� SU VALOR A DEBE_H EN f_valida_debe_haber
		$control->set_onChange("f_valida_debe_haber(this);debe_haber(this);");
		$this->set_computed('DEBE2', '[DEBE]');
		$this->controls['DEBE2']->type = 'hidden';
		$this->accumulate('DEBE2');
		
		//HABER *****************************************************
		$this->add_control($control = new edit_num('HABER', 20, 20));
		$control->set_onChange("f_valida_debe_haber(this);debe_haber(this);");
		$this->set_computed('HABER2', '[HABER]');
		$this->controls['HABER2']->type = 'hidden';
		$this->accumulate('HABER2');
		
		$this->add_control(new edit_text('COD_COMPROBANTE_H',5,5, 'hidden'));
		$this->add_control(new edit_text('COD_ITEM_COMPROBANTE_H',5,5, 'hidden'));
	}
	
	function fill_record(&$temp, $record){
		parent::fill_record($temp, $record);
		
		if ($this->entrable) {
			$row = $this->redirect($record);
			$eliminar = '<img src="../../../../commonlib_seven/trunk/images/b_delete_line.jpg" onClick="del_line(\''.$this->label_record.'_'.$row.'\', \''.$this->nom_tabla.'\');" style="cursor:pointer">';
			$temp->setVar($this->label_record.".ELIMINAR_".strtoupper($this->label_record), $eliminar);
		}
	}
	function update($db,$COD_COMPROBANTE){
		
		$sp = 'spu_item_comprobante';
		
		for ($i = 0; $i < $this->row_count(); $i++){	
			$statuts = $this->get_status_row($i);
			
			if ($statuts == K_ROW_NOT_MODIFIED || $statuts == K_ROW_NEW)
				continue;
				
			//TOMO VALORES
			$COD_ITEM_COMPROBANTE=$this->get_item($i, 'COD_ITEM_COMPROBANTE_H');
			$COD_CUENTA_CONTABLE= $this->get_item($i, 'COD_CUENTA_CONTABLE');
			$MONTO_DEBE=$this->get_item($i, 'DEBE');
			$MONTO_HABER=$this->get_item($i, 'HABER');
			
			if ($statuts == K_ROW_NEW_MODIFIED)$operacion = 'INSERT';
			elseif ($statuts == K_ROW_MODIFIED)$operacion = 'UPDATE';
			
			//SETEAR NULOS
			$COD_ITEM_COMPROBANTE		= ($COD_ITEM_COMPROBANTE=='') ? "null" : $COD_ITEM_COMPROBANTE;
			
			$param = "'$operacion'
						, $COD_ITEM_COMPROBANTE
						, $COD_COMPROBANTE
						, $COD_CUENTA_CONTABLE
						, $MONTO_DEBE
						, $MONTO_HABER";
			
			if (!$db->EXECUTE_SP($sp, $param)) 
				return false;
			else {
				if ($statuts == K_ROW_NEW_MODIFIED){
					$COD_ITEM_COMPROBANTE = $db->GET_IDENTITY('item_comprobante');
					$this->set_item($i, 'COD_ITEM_COMPROBANTE_H', $COD_ITEM_COMPROBANTE);		
				}
			}
		}
		
		for ($i = 0; $i < $this->row_count('delete'); $i++) 
		{
				$statuts_d = $this->get_status_row($i, 'delete');
				if ($statuts_d == K_ROW_NEW || $statuts_d == K_ROW_NEW_MODIFIED)
					continue;
					
				$COD_ITEM_COMPROBANTE = $this->get_item($i, 'COD_ITEM_COMPROBANTE_H', 'delete');
				
				if (!$db->EXECUTE_SP($sp, "'DELETE', $COD_ITEM_COMPROBANTE"))
					return false;
		}
		return true;
	}
}

class wi_comprobante_base extends w_input {
	const K_ESTADO_COMPROBANTE_PENDIENTE=1;
	const K_ESTADO_COMPROBANTE_APROBADO=2;
	const K_ESTADO_COMPROBANTE_ANULADO=3;
	const K_COD_ORIGEN_COMPROBANTE_MANUAL=1;
	const K_AUTORIZA_MODIFICA_FECHA_COMPROBANTE = '992505';
	
	const K_PARAM_RUT_U				= 4;
	const K_PARAM_NOM_U 			= 5;
	const K_PARAM_DIRECCCION 		= 9;
	const K_PARAM_FONO 				= 10;
	const K_PARAM_COMUNA 			= 11;
	const K_PARAM_CIUDAD 			= 12;
	const K_PARAM_E_MAIL 			= 13;
	const K_PARAM_SITIO_WEB 		= 14;
	const K_PARAM_FAX 				= 15;
	const K_PARAM_PAIS 				= 16;
	const K_ORIGEN_C_SUSPENCION		= 3;
	const K_ORIGEN_C_CONDONACION	= 4;
	const K_ORIGEN_C_PRORROGA		= 5;
	const K_ORIGEN_C_NOVACION		= 6;
	const K_ORIGEN_MANUAL			= 1;
	
	
	
	function __construct($cod_item_menu) {
	    parent::__construct('comprobante', $cod_item_menu);
		$this->b_delete_visible  = false;

		$sql = sql_comprobante::get_sql('INPUT');

		$this->dws['dw_comprobante'] = new datawindow($sql);
		$this->dws['dw_detalle_cuenta'] = new dw_detalle_cuenta($sql);
		$this->dws['dw_analitico_comprobante'] = new dw_analitico_comprobante($sql);
			
		//CONTROLES
		$this->dws['dw_comprobante']->add_control(new static_text('COD_COMPROBANTE'));
		$this->dws['dw_comprobante']->add_control(new edit_text('NOM_COMPROBANTE', 100, 100));
		$this->dws['dw_comprobante']->add_control(new static_text('NOM_USUARIO'));
		$this->dws['dw_comprobante']->add_control(new static_text('NOM_USUARIO_ESTADO'));
		$this->dws['dw_comprobante']->add_control(new static_text('FECHA_ESTADO'));
		$this->dws['dw_comprobante']->add_control(new edit_text('FECHA_ESTADO_H', 10, 10, 'hidden'));
		$this->dws['dw_comprobante']->add_control(new edit_date('FECHA_COMPROBANTE'));
		
		//HIDDEN
		$this->dws['dw_comprobante']->add_control(new edit_text('COD_USUARIO', 10, 10, 'hidden'));
		$this->dws['dw_comprobante']->add_control(new edit_text('COD_USUARIO_ESTADO_H', 10, 10, 'hidden'));
		$this->dws['dw_comprobante']->add_control(new edit_text('COD_ORIGEN_COMPROBANTE_H', 10, 10, 'hidden'));
		$this->dws['dw_comprobante']->add_control(new edit_text('COD_ESTADO_COMPROBANTE',10,10, 'hidden'));
		
		//DROPS
		
		$sql_familia = sql_comprobante::get_sql('DD_COD_FAMILIA_DEUDA');
				
		$this->dws['dw_comprobante']->add_control(new drop_down_dw('COD_FAMILIA_DEUDA', $sql_familia, 150));
		
		$sql_tipo_comprobante = sql_comprobante::get_sql('DD_COD_TIPO_COMPROBANTE');
		
		$this->dws['dw_comprobante']->add_control(new drop_down_dw('COD_TIPO_COMPROBANTE', $sql_tipo_comprobante, 150));
		
		$sql_estado = sql_comprobante::get_sql('DD_COD_ESTADO_COMPROBANTE_1');
		
		$this->dws['dw_comprobante']->add_control($control =new drop_down_dw('COD_ESTADO_COMPROBANTE', $sql_estado, 150));
		$control->set_onChange("f_get_aprobado($this->cod_usuario);");

		//auditoria al cambiar la fecha en estado aprobado, con privilegio...
		//para todas las UES.
		$this->add_auditoria('FECHA_COMPROBANTE');
		$this->add_auditoria('NOM_COMPROBANTE');
		
		//mandatory
		$this->dws['dw_comprobante']->set_mandatory('NOM_COMPROBANTE', 'Glosa');
		$this->dws['dw_comprobante']->set_mandatory('COD_FAMILIA_DEUDA', 'Familia Deuda');
		$this->dws['dw_comprobante']->set_mandatory('COD_TIPO_COMPROBANTE', 'Tipo Comprobante');
		$this->dws['dw_comprobante']->set_mandatory('COD_ESTADO_COMPROBANTE', 'Estado Comprobante');
		
		$this->dws['dw_detalle_cuenta']->set_mandatory('COD_CUENTA_CONTABLE', 'Cuenta Contable');
		$this->dws['dw_detalle_cuenta']->set_mandatory('DEBE','Debe o Haber');
		$this->dws['dw_detalle_cuenta']->set_mandatory('HABER','Debe o Haber');
		
	}

	function load_record(){
		$cod_comprobante	= $this->get_item_wo($this->current_record, 'COD_COMPROBANTE');
		$this->dws['dw_comprobante']->retrieve($cod_comprobante);
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT COUNT(*) AS CANT_ANALITICO FROM ANALITICO_COMPROBANTE WHERE COD_COMPROBANTE = $cod_comprobante";		 	
		$result = $db->build_results($sql);
		$cant_analitico = $result[0]['CANT_ANALITICO'];	
				 
		if ($cant_analitico < 100){			
			$this->dws['dw_comprobante']->set_item(0, 'DISPLAY_ANALITICO', '');
			$this->dws['dw_comprobante']->set_item(0, 'DISPLAY_MENSAJE_ANALITICO', 'none');
		}		
		else {
			$this->dws['dw_comprobante']->set_item(0, 'DISPLAY_ANALITICO', 'none');
			$this->dws['dw_comprobante']->set_item(0, 'DISPLAY_MENSAJE_ANALITICO', '');			
		}
				
		$this->dws['dw_detalle_cuenta']->retrieve($cod_comprobante);
		$this->dws['dw_analitico_comprobante']->retrieve($cod_comprobante);
		$COD_ESTADO_COMPROBANTE = $this->dws['dw_comprobante']->get_item(0, 'COD_ESTADO_COMPROBANTE');
		$COD_ORIGEN_COMPROBANTE = $this->dws['dw_comprobante']->get_item(0, 'COD_ORIGEN_COMPROBANTE_H');
		
		$sql_estado = sql_comprobante::get_sql('DD_COD_ESTADO_COMPROBANTE_2');
		$this->dws['dw_comprobante']->controls['COD_ESTADO_COMPROBANTE']->set_sql($sql_estado);
		$this->dws['dw_comprobante']->controls['COD_ESTADO_COMPROBANTE']->retrieve();
		
		$this->dws['dw_comprobante']->set_visible('COD_ESTADO_COMPROBANTE', true);

		if ($COD_ESTADO_COMPROBANTE == self::K_ESTADO_COMPROBANTE_ANULADO) {
			$this->b_print_visible 	 = false;
			$this->b_no_save_visible = false;
			$this->b_save_visible 	 = false;
			$this->b_modify_visible  = false;
		}
		else if ($COD_ESTADO_COMPROBANTE == self::K_ESTADO_COMPROBANTE_APROBADO) {
			echo '2';
			$sql_estado = sql_comprobante::get_sql('DD_COD_ESTADO_COMPROBANTE_2');
			$this->dws['dw_comprobante']->controls['COD_ESTADO_COMPROBANTE']->set_sql($sql_estado);
			$this->dws['dw_comprobante']->controls['COD_ESTADO_COMPROBANTE']->retrieve();
			$this->dws['dw_comprobante']->set_entrable('COD_ESTADO_COMPROBANTE', true);
			$this->b_print_visible 	 = true;
			$this->b_no_save_visible = false;
			$this->b_save_visible 	 = false;
			$this->b_modify_visible  = false;
		}
		else if ($COD_ORIGEN_COMPROBANTE != self::K_COD_ORIGEN_COMPROBANTE_MANUAL){
			echo '1';
			$sql_estado = sql_comprobante::get_sql('DD_COD_ESTADO_COMPROBANTE_3');
			$this->dws['dw_comprobante']->controls['COD_ESTADO_COMPROBANTE']->set_sql($sql_estado);
			$this->dws['dw_comprobante']->controls['COD_ESTADO_COMPROBANTE']->retrieve();
			$this->dws['dw_comprobante']->set_entrable('NOM_COMPROBANTE', false);
			$this->dws['dw_comprobante']->set_entrable('COD_FAMILIA_DEUDA', false);
			$this->dws['dw_comprobante']->set_entrable('COD_TIPO_COMPROBANTE', false);
			$this->dws['dw_comprobante']->set_entrable('COD_ESTADO_COMPROBANTE', true);
			$this->dws['dw_comprobante']->set_entrable('FECHA_COMPROBANTE', false);
			
			$this->dws['dw_detalle_cuenta']->set_entrable_dw(true);
			$this->dws['dw_analitico_comprobante']->set_entrable_dw(true);
			
			$this->dws['dw_analitico_comprobante']->set_entrable('MONTO_DEBE', true);
			$this->dws['dw_analitico_comprobante']->set_entrable('MONTO_HABER', true);

			if ($COD_ESTADO_COMPROBANTE == self::K_ESTADO_COMPROBANTE_PENDIENTE){
				$priv = $this->get_privilegio_opcion_usuario(self::K_AUTORIZA_MODIFICA_FECHA_COMPROBANTE, $this->cod_usuario);
				
				if ($priv == 'E'){
					$this->dws['dw_comprobante']->set_entrable('FECHA_COMPROBANTE', true);
					$this->dws['dw_comprobante']->set_entrable('NOM_COMPROBANTE', true);
				}
			}
		}
		else if ($COD_ORIGEN_COMPROBANTE == self::K_COD_ORIGEN_COMPROBANTE_MANUAL && $COD_ESTADO_COMPROBANTE == self::K_ESTADO_COMPROBANTE_PENDIENTE){
			echo 'aqui';
			$sql_estado = sql_comprobante::get_sql('DD_COD_ESTADO_COMPROBANTE_3');
			$this->dws['dw_comprobante']->controls['COD_ESTADO_COMPROBANTE']->set_sql($sql_estado);
			$this->dws['dw_comprobante']->controls['COD_ESTADO_COMPROBANTE']->retrieve();			
			$this->dws['dw_comprobante']->set_entrable('NOM_COMPROBANTE', true);
			$this->dws['dw_comprobante']->set_entrable('COD_FAMILIA_DEUDA', true);
			$this->dws['dw_comprobante']->set_entrable('COD_TIPO_COMPROBANTE', true);
			$this->dws['dw_comprobante']->set_entrable('COD_ESTADO_COMPROBANTE', true);
			$this->dws['dw_comprobante']->set_entrable('FECHA_COMPROBANTE', true);
			$this->dws['dw_detalle_cuenta']->set_entrable_dw(true);
			
			if ($COD_ESTADO_COMPROBANTE == self::K_ESTADO_COMPROBANTE_PENDIENTE){
				$priv = $this->get_privilegio_opcion_usuario(self::K_AUTORIZA_MODIFICA_FECHA_COMPROBANTE, $this->cod_usuario);
				
				if ($priv == 'E'){
					$this->dws['dw_comprobante']->set_entrable('FECHA_COMPROBANTE', true);
					$this->dws['dw_comprobante']->set_entrable('NOM_COMPROBANTE', true);	
				}
			}
		}
		else if ($COD_ESTADO_COMPROBANTE == self::K_ESTADO_COMPROBANTE_PENDIENTE){
			echo '3';
			$sql_estado = sql_comprobante::get_sql('DD_COD_ESTADO_COMPROBANTE_3');
			$this->dws['dw_comprobante']->controls['COD_ESTADO_COMPROBANTE']->set_sql($sql_estado);
			$this->dws['dw_comprobante']->controls['COD_ESTADO_COMPROBANTE']->retrieve();
			$this->dws['dw_comprobante']->set_entrable('COD_FAMILIA_DEUDA', false);
			$this->dws['dw_comprobante']->set_entrable('COD_TIPO_COMPROBANTE', false);
			$this->dws['dw_comprobante']->set_entrable('COD_ESTADO_COMPROBANTE', true);
			$this->dws['dw_comprobante']->set_entrable('FECHA_COMPROBANTE', false);
			
			$this->dws['dw_detalle_cuenta']->set_entrable_dw(true);
			$this->dws['dw_analitico_comprobante']->set_entrable_dw(true);
			
			$priv = $this->get_privilegio_opcion_usuario(self::K_AUTORIZA_MODIFICA_FECHA_COMPROBANTE, $this->cod_usuario);
			if ($priv == 'E'){
				$this->dws['dw_comprobante']->set_entrable('FECHA_COMPROBANTE', true);
				$this->dws['dw_comprobante']->set_entrable('NOM_COMPROBANTE', true);	
			}
		}
	}
	function get_key(){
		return $this->dws['dw_comprobante']->get_item(0, 'COD_COMPROBANTE');
	}
	function new_record() 
	{
		$this->dws['dw_comprobante']->insert_row();
		$this->dws['dw_comprobante']->set_item(0, 'COD_USUARIO', $this->cod_usuario);
		$this->dws['dw_comprobante']->set_item(0, 'NOM_USUARIO', $this->nom_usuario);
		$this->dws['dw_comprobante']->set_item(0, 'FECHA_COMPROBANTE', $this->current_date());
		$this->dws['dw_comprobante']->set_item(0, 'NOM_ORIGEN_COMPROBANTE', 'Manual');
		$this->dws['dw_comprobante']->set_item(0, 'COD_ORIGEN_COMPROBANTE_H', self::K_COD_ORIGEN_COMPROBANTE_MANUAL);		
		$this->dws['dw_comprobante']->set_item(0, 'COD_ESTADO_COMPROBANTE', self::K_ESTADO_COMPROBANTE_PENDIENTE);
		$this->b_print_visible 	 = false;
	}
	function save_record($db) 
	{
		$COD_COMPROBANTE = $this->get_key();
		$FECHA_COMPROBANTE = $this->dws['dw_comprobante']->get_item(0, 'FECHA_COMPROBANTE');
		$COD_USUARIO= $this->dws['dw_comprobante']->get_item(0, 'COD_USUARIO');
		$NOM_COMPROBANTE= $this->dws['dw_comprobante']->get_item(0, 'NOM_COMPROBANTE');
		$COD_FAMILIA_DEUDA= $this->dws['dw_comprobante']->get_item(0, 'COD_FAMILIA_DEUDA');
		$COD_ESTADO_COMPROBANTE= $this->dws['dw_comprobante']->get_item(0, 'COD_ESTADO_COMPROBANTE');
		$COD_TIPO_COMPROBANTE= $this->dws['dw_comprobante']->get_item(0, 'COD_TIPO_COMPROBANTE');
		$COD_ORIGEN_COMPROBANTE= $this->dws['dw_comprobante']->get_item(0, 'COD_ORIGEN_COMPROBANTE_H');
		$COD_USUARIO_ESTADO= $this->dws['dw_comprobante']->get_item(0, 'COD_USUARIO_ESTADO_H');
		$FECHA_ESTADO= $this->dws['dw_comprobante']->get_item(0, 'FECHA_ESTADO_H');
		
		$FECHA_COMPROBANTE = $this->str2date($FECHA_COMPROBANTE);
			
		//SETEAR NULOS 
		$COD_COMPROBANTE		= ($COD_COMPROBANTE=='') ? "null" : $COD_COMPROBANTE;
		$COD_USUARIO_ESTADO		= ($COD_USUARIO_ESTADO=='') ? "null" : $COD_USUARIO_ESTADO;
		$FECHA_ESTADO			= ($FECHA_ESTADO=='') ? "null" : $FECHA_ESTADO;
		
		if ($FECHA_ESTADO != "null"){
			if(K_TIPO_BD == "oci"){
				$FECHA_ESTADO = "TO_DATE('".$FECHA_ESTADO."','dd/mm/yyyy')";
			}else if(K_TIPO_BD == "mssql"){
				$FECHA_ESTADO = $this->str2date($FECHA_ESTADO);
			}
		}
		
		if ($this->is_new_record())$operacion = 'INSERT';
	    else $operacion = 'UPDATE';

		$param	= "'$operacion', 
					$COD_COMPROBANTE,  
					$FECHA_COMPROBANTE, 
					$COD_USUARIO, 
					'$NOM_COMPROBANTE', 
					$COD_FAMILIA_DEUDA, 
					$COD_ESTADO_COMPROBANTE,
					$COD_TIPO_COMPROBANTE, 
					$COD_ORIGEN_COMPROBANTE,
					$COD_USUARIO_ESTADO, 
					$FECHA_ESTADO";

		$sp = 'spu_comprobante';
		if ($db->EXECUTE_SP($sp, $param))
		{
			if ($this->is_new_record()) 
			{
				$COD_COMPROBANTE = $db->GET_IDENTITY("comprobante");
				$this->dws['dw_comprobante']->set_item(0, 'COD_COMPROBANTE', $COD_COMPROBANTE);
			}	
			//SETEO EL COD_CONDONACION EN TODA LA DATA DE ITEMS DE LA DEUDA
			for ($i=0; $i<$this->dws['dw_detalle_cuenta']->row_count(); $i++) 
				$this->dws['dw_detalle_cuenta']->set_item($i, 'COD_COMPROBANTE_H', $COD_COMPROBANTE);
			
				//VOY A UPDETEAR(ingresar) LA DATA
			if (!$this->dws['dw_detalle_cuenta']->update($db, $COD_COMPROBANTE)) return false;
			
			if (!$this->dws['dw_analitico_comprobante']->update($db, $COD_COMPROBANTE)) return false;
			
			if($COD_ORIGEN_COMPROBANTE == self::K_ORIGEN_MANUAL){
				$operacion = 'MANUAL';
				$param = "'$operacion',
							$COD_COMPROBANTE";
				
				if (!$db->EXECUTE_SP($sp, $param)){
					return false;
				}
			}
			return true;
		}
		return false;
		
	}
	function print_record() {				    	
		$this->print_comprobante_pdf('logo');
		$this->redraw();
	}
	function print_comprobante_pdf($con_logo) {	
		$cod_comprobante = $this->get_key();		
		
		$sql= sql_comprobante::get_sql('PRINT_COMPROBANTE',$cod_comprobante);						
			// reporte
		$labels = array();
		$labels['strCOD_COMPROBANTE'] = $cod_comprobante;
		$COD_ORIGEN_COMPROBANTE= $this->dws['dw_comprobante']->get_item(0, 'COD_ORIGEN_COMPROBANTE_H');
		
		if($COD_ORIGEN_COMPROBANTE == self::K_ORIGEN_C_SUSPENCION or $COD_ORIGEN_COMPROBANTE == self::K_ORIGEN_C_CONDONACION 
			or $COD_ORIGEN_COMPROBANTE == self::K_ORIGEN_C_PRORROGA or $COD_ORIGEN_COMPROBANTE == self::K_ORIGEN_C_NOVACION){
			$rpt = new reporte_helen($sql, $this->root_dir.'appl/comprobante/comprobante1.xml', $labels, "Comprobante".$cod_comprobante.'.pdf',1);
		}
		else
		    $rpt = new reporte_helen($sql, $this->root_dir.'appl/comprobante/comprobante.xml', $labels, "Comprobante".$cod_comprobante.'.pdf',1);
	}
	
	function export_excel() {
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 1800);//30 minutos

		require_once dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/libs/simplexlsxgen/src/SimpleXLSXGen.php";

		$cod_comprobante = $this->get_key();

		$sql = $this->dws['dw_analitico_comprobante']->get_sql();
		$sql = $this->replace_KEYS($sql, array($cod_comprobante));

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$res = $db->query($sql);

		$rows = [['<b>RUT DEUDOR</b>', '<b>DIG VERIF</b>', '<b>N° DEUDA</b>', '<b>TIPO DEUDA</b>', '<b>CUENTA</b>', '<b>NOMBRE CUENTA</b>', '<b>MONTO DEBE</b>', '<b>MONTO HABER</b>']];
		while ($my_row = $db->get_row()) {
			$rows[] = [$my_row['RUT'], $my_row['DIG_VERIF'], $my_row['COD_DEUDA'], $my_row['NOM_TIPO_DEUDA'], $my_row['COD_CUENTA_CONTABLE_CC'], $my_row['NOM_CUENTA_CONTABLE_CC'], $my_row['MONTO_DEBE'], $my_row['MONTO_HABER']];
		}

		if ($db->database_type == "oci") {
			oci_free_statement($db->query_id);
			$db->query_id = false;
		}

		\Shuchkin\SimpleXLSXGen::fromArray($rows, 'ANALITICO_COMPROBANTE'.$cod_comprobante)->downloadAs('ANALITICO_COMPROBANTE_'.$cod_comprobante.'.xlsx');
	}
	
	function procesa_event() {		
		if(isset($_POST['b_export_x'])) {
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$cod_comprobante = $this->get_key();			
			$this->export_excel();
		}		
		else
			parent::procesa_event();

	}
	
	
	
}
/////////////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wi_comprobante.php";
if (file_exists($file_name)) 
	require_once($file_name);
else {
	class wi_comprobante extends wi_comprobante_base {
	    function __construct($cod_item_menu) {
	        parent::__construct($cod_item_menu); 
		}
	}
}
?>