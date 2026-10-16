<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/".K_CLIENTE."/class_print_suspension.php");
/*
 Clase : WI_SUSPENSION
 */
class wi_suspension_base extends w_input {
	const K_ESTADO_SUSPENSION_SOLICITADO	= 1;
	const K_ESTADO_SUSPENSION_APROBADO		= 2;
	const K_ESTADO_SUSPENSION_RECHAZADO		= 3;
	
	function __construct($cod_item_menu)	{
	    parent::__construct('suspension', $cod_item_menu);
		$this->b_delete_visible  = false;
		
		//////////////// CLASS_SQL = INPUT ///////////////	
		$sql = sql_suspension::get_sql('INPUT');

		$this->dws['dw_suspension'] = new datawindow($sql);
		$this->dws['dw_suspension']->add_control($control = new edit_rut('RUT', 10, 10));
		$control->set_onChange("f_get_info_deudor();");

		//tipos de deuda asociados al deudor de la suspensión que tengan cuota del año (usar año de la suspensión) y CS o CSR
		$this->dws['dw_suspension']->add_control(new static_text('DIG_VERIF'));
		$this->dws['dw_suspension']->add_control(new static_text('NOMBRE'));
		$this->dws['dw_suspension']->add_control(new static_text('COD_TIPO_DEUDA'));
		$this->dws['dw_suspension']->add_control(new edit_text('COD_TIPO_DEUDA_H', 10, 10, 'hidden'));
		$this->dws['dw_suspension']->add_control(new static_link('COD_DEUDA', '../../../../commonlib_seven/trunk/php/link_wi.php?modulo_origen=suspension&modulo_destino=deuda&cod_modulo_destino=[COD_DEUDA]&cod_item_menu=1010'));
		
		
		$this->dws['dw_suspension']->add_control(new edit_text('COD_DEUDA_H', 10, 10, 'hidden'));
		
		//$this->dws['dw_suspension']->add_control(new static_text('ANO_SUSPENSION',0));
		$this->dws['dw_suspension']->add_control($control = new edit_num('ANO_SUSPENSION', 4, 4, 0, true, false, false));
		$fecha = getdate();
		$ano = $fecha["year"];
		$control->set_onChange("f_valida_ano(this,".$ano.");");
		$this->dws['dw_suspension']->add_control(new edit_text('ANO_SUSPENSION_H', 10, 10, 'hidden'));

		$this->dws['dw_suspension']->add_control(new edit_text_multiline('OBS', 80, 8));
		$this->dws['dw_suspension']->add_control(new edit_text('EXISTE', 2,2, 'hidden'));
		$this->dws['dw_suspension']->add_control(new edit_text('COD_DEUDOR', 2,2, 'hidden'));

		//DROPS
		//////////////// CLASS_SQL = INPUT_SUSPENSION ///////////////
		$sql_ts = sql_suspension::get_sql('INPUT_SUSPENSION');
		
		$this->dws['dw_suspension']->add_control($control = new drop_down_dw('COD_TIPO_SUSPENSION', $sql_ts, 140));
		$control->set_onChange("f_get_anos_suspendido(this); max_ano_suspension();");
		$this->dws['dw_suspension']->add_control(new edit_text_hidden('MAX_SUSP_X_TIPO'));
		
		$this->dws['dw_suspension']->add_control(new static_text('CANT_SUSPENSIONES'));
		
		//los que pueden modificarse
		//////////////// CLASS_SQL = INPUT_ESTADO_SUSPENSION ///////////////
		$sql_ec = sql_suspension::get_sql('INPUT_ESTADO_SUSPENSION');
		
		$this->dws['dw_suspension']->add_control(new drop_down_dw('COD_ESTADO_SUSPENSION', $sql_ec, 140));
		$this->dws['dw_suspension']->add_control(new static_text('FECHA_CONFIRMA_NO_ING'));
		$this->dws['dw_suspension']->add_control(new edit_text('COD_USUARIO_CONFIRMA',10,10, 'hidden'));
		$this->dws['dw_suspension']->add_control(new static_text('NOM_USUARIO_CONFIRMA'));
		$this->dws['dw_suspension']->add_control(new edit_date('FECHA_SOLICITUD_TRAMITE', 10,10));
		
		//mandatory
		$this->dws['dw_suspension']->set_mandatory('RUT', 'Rut');
		$this->dws['dw_suspension']->set_mandatory('ANO_SUSPENSION', 'año Suspensión');
		$this->dws['dw_suspension']->set_mandatory('COD_TIPO_SUSPENSION', 'Tipo Suspensión');
		$this->dws['dw_suspension']->set_mandatory('COD_ESTADO_SUSPENSION', 'Estado Suspensión');
		$this->dws['dw_suspension']->set_mandatory('OBS', 'Observación Suspensión');
	}
	function new_record() {
		$this->b_print_visible = FALSE;
		$this->dws['dw_suspension']->insert_row();
		$this->dws['dw_suspension']->set_item(0, 'FECHA_SUSPENSION', $this->current_date());
		$this->dws['dw_suspension']->set_item(0, 'COD_USUARIO', $this->cod_usuario);
		$this->dws['dw_suspension']->set_item(0, 'NOM_USUARIO', $this->nom_usuario);
		$this->dws['dw_suspension']->set_item(0, 'COD_ESTADO_SUSPENSION', self::K_ESTADO_SUSPENSION_SOLICITADO);
		$this->dws['dw_suspension']->set_entrable('COD_ESTADO_SUSPENSION', false);
		$this->dws['dw_suspension']->set_item(0, 'FECHA_SOLICITUD_TRAMITE', $this->current_date());
		
		$fecha = getdate();
		$ano = $fecha["year"];
		$this->dws['dw_suspension']->set_item(0, 'ANO_SUSPENSION', $ano);
		$this->dws['dw_suspension']->set_item(0, 'ANO_SUSPENSION_H', $ano);
		
	}
	function load_record() {
		$cod_suspension = $this->get_item_wo($this->current_record, 'COD_SUSPENSION');
		$this->dws['dw_suspension']->retrieve($cod_suspension);
		$cod_estado_suspension = $this->dws['dw_suspension']->get_item(0, 'COD_ESTADO_SUSPENSION');
		$nombre_usuario = $this->nom_usuario;
		$this->dws['dw_suspension']->set_entrable('RUT', false);
		$rut	= $this->dws['dw_suspension']->get_item(0, 'RUT');
		$K_PARAM_CARTERA = 2;
				
		if ($cod_estado_suspension == self::K_ESTADO_SUSPENSION_SOLICITADO){
			$this->dws['dw_suspension']->set_entrable('COD_ESTADO_SUSPENSION', true);
			$fecha_confirma = $this->current_date();
			
			//////////////// CLASS_SQL = INPUT_DD_SOLICITADO ///////////////
			$sql = sql_suspension::get_sql('INPUT_DD_SOLICITADO');

			unset($this->dws['dw_suspension']->controls['COD_ESTADO_SUSPENSION']);
			$this->dws['dw_suspension']->add_control($control = new drop_down_dw('COD_ESTADO_SUSPENSION',$sql,125));
			$control->set_onChange("f_usuario_confirma('$nombre_usuario','$fecha_confirma');");
			
			$this->b_modify_visible	 	= true;
			$this->b_save_visible	 	= true;
			$this->b_no_save_visible	= true;
			$this->b_print_visible		= false;
			
			/*select que permite ver la cantidad de suspensiones que se pueden hacer por tipo*/
			$cod_tipo_suspension = $this->dws['dw_suspension']->get_item(0, 'COD_TIPO_SUSPENSION');
			
			$db				= new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$sql			= sql_suspension::get_sql('AJAX_MAX_SUSP_X_TIPO', $cod_tipo_suspension);
			$result 		= $db->build_results($sql);
			$periodo_maximo = $result[0]['PERIODO_MAXIMO'];
			
			$this->dws['dw_suspension']->set_item(0, 'MAX_SUSP_X_TIPO', $periodo_maximo); 
			
		}
		else if ($cod_estado_suspension==self::K_ESTADO_SUSPENSION_APROBADO){
			$this->b_print_visible		= true;
			//////////////// CLASS_SQL = INPUT_DD_APROBADO ///////////////
			$sql_1 = sql_suspension::get_sql('INPUT_DD_APROBADO');
			
			$this->dws['dw_suspension']->add_control($control = new drop_down_dw('COD_ESTADO_SUSPENSION', $sql_1, 90));

			$this->b_modify_visible	 	= false;
			$this->b_save_visible	 	= false;
			$this->b_no_save_visible	= false;
			
		}
		else if ($cod_estado_suspension==self::K_ESTADO_SUSPENSION_RECHAZADO){
			$this->b_modify_visible	 	= false;
			$this->b_save_visible	 	= false;
			$this->b_no_save_visible	= false;
			$this->b_print_visible		= false;
				
		}
		
		
	}
	function get_key() {
		return $this->dws['dw_suspension']->get_item(0, 'COD_SUSPENSION');
	}
	function validate_record() {
		$db 					= new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$cod_estado_suspension	= $this->dws['dw_suspension']->get_item(0, 'COD_ESTADO_SUSPENSION');
		$cod_tipo_deuda			= $this->dws['dw_suspension']->get_item(0, 'COD_TIPO_DEUDA');

		$ano_suspension	= $this->dws['dw_suspension']->get_item(0, 'ANO_SUSPENSION');
		if (strlen($ano_suspension)<4 or strlen($ano_suspension)>4 or $ano_suspension<1900){
			return 'año erróneo, escriba un año válido';
		}
		else{
			return '';
		}
		//////////////// CLASS_SQL = INPUT_ESTADO ///////////////
		//estados suspension
		$sql_estado_solicitado= sql_suspension::get_sql('INPUT_ESTADO');
		
		$result1 = $db->build_results($sql_estado_solicitado);
		$cod_estado_susp_solic = $result1[0]['COD_ESTADO_SUSPENSION'];

		//////////////// CLASS_SQL = INPUT_ESTADO_APROBADO ///////////////
		$sql_estado_aprobado= sql_suspension::get_sql('INPUT_ESTADO_APROBADO');
		
		$result2 = $db->build_results($sql_estado_aprobado);
		$cod_estado_susp_aprob = $result2[0]['COD_ESTADO_SUSPENSION'];

	//solo puede suspenderse dentro del año que se aprobó
		if (($cod_estado_suspension_antiguo=$cod_estado_susp_solic)
		and ($cod_estado_suspension=$cod_estado_susp_aprob)
		and ($cod_tipo_deuda = 'CS'))
		{
			$ano_suspension	= $this->dws['dw_suspension']->get_item(0, 'ANO_SUSPENSION');

			//////////////// CLASS_SQL = INPUT_BUSCA_PAGO ///////////////
			//BUSCO COD_PLAN_PAGO
			$sql =	sql_suspension::get_sql('INPUT_BUSCA_PAGO', $cod_deuda, $ano_suspension);	

			$result = $db->build_results($sql);
			$COD_PLAN_PAGO = $result[0]['COD_PLAN_PAGO'];

			if ($cod_plan_pago != '') {
				//BUSCAR COD_PLAN_PAGO EN ABONO, SI EXISTEN MANDAR MENSAJE Y NO GRABAR
				$sql_abono =sql_suspension::get_sql('ABONO',$COD_PLAN_PAGO);
				$result = $db->build_results($sql_abono);
				$cuenta_abono = $result[0]['CUENTA_ABONO'];
				if ($cuenta_abono>0){
					return 'No se puede suspender esta cuota, tiene abonos comprometidos';
				}
			}

			//////////////// CLASS_SQL = INPUT_BUSCO_DESCUENTO ///////////////
			//BUSCAR COD_PLAN_PAGO EN DESCUENTO, SI EXISTEN MANDAR MENSAJE Y NO GRABAR
			$sql_abono	=	sql_suspension::get_sql('INPUT_BUSCO_DESCUENTO', $cod_plan_pago);
							
							
			$result 			= $db->build_results($sql_abono);
			$cuenta_descuento 	= $result[0]['CUENTA_DESCUENTO'];

			if ($cuenta_descuento>0){
				return 'No se puede suspender esta cuota, tiene Descuentos comprometidos';
			}
			else{
				return '';
			}
		}
	}
	function save_record($db){
		$cod_suspension			= $this->dws['dw_suspension']->get_item(0, 'COD_SUSPENSION');
		$cod_usuario			= $this->dws['dw_suspension']->get_item(0, 'COD_USUARIO');
		$cod_deuda				= $this->dws['dw_suspension']->get_item(0, 'COD_DEUDA_H');
		$ano_suspension			= $this->dws['dw_suspension']->get_item(0, 'ANO_SUSPENSION');
		$cod_tipo_suspension	= $this->dws['dw_suspension']->get_item(0, 'COD_TIPO_SUSPENSION');

		if ($cod_tipo_suspension == ''){
			$cod_tipo_suspension = $this->dws['dw_suspension']->get_item(0, 'TIPO_SUSPENSION');
		}
		$obs					= $this->dws['dw_suspension']->get_item(0, 'OBS');
		$cod_comprobante		= $this->dws['dw_suspension']->get_item(0, 'COD_COMPROBANTE');
		$fecha_suspension		= $this->dws['dw_suspension']->get_item(0, 'FECHA_SUSPENSION');
		$cod_estado_suspension	= $this->dws['dw_suspension']->get_item(0, 'COD_ESTADO_SUSPENSION');
		$cod_usuario_confirma   = $this->cod_usuario;
		$fecha_confirma         = $this->dws['dw_suspension']->get_item(0, 'FECHA_CONFIRMA');
		$cod_tipo_deuda			= $this->dws['dw_suspension']->get_item(0, 'COD_TIPO_DEUDA_H');
		$fecha_solicitud_tramite = $this->dws['dw_suspension']->get_item(0, 'FECHA_SOLICITUD_TRAMITE');
		
		if(K_TIPO_BD == "oci")
			$fecha_suspension = "'$fecha_suspension'";
		else if(K_TIPO_BD == "mssql")
			$fecha_suspension = $this->str2date($fecha_suspension);	

		$sp = 'spu_suspension';
		if ($this->is_new_record()){
			$operacion = 'INSERT';
		}
		else {
			$operacion = 'UPDATE';
		}

		$cod_suspension					= ($cod_suspension=='') ? "null" : $cod_suspension;
		$cod_comprobante				= ($cod_comprobante=='') ? "null" : $cod_comprobante;
		$cod_usuario					= ($cod_usuario=='') ? "null" : $cod_usuario;
		$cod_deuda						= ($cod_deuda=='') ? "null" : $cod_deuda;
		$ano_suspension					= ($ano_suspension=='') ? "null" : $ano_suspension;
		$cod_usuario_confirma			= ($cod_usuario_confirma=='') ? "null" : $cod_usuario_confirma;
		$obs							= ($obs=='') ? "null" : "'$obs'";
		$fecha_solicitud_tramite		= ($fecha_solicitud_tramite=='') ? "null" : "'$fecha_solicitud_tramite'";
		
		$param	= "'$operacion',
		$cod_suspension,
		$fecha_suspension,
		$cod_usuario,
		$cod_deuda,
		$ano_suspension,
		$cod_tipo_suspension,
		$obs,
		$cod_comprobante,
		$cod_estado_suspension,
		$cod_usuario_confirma,
		'$cod_tipo_deuda',
		$fecha_solicitud_tramite";

		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$cod_suspension = $db->GET_IDENTITY("suspension");
				$this->dws['dw_suspension']->set_item(0, 'COD_SUSPENSION', $cod_suspension);
			}
			return true;
		}
		return false;
	}

	function print_record() {
		$K_NOM_ADMIN=19;
		$cod_suspension = $this->get_key();
	 	$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql= sql_suspension::get_sql('PRINT_RECORD',$cod_suspension,$K_NOM_ADMIN);
		// reporte
		$labels = array();
		$file_name = $this->find_file('suspension', 'suspension.xml');					
		$rpt = new print_suspension($sql, $file_name, $labels, "suspension".$cod_suspension.'.pdf', 1);										
		$this->_load_record();
	}
	function create_ingreso() {
		$cod_condonacion = $this->dws['dw_suspension']->get_item(0, 'COD_suspension');
		session::set("wi_suspension.create_ingreso", $cod_suspension);
		$url = $this->root_url.'../../commonlib_seven/trunk/php/link_wi.php?modulo_origen=suspension&modulo_destino=suspension&cod_modulo_destino='.K_NEW_RECORD.'&cod_item_menu=1045';
		header ('Location: '.$url);
	}
	function procesa_event() {
		if(isset($_POST['b_create_ingreso_x']))  {
			$this->create_ingreso();
		}
		else
			parent::procesa_event();
	}
}
// Se separa por UNIVERSIDAD

$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wi_suspension.php";
if (file_exists($file_name)) 
	require_once($file_name);
else {
	class wi_suspension extends wi_suspension_base {
	    function __construct($cod_item_menu) {
	        parent::__construct($cod_item_menu); 
		}
	}
}
?>