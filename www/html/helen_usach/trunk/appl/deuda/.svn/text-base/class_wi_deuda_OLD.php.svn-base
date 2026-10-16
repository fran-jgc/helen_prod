<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");

class wi_deuda_base extends w_input {
	const K_ESTADO_DEUDA_NO_EXIGIBLE= 1;
	const K_ESTADO_DEUDA_EXIGIBLE 	= 2;
	const K_ESTADO_DEUDA_CANCELADA 	= 4;
	var   $K_PAGARE_DEVUELTO;
	
	const K_COMP_DEUDA_NO_EXIGIBLE	= 1;
	const K_COMP_DEUDA_VENCIDA	 	= 2;
	const K_COMP_DEUDA_VIGENTE	 	= 3;
	const K_COMP_DEUDA_FUTURA	 	= 4;
	
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
	const K_CARTERA_UACH			= 7;
	const K_ESTADO_PP_MOROSA		= 4;
	const K_ESTADO_PP_PAGADA		= 3;
	const K_PARAM_CARTERA			= 2;
	const K_AUTORIZA_CAMBIA_CARTERA = '990205';
	const K_AUTORIZA_CAMBIA_UNIV = '990210';
	
	function wi_deuda_base($cod_item_menu) {
		$this->K_PAGARE_DEVUELTO = $this->get_constante('K_EST_PAGARE_DEV');
		
		parent::w_input('deuda', $cod_item_menu);
		$this->b_delete_visible  = false;

		$sql = sql_deuda::get_sql('INPUT');
		
		$this->dws['dw_deuda'] = new datawindow($sql);
		$this->dws['dw_deuda']->add_control(new static_num('RUT'));
		$this->dws['dw_deuda']->add_control(new edit_text_hidden('RUT_H',10,10));
		$this->dws['dw_deuda']->add_control(new edit_text_hidden('COD_DEUDOR'));
		$this->dws['dw_deuda']->add_control(new edit_date('FECHA_INICIO_COBRO', 10, 10));
		$sql_1 = sql_deuda::get_sql('DD_COD_CARTERA');
		$this->dws['dw_deuda']->add_control(new drop_down_dw('COD_CARTERA', $sql_1, 150));
		$sql_1 = sql_deuda::get_sql('DD_COD_UNIVERSIDAD');
		$this->dws['dw_deuda']->add_control(new drop_down_dw('COD_UNIVERSIDAD', $sql_1, 150));
		
		$this->dws['dw_deuda']->add_control(new static_num('MONTO_INICIO_COBRO',4));
		$this->dws['dw_deuda']->add_control(new static_num('CAT',4));
		// saldos por componente
		$this->dws['dw_deuda']->add_control(new static_num('SALDO_NO_EXIGIBLE',4));
		$this->dws['dw_deuda']->add_control(new static_num('SALDO_NO_EXIGIBLE_PESOS',0));
		$this->dws['dw_deuda']->add_control(new static_num('SALDO_VENCIDA',4));
		$this->dws['dw_deuda']->add_control(new static_num('SALDO_VENCIDA_PESOS',0));
		$this->dws['dw_deuda']->add_control(new static_num('SALDO_VIGENTE',4));
		$this->dws['dw_deuda']->add_control(new static_num('SALDO_VIGENTE_PESOS',0));
		$this->dws['dw_deuda']->add_control(new static_num('SALDO_FUTURO',4));
		$this->dws['dw_deuda']->add_control(new static_num('SALDO_FUTURO_PESOS',0));
		$this->dws['dw_deuda']->add_control(new static_num('SALDO_TOTAL',4));
		$this->dws['dw_deuda']->add_control(new static_num('SALDO_TOTAL_PESOS',0));
		$this->dws['dw_deuda']->add_control(new edit_text_hidden('COD_ESTADO_DEUDA_H',10,10));
		$this->dws['dw_deuda']->add_control(new static_num('SUMA_MONTO_ACTUALIZADO',4));
		$this->dws['dw_deuda']->add_control(new static_num('SUMA_MONTO_PORC',2));
		
		//$sql_estado = sql_deuda::get_sql('DD_COD_ESTADO_DEUDA');
		//$this->dws['dw_deuda']->add_control(new drop_down_dw('COD_ESTADO_DEUDA', $sql_estado, 20, '', false));
				
		$this->dws['dw_deuda']->add_control($control = new edit_check_box('SELECCIONA_DICOM', 'S', 'N'));
		$control->set_onChange("seleccion_dicom();");
		
		$sql_empresa_cobranza = sql_deuda::get_sql('DD_EMPRESA_COBRANZA');
		$this->dws['dw_deuda']->add_control($control = new drop_down_dw('COD_EMPRESA_COBRANZA', $sql_empresa_cobranza, 20));
		$control->set_onChange("seleccion_emp_cobranza();");
		
		$this->dws['dw_deuda']->add_control(new edit_text_upper('VALOR_DICOM_H', 10, 10, 'hidden'));
		$this->dws['dw_deuda']->add_control(new edit_text_upper('VALOR_EM_CO_H', 10, 10, 'hidden'));
		
		$this->dws['dw_deuda_plan_pago'] = new dw_deuda_plan_pago();
		$this->dws['dw_deuda_pagares'] = new dw_deuda_pagares();
		$this->dws['dw_deuda_cruzados'] = new dw_deuda_cruzados();
		$this->dws['dw_deuda_cruzados_interno'] = new dw_deuda_cruzados_interno();
		$this->dws['dw_deuda_abonos'] = new dw_deuda_abonos();
		$this->dws['dw_deuda_descuentos'] = new dw_deuda_descuentos();
		
		// asigna los mandatorys		
		$this->dws['dw_deuda']->set_mandatory('COD_CARTERA', 'Cartera de la deuda');
		$this->dws['dw_deuda']->set_mandatory('COD_UNIVERSIDAD', 'Universidad que cobra la deuda');
		$this->dws['dw_deuda']->set_mandatory('FECHA_INICIO_COBRO', 'Fecha Inicio cobro de la deuda');
		
		//mandatory en cruzados_deuda
		$this->dws['dw_deuda']->set_mandatory('CRUZADO_COD_UNIVERSIDAD', 'Universidad en Cruzados');
		$this->dws['dw_deuda']->set_mandatory('MONTO_ACTUALIZADO', 'Monto Actualizado en Cruzados');
		$this->dws['dw_deuda']->set_mandatory('FECHA_CONSOLIDACION', 'Fecha Consolidacion en cruzados');

		//audoria
		$this->add_auditoria('COD_CARTERA');
		$this->add_auditoria('COD_UNIVERSIDAD');
		$this->add_auditoria('FECHA_INICIO_COBRO');
	}

	function new_record() {
		$this->dws['dw_deuda']->insert_row();
	}
	function load_record() {
		$cod_deuda	= $this->get_item_wo($this->current_record, 'COD_DEUDA');
		$this->dws['dw_deuda']->retrieve($cod_deuda);
		
		$this->dws['dw_deuda_plan_pago']->retrieve($cod_deuda);
		$this->dws['dw_deuda_pagares']->retrieve($cod_deuda);
		$this->dws['dw_deuda_cruzados']->retrieve($cod_deuda);
		$this->dws['dw_deuda_cruzados_interno']->retrieve($cod_deuda);
		$this->dws['dw_deuda_abonos']->retrieve($cod_deuda);
		$this->dws['dw_deuda_descuentos']->retrieve($cod_deuda);

		$this->b_print_visible = true;
		
		//suma de cruzados
		$monto_actualizado_aux = 0;
		$monto_porc_aux = 0;
		for ($i = 0; $i < $this->dws['dw_deuda_cruzados']->row_count(); $i++) {
        	$monto_actualizado = $this->dws['dw_deuda_cruzados']->get_item($i, 'MONTO_ACTUALIZADO');
			$monto_actualizado_aux = $monto_actualizado_aux + $monto_actualizado; 
			///porcentaje
			$monto_porc = $this->dws['dw_deuda_cruzados']->get_item($i, 'PORC');
			$monto_porc_aux = $monto_porc_aux + $monto_porc; 
        }
		$this->dws['dw_deuda']->set_item(0, 'SUMA_MONTO_ACTUALIZADO', $monto_actualizado_aux);
		$this->dws['dw_deuda']->set_item(0, 'SUMA_MONTO_PORC', $monto_porc_aux);
		
		/*Lo que se busca, es que si el deudor tiene un plan de pago en 
		alguna empresa de cobranza(ej. EXTERNA o DICOM), se mostrara un mensaje en el input alertando sobre eso*/
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = sql_deuda::get_sql('GET_DEUDA_MOROSA', $cod_deuda);		  
		$result = $db->build_results($sql);
		$row_count = $db->count_rows();	
		
		$this->dws['dw_deuda_plan_pago']->set_entrable('PUBLICA_DICOM', true);
		
		if($row_count > 0)
			$this->dws['dw_deuda']->set_item(0, 'VISIBLE_CAMPO', '');
		else 
			$this->dws['dw_deuda']->set_item(0, 'VISIBLE_CAMPO', 'none');
			
		///display_cruzado
		$db_c = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql_c = "SELECT COUNT(*) CANT
				  FROM	CRUZADO 
			      WHERE	COD_DEUDA = $cod_deuda"; //DEUDA ESTA CRUZADA
		$result_c = $db_c->build_results($sql_c);
		$cruzado = $result_c[0]['CANT'];
		$display_cruzado = 'none';
		
		if ($cruzado > 0){
			$display_cruzado = '';
			$txt = "DEUDA CRUZADA";
			$cod_cartera = $this->dws['dw_deuda']->get_item(0, 'COD_CARTERA');
			$cod_cartera_defecto = base::get_parametro(self::K_PARAM_CARTERA); 
			
			if ($cod_cartera != $cod_cartera_defecto)
				$txt .= " COBRA OTRA UNIVERSIDAD";
			
		}else {
			$txt = "";
			$display_cruzado = 'none';			
		}
		$this->dws['dw_deuda']->set_item(0, 'TITULO_DEUDOR_CRUZADO', $txt);
		$this->dws['dw_deuda']->set_item(0, 'VISIBLE_DEUDOR_CRUZADO', $display_cruzado);
			
		////end display_cruzado
		//INGRESABLE TIPO REBAJA SIEMPRE QUE LA DEUDA SEA NO EXIGIBLE.
		$COD_ESTADO_DEUDA = $this->dws['dw_deuda']->get_item(0, 'COD_ESTADO_DEUDA');
		if($COD_ESTADO_DEUDA == self::K_ESTADO_DEUDA_NO_EXIGIBLE){
			$this->dws['dw_deuda_pagares']->set_entrable('COD_TIPO_REBAJA_PAGARE', true);
			$this->dws['dw_deuda_pagares']->set_entrable('MONTO_MONEDA_REBAJA', true);
			$this->dws['dw_deuda_pagares']->set_entrable('MONTO_PESOS_REBAJA', true);
		}else{
			$this->dws['dw_deuda_pagares']->set_entrable('COD_TIPO_REBAJA_PAGARE', false);
			$this->dws['dw_deuda_pagares']->set_entrable('MONTO_MONEDA_REBAJA', false);
			$this->dws['dw_deuda_pagares']->set_entrable('MONTO_PESOS_REBAJA', false);
		}
		
		/*Maneja el valor que tiene cod_empresa_cobranza, se utiliza para el update*/	
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = sql_deuda::get_sql('GET_EMPRESA_COBRANZA', $cod_deuda);
		$result = $db->build_results($sql);
		$row_count = $db->count_rows();
		if($row_count != 0){
			$cod_empresa_cobranza = $result[0]['COD_EMPRESA_COBRANZA'];
			$this->dws['dw_deuda']->set_item(0, 'VALOR_EM_CO_H', $cod_empresa_cobranza);
		}else{
			$this->dws['dw_deuda']->set_item(0, 'VALOR_EM_CO_H', '');
		}
		
		/*Maneja el tema de check_box de publica_dicom, si publica dicom = 'S' muestra el campo SELECCION EN = 'S'
		de lo contrario lo muestra SELECCION = 'N'*/
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = sql_deuda::get_sql('GET_PUBLICA_DICOM', $cod_deuda);
		$result = $db->build_results($sql);
		$row_count = $db->count_rows();	
		
		if($row_count == 0){
			$this->dws['dw_deuda']->set_item(0, 'VALOR_DICOM_H', 0);
			$this->dws['dw_deuda']->set_item(0, 'SELECCIONA_DICOM', 'N');
		}	
		else{ 	
			$this->dws['dw_deuda']->set_item(0, 'VALOR_DICOM_H', 1);
			$this->dws['dw_deuda']->set_item(0, 'SELECCIONA_DICOM', 'S');
		}
		
		//valida cartera diferente universidad, no puede cambiar empresa de cobranza y marcar DICOM		
		$rut 	= $this->dws['dw_deuda']->get_item(0, 'RUT');
		$cartera_defecto = base::get_parametro(self::K_PARAM_CARTERA);

		$sql = 	"SELECT COUNT(*) CANT
								FROM DEUDOR DR, DEUDA DA 
								WHERE DR.COD_DEUDOR = DA.COD_DEUDOR 
								AND DA.COD_CARTERA  <> $cartera_defecto
								AND DR.RUT = $rut
								AND COD_TIPO_DEUDA IN ('CS', 'CSR')";
		$result = $db->build_results($sql);
		
		if ($result[0]['CANT']>0){
			$this->dws['dw_deuda']->set_visible('COD_EMPRESA_COBRANZA', false);
			$this->dws['dw_deuda']->set_visible('SELECCIONA_DICOM', false);
			
		}else{
			$this->dws['dw_deuda']->set_visible('COD_EMPRESA_COBRANZA', true);
			$this->dws['dw_deuda']->set_visible('SELECCIONA_DICOM', true);
		}
		
		//si el usuario tiene privilegio especial, puede modificar loa cartera
		$priv = $this->get_privilegio_opcion_usuario(self::K_AUTORIZA_CAMBIA_CARTERA, $this->cod_usuario);
		if ($priv=='E'){
			$sql = sql_deuda::get_sql('DD_COD_CARTERA');
			$this->dws['dw_deuda']->add_control(new drop_down_dw('COD_CARTERA', $sql, 150));
		}else{
			$sql = sql_deuda::get_sql('DD_COD_CARTERA');
			$this->dws['dw_deuda']->add_control(new drop_down_dw('COD_CARTERA', $sql, 150));
			$this->dws['dw_deuda']->set_entrable('COD_CARTERA', false);
		}

		//si el usuario tiene privilegio especial, puede modificar la universidad
		$priv = $this->get_privilegio_opcion_usuario(self::K_AUTORIZA_CAMBIA_UNIV, $this->cod_usuario);
		if ($priv=='E'){
			$sql = sql_deuda::get_sql('DD_COD_UNIVERSIDAD');
			$this->dws['dw_deuda']->add_control(new drop_down_dw('COD_UNIVERSIDAD', $sql, 150));
		}else{
			$sql = sql_deuda::get_sql('DD_COD_UNIVERSIDAD');
			$this->dws['dw_deuda']->add_control(new drop_down_dw('COD_UNIVERSIDAD', $sql, 150));
			$this->dws['dw_deuda']->set_entrable('COD_UNIVERSIDAD', false);
		}
		
		//muestra mensaje, si el deudor tiene documentos protestados
		$sql_doc_protes = sql_deuda::get_sql('DOCTO_PROTESTADO', $cod_deuda);
		$result	= $db->build_results($sql_doc_protes);
		$count 	= $result[0]['CANT'];
		if ($count == 0)
			$this->dws['dw_deuda']->set_item(0, 'VISIBLE_CAMPO_PROTES', 'none');
		else if	($count > 0)
			$this->dws['dw_deuda']->set_item(0, 'VISIBLE_CAMPO_PROTES', '');
			
		//muestra mensaje si el deudor tiene pagares protestados
		$sql_pagare_protes = sql_deuda::get_sql('PAGARE_PROTESTADO', $cod_deuda);
		$result	= $db->build_results($sql_pagare_protes);
		$count 	= $result[0]['CANT'];
		if($count == 0){
			$this->dws['dw_deuda']->set_item(0, 'VISIBLE_PAGARE_PROTES', 'none');
		}
		else if	($count > 0){
			$this->dws['dw_deuda']->set_item(0, 'VISIBLE_PAGARE_PROTES', '');
		}
	}
	function get_key() {
		return $this->dws['dw_deuda']->get_item(0, 'COD_DEUDA');
	}
	function habilitar(&$temp, $habilita) {
		$CANT_PAGARE = $this->dws['dw_deuda_pagares']->row_count();
		if($CANT_PAGARE == 0) //cuando no tiene pagares al ejecutar el get_item de m�s abajo se cae. por eso se asigna como devuelto
			$COD_ESTADO_PAGARE_H = $this->K_PAGARE_DEVUELTO; 
		else
			$COD_ESTADO_PAGARE_H = $this->dws['dw_deuda_pagares']->get_item(0, 'COD_ESTADO_PAGARE_H');
		
		if ($COD_ESTADO_PAGARE_H == $this->K_PAGARE_DEVUELTO){
			$temp->setVar("WI_DEVOLUCION", '');
		
		}else{
			//devolucion de pagares
			$control = '<input name="b_devolucion_pagare" id="b_devolucion_pagare" src="../../images_appl/b_devolucion_pagare.jpg" type="image" '.
								 'onMouseDown="MM_swapImage(\'b_devolucion_pagare\',\'\',\'../../images_appl/b_devolucion_pagare_click.jpg\',1)" '.
								 'onMouseUp="MM_swapImgRestore()" onMouseOut="MM_swapImgRestore()" '.
								 'onMouseOver="MM_swapImage(\'b_devolucion_pagare\',\'\',\'../../images_appl/b_devolucion_pagare_over.jpg\',1)" onclick="return dlg_print_devolucion();"/>';
			$temp->setVar("WI_DEVOLUCION", $control);
		
		}	
		
		$control = '<input name="b_create_boleta" id="b_create_boleta" src="../../images_appl/b_create_boleta.jpg" type="image" '.
							 'onMouseDown="MM_swapImage(\'b_create_boleta\',\'\',\'../../images_appl/b_create_boleta_click.jpg\',1)" '.
							 'onMouseUp="MM_swapImgRestore()" onMouseOut="MM_swapImgRestore()" '.
							 'onMouseOver="MM_swapImage(\'b_create_boleta\',\'\',\'../../images_appl/b_create_boleta_over.jpg\',1)" />';
		$temp->setVar("WDEUDA_CREA_BOLETA", $control);

		
	}
	function save_record($db) {
		$COD_DEUDA 	= $this->dws['dw_deuda']->get_item(0, 'COD_DEUDA');
		$COD_CARTERA 	= $this->dws['dw_deuda']->get_item(0, 'COD_CARTERA');
		$COD_UNIVERSIDAD = $this->dws['dw_deuda']->get_item(0, 'COD_UNIVERSIDAD');
		$COD_ESTADO_DEUDA = $this->dws['dw_deuda']->get_item(0, 'COD_ESTADO_DEUDA');
		$FECHA_INICIO_COBRO = $this->dws['dw_deuda']->get_item(0, 'FECHA_INICIO_COBRO');
		
		//$FECHA_INICIO_COBRO	= ($FECHA_INICIO_COBRO =='') ? "null" : "$FECHA_INICIO_COBRO";
		$FECHA_INICIO_COBRO = $this->str2date($FECHA_INICIO_COBRO);
		
		$sp = 'spu_deuda';
		if ($this->is_new_record()){
			$operacion = 'INSERT';
		}
		else{
			$operacion = 'UPDATE';
		}
		
		if (K_CLIENTE=="UV" || K_CLIENTE=="UACH")
			$param = "'$operacion',$COD_DEUDA,$COD_CARTERA,$COD_UNIVERSIDAD,null,null,$FECHA_INICIO_COBRO,".$this->cod_usuario.",null,null,null,$COD_ESTADO_DEUDA";
		else
			$param = "'$operacion',$COD_DEUDA,$COD_CARTERA,$COD_UNIVERSIDAD,null,null,$FECHA_INICIO_COBRO,".$this->cod_usuario;

			if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$COD_DEUDA = $db->GET_IDENTITY("deuda");
				$this->dws['dw_deuda']->set_item(0, 'COD_DEUDA', $COD_DEUDA);
			}
			
			$valor_dicom = $this->dws['dw_deuda']->get_item(0, 'VALOR_DICOM_H');
			$valor_emp_co = $this->dws['dw_deuda']->get_item(0, 'VALOR_EM_CO_H');
			
			$valor_emp_co				= ($valor_emp_co ==0) ? 'NULL' : $valor_emp_co;
			
			if (!$db->EXECUTE_SP($sp, "'EMPRESA_COBRANZA', $COD_DEUDA, $valor_emp_co"))
				return false;
			
			for ($i = 0; $i < $this->dws['dw_deuda_plan_pago']->row_count(); $i++) {
	        	$publica_dicom_h = $this->dws['dw_deuda_plan_pago']->get_item($i, 'PUBLICA_DICOM_H');
	        	$cod_plan_pago_h = $this->dws['dw_deuda_plan_pago']->get_item($i, 'COD_PLAN_PAGO_H');
	        	$cod_estado_plan_pago_h = $this->dws['dw_deuda_plan_pago']->get_item($i, 'COD_ESTADO_PLAN_PAGO_H');
	        	
	        	if($publica_dicom_h == 'S')
	        		$publica_dicom_h = 1;
	        	else
	        		$publica_dicom_h = 0; 
				
	        	//se modifica para que permita ingresar tambien a los plan de pagos en estado pagado, pero que solo permita desmarcar, no marcar dicom
	        	if(($cod_estado_plan_pago_h == self::K_ESTADO_PP_MOROSA)||(($cod_estado_plan_pago_h == self::K_ESTADO_PP_PAGADA)&&($publica_dicom_h == 0))){	
	        		if (!$db->EXECUTE_SP('spu_plan_pago', "'DICOM', $cod_plan_pago_h, $publica_dicom_h"))
		        		return false;	
				}		
	        }
			
			//INGRESABLE TIPO REBAJA SIEMPRE QUE LA DEUDA SEA NO EXIGIBLE.
			$COD_ESTADO_DEUDA = $this->dws['dw_deuda']->get_item(0, 'COD_ESTADO_DEUDA');
			//graba rebajas
			for ($i = 0; $i < $this->dws['dw_deuda_pagares']->row_count(); $i++) {
				$operacion		 = 'REBAJA_PAGARE_DEUDA';
				$cod_pagare		 = $this->dws['dw_deuda_pagares']->get_item($i, 'COD_PAGARE_H');
				$vl_fecha_pagare = "null";
				$vl_folio_pagare = "null";
				$vl_monto_moneda = "null";
				$vl_monto_pesos  = "null";
				$vl_estado_pagare  = "null";
				$vl_cod_deuda    = "null";
				$vl_cod_usuario  = $this->cod_usuario;
				$vl_fecha_protesto = "null";
				$vl_cod_usuario_protesto = "null";
				$cod_tipo_rebaja_pagare  = $this->dws['dw_deuda_pagares']->get_item($i, 'COD_TIPO_REBAJA_PAGARE');
	        	$monto_moneda_rebaja	 = $this->dws['dw_deuda_pagares']->get_item($i, 'MONTO_MONEDA_REBAJA');
	        	$monto_pesos_rebaja		 = $this->dws['dw_deuda_pagares']->get_item($i, 'MONTO_PESOS_REBAJA');
 				
	        	$cod_tipo_rebaja_pagare = ($cod_tipo_rebaja_pagare=='') ? "null" : $cod_tipo_rebaja_pagare;
	        	$monto_moneda_rebaja 	= ($monto_moneda_rebaja=='') ? "null" : $monto_moneda_rebaja;
	        	$monto_pesos_rebaja 	= ($monto_pesos_rebaja=='') ? "null" : $monto_pesos_rebaja;
	        	
	        	if($COD_ESTADO_DEUDA == self::K_ESTADO_DEUDA_NO_EXIGIBLE){
					$param_p = "'$operacion'			,$cod_pagare		,$vl_fecha_pagare			,$vl_folio_pagare
								,$vl_monto_moneda		,$vl_monto_pesos	,$vl_estado_pagare			,$vl_cod_deuda
								,$vl_cod_usuario		,$vl_fecha_protesto	,$vl_cod_usuario_protesto	,$cod_tipo_rebaja_pagare
								,$monto_moneda_rebaja	,$monto_pesos_rebaja";
					
					if (!$db->EXECUTE_SP('spu_pagare', $param_p))
		        		return false;	
				}		
	        }
	        
			if (!$this->dws['dw_deuda_cruzados']->update($db,$COD_DEUDA,$vl_cod_usuario)){
				return false;
			}
	        
	        return true;
		}
		return false;
	}
	
	function print_record() {
		$cod_deuda = $this->get_key();
		$COD_ESTADO_DEUDA = $this->dws['dw_deuda']->get_item(0, 'COD_ESTADO_DEUDA');
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		if($COD_ESTADO_DEUDA == self::K_ESTADO_DEUDA_NO_EXIGIBLE){
			
			$sql= sql_deuda::get_Sql('PRINT_RECORD', $cod_deuda);
			$result	= $db->build_results($sql);
			$hora	= $result[0]['HORA_IMPRESO'];
			$fecha  = $result[0]['FECHA_IMPRESO'];
			// reporte
			$labels = array();
			$labels['strCOD_DEUDA'] = $cod_deuda;
			$labels['strHORA_FECHA'] = $fecha.' '.$hora;
			$rpt = new reporte_helen($sql, $this->root_dir.'appl/deuda/deuda_no_exigible.xml', $labels, "Situaci�n Deuda".$cod_deuda,1);
		}
		else if($COD_ESTADO_DEUDA == self::K_ESTADO_DEUDA_EXIGIBLE ){
			$sql= sql_deuda::get_Sql('PRINT_RECORD_1', $cod_deuda);
			$result	= $db->build_results($sql);
			$hora	= $result[0]['HORA_IMPRESO'];
			$fecha  = $result[0]['FECHA_IMPRESO'];
			// reporte
			$labels = array();
			$labels['strCOD_DEUDA'] = $cod_deuda;
			$labels['strHORA_FECHA'] = $fecha.' '.$hora;
			
			$rpt = new reporte_helen($sql, $this->root_dir.'appl/deuda/deuda_exigible.xml', $labels, "Situaci�n Deuda".$cod_deuda,1);
		}
	
		else {
			$sql= sql_deuda::get_Sql('PRINT_RECORD_2', $cod_deuda);
			$result	= $db->build_results($sql);
			$hora	= $result[0]['HORA_IMPRESO'];
			$fecha  = $result[0]['FECHA_IMPRESO'];
			// reporte
			$labels = array();
			$labels['strCOD_DEUDA'] = $cod_deuda;
			$labels['strHORA_FECHA'] = $fecha.' '.$hora;
			$rpt = new reporte_helen($sql, $this->root_dir.'appl/deuda/deuda_otros.xml', $labels, "Situaci�n Deuda".$cod_deuda,1);
		}
		
		$this->redraw();		
	}
	function create_boleta() {
		$rut = $this->dws['dw_deuda']->get_item(0, 'RUT');
		$cod_deuda = $this->dws['dw_deuda']->get_item(0, 'COD_DEUDA');
		session::set("wi_deuda.create_boleta", $rut.'|'.$cod_deuda);
		$url = $this->root_url.'../../commonlib_seven/trunk/php/link_wi.php?modulo_origen=deuda&modulo_destino=boleta&cod_modulo_destino='.K_NEW_RECORD.'&cod_item_menu=1505';
		header ('Location: '.$url);
	}
	function devolucion_pagare() {
		$devoluion_a = $_POST['wi_hidden'];
		$dato = explode('|', $devoluion_a);
		
		$nombre_receptor = $dato[0];
		$rut_receptor = $dato[1];
		$motivo = $dato[2];

				
		$cod_deuda = $this->get_key();
		$cod_usuario = $this->cod_usuario;
		
		if($devoluion_a != ''){
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);		
			$sp = 'spu_deuda';
			$param = "'DEVOLUCION', $cod_deuda,null,null,null,null,null,$cod_usuario,'$nombre_receptor','$rut_receptor','$motivo'";
	
			$db->BEGIN_TRANSACTION();
			if ($db->EXECUTE_SP($sp, $param)){
				$db->COMMIT_TRANSACTION();
				
				$sql= sql_deuda::get_Sql('PRINT_DEVUELTO', $cod_deuda);
				$labels = array();
        		$labels['strCOD_DEUDA'] = $cod_deuda;
        		$rpt = new reporte_helen($sql, $this->root_dir.'appl/deuda/devolucion_pagare.xml', $labels, "Devoluci�n de pagar�s".$cod_deuda,1);
     
				$this->_load_record();
				return true;
			}else {
				$db->ROLLBACK_TRANSACTION();
				return false;	
			}
		}else{
			$this->_load_record();
		}
	}
	function procesa_event() {
		if(isset($_POST['b_create_boleta_x']))  {
			$this->create_boleta();
		}
		else if(isset($_POST['b_devolucion_pagare_x']))  {
			$this->devolucion_pagare();
		}
		else
			parent::procesa_event();
	}
}

class dw_deuda_plan_pago extends datawindow{
	function dw_deuda_plan_pago() {
		$sql = sql_deuda::get_sql('DW_DEUDA_PLAN_PAGO');

		parent::datawindow($sql, 'DEUDA_PLAN_PAGO', true, true);
		$this->add_control(new static_link('ANO_COBRO', '../../../../commonlib_seven/trunk/php/link_wi.php?modulo_origen=deuda&modulo_destino=plan_pago&cod_modulo_destino=[ANO_COBRO]&cod_item_menu=1020'));
		$this->add_control(new computed('CUOTA_ANUAL',4));
		$this->add_control(new computed('MONTO_CAPITAL',4));
		$this->add_control(new computed('MONTO_INTERES',4));
		$this->add_control(new computed('ABONO',4));
		$this->add_control(new computed('DESCUENTOS',4));
		$this->add_control(new computed('SALDO_K',4));
		$this->add_control(new computed('MULTA',4));
		$this->add_control(new computed('SALDO_VENCIDO',4));
		
		$this->add_control(new computed('SALDO_PESOS',0));
		
		$this->add_control($control = new edit_check_box('PUBLICA_DICOM', 'S', 'N'));
		$control->set_onChange("cambio_dicom(this);");
		$this->add_control(new edit_text_upper('COD_ESTADO_PLAN_PAGO_H', 10, 10, 'hidden'));
		$this->add_control(new edit_text_upper('COD_PLAN_PAGO_H', 10, 10, 'hidden'));
		$this->add_control(new edit_text_upper('PUBLICA_DICOM_H', 10, 10, 'hidden'));
		
		$this->add_control(new static_text('NOM_EMPRESA_COBRANZA'));
		
		$this->accumulate('CUOTA_ANUAL', '', false);
		$this->accumulate('MONTO_CAPITAL', '', false);
		$this->accumulate('MONTO_INTERES', '', false);
		$this->accumulate('ABONO', '', false);
		$this->accumulate('DESCUENTOS', '', false);
		$this->accumulate('SALDO_K', '', false);
		$this->accumulate('MULTA', '', false);
		$this->accumulate('SALDO_VENCIDO', '', false);

		$this->accumulate('SALDO_PESOS', '', false);
		
	}
}

/*************************************		PAGAR�S			************************************/

class dw_deuda_pagares extends datawindow{
	function dw_deuda_pagares() {
		$sql = sql_deuda::get_sql('DW_DEUDA_PAGARES');

		parent::datawindow($sql, 'DEUDA_PAGARES', true, true);
		
		
		/*crear otro control edit_text_hidden pero para el campo COD_ESTADO_PAGARE_H
		
		dejar en el htm la variable*/
		$this->add_control(new edit_text_hidden('COD_ESTADO_PAGARE_H',10,10));
		$this->add_control(new edit_text_hidden('COD_PAGARE_H',10,10));
		
		$this->add_control(new static_link('FOLIO_PAGARE', '../../../../commonlib_seven/trunk/php/link_wi.php?modulo_origen=deuda&modulo_destino=pagare&cod_modulo_destino=[FOLIO_PAGARE]&cod_item_menu=1015'));
		
		$sql_tipo_rebaja_pagare = sql_deuda::get_sql('DD_COD_TIPO_REBAJA_PAGARE');
		
		$this->add_control(new drop_down_dw('COD_TIPO_REBAJA_PAGARE', $sql_tipo_rebaja_pagare, 100));
		$this->add_control(new edit_num('MONTO_MONEDA_REBAJA',15,10,4,false));
		
		$this->add_control(new edit_num('MONTO_PESOS_REBAJA',15,10,false));
		$this->add_control(new static_num('MONTO_MONEDA',4));
		$this->add_control(new static_num('MONTO_PESOS',0));
		
		$this->add_control(new computed('PAGARE_MONTO_MONEDA',4));
		$this->add_control(new computed('PAGARE_MONTO_PESOS',0));

		$this->accumulate('PAGARE_MONTO_MONEDA', '', false);
		$this->accumulate('PAGARE_MONTO_PESOS', '', false);
		$this->accumulate('MONTO_MONEDA_REBAJA', '', false);
		$this->accumulate('MONTO_PESOS_REBAJA', '', false);
		$this->accumulate('MONTO_MONEDA', '', false);
		$this->accumulate('MONTO_PESOS', '', false);
	}
}

class dw_deuda_abonos extends datawindow{
	const K_COMPONENTE_NO_EXIGIBLE = 1;
	const K_ESTADO_PAGO_VIGENTE = 1;
	
	function dw_deuda_abonos() {
		$sql = sql_deuda::get_sql('DW_DEUDA_ABONOS');
		
		parent::datawindow($sql, 'DEUDA_ABONOS', true, true);
		
		$this->add_control(new static_num('ABONO_MONTO_MONEDA',4));
		$this->add_control(new static_num('ABONO_MONTO_PESOS',0));

		// Se agregan estos controls, pero no es necesario agregarlos al htm
		$this->add_control(new static_num('ABONO_MONTO_MONEDA_V',4));
		$this->add_control(new static_num('ABONO_MONTO_PESOS_V',0));
		$this->add_control(new static_link('COD_ABONO', '../../../../commonlib_seven/trunk/php/link_wi.php?modulo_origen=deuda&modulo_destino=abono&cod_modulo_destino=[COD_ABONO]&cod_item_menu=1025'));
		
		$this->accumulate('ABONO_MONTO_MONEDA_V', '', false);
		$this->accumulate('ABONO_MONTO_PESOS_V', '', false);
	}
}

class dw_deuda_descuentos extends datawindow{
	const K_COMPONENTE_NO_EXIGIBLE = 1;
	const K_ESTADO_PAGO_VIGENTE = 1;
	
	function dw_deuda_descuentos() {
		$sql = sql_deuda::get_sql('DW_DEUDA_DESCUENTOS');

		parent::datawindow($sql, 'DEUDA_DESCUENTOS', true, true);
		
		$this->add_control(new static_num('DESCUENTO_MONTO_MONEDA',4));
		$this->add_control(new static_num('DESCUENTO_MONTO_PESOS',0));

		// Se agregan estos controls, pero no es necesario agregarlos al htm
		$this->add_control(new static_num('DESCUENTO_MONTO_MONEDA_V',4));
		$this->add_control(new static_num('DESCUENTO_MONTO_PESOS_V',0));
		$this->add_control(new static_link('COD_DESCUENTO', '../../../../commonlib_seven/trunk/php/link_wi.php?modulo_origen=deuda&modulo_destino=descuento&cod_modulo_destino=[COD_DESCUENTO]&cod_item_menu=1030'));
		
		$this->accumulate('DESCUENTO_MONTO_MONEDA_V', '', false);
		$this->accumulate('DESCUENTO_MONTO_PESOS_V', '', false);
	}
}

/*************************************		CRUZADOS			****************************************/

class dw_deuda_cruzados extends datawindow{
	function dw_deuda_cruzados() {
		$sql = sql_deuda::get_sql('DW_DEUDA_CRUZADO');

		parent::datawindow($sql, 'DEUDA_CRUZADOS', true, true);
		
		$this->add_control(new edit_text('COD_CRUZADO',10, 10, 'hidden'));
		$sql_uni = "select COD_UNIVERSIDAD, NOM_UNIVERSIDAD from  UNIVERSIDAD order by COD_UNIVERSIDAD desc";
		$this->add_control(new drop_down_dw('CRUZADO_COD_UNIVERSIDAD', $sql_uni, 300));
		$this->add_control(new static_num('PORC', 2));
		$this->add_control(new edit_text('PORC_H', 10,10, 'hidden'));
		$this->add_control(new edit_date('FECHA_CONSOLIDACION', 10, 10));
		
		$this->add_control($control = new edit_num('MONTO_ACTUALIZADO', 10, 8, 4));
		$control->set_onChange("sum_total_montos();");
	}
	function fill_record(&$temp, $record) {
        parent::fill_record($temp, $record);
        $row = $this->redirect($record);
        if ($this->entrable)
            $eliminar = '<img src="../../../../commonlib_seven/trunk/images/b_delete_line.jpg" onClick="del_line_cruzado(\''.$this->label_record.'_'.$row.'\', \''.$this->nom_tabla.'\');" style="cursor:pointer"/>';
        else
            $eliminar = '<img src="../../../../commonlib_seven/trunk/images/b_delete_line_d.jpg"/>';
        $temp->setVar($this->label_record.".ELIMINAR_".strtoupper($this->label_record), $eliminar);
    }
	function update($db, $cod_deuda,$cod_usuario)	{
		$sp = 'spu_deuda_cruzados';
		
		for ($i = 0; $i < $this->row_count(); $i++) {
			$statuts = $this->get_status_row($i);
			if ($statuts == K_ROW_NOT_MODIFIED || $statuts == K_ROW_NEW)
				continue;

			$cod_cruzado	   = $this->get_item($i, 'COD_CRUZADO');
			$cod_universidad   = $this->get_item($i, 'CRUZADO_COD_UNIVERSIDAD');
			$monto_actualizado = $this->get_item($i, 'MONTO_ACTUALIZADO');
			$porc			   = $this->get_item($i, 'PORC_H');
			$fecha_consolidacion = $this->get_item($i, 'FECHA_CONSOLIDACION');
			$cod_cruzado 	= ($cod_cruzado == '') ? "null" : $cod_cruzado;
			
			if ($fecha_consolidacion != "null"){
                $fecha_consolidacion = $this->str2date($fecha_consolidacion);
            }
			
			if($statuts == K_ROW_NEW_MODIFIED){
				$operacion = 'INSERT';
				
			}elseif($statuts == K_ROW_MODIFIED){
				$operacion = 'UPDATE';
			}

			$param	 = "'$operacion'
						,$cod_cruzado
						,$cod_deuda
						,$cod_universidad
						,$monto_actualizado
						,$porc
						,$fecha_consolidacion";

			if (!$db->EXECUTE_SP($sp, $param))
				return false;
		}
		
		for ($i = 0; $i < $this->row_count('delete'); $i++){
			$statuts_d = $this->get_status_row($i, 'delete');
			if ($statuts_d == K_ROW_NEW || $statuts_d == K_ROW_NEW_MODIFIED){
				continue;
			}
			$cod_cruzado   = $this->get_item($i, 'COD_CRUZADO', 'delete');
			
			if (!$db->EXECUTE_SP($sp, "'DELETE', $cod_cruzado, $cod_usuario")){
				return false;
			}
		}
		return true;
	}
}

class dw_deuda_cruzados_interno extends datawindow{
	function dw_deuda_cruzados_interno() {
		$sql = sql_deuda::get_sql('DW_DEUDA_CRUZADO_INTERNO');

		parent::datawindow($sql, 'DEUDA_CRUZADOS_INTERNO', true, true);
		
		$this->add_control(new computed('MONTO_ACTUALIZADO_INT',4));
		$this->add_control(new computed('PORC_INT',2));
		
		$this->accumulate('MONTO_ACTUALIZADO_INT', '', false);
		$this->accumulate('PORC_INT', '', false);
	}
}
/////////////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wi_deuda.php";
if (file_exists($file_name)) 
	require_once($file_name);
else {
	class wi_deuda extends wi_deuda_base {
		function wi_deuda($cod_item_menu) {
			parent::wi_deuda_base($cod_item_menu); 
		}
	}
}
?>