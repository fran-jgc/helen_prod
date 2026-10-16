<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../deudor/class_direccion_deudor.php");
require_once(dirname(__FILE__)."/class_dw_declaracion.php");
require_once(dirname(__FILE__)."/class_dw_declaracion_mes.php");
require_once(dirname(__FILE__)."/class_print_declaracion.php");

class wi_declaracion_base extends w_input {
	const K_EST_DECL_INGRESADA			= 1;
	const K_EST_DECL_APROBADA			= 2;
	const K_EST_DECL_ANULADA			= 3;
	const K_EST_DECL_OBJETADA			= 4;
	const K_AUTORIZA_MODIFICA_DECL_APROBADA = '991005';
	const K_CARTERA_UACH				= 7;
	
	function __construct($cod_item_menu) {
		// me aseguro que no este en la session, para evitar alerta. 
		session::un_set("RE_CALCULO_CUOTA");
		
		parent::__construct('declaracion', $cod_item_menu);
		$this->b_delete_visible	= false;

		$this->dws['dw_direccion_deudor_comercial']		= new dw_direccion_deudor('C','declaracion');
		$this->dws['dw_direccion_deudor_comercial']->set_visible('ES_VALIDA_COMERCIAL', false);
		$this->dws['dw_direccion_deudor_particular']	= new dw_direccion_deudor('P','declaracion');
		$this->dws['dw_direccion_deudor_particular']->set_visible('ES_VALIDA_PARTICULAR', false);
		
		$this->dws['dw_declaracion'] = new dw_declaracion();
		$this->dws['dw_declaracion_mes'] = new dw_declaracion_mes();

		$this->set_first_focus('RUT');
	}
	
	function new_record() {		
		$this->dws['dw_direccion_deudor_comercial']->insert_row();
		$this->dws['dw_direccion_deudor_particular']->insert_row();
		
		$this->dws['dw_declaracion']->new_declaracion();
		$this->dws['dw_declaracion_mes']->new_declaracion();
		$this->b_save_visible = true;
	}
	function load_record() {		
		$cod_declaracion		= $this->get_item_wo($this->current_record, 'COD_DECLARACION');
		$this->dws['dw_declaracion']->retrieve($cod_declaracion);
		$cod_estado_declaracion = $this->dws['dw_declaracion']->get_item(0, 'COD_ESTADO_DECLARACION');
		$cod_deudor		= $this->dws['dw_declaracion']->get_item(0, 'COD_DEUDOR');
		$nombre_usuario = $this->nom_usuario;
		$this->dws['dw_direccion_deudor_particular']->retrieve($cod_deudor);

		//habilita el boton para calcular el CS, CSR
		$this->dws['dw_declaracion']->set_item(0, 'DISABLE_BUTTON_CREA_CUOTA', 'disabled="disabled"');
		$this->dws['dw_declaracion']->set_item(0, 'VISIBLE_BOTON', 'none');
		$this->dws['dw_declaracion']->set_item(0, 'VISIBLE_TD', '');
		
		if ($this->dws['dw_direccion_deudor_particular']->row_count() == 0){
			$this->dws['dw_direccion_deudor_particular']->insert_row();
		}
		$this->dws['dw_direccion_deudor_comercial']->retrieve($cod_deudor);
		if ($this->dws['dw_direccion_deudor_comercial']->row_count() == 0){
			$this->dws['dw_direccion_deudor_comercial']->insert_row();
		}
		if ($cod_estado_declaracion == self::K_EST_DECL_INGRESADA){
			$fecha_confirma = $this->current_date();
			$sql = sql_declaracion::get_sql ('DD_COD_ESTADO_DECLARACION_1');

			unset($this->dws['dw_declaracion']->controls['COD_ESTADO_DECLARACION']);
			$this->dws['dw_declaracion']->add_control($control = new drop_down_dw('COD_ESTADO_DECLARACION',$sql,125));
			$control->set_onChange("f_usuario_confirma('$nombre_usuario','$fecha_confirma');");
			
			$this->b_modify_visible	 	= true;
			$this->b_save_visible	 	= true;
			$this->b_no_save_visible	= true;
			$this->b_print_visible		= true;
		}
		else if ($cod_estado_declaracion==self::K_EST_DECL_APROBADA){
			// Si esta aprobada no peude modifcar rut o año
			$this->dws['dw_declaracion']->set_entrable('RUT', false);
			$this->dws['dw_declaracion']->set_entrable('ANO_DECLARACION', false);
				
			$cod_declaracion = $this->get_key();

			//mostrar valor cuota
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$sql = sql_declaracion::get_sql ('VALOR_CUOTA',$cod_declaracion);
			$result = $db->build_results($sql);
			$count = count($result);
			
			if($count != 0){
				$valor_cuota = $result[0]['VALOR_CUOTA'];
				$this->dws['dw_declaracion']->set_item(0, 'VALOR_CUOTA', $valor_cuota);
				$this->dws['dw_declaracion']->set_item(0, 'TD_LABEL_CUOTA', 'none');
				$this->dws['dw_declaracion']->set_item(0, 'TD_VALOR_CUOTA', '');
			}

			$priv = $this->get_privilegio_opcion_usuario(self::K_AUTORIZA_MODIFICA_DECL_APROBADA, $this->cod_usuario);
			if ($priv=='E') {
				$fecha_confirma = $this->current_date();
				$sql_1 = sql_declaracion::get_sql ('DD_COD_ESTADO_DECLARACION_2');
				$this->dws['dw_declaracion']->add_control(new drop_down_dw('COD_ESTADO_DECLARACION', $sql_1, 90));
				
				if (!$this->modify)
					$this->dws['dw_declaracion']->set_item(0, 'DISABLE_BUTTON_CREA_CUOTA', '');

				$cuota_calculada = $this->cuota_calculada($cod_declaracion);
				if ($cuota_calculada=='N') {
					$this->dws['dw_declaracion']->set_item(0, 'VISIBLE_BOTON', '');
					$this->dws['dw_declaracion']->set_item(0, 'VISIBLE_TD', 'none');
				}
				$this->b_modify_visible	 	= true;
				$this->b_save_visible	 	= true;
				$this->b_no_save_visible	= true;
				$this->b_print_visible		= true;
			}
			else {
				$sql_1 = sql_declaracion::get_sql ('DD_COD_ESTADO_DECLARACION_2');
				$this->dws['dw_declaracion']->add_control(new drop_down_dw('COD_ESTADO_DECLARACION', $sql_1, 90));
	
				$this->dws['dw_declaracion']->set_entrable('ENTREGA_F22', false);
				$this->dws['dw_declaracion']->set_entrable('COD_AFP', false);
				$this->dws['dw_declaracion']->set_entrable('CON_HIJO', false);
				$this->dws['dw_declaracion']->set_entrable('COD_ESTADO_CIVIL', false);
				$this->dws['dw_declaracion']->set_entrable('RUT_CONYUGE', false);
				$this->dws['dw_declaracion']->set_entrable('NOM_CONYUGE', false);
				$this->dws['dw_declaracion']->set_entrable('CONYUGE_ES_DEUDOR', false);
				$this->dws['dw_declaracion']->set_entrable('CONYUGE_DECLARA', false);
				
				$this->dws['dw_direccion_deudor_particular']->set_entrable_dw(false);
				$this->dws['dw_direccion_deudor_comercial']->set_entrable_dw(false);
				$this->b_modify_visible	 	= true;
				$this->b_save_visible	 	= false;
				$this->b_no_save_visible	= false;
				$this->b_print_visible		= true;
				//$this->dws['dw_declaracion_mes']->set_entrable_dw(false);
			}
		}
		else if($cod_estado_declaracion==self::K_EST_DECL_OBJETADA or $cod_estado_declaracion==self::K_EST_DECL_ANULADA){
			$this->b_modify_visible	 	= false;
			$this->b_save_visible	 	= false;
			$this->b_no_save_visible	= false;
			$this->b_print_visible		= true;
						
		}
		$this->dws['dw_declaracion_mes']->retrieve($cod_declaracion);
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$COD_DECLARACION = $this->dws['dw_declaracion']->get_item(0, 'COD_DECLARACION');
		
		if (K_TIPO_BD=="oci"){
			$sql_es_cruzado =	"SELECT 	count(*) CANT	 
								FROM  	DECLARACION DE, DEUDA D, CRUZADO C
								WHERE   DE.COD_DEUDOR = D.COD_DEUDOR 
								AND     D.COD_DEUDA = C.COD_DEUDA 
						  		and 	D.COD_TIPO_DEUDA in ('CS', 'CSR', 'CR12')
								AND     DE.COD_DECLARACION = $COD_DECLARACION
								AND		D.COD_ESTADO_DEUDA = 2";	// exigible	
		}else if (K_TIPO_BD=="mssql"){
			$sql_es_cruzado	=	"SELECT 	count(*) CANT	 
								FROM  	DECLARACION DE, DEUDA D, CRUZADO C
								WHERE   DE.COD_DEUDOR = D.COD_DEUDOR 
								AND     D.COD_DEUDA = C.COD_DEUDA 
						  		and 	D.COD_TIPO_DEUDA in ('CS', 'CSR', 'CR12')
								AND     DE.COD_DECLARACION = $COD_DECLARACION
								AND		D.COD_ESTADO_DEUDA = 2";	// exigible	
		}
		
		$result	= $db->build_results($sql_es_cruzado);
		$count 	= $result[0]['CANT'];
		
		if ($count == 0)
			$this->dws['dw_declaracion']->set_item(0, 'VISIBLE_TABLE', 'none');
		else if	($count != 0)
			$this->dws['dw_declaracion']->set_item(0, 'VISIBLE_TABLE', '');
			

}
	function redraw() {
		parent::redraw();

		if (session::is_set("RE_CALCULO_CUOTA")) {
			session::un_set("RE_CALCULO_CUOTA");
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$COD_DECLARACION = $this->dws['dw_declaracion']->get_item(0, 'COD_DECLARACION');
			if (K_TIPO_BD=="oci"){
				$sql = "select f_pp_saldo_plan_pago(P.COD_PLAN_PAGO, SYSDATE) SALDO_A_PAGAR
					from DECLARACION D, DEUDA DA, PLAN_PAGO P
					where D.COD_DECLARACION = $COD_DECLARACION
					  and DA.COD_DEUDOR = D.COD_DEUDOR
					  and DA.COD_TIPO_DEUDA in ('CS', 'CSR', 'CR12')
					  AND DA.COD_ESTADO_DEUDA = 2	-- exigible	
					  and P.COD_DEUDA = DA.COD_DEUDA
					  and P.ANO_COBRO = D.ANO + 1";
			}else if (K_TIPO_BD=="mssql"){
				$sql = "select dbo.f_pp_saldo_plan_pago(P.COD_PLAN_PAGO, getdate()) SALDO_A_PAGAR
					from DECLARACION D, DEUDA DA, PLAN_PAGO P
					where D.COD_DECLARACION = $COD_DECLARACION
					  and DA.COD_DEUDOR = D.COD_DEUDOR
					  and DA.COD_TIPO_DEUDA in ('CS', 'CSR', 'CR12')
					  AND DA.COD_ESTADO_DEUDA = 2	-- exigible	
					  and P.COD_DEUDA = DA.COD_DEUDA
					  and P.ANO_COBRO = D.ANO + 1";
			}

			$result	= $db->build_results($sql);
			$saldo_a_pagar = $result[0]['SALDO_A_PAGAR']; 
			$saldo_a_pagar = number_format($saldo_a_pagar, 4, ',' , '.');
			$this->alert("La cuota ha sido recalculada. Monto a pagar: $saldo_a_pagar");
		}
	}
	function get_key() {
		return $this->dws['dw_declaracion']->get_item(0, 'COD_DECLARACION');
	}
	function save_record($db) {
		$cod_declaracion        = $this->get_key();
		$cod_estado_declaracion	= $this->dws['dw_declaracion']->get_item(0, 'COD_ESTADO_DECLARACION');
		$cod_usuario            = $this->cod_usuario;
		$cod_deudor				= $this->dws['dw_declaracion']->get_item(0, 'COD_DEUDOR');
		$cod_via_declaracion    = $this->dws['dw_declaracion']->get_item(0, 'COD_VIA_DECLARACION');
		$fecha_confirma         = $this->dws['dw_declaracion']->get_item(0, 'FECHA_CONFIRMA');
		$cod_usuario_confirma   = $this->cod_usuario;
		$fecha_declaracion      = $this->dws['dw_declaracion']->get_item(0, 'FECHA_DECLARACION');
		$ano_declaracion        = $this->dws['dw_declaracion']->get_item(0, 'ANO_DECLARACION');
		$con_hijo               = $this->dws['dw_declaracion']->get_item(0, 'CON_HIJO');
		$cod_afp                = $this->dws['dw_declaracion']->get_item(0, 'COD_AFP');
		$cod_estado_civil       = $this->dws['dw_declaracion']->get_item(0, 'COD_ESTADO_CIVIL');
		$rut_conyuge            = $this->dws['dw_declaracion']->get_item(0, 'RUT_CONYUGE');
		$dig_verif_conyuge      = $this->dws['dw_declaracion']->get_item(0, 'DIG_VERIF_CONYUGE');
		$nom_conyuge            = $this->dws['dw_declaracion']->get_item(0, 'NOM_CONYUGE');
		$conyuge_es_deudor      = $this->dws['dw_declaracion']->get_item(0, 'CONYUGE_ES_DEUDOR');
		$conyuge_declara        = $this->dws['dw_declaracion']->get_item(0, 'CONYUGE_DECLARA');
		$entrega_f22            = $this->dws['dw_declaracion']->get_item(0, 'ENTREGA_F22');
		
		$cert_sueldo            = $this->dws['dw_declaracion']->get_item(0, 'CERT_SUELDO');
		$cert_honorario         = $this->dws['dw_declaracion']->get_item(0, 'CERT_HONORARIO');
		$cert_afp				= $this->dws['dw_declaracion']->get_item(0, 'CERT_AFP');
		$declara_renta 			= $this->dws['dw_declaracion']->get_item(0, 'DECLARA_RENTA');
		$cert_notarial_ing 		= $this->dws['dw_declaracion']->get_item(0, 'CERT_NOTARIAL_ING');
		$antec_conyuge 			= $this->dws['dw_declaracion']->get_item(0, 'ANTEC_CONYUGE');
		$cert_alum_regular		= $this->dws['dw_declaracion']->get_item(0, 'CERT_ALUM_REGULAR'); 

		$cod_declaracion		= ($cod_declaracion=='') ? "null" : $cod_declaracion;
		$cod_usuario			= ($cod_usuario=='') ? "null" : $cod_usuario;
		$fecha_confirma			= ($fecha_confirma=='') ? "null" : $fecha_confirma;
		$cod_usuario_confirma	= ($cod_usuario_confirma=='') ? "null" : $cod_usuario_confirma;
		$cod_estado_civil		= ($cod_estado_civil=='') ? "null" : $cod_estado_civil;
		$rut_conyuge			= ($rut_conyuge=='') ? "null" : $rut_conyuge;
		$dig_verif_conyuge		= ($dig_verif_conyuge=='') ? "null" : "'$dig_verif_conyuge'";
		$nom_conyuge			= ($nom_conyuge=='') ? "null" : "'$nom_conyuge'";
		
		$fecha_declaracion =$this->str2date($fecha_declaracion);

		// Obtiene los valores que inciden en el CALCULO_CUOTA antes de grabar 
		$result_antes = $this->datos_calcula_cuota($cod_declaracion);
		
		$sp = 'spu_declaracion';
		if ($this->is_new_record()){
			$operacion = 'INSERT';
			$cod_estado_ant = 0;	// cualquier cosa <> APROBADA 		
		}
		else{
			$operacion = 'UPDATE';
			// obtiene estado anterior
			if (K_TIPO_BD=="oci"){ 
				$sql = "select COD_ESTADO_DECLARACION
						from DECLARACION
						where COD_DECLARACION = $cod_declaracion";
			}else if (K_TIPO_BD=="mssql"){
				$sql = "select COD_ESTADO_DECLARACION
						from DECLARACION
						where COD_DECLARACION = $cod_declaracion";				
			}
			
			$result = $db->build_results($sql);
			$cod_estado_ant = $result[0]['COD_ESTADO_DECLARACION']; 		
		}
		$param	= "'$operacion',
					$cod_declaracion,
					$cod_estado_declaracion,
					$cod_usuario,
					$cod_deudor,
					$cod_via_declaracion,
					$cod_usuario_confirma,
					$fecha_declaracion,
					$ano_declaracion,
					'$con_hijo',
					$cod_afp,
					$cod_estado_civil,
					$rut_conyuge,
					$dig_verif_conyuge,
					$nom_conyuge,
					'$conyuge_es_deudor',
					'$conyuge_declara',
					'$entrega_f22',
					'$cert_sueldo',
					'$cert_honorario',
					'$cert_afp',
					'$declara_renta',
					'$cert_notarial_ing',
					'$antec_conyuge',
					'$cert_alum_regular'";
					
		if ($this->dws['dw_declaracion']->is_modified) {
			if ($db->EXECUTE_SP($sp, $param)){
				if ($this->is_new_record()) {
					$cod_declaracion = $db->GET_IDENTITY("declaracion");
					$this->dws['dw_declaracion']->set_item(0, 'COD_DECLARACION', $cod_declaracion);
				}
			}
			else
				return false;
		}

		if (!$this->dws['dw_direccion_deudor_particular']->update($db,$cod_deudor)){
			return false;
		}
		if (!$this->dws['dw_direccion_deudor_comercial']->update($db,$cod_deudor)){
			return false;
		}
		if (!$this->dws['dw_declaracion_mes']->update($db,$cod_declaracion,$operacion)){
			return false;
		}

		$recalcula = false;
		$cuota_calculada = $this->cuota_calculada($cod_declaracion);
		if ($cuota_calculada=='S') {
			if ($cod_estado_declaracion==self::K_EST_DECL_ANULADA &&  $result_antes[0]['COD_ESTADO_DECLARACION']==self::K_EST_DECL_APROBADA) {
				// se anula una DJ aprobada
				$recalcula = true;
			}
			else if ($cod_estado_declaracion==self::K_EST_DECL_APROBADA) {
				$result_despues = $this->datos_calcula_cuota($cod_declaracion);
				if ($result_antes[0]['COD_ESTADO_DECLARACION'] != $result_despues[0]['COD_ESTADO_DECLARACION'] ||
					$result_antes[0]['CON_HIJO'] != $result_despues[0]['CON_HIJO'] ||
					$result_antes[0]['COD_ESTADO_CIVIL'] != $result_despues[0]['COD_ESTADO_CIVIL'] ||
					$result_antes[0]['CONYUGE_ES_DEUDOR'] != $result_despues[0]['CONYUGE_ES_DEUDOR'] ||
					$result_antes[0]['CONYUGE_DECLARA'] != $result_despues[0]['CONYUGE_DECLARA'] ||
					$result_antes[0]['MONTO_DEUDOR'] != $result_despues[0]['MONTO_DEUDOR'] ||
					$result_antes[0]['MONTO_CONYUGE'] != $result_despues[0]['MONTO_CONYUGE'])
					$recalcula = true;
			}
		}
			// Obtiene los valores que inciden en el CALCULO_CUOTA antes de grabar 
		if ($recalcula) {
			$cod_usuario = $this->cod_usuario;
			if (!$db->EXECUTE_SP($sp, "'RE_CALCULO_CUOTA', $cod_declaracion, null, $cod_usuario"))
				return false;
			session::set("RE_CALCULO_CUOTA", "true");
		}
			
		return true;
	}
	function datos_calcula_cuota($cod_declaracion) {
		// Obtiene los valores que inciden en el CALCULO_CUOTA antes de grabar 
		if ($cod_declaracion != 'null') {
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			
			if (K_TIPO_BD=="oci"){ 
				$sql_calculo_cuota = "select	D.COD_DECLARACION
												,D.COD_ESTADO_DECLARACION
												,D.CON_HIJO
												,D.COD_ESTADO_CIVIL
												,D.CONYUGE_ES_DEUDOR
												,D.CONYUGE_DECLARA
												,(select NVL(sum(MONTO_DEUDOR_MONEDA), 0) from DECLARACION_MES DM where DM.COD_DECLARACION = D.COD_DECLARACION) MONTO_DEUDOR
												,(select NVL(sum(MONTO_CONYUGE_MONEDA),0) from DECLARACION_MES DM where DM.COD_DECLARACION = D.COD_DECLARACION) MONTO_CONYUGE
										from DECLARACION D
										where COD_DECLARACION = $cod_declaracion";
			}else if (K_TIPO_BD=="mssql"){
				$sql_calculo_cuota = "select	D.COD_DECLARACION
												,D.COD_ESTADO_DECLARACION
												,D.CON_HIJO
												,D.COD_ESTADO_CIVIL
												,D.CONYUGE_ES_DEUDOR
												,D.CONYUGE_DECLARA
												,(select isnull(sum(MONTO_DEUDOR_MONEDA), 0) from DECLARACION_MES DM where DM.COD_DECLARACION = D.COD_DECLARACION) MONTO_DEUDOR
												,(select isnull(sum(MONTO_CONYUGE_MONEDA),0) from DECLARACION_MES DM where DM.COD_DECLARACION = D.COD_DECLARACION) MONTO_CONYUGE
										from DECLARACION D
										where COD_DECLARACION = $cod_declaracion";
			}
			
			$result = $db->build_results($sql_calculo_cuota);
		}
		else
			$result = array(0 => array('COD_DECLARACION'			=>	0,
											 'COD_ESTADO_DECLARACION' 	=>	0,
											 'CON_HIJO' 				=>	'',
											 'COD_ESTADO_CIVIL' 		=>	0,
											 'CONYUGE_ES_DEUDOR' 		=>	'',
											 'CONYUGE_DECLARA' 			=>	'',
											 'MONTO_DEUDOR' 			=>	0,
											 'MONTO_CONYUGE' 			=>	0,
											 )
								  );
		return $result;
	}
	function cuota_calculada($cod_declaracion) {
		// retorna 'S' o 'N' segun si tiene registros de que la cuota ya fue calculada
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		// obtiene el plan_pago
		if (K_TIPO_BD=="oci"){
			$sql = "select P.COD_PLAN_PAGO
							,DA.COD_TIPO_DEUDA
						from DECLARACION D, DEUDA DA, PLAN_PAGO P
						where D.COD_DECLARACION = $cod_declaracion
						  and DA.COD_DEUDOR = D.COD_DEUDOR
						  and DA.COD_TIPO_DEUDA in ('CS', 'CSR')
					  	  AND DA.COD_ESTADO_DEUDA = 2	-- exigible	
						  and P.COD_DEUDA = DA.COD_DEUDA
						  and P.ANO_COBRO = D.ANO + 1
						  AND ROWNUM = 1";
		}else if (K_TIPO_BD=="mssql"){
			$sql = "select top 1 P.COD_PLAN_PAGO
					,DA.COD_TIPO_DEUDA
				from DECLARACION D, DEUDA DA, PLAN_PAGO P
				where D.COD_DECLARACION = $cod_declaracion
				  and DA.COD_DEUDOR = D.COD_DEUDOR
				  and DA.COD_TIPO_DEUDA in ('CS', 'CSR')
				  AND DA.COD_ESTADO_DEUDA = 2	-- exigible	
				  and P.COD_DEUDA = DA.COD_DEUDA
				  and P.ANO_COBRO = D.ANO + 1";
		}
		
		$result = $db->build_results($sql);
		if (count($result)==0)
			return 'N';

		$cod_plan_pago = $result[0]['COD_PLAN_PAGO']; 		
		$cod_tipo_deuda = $result[0]['COD_TIPO_DEUDA']; 		
		
		// obtiene si la cuota ya fue calculada
		if (K_TIPO_BD=="oci"){		  
			$sql = "select count(*) CANT
					from LOG_CAMBIO
					where NOM_TABLA = 'PLAN_PAGO'
					  and KEY_TABLA = '$cod_plan_pago'
					  and TIPO_CAMBIO = 'R'";
		}else if (K_TIPO_BD=="mssql"){
			$sql = "select count(*) CANT
					from LOG_CAMBIO
					where NOM_TABLA = 'PLAN_PAGO'
					  and KEY_TABLA = '$cod_plan_pago'
					  and TIPO_CAMBIO = 'R'";
		}
		
		$result = $db->build_results($sql);
		$cant = $result[0]['CANT'];

		if ($cant > 0)
			return 'S';
		else if ($cod_tipo_deuda=='CS')
			return 'S';	// a pesar de no tener LOG_CAMBIO pero la cuota existe
		else
			return 'N';
	}
	function print_record() {
		$cod_declaracion = $this->get_key();
		$sql= sql_declaracion::get_sql('PRINT_RECORD',$cod_declaracion);
		
		// reporte
		$labels = array();
		$file_name = $this->find_file('declaracion', 'declaracion.xml');
		$rpt = new print_declaracion($sql, $file_name, $labels, "declaracion".$cod_declaracion.'.pdf', 0);
		$this->_load_record();
	}
	function crea_cuota() {
		$cod_declaracion = $this->get_key();
		$cod_usuario = $this->cod_usuario;
		$sp = 'spu_declaracion';
		$param	= "'CALCULO_CUOTA', $cod_declaracion, NULL ,$cod_usuario";
		
		//primero se ejecuta sp, y después se valida si es candidato a condonación, 
		//ya que en el sp puede crear registro de CONDONACION_CS
	 	$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$db->BEGIN_TRANSACTION();
		if ($db->EXECUTE_SP($sp, $param))
			$db->COMMIT_TRANSACTION();
		else
			$db->ROLLBACK_TRANSACTION();			

		//muestra mensaje si es candidato a condonacion
		if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
			$sql = "SELECT DA.COD_DEUDA
					FROM	DECLARACION DE, DEUDA DA
					WHERE	DE.COD_DECLARACION = $cod_declaracion
					  AND	DE.COD_DEUDOR = DA.COD_DEUDOR
					  AND	DA.COD_TIPO_DEUDA = 'CS'
					  AND	DA.COD_ESTADO_DEUDA = 2"; //exigible
		}
		$result = $db->build_results($sql);
		$row_count = $db->count_rows();
		
		if ($row_count == 0)
			$cant = 0;	
		else{
			$cod_deuda = $result[0]['COD_DEUDA'];
			if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
				$sql = "SELECT	count(*) CANT
					FROM	CONDONACION_CS
					WHERE	COD_DEUDA = $cod_deuda
						AND COD_ESTADO_CONDONACION_CS IN (1,2)"; //emitida o aprobgada
			}
			$result = $db->build_results($sql);
			$cant = $result[0]['CANT'];
		}
			
		if ($cant > 0){
			$this->redraw();
			$this->message('El deudor es candidato a condonación, por lo tanto no se calculará su cuota. Favor revise el modulo de "Candidatos Condonación CS".');
			return;
		}
		
		$this->_load_record();
	}
	function procesa_event() {
		if(isset($_POST['b_crea_cuota'])) {
			$this->crea_cuota();
		}
		else
			parent::procesa_event();
	}
}

/////////////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wi_declaracion.php";
if (file_exists($file_name)) 
	require_once($file_name);
else {
	class wi_declaracion extends wi_declaracion_base {
	    function __construct($cod_item_menu) {
	        parent::__construct($cod_item_menu); 
		}
	}
}
?>