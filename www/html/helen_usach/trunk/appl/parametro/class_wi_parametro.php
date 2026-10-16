<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");


class dw_lock_table extends datawindow {
	function __construct() 	{
				
		$sql = sql_parametro::get_sql('DW_LOCK_TABLE');	
		
		parent::__construct($sql, 'LOCK_TABLE', false, true);
	}
	function update($db)	{
		$sp = 'spd_lock_table';		
		for ($i = 0; $i < $this->row_count('delete'); $i++) {
			$statuts = $this->get_status_row($i, 'delete');			
			if ($statuts == K_ROW_NEW || $statuts == K_ROW_NEW_MODIFIED)
				continue;
			
			$dato = $this->get_item($i, 'ELIMINAR', 'delete');
			$largo = strlen($dato);
			$pos = strpos($dato, '/');
			$nom_modulo = substr($dato, 0, $pos);
			$nro_modulo = substr($dato, $pos + 1, $largo);
			$param = "'" . $nro_modulo . "', '" . $nom_modulo . "',".$this->get_item($i, 'COD_USUARIO', 'delete');
			if (!$db->EXECUTE_SP($sp, $param))			
				return false;				
		}
		return true;
	}
}

 
class wi_parametro extends w_input{
	const K_PARAM_MAX_PRORROGA 			= 1;
	const K_PARAM_CARTERA_DEFECT		= 2;
	const K_PARAM_UNIV_DEFECTO			= 3;
	const K_PARAM_RUT_U 				= 4;
	const K_PARAM_NOM_U 				= 5;
	const K_PARAM_NOM_COORDINADOR 		= 6;
	const K_PARAM_TEL_COORDINADOR 		= 7;
	const K_PARAM_MAX_PERMITIDO_PAGO	= 8;
	const K_PARAM_DIRECCION 			= 9; 
	const K_PARAM_FONO		 			= 10; 
	const K_PARAM_COMUNA	 			= 11;
	const K_PARAM_CIUDAD	 			= 12; 
	const K_PARAM_EMAIL		 			= 13; 
	const K_PARAM_SITIO_WEB	 			= 14;
	const K_PARAM_FAX		 			= 15;
	const K_PARAM_PAIS	 				= 16;
	const K_PARAM_PROM_PERMANENCIA 		= 17;
	const K_PARAM_ADMINISTRADOR	 		= 19;
	const K_PARAM_CTA_UTILIDAD			= 20;
	const K_PARAM_CTA_CORREC_MONET		= 21;
	const K_PARAM_TAZA_INTERES_CHEQUE	= 22;
	const K_PARAM_MAX_NUMERO_CHEQUE		= 23;
	const K_PARAM_MAX_VALIDEZ_SIMULACION = 24;
	const K_PARAM_NRO_CUENTA_UNIVERSIDAD = 25;
	const K_PARAM_PORC_SUGERIDO_RENEGOCIACION = 28;
	const K_PARAM_PORC_INTERES_CONVENIO = 30;
	const K_PARAM_PORC_SUGERIDO_CONVENIO = 31;
	const K_PARAM_ANO_VIG_CONT = 32;
	const K_PARAM_PASSWORD_MINEDUC = 33;
	const K_PARAM_FONO_CENTRAL = 34;
	
	function __construct() 	{	
		$this->tiene_wo = false;
		parent::__construct('parametro', $_REQUEST['cod_item_menu']);
						
		$sql = sql_parametro::get_sql('INPUT');

		$this->dws['dw_parametro'] = new datawindow($sql);
		$this->dws['dw_parametro']->add_control(new edit_num('MAXIMO_PRORROGA',10,10));
		$sql = sql_parametro::get_sql('DD_CARTERA');
		$this->dws['dw_parametro']->add_control(new drop_down_dw('CARTERA_DEFEC', $sql, 250));
		
		$sql = sql_parametro::get_sql('DD_UNIVERSIDAD');
		$this->dws['dw_parametro']->add_control(new drop_down_dw('UNIV_DEFECTO', $sql, 250));
		
		//$this->dws['dw_parametro']->add_control(new edit_num('UNIV_DEFECTO',3,4));	
		$this->dws['dw_parametro']->add_control(new edit_text('RUT_U',30,30));
		$this->dws['dw_parametro']->add_control(new edit_text_upper('NOM_U',80,100));
		$this->dws['dw_parametro']->add_control(new edit_text_upper('NOM_COORDINADOR',80,100));
		$this->dws['dw_parametro']->add_control(new edit_text_upper('TEL_COORDINADOR',30,30));
		$this->dws['dw_parametro']->add_control(new edit_num('MAX_PERMITIDO_PAGO',10,10));
		$this->dws['dw_parametro']->add_control(new edit_text_upper('DIRECCION',80,100));
		$this->dws['dw_parametro']->add_control(new edit_text_upper('FONO',30,30));
		$this->dws['dw_parametro']->add_control(new edit_text_upper('COMUNA',30,30));
		$this->dws['dw_parametro']->add_control(new edit_text_upper('CIUDAD',30,30));
		$this->dws['dw_parametro']->add_control(new edit_mail('EMAIL',80,100));
		$this->dws['dw_parametro']->add_control(new edit_text_lower('SITIO_WEB',80,100));
		$this->dws['dw_parametro']->add_control(new edit_text_upper('FAX',30,30));
		$this->dws['dw_parametro']->add_control(new edit_text_upper('PAIS',30,30));
		$this->dws['dw_parametro']->add_control(new edit_text_upper('ADMINISTRADOR',30,30));
		$this->dws['dw_parametro']->add_control(new edit_num('PROM_PERMANENCIA',3,4));
		$this->dws['dw_parametro']->add_control(new edit_num('TAZA_INTERES_CHEQUE',3,4,2));
		$this->dws['dw_parametro']->add_control(new edit_num('MAX_NUMERO_CHEQUE',3,4));
		$this->dws['dw_parametro']->add_control(new edit_num('MAX_VALIDEZ_SIMULACION',3,4));
		$this->dws['dw_parametro']->add_control(new edit_num('PORC_SUGERIDO_RENEGOCIACION',3,4,2));
		$this->dws['dw_parametro']->add_control(new edit_num('PORC_INTERES_CONVENIO',3,4,2));
		$this->dws['dw_parametro']->add_control(new edit_num('PORC_SUGERIDO_CONVENIO',3,4,2));
		$this->dws['dw_parametro']->add_control(new edit_text('PASSWORD_MINEDUC',30,30));
		$this->dws['dw_parametro']->add_control(new edit_text('FONO_CENTRAL',30,30));
		
		$sql = sql_parametro::get_sql('DD_CUENTA_CONTABLE');
		$this->dws['dw_parametro']->add_control(new drop_down_dw('COD_CUENTA_CONTABLE_U', $sql, 250));
		$this->dws['dw_parametro']->add_control(new drop_down_dw('COD_CUENTA_CONTABLE_CM', $sql, 250));
		
		$sql = sql_parametro::get_sql('DD_NRO_CUENTA_UNIVERSIDAD');
		$this->dws['dw_parametro']->add_control(new drop_down_dw('NRO_CUENTA_UNIVERSIDAD', $sql, 250));
		
		//mandatorys
		$this->dws['dw_parametro']->set_mandatory('RUT_U', 'Rut Universidad');
		$this->dws['dw_parametro']->set_mandatory('NOM_U', 'Nombre Universidad');
		$this->dws['dw_parametro']->set_mandatory('DIRECCION', 'Dirección');
		$this->dws['dw_parametro']->set_mandatory('COMUNA', 'Comuna');
		$this->dws['dw_parametro']->set_mandatory('CIUDAD', 'Ciudad');
		$this->dws['dw_parametro']->set_mandatory('PAIS', 'País');
		$this->dws['dw_parametro']->set_mandatory('FONO', 'Fono');
		$this->dws['dw_parametro']->set_mandatory('FAX', 'Fax');
		$this->dws['dw_parametro']->set_mandatory('EMAIL', 'E-Mail');
		$this->dws['dw_parametro']->set_mandatory('SITIO_WEB', 'Dirección Internet');
		$this->dws['dw_parametro']->set_mandatory('ADMINISTRADOR', 'Administrador');
		
		$this->dws['dw_parametro']->set_mandatory('NOM_COORDINADOR', 'Nombre Cordinador');
		$this->dws['dw_parametro']->set_mandatory('TEL_COORDINADOR', 'Teléfono Cordinador');
		
		$this->dws['dw_parametro']->set_mandatory('MAXIMO_PRORROGA', 'Máximo Prórroga');
		$this->dws['dw_parametro']->set_mandatory('CARTERA_DEFEC', 'Cartera por Defecto');
		$this->dws['dw_parametro']->set_mandatory('PROM_PERMANENCIA', 'Promedio Permanencia');
		$this->dws['dw_parametro']->set_mandatory('UNIV_DEFECTO', 'Universidad por defecto');
		$this->dws['dw_parametro']->set_mandatory('MAX_PERMITIDO_PAGO', 'Dif. Máx. Permitida en un Pago');
		$this->dws['dw_parametro']->set_mandatory('COD_CUENTA_CONTABLE_U', 'Cuenta Contable de Utilidad para los cierres contables');
		$this->dws['dw_parametro']->set_mandatory('COD_CUENTA_CONTABLE_CM', 'Cuenta Contable de Corrección Monetaria');
		$this->dws['dw_parametro']->set_mandatory('NRO_CUENTA_UNIVERSIDAD', 'Una Cuenta Corriente de la Universidad');

		$this->dws['dw_lock_table'] = new dw_lock_table();
		$this->save_SESSION();
		$this->need_redraw();
		header("Location: wi_parametro.php");
	}
	
	function new_record() {				
		$this->dws['dw_parametro']->insert_row();
	}	
	
	function load_record() 	{
		$this->current_record = 0;
		$this->dws['dw_parametro']->retrieve();
		$this->dws['dw_lock_table']->retrieve();
		$this->dws['dw_lock_table']->retrieve();
		
		/*
		 * en la UACH, si el usuario es Leandro, puede consultar comprobantes descuadrados 16/04/2012  IS 
		 */
		if (K_CLIENTE=="UACH" && $this->cod_usuario == 18){
			$this->dws['dw_parametro']->set_item(0, 'VISIBLE_TR', '');
		}
		else{
			$this->dws['dw_parametro']->set_item(0, 'VISIBLE_TR', 'none');
		}
	}
	
	function habilitar(&$temp, $habilita) { 
		$this->habilita_boton($temp, 'modify', (!$habilita));	// privilegio de escritura
	} 
	
	function get_key() 	{
		return 0;
	}
	
	function save_record($db) {
		$sp = 'spu_parametro';
		
		$MAXIMO_PRORROGA	= $this->dws['dw_parametro']->get_item(0, 'MAXIMO_PRORROGA');
		$param = self::K_PARAM_MAX_PRORROGA.",'$MAXIMO_PRORROGA'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;
			
		$CARTERA_DEFEC		= $this->dws['dw_parametro']->get_item(0, 'CARTERA_DEFEC');
		$param = self::K_PARAM_CARTERA_DEFECT.",'$CARTERA_DEFEC'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;	
			
		$UNIV_DEFECTO		= $this->dws['dw_parametro']->get_item(0, 'UNIV_DEFECTO');
		$param = self::K_PARAM_UNIV_DEFECTO.",'$UNIV_DEFECTO'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;
			
		$RUT_U				= $this->dws['dw_parametro']->get_item(0, 'RUT_U');
		$param = self::K_PARAM_RUT_U.",'$RUT_U'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;
		
		$NOM_U				= $this->dws['dw_parametro']->get_item(0, 'NOM_U');
		$param = self::K_PARAM_NOM_U.",'$NOM_U'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;
			
		$NOM_COORDINADOR	= $this->dws['dw_parametro']->get_item(0, 'NOM_COORDINADOR');
		$param = self::K_PARAM_NOM_COORDINADOR.",'$NOM_COORDINADOR'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;
		
		$TEL_COORDINADOR	= $this->dws['dw_parametro']->get_item(0, 'TEL_COORDINADOR');
		$param = self::K_PARAM_TEL_COORDINADOR.",'$TEL_COORDINADOR'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;
			
		$MAX_PERMITIDO_PAGO	= $this->dws['dw_parametro']->get_item(0, 'MAX_PERMITIDO_PAGO');		
		$param = self::K_PARAM_MAX_PERMITIDO_PAGO.",'$MAX_PERMITIDO_PAGO'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;
			
		$TAZA_INTERES_CHEQUE	= $this->dws['dw_parametro']->get_item(0, 'TAZA_INTERES_CHEQUE');		
		$param = self::K_PARAM_TAZA_INTERES_CHEQUE.",'$TAZA_INTERES_CHEQUE'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;
			
		$MAX_NUMERO_CHEQUE	= $this->dws['dw_parametro']->get_item(0, 'MAX_NUMERO_CHEQUE');		
		$param = self::K_PARAM_MAX_NUMERO_CHEQUE.",'$MAX_NUMERO_CHEQUE'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;

		$MAX_VALIDEZ_SIMULACION	= $this->dws['dw_parametro']->get_item(0, 'MAX_VALIDEZ_SIMULACION');		
		$param = self::K_PARAM_MAX_VALIDEZ_SIMULACION.",'$MAX_VALIDEZ_SIMULACION'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;			
					
		$DIRECCION			= $this->dws['dw_parametro']->get_item(0, 'DIRECCION');
		$param = self::K_PARAM_DIRECCION.",'$DIRECCION'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;
		
		$FONO				= $this->dws['dw_parametro']->get_item(0, 'FONO');
		$param = self::K_PARAM_FONO.",'$FONO'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;
			
		$COMUNA				= $this->dws['dw_parametro']->get_item(0, 'COMUNA');
		$param = self::K_PARAM_COMUNA.",'$COMUNA'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;
			
		$CIUDAD				= $this->dws['dw_parametro']->get_item(0, 'CIUDAD');
		$param = self::K_PARAM_CIUDAD.",'$CIUDAD'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;

		$EMAIL				= $this->dws['dw_parametro']->get_item(0, 'EMAIL');
		$param = self::K_PARAM_EMAIL.",'$EMAIL'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;
			
		$SITIO_WEB			= $this->dws['dw_parametro']->get_item(0, 'SITIO_WEB');
		$param = self::K_PARAM_SITIO_WEB.",'$SITIO_WEB'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;
			
		$ADMINISTRADOR			= $this->dws['dw_parametro']->get_item(0, 'ADMINISTRADOR');
		$param = self::K_PARAM_ADMINISTRADOR.",'$ADMINISTRADOR'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;
		
		$FAX				= $this->dws['dw_parametro']->get_item(0, 'FAX');
		$param = self::K_PARAM_FAX.",'$FAX'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;
		
		$PAIS				= $this->dws['dw_parametro']->get_item(0, 'PAIS');
		$param = self::K_PARAM_PAIS.",'$PAIS'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;
		
		$PROM_PERMANENCIA	= $this->dws['dw_parametro']->get_item(0, 'PROM_PERMANENCIA');
		$param = self::K_PARAM_PROM_PERMANENCIA.",'$PROM_PERMANENCIA'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;

		$COD_CUENTA_CONTABLE_U	= $this->dws['dw_parametro']->get_item(0, 'COD_CUENTA_CONTABLE_U');
		$param = self::K_PARAM_CTA_UTILIDAD.",'$COD_CUENTA_CONTABLE_U'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;
		
		$COD_CUENTA_CONTABLE_CM	= $this->dws['dw_parametro']->get_item(0, 'COD_CUENTA_CONTABLE_CM');
		$param = self::K_PARAM_CTA_CORREC_MONET.",'$COD_CUENTA_CONTABLE_CM'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;

		$NRO_CUENTA_UNIVERSIDAD	= $this->dws['dw_parametro']->get_item(0, 'NRO_CUENTA_UNIVERSIDAD');
		$param = self::K_PARAM_NRO_CUENTA_UNIVERSIDAD.",'$NRO_CUENTA_UNIVERSIDAD'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;
		
		$PORC_SUGERIDO_RENEGOCIACION = $this->dws['dw_parametro']->get_item(0, 'PORC_SUGERIDO_RENEGOCIACION');
		$param = self::K_PARAM_PORC_SUGERIDO_RENEGOCIACION.",'$PORC_SUGERIDO_RENEGOCIACION'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;	
		
		$PORC_INTERES_CONVENIO = $this->dws['dw_parametro']->get_item(0, 'PORC_INTERES_CONVENIO');
		$param = self::K_PARAM_PORC_INTERES_CONVENIO.",'$PORC_INTERES_CONVENIO'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;
		
		$PORC_SUGERIDO_CONVENIO = $this->dws['dw_parametro']->get_item(0, 'PORC_SUGERIDO_CONVENIO');
		$param = self::K_PARAM_PORC_SUGERIDO_CONVENIO.",'$PORC_SUGERIDO_CONVENIO'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;
		
		$PASSWORD_MINEDUC = $this->dws['dw_parametro']->get_item(0, 'PASSWORD_MINEDUC');
		$param = self::K_PARAM_PASSWORD_MINEDUC.",'$PASSWORD_MINEDUC'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;
			
		$FONO_CENTRAL = $this->dws['dw_parametro']->get_item(0, 'FONO_CENTRAL');
		$param = self::K_PARAM_FONO_CENTRAL.",'$FONO_CENTRAL'";
		if (!$db->EXECUTE_SP($sp,$param))
			return false;
			
		return $this->dws['dw_lock_table']->update($db);	
		
	}

	// Se reimplementa para que no se ejecute codigo respecto a los navegadores
	function navegacion(&$temp){
		$temp->setVar("WI_RUTA_MENU", $this->ruta_menu);
		$temp->setVar("WI_FECHA_ACTUAL", 'Fecha Actual: ' . $this->current_date());
		$key = $this->limpia_key($this->get_key());
		$temp->setVar("WI_FECHA_MODIF", '');		
		$this->habilita_boton($temp, 'back', true);
	}
	
	function goto_list(){
		$this->unlock_record();
		header('Location:' . $this->root_url . '../../commonlib_seven/trunk/php/presentacion.php');		
	}
	
	function procesa_event(){		
		if (session::is_set('REDRAW_' . $this->nom_tabla)) {
			session::un_set('REDRAW_' . $this->nom_tabla);
			$this->load_record();
			$this->redraw();
		} 
		else
			parent::procesa_event();
	}

}
?>