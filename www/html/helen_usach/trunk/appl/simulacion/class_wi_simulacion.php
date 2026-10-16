<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../ingreso/class_wi_ingreso.php");
require_once(dirname(__FILE__)."/../ingreso/load_cuotas.php");


class wi_simulacion_base extends wi_ingreso{
    
	const K_ESTADO_SIMULACION		= 1000;
	
	function __construct($cod_item_menu){
	    
	    parent::__construct($cod_item_menu);
		w_input::__construct('simulacion', $cod_item_menu);
		
		
		$this->dws["dw_documento_pago"]->remove_control("NRO_DOCUMENTO_PAGO");
		$this->dws["dw_documento_pago"]->remove_control("FECHA_DOCUMENTO_PAGO");
		
		$this->dws["dw_documento_pago_ch_fecha"]->remove_control("NRO_DOCUMENTO_PAGO_CH_FECH");
		$this->dws["dw_documento_pago_ch_fecha"]->remove_control("RUT_GIRADOR");
		$this->dws["dw_documento_pago_ch_fecha"]->remove_control("DIG_VERIF_GIRADOR");
		
		$this->dws['dw_ingreso']->add_control(new edit_text('TASA_INTERES', 10, 10, 'hidden'));
		$this->dws['dw_ingreso']->add_control(new edit_text('SUMA_MONTO_CH_FECHA', 20, 20, 'hidden'));
		$this->dws['dw_ingreso']->add_control(new edit_text('STATIC_MONTO_INTERES2', 20, 20, 'hidden'));
		$this->dws['dw_ingreso']->add_control(new edit_text('POR_PAGAR2', 20, 20, 'hidden'));
	
		$this->remove_auditoria('COD_ESTADO_INGRESO');
	}
	function load_wo() {
		$this->wo = unserialize(session::get("wo_simulacion"));
	}
	function get_item_wo($record, $field) {
		// Cuando viene de simulacion en el output esta COD_SIMULACION en vez de COD_INGRESO
		return parent::get_item_wo($this->current_record, 'COD_SIMULACION');
	}
	
	function new_record() {
		parent::new_record();

		$this->dws['dw_ingreso']->set_item(0, 'COD_ESTADO_INGRESO', self::K_ESTADO_SIMULACION);

		//validez de la SIMULACIï¿½N
		$validez_simulacion = $this->get_parametro(self::K_PARAM_MAX_VALIDEZ_SIMULACION);
		$this->dws['dw_ingreso']->set_item(0, 'VALIDEZ_SIMULACION', $validez_simulacion);
		
		if (session::is_set("CREADA_DESDE_DEUDOR")) {
			$this->b_modify_visible	 	= false;
			$cod_deudor = session::get("CREADA_DESDE_DEUDOR");
			session::un_set("CREADA_DESDE_DEUDOR");
			$this->crear_desde_deudor($cod_deudor);
		}

		unset($this->dws['dw_ingreso']->controls['FECHA_INGRESO']);
		$this->dws['dw_ingreso']->add_control(new static_text('FECHA_INGRESO'));

		unset($this->dws['dw_ingreso']->controls['RUT']);
		$this->dws['dw_ingreso']->add_control($control = new edit_num('RUT', 10, 10));
        $control->set_onChange("selecciona_pago(this); alert_doc_protestado();");
		
	}
	function crear_desde_deudor($cod_deudor) {
		$this->set_first_focus('REFERENCIA');		
		
		$this->dws['dw_ingreso']->set_entrable('RUT', false);
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		if(K_TIPO_BD == "oci"){
			$sql = "select 	RUT
							,DIG_VERIF
							,COD_DEUDOR
							,NOMBRE||' '||APELLIDO_PATERNO||' '||APELLIDO_MATERNO NOMBRE
							,f_mon_get_valor_moneda(3, sysdate)  VALOR_MONEDA
					FROM 	DEUDOR 
					WHERE 	COD_DEUDOR = $cod_deudor";
			
		}
		else if(K_TIPO_BD == "mssql"){
			$sql = "select 	RUT
							,DIG_VERIF
							,COD_DEUDOR
							,NOMBRE+' '+APELLIDO_PATERNO+' '+APELLIDO_MATERNO NOMBRE
							,dbo.f_mon_get_valor_moneda(3, getdate())  VALOR_MONEDA
					FROM 	DEUDOR 
					WHERE 	COD_DEUDOR = $cod_deudor";
		}			
					
		$result = $db->build_results($sql);
		$this->dws['dw_ingreso']->set_item(0, 'RUT', $result[0]['RUT']);
		$this->dws['dw_ingreso']->set_item(0, 'RUT_H', $result[0]['RUT']);
		$this->dws['dw_ingreso']->set_item(0, 'COD_DEUDOR', $result[0]['COD_DEUDOR']);
		$this->dws['dw_ingreso']->set_item(0, 'DIG_VERIF', $result[0]['DIG_VERIF']);
		$this->dws['dw_ingreso']->set_item(0, 'NOMBRE', $result[0]['NOMBRE']);
		$this->dws['dw_ingreso']->set_item(0, 'VALOR_MONEDA', $result[0]['VALOR_MONEDA']);
		
		$rut = $result[0]['RUT'];
		$result = load_cuotas::get_cuotas($rut);

		if($result == 'DEUDOR_SIN_DEUDAS'){
			$this->redraw();
			$this->message('El deudor no tiene deudas en cobranza.');
		}else{
			$result_final = array();
			for ($i=0; $i<count($result); $i++) {
				if ($result[$i]['SALDO_TOTAL'] > 0){
					$row = $this->dws['dw_item_ingreso']->insert_row();
					$this->dws['dw_item_ingreso']->set_item($row, 'SELECCION', 'N');
					$this->dws['dw_item_ingreso']->set_item($row, 'COD_DEUDA', $result[$i]['COD_DEUDA']);
					$this->dws['dw_item_ingreso']->set_item($row, 'COD_DEUDA_H', $result[$i]['COD_DEUDA']);
					$this->dws['dw_item_ingreso']->set_item($row, 'COD_TIPO_DEUDA', $result[$i]['COD_TIPO_DEUDA']);
					$this->dws['dw_item_ingreso']->set_item($row, 'COD_FAMILIA_DEUDA_H', $result[$i]['COD_FAMILIA_DEUDA_H']);
					$this->dws['dw_item_ingreso']->set_item($row, 'SIMBOLO', $result[$i]['SIMBOLO']);
					$this->dws['dw_item_ingreso']->set_item($row, 'COD_PLAN_PAGO', $result[$i]['COD_PLAN_PAGO']);
					$this->dws['dw_item_ingreso']->set_item($row, 'ANO_COBRO', $result[$i]['ANO_COBRO']);
					$this->dws['dw_item_ingreso']->set_item($row, 'FECHA_VENCTO', $result[$i]['FECHA_VENCTO']);
					$this->dws['dw_item_ingreso']->set_item($row, 'SALDO_CUOTA', $result[$i]['SALDO_CUOTA']);
					$this->dws['dw_item_ingreso']->set_item($row, 'SALDO_CUOTA_H', $result[$i]['SALDO_CUOTA']);
					$this->dws['dw_item_ingreso']->set_item($row, 'SALDO_MULTA', $result[$i]['SALDO_MULTA']);
					$this->dws['dw_item_ingreso']->set_item($row, 'SALDO_MULTA_H', $result[$i]['SALDO_MULTA']);
					$this->dws['dw_item_ingreso']->set_item($row, 'SALDO_TOTAL_SIN_DESCTO_H', $result[$i]['SALDO_TOTAL']);
					$this->dws['dw_item_ingreso']->set_item($row, 'SALDO_TOTAL', $result[$i]['SALDO_TOTAL']);
					$this->dws['dw_item_ingreso']->set_item($row, 'SALDO_TOTAL_H', $result[$i]['SALDO_TOTAL']);
					$this->dws['dw_item_ingreso']->set_item($row, 'SALDO_TOTAL_PESOS', $result[$i]['SALDO_TOTAL_PESOS']);
					$this->dws['dw_item_ingreso']->set_item($row, 'SALDO_TOTAL_PESOS_H', $result[$i]['SALDO_TOTAL_PESOS']);
					$this->dws['dw_item_ingreso']->set_item($row, 'MONTO_PAGO', $result[$i]['MONTO_PAGO']);
					$this->dws['dw_item_ingreso']->set_item($row, 'MONTO_PAGO_H', $result[$i]['MONTO_PAGO']);
					$this->dws['dw_item_ingreso']->set_item($row, 'MONTO_PAGO_PESOS', $result[$i]['MONTO_PAGO_PESOS']);
					$this->dws['dw_item_ingreso']->set_item($row, 'MONTO_PAGO_PESOS_H', $result[$i]['MONTO_PAGO_PESOS']);
					$this->dws['dw_item_ingreso']->set_item($row, 'DISPLAY_TR', $result[$i]['DISPLAY_TR']);
					$result_final[] = $result[$i];
				}
				
			}
			if (count($result_final)==0) {
				session::set('SIMULACION.MENSAJE', 'El deudor no tiene deudas en cobranza.');
			}
		}
	
	
	}
	function redraw() {
		parent::redraw();
		if(session::is_set('SIMULACION.MENSAJE')){
			$this->alert(session::get('SIMULACION.MENSAJE'));
			session::un_set('SIMULACION.MENSAJE');
		}
		if(session::is_set('SIMULACION.MENSAJE2')){
			$this->alert(session::get('SIMULACION.MENSAJE2'));
			session::un_set('SIMULACION.MENSAJE2');
		}
		
	}
	function habilitar(&$temp, $habilita) {
		if (!$this->is_new_record()) {
			$control = '<input name="b_create_ingreso" id="b_create_ingreso" src="../../images_appl/b_create_ingreso.jpg" type="image" '.
								 'onMouseDown="MM_swapImage(\'b_create_ingreso\',\'\',\'../../images_appl/b_create_ingreso_click.jpg\',1)" '.
								 'onMouseUp="MM_swapImgRestore()" onMouseOut="MM_swapImgRestore()" '.
								 'onMouseOver="MM_swapImage(\'b_create_ingreso\',\'\',\'../../images_appl/b_create_ingreso_over.jpg\',1)" />';
			$temp->setVar("WSIMULACION_CREA_INGRESO", $control);
		}
	}
	function create_ingreso() {
		$cod_simulacion = $this->dws['dw_ingreso']->get_item(0, 'COD_INGRESO');
		
		$cod_ingreso_simulacion = $this->dws['dw_ingreso']->get_item(0, 'COD_INGRESO_SIMULACION');
		if ($cod_ingreso_simulacion != ''){
			session::set('SIMULACION.MENSAJE2', 'La simulación ya tiene un ingreso creado.');
			$this->redraw();
		}else{
			session::set("CREADA_DESDE_SIMULACION", $cod_simulacion);
			$url = $this->root_url.'../../commonlib_seven/trunk/php/link_wi.php?modulo_origen=simulacion&modulo_destino=ingreso&cod_modulo_destino='.K_NEW_RECORD.'&cod_item_menu=1515';
			header ('Location: '.$url);
		}
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
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wi_simulacion.php";
if (file_exists($file_name)) 
	require_once($file_name);
else {
	class wi_simulacion extends wi_simulacion_base {
		function __construct($cod_item_menu) {
		    parent::__construct($cod_item_menu); 
		}
	}
}
?>