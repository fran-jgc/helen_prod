<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class dw_tab_subcuota_detalle extends datawindow{
    
	const K_ESTADO_DEUDA_NO_EXIGIBLE	= 1;
	const K_EST_PP_EMITIDA				= 1;
	const K_EST_PP_MOROSA				= 4;
	
	const K_COMP_NO_EXIGIBLE			= 1;
	const K_COMP_VENCIDA				= 2;
	const K_COMP_VIGENTE 				= 3;
	const K_COMP_FUTURA 				= 4;
	
	function __construct() {
		
		$sql = sql_boleta::get_sql('DW_TAB_SUBCUOTA_DETALLE');

		parent::__construct($sql, 'TAB_SUBCUOTA_DETALLE', true, true);
		
		$this->add_control(new static_text('COD_SUB_BOLETA'));
		$this->add_control(new edit_text('SC_COD_SUB_BOLETA_H', 10,10, 'hidden'));
		$this->add_control(new static_num('SC_NRO_SUB_BOLETA'));
		$this->add_control(new edit_text('SC_NRO_SUB_BOLETA_H', 10,10, 'hidden'));
		$this->add_control(new static_text('SC_FECHA_VENCTO'));
		$this->add_control(new edit_text('SC_FECHA_VENCTO_H', 10,10, 'hidden'));
		$this->add_control(new static_num('SC_MONTO_CAPITAL',4));
		$this->add_control(new edit_text('SC_MONTO_CAPITAL_H', 10,10, 'hidden'));
		$this->add_control(new static_num('SC_MONTO_INTERES', 4));
		$this->add_control(new edit_text('SC_MONTO_INTERES_H', 10,10, 'hidden'));
		$this->add_control(new static_num('SC_MONTO_MULTA', 4));
		$this->add_control(new edit_text('SC_MONTO_MULTA_H', 10,10, 'hidden'));
		$this->add_control(new static_num('SC_TOTAL', 4));
		$this->add_control(new static_text('SC_FECHA_PAGO', 4));
		$this->add_control(new edit_text('SC_FECHA_PAGO_H', 10,10, 'hidden'));

		// sumas
		$this->set_computed('MONTO_CAPITAL_C', '[SC_MONTO_CAPITAL]',4);
		$this->controls['MONTO_CAPITAL_C']->type = 'hidden';
		$this->accumulate('MONTO_CAPITAL_C');

		$this->set_computed('MONTO_INTERES_C', '[SC_MONTO_INTERES]',4);
		$this->controls['MONTO_INTERES_C']->type = 'hidden';
		$this->accumulate('MONTO_INTERES_C');

		$this->set_computed('MONTO_MULTA_C', '[SC_MONTO_MULTA]',4);
		$this->controls['MONTO_MULTA_C']->type = 'hidden';
		$this->accumulate('MONTO_MULTA_C');

		$this->set_computed('TOTAL_C', '[SC_TOTAL]',4);
		$this->controls['TOTAL_C']->type = 'hidden';
		$this->accumulate('TOTAL_C');
	}
	function update($db){
		// Asume que al menos existe una subboleta
		$cod_boleta		= $this->get_item(0, 'COD_BOLETA');
		
		$sql	= sql_boleta::get_sql('SELECT_BOLETA',$cod_boleta);
		$result 				= $db->build_results($sql);
		$cod_estado_deuda		= $result[0]['COD_ESTADO_DEUDA'];
		$cod_plan_pago 			= $result[0]['COD_PLAN_PAGO'];
		$cod_estado_plan_pago	= $result[0]['COD_ESTADO_PLAN_PAGO'];
		$cod_plan_pago_vig 		= $result[0]['COD_PLAN_VIGENTE'];
		
		$sp = 'spu_sub_boleta ';
		for ($i = 0; $i < $this->row_count(); $i++) {

			$statuts = $this->get_status_row($i);

			if ($statuts != K_ROW_NEW_MODIFIED){
				continue;
			}
			$operacion 		= 'INSERT';
			$cod_boleta		= $this->get_item($i, 'COD_BOLETA');
			$cod_sub_boleta = $this->get_item($i, 'SC_COD_SUB_BOLETA_H');
			$nro_sub_boleta = $this->get_item($i, 'SC_NRO_SUB_BOLETA_H');
			$fecha_vencto   = $this->get_item($i, 'SC_FECHA_VENCTO_H');
			$monto_capital  = $this->get_item($i, 'SC_MONTO_CAPITAL_H');
			$monto_interes  = $this->get_item($i, 'SC_MONTO_INTERES_H');
			$monto_multa    = $this->get_item($i, 'SC_MONTO_MULTA_H');
			$fecha_pago		= $this->get_item($i, 'SC_FECHA_PAGO_H');
			
			$cod_sub_boleta = ($cod_sub_boleta =='') ? "null" : $cod_sub_boleta;
			$nro_sub_boleta	= ($nro_sub_boleta =='') ? "null" : $nro_sub_boleta;
			
			$fecha_pago		= ($fecha_pago =='') ? "null" : "$fecha_pago";
			if ($fecha_pago != "null")
				$fecha_pago 	= $this->str2date($fecha_pago);
					
			$fecha_vencto 	= $this->str2date($fecha_vencto, '23:59:59');
			
			$monto_multa	= ($monto_multa =='') ? 0 : "$monto_multa";
			
			if ($cod_estado_deuda == self::K_ESTADO_DEUDA_NO_EXIGIBLE)
				$cod_comp_deuda = self::K_COMP_NO_EXIGIBLE;	
			elseif ($cod_estado_plan_pago == self::K_EST_PP_MOROSA)
				$cod_comp_deuda = self::K_COMP_VENCIDA;
			elseif($cod_plan_pago == $cod_plan_pago_vig)
				$cod_comp_deuda = self::K_COMP_VIGENTE;
			else			// cualquier otro caso es FUTURA
				$cod_comp_deuda = self::K_COMP_FUTURA;
			$param = "'$operacion'
					,$cod_sub_boleta
					,$cod_boleta
					,$nro_sub_boleta
					,$fecha_vencto
					,$monto_capital
					,$monto_interes
					,$monto_multa
					,$fecha_pago
					,$cod_comp_deuda";
					
			if (!$db->EXECUTE_SP($sp, $param)){
				return false;
			}
		}
		return true;
	}
}
class dw_tab_subcuota extends datawindow{
    
    function __construct() {
        
		$sql = sql_boleta::get_sql('DW_TAB_SUBCUOTA');
		parent::__construct($sql, 'TAB_SUBCUOTA', true, true);
		
		$this->add_control(new static_text('FECHA_PRIMERA_CUOTA'));
		$this->add_control(new edit_text('FECHA_PRIMERA_CUOTA_H', 10,10, 'hidden'));
		$this->add_control($control = new edit_num('CANT', 10, 10));
		$control->set_onChange("get_max_subcuota();division_tipo_otro();");
		
		//MANDATORY
		$this->set_mandatory('FECHA_PRIMERA_CUOTA', 'Fecha Primera Cuota');
		$this->set_mandatory('CANT', 'Cantidad de Cuotas');
	}
}
class dw_subcuota extends datawindow{
    
    function __construct() {
		
		$sql = sql_boleta::get_sql('DW_SUBCUOTA');

		parent::__construct($sql, 'SUBCUOTA', true, true);

		$this->add_control(new edit_text('D_RECORD_COD_PLAN_PAGO_H', 10,10, 'hidden'));
		$this->add_control(new edit_text('D_FECHA_VENCE_PLAN_H', 10,10, 'hidden'));
		$this->add_control(new edit_text('D_ANO_COBRO_H', 10,10, 'hidden'));
		
		$this->add_control(new static_num('D_SALDO_CAPITAL', 4));
		$this->add_control(new static_num('D_SALDO_INTERES', 4));
		$this->add_control(new static_num('D_SALDO_MULTA', 4));
		$this->add_control(new static_num('D_SALDO_MONEDA', 4));
		$this->add_control(new static_num('D_SALDO_PESOS'));

		$this->add_control(new static_num('D_MONTO_CAPITAL', 4));
		$this->add_control(new edit_text('D_MONTO_CAPITAL_H', 10,10, 'hidden'));
		$this->add_control(new static_num('D_MONTO_INTERES', 4));
		$this->add_control(new edit_text('D_MONTO_INTERES_H', 10,10, 'hidden'));
		$this->add_control(new static_num('D_MONTO_MULTA', 4));
		$this->add_control(new edit_text('D_MONTO_MULTA_H', 10,10, 'hidden'));
		$this->add_control(new static_num('D_MONTO_MONEDA', 4));
		$this->add_control(new edit_text('D_MONTO_MONEDA_H', 10,10, 'hidden'));
		
		$this->add_control($control=new edit_num('D_MONTO_PESOS', 10));
		$control->set_onChange("checked_radio_button(null);");

		$sql = "select COD_TIPO_DESCUENTO
						,NOM_TIPO_DESCUENTO
				from TIPO_DESCUENTO
				order by COD_TIPO_DESCUENTO";
		$this->add_control($control = new drop_down_dw('D_COD_TIPO_DESCUENTO', $sql));
		$control->set_onChange("checked_radio_button(null);");
		
		$this->add_control(new edit_text('D_MONTO_PESOS_H', 10,10, 'hidden'));
		$this->add_control(new static_num('D_DSCTO_CAPITAL', 4));
		$this->add_control(new edit_text('D_DSCTO_CAPITAL_H', 10,10, 'hidden'));
		$this->add_control(new static_num('D_DSCTO_INTERES', 4));
		$this->add_control(new edit_text('D_DSCTO_INTERES_H', 10,10, 'hidden'));
		$this->add_control(new static_num('D_DSCTO_MULTA', 4));
		$this->add_control(new edit_text('D_DSCTO_MULTA_H', 10,10, 'hidden'));
		$this->add_control(new static_num('D_DSCTO_PESOS'));
		$this->add_control(new edit_text('D_DSCTO_PESOS_H', 10,10, 'hidden'));

		$this->set_computed('D_SALDO_MONEDA', '[D_SALDO_CAPITAL] + [D_SALDO_INTERES] + [D_SALDO_MULTA]', 4);
		$this->set_computed('D_DSCTO_MONEDA', '[D_DSCTO_CAPITAL] + [D_DSCTO_INTERES] + [D_DSCTO_MULTA]', 4);

		$this->set_computed('D_TOTAL_MONEDA', '[D_MONTO_MONEDA] - [D_DSCTO_MONEDA]', 4);
		$this->set_computed('D_TOTAL_PESOS', '[D_MONTO_PESOS] - [D_DSCTO_PESOS]');
	}
}

class dw_detalle_plan_pago_base extends datawindow{
    
    function __construct(){
        
		$sql = sql_boleta::get_sql('DW_DETALLE_PLAN_PAGO');

		parent::__construct($sql, 'DETALLE_PLAN_PAGO', true, true);
			
		$this->add_control($control=new edit_radio_button('COD_PLAN_PAGO', 'S', 'N', '','DETALLE_PLAN_PAGO'));
		$control->set_onChange("checked_radio_button(this);get_max_subcuota();");

		$this->add_control(new edit_text('COD_PLAN_PAGO_H', 10,10, 'hidden'));
		
		$this->add_control(new edit_text('COD_ESTADO_PLAN_PAGO_H', 10,10, 'hidden'));
		$this->add_control(new static_text('FECHA_VENCTO'));
		$this->add_control(new static_text('ANO_COBRO'));
		$this->add_control(new static_text('NRO_CUOTA'));
		$this->add_control(new static_num('SALDO_CAPITAL',4));
		$this->add_control(new static_num('SALDO_INTERES',4));
		$this->add_control(new static_num('SALDO_MULTA',4));
		$this->add_control(new static_num('SALDO_MONEDA',4));
		$this->add_control(new static_num('SALDO_PESOS'));
		$this->add_control(new static_text('NOM_ESTADO_PLAN_PAGO'));

		//MANDATORY
		$this->set_mandatory('D_MONTO_PESOS', 'Monto en Pesos');
	}
}

class wi_boleta_base extends w_input {
    
    function __construct($cod_item_menu) {
	    
        parent::__construct('boleta', $cod_item_menu);
		$this->b_delete_visible  	= false;
		
		$sql = sql_boleta::get_sql('INPUT');
		 
		$this->dws['dw_boleta'] 					= new datawindow($sql);
		$this->dws['dw_detalle_plan_pago'] 			= new dw_detalle_plan_pago($sql);
		$this->dws['dw_subcuota'] 					= new dw_subcuota($sql);
		$this->dws['dw_tab_subcuota'] 				= new dw_tab_subcuota($sql);
		$this->dws['dw_tab_subcuota_detalle'] 		= new dw_tab_subcuota_detalle($sql);

		//CONTROLES
		$this->dws['dw_boleta']->add_control(new edit_text('FECHA_HOY_H', 10,10, 'hidden'));
		$this->dws['dw_boleta']->add_control(new edit_text('COD_BOLETA_H', 10,10, 'hidden'));
		$this->dws['dw_boleta']->add_control(new edit_text('B_COD_PLAN_PAGO', 10,10, 'hidden'));
		$this->dws['dw_boleta']->add_control(new edit_text('COD_DEUDA_H', 10,10, 'hidden'));
		$this->dws['dw_boleta']->add_control(new edit_text('B_TASA_INTERES_PENAL_H', 10,10, 'hidden'));
		
		$this->dws['dw_boleta']->add_control(new static_text('COD_BOLETA'));
		$this->dws['dw_boleta']->add_control(new static_text('SIMBOLO'));
		$this->dws['dw_boleta']->add_control(new static_text('NOMBRE'));
		$this->dws['dw_boleta']->add_control(new static_text('DIG_VERIF'));
		$this->dws['dw_boleta']->add_control(new static_text('COD_DEUDA'));
		
		$this->dws['dw_boleta']->add_control(new static_text('ANO_COBRO_PP'));
		$this->dws['dw_boleta']->add_control(new static_text('NRO_CUOTA_PP'));
		$this->dws['dw_boleta']->add_control(new static_text('FECHA_VENCTO_PP'));

		$this->dws['dw_boleta']->add_control($control = new edit_rut('RUT'));
		$control->set_drop_down_dependiente('boleta', 'COD_TIPO_DEUDA');
		$js = $control->get_onChange();
		$control->set_onChange($js." get_datos_deudor();");

		$sql = sql_boleta::get_sql('DD_COD_TIPO_DEUDA');

		$this->dws['dw_boleta']->add_control($control = new drop_down_dw('COD_TIPO_DEUDA', $sql, 90));
		$js1 = $control->get_onChange();
		$control->set_onChange($js1."get_cod_deuda();get_datos_deuda();set_null_totales();");

		$this->dws['dw_boleta']->add_control(new edit_check_box('CARTERA_CONOCIDA', 'S', 'N'));
		
		$this->dws['dw_boleta']->add_control(new static_text('CUOTA_MOROSA'));
		//MANDATORYS
		$this->dws['dw_boleta']->set_mandatory('RUT', 'RUT del Deudor');
		$this->dws['dw_boleta']->set_mandatory('COD_TIPO_DEUDA', 'Tipo de Deuda');

		//FIRST FOCUS
		$this->set_first_focus('RUT');
	}

	function new_record() {
		$sql_ultimo_dia = sql_boleta::get_sql('ULTIMO_DIA');
	
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result = $db->build_results($sql_ultimo_dia);

		////////////////////////////dw_boleta////////////////////////////
		$this->dws['dw_boleta']->insert_row();
		$this->dws['dw_boleta']->set_item(0, 'FECHA_BOLETA', $this->current_date());
		$this->dws['dw_boleta']->set_item(0, 'FECHA_HOY_H', $this->current_date());
		$this->dws['dw_boleta']->set_item(0, 'NOM_USUARIO', $this->nom_usuario);

		////////////////////////////dw_tab_subcuota////////////////////////////
		$this->dws['dw_tab_subcuota']->insert_row();
		$this->dws['dw_tab_subcuota']->set_item(0, 'FECHA_PRIMERA_CUOTA', $result[0]['ULTIMO_DIA_DEL_MES']);
		$this->dws['dw_tab_subcuota']->set_item(0, 'FECHA_PRIMERA_CUOTA_H', $result[0]['ULTIMO_DIA_DEL_MES']);
		$this->dws['dw_tab_subcuota']->set_item(0, 'CANT', 1);

		////////////////////////////dw_subcuota////////////////////////////
		$this->dws['dw_subcuota']->insert_row();

		////////////////////////////dw_tab_subcuota_detalle////////////////////////////
		$this->dws['dw_tab_subcuota_detalle']->insert_row();

		$this->dws['dw_tab_subcuota_detalle']->set_item(0, 'SC_FECHA_VENCTO', $result[0]['ULTIMO_DIA_DEL_MES']);
		$this->dws['dw_tab_subcuota_detalle']->set_item(0, 'SC_NRO_SUB_BOLETA', 1);
		
		$this->dws['dw_boleta']->set_item(0, 'CARTERA_CONOCIDA', 'N');
		$this->dws['dw_boleta']->set_item(0, 'MOROSA_DISPLAY', 'none');
	}
	function redraw() {
		parent::redraw();
		if (session::is_set("wi_deuda.create_boleta")) {
			$dato = session::get("wi_deuda.create_boleta");
			$dato = explode('|', $dato);
			$rut = $dato[0];
			$cod_deuda = $dato[1];
			session::un_set("wi_deuda.create_boleta");
			print '<script type="text/javascript">
					// rut deudor
					document.getElementById(\'RUT_0\').value = '.$rut.'; 
					load_drop_down_from_text(\'boleta\', \'RUT_0\', \'COD_TIPO_DEUDA\');  
					get_datos_deudor();
					
					// seleccion tipo deuda
					var tipo_deuda = document.getElementById(\'COD_TIPO_DEUDA_0\');
					tipo_deuda.value = '.$cod_deuda.';
					get_cod_deuda();
					get_datos_deuda();
					set_null_totales();
					</script>';
		}
	}
	function load_record() {
		$this->b_save_visible  		= false;
		$this->b_no_save_visible	= false;
		$this->b_modify_visible  	= false;
		$this->b_print_visible		= true;

		$cod_boleta		= $this->get_item_wo($this->current_record, 'COD_BOLETA');
		$this->dws['dw_boleta']->retrieve($cod_boleta);
		$this->dws['dw_boleta']->remove_control('COD_TIPO_DEUDA');
		$this->dws['dw_detalle_plan_pago']->retrieve($cod_boleta);
		$ano_cobro 		= $this->dws['dw_detalle_plan_pago']->get_item(0, 'ANO_COBRO_H');
		$nro_cuota		= $this->dws['dw_detalle_plan_pago']->get_item(0, 'NRO_CUOTA');
		$fecha_vencto	= $this->dws['dw_detalle_plan_pago']->get_item(0, 'FECHA_VENCTO');

		$this->dws['dw_boleta']->set_item(0, 'ANO_COBRO_PP', $ano_cobro);
		$this->dws['dw_boleta']->set_item(0, 'NRO_CUOTA_PP', $nro_cuota);
		$this->dws['dw_boleta']->set_item(0, 'FECHA_VENCTO_PP', $fecha_vencto);
		
		$this->dws['dw_subcuota']->retrieve($cod_boleta);
		$this->dws['dw_tab_subcuota']->retrieve($cod_boleta);
		$this->dws['dw_tab_subcuota_detalle']->retrieve($cod_boleta);
	}
	function get_key() {
		return $this->dws['dw_boleta']->get_item(0, 'COD_BOLETA');
	}
	function habilitar(&$temp, $habilita) {
		if (!$this->is_new_record()) {
			$control = '<input name="b_create_ingreso" id="b_create_ingreso" src="../../images_appl/b_create_ingreso.jpg" type="image" '.
								 'onMouseDown="MM_swapImage(\'b_create_ingreso\',\'\',\'../../images_appl/b_create_ingreso_click.jpg\',1)" '.
								 'onMouseUp="MM_swapImgRestore()" onMouseOut="MM_swapImgRestore()" '.
								 'onMouseOver="MM_swapImage(\'b_create_ingreso\',\'\',\'../../images_appl/b_create_ingreso_over.jpg\',1)" />';
			$temp->setVar("WBOLETA_CREA_INGRESO", $control);
		}
	}
	function save_record($db) {
		$cod_boleta            	= $this->dws['dw_boleta']->get_item(0, 'COD_BOLETA');
		$cod_usuario           	= $this->cod_usuario;
		$cod_deuda             	= $this->dws['dw_boleta']->get_item(0, 'COD_DEUDA_H');
		$cod_plan_pago         	= $this->dws['dw_boleta']->get_item(0, 'B_COD_PLAN_PAGO');
		$monto_capital         	= $this->dws['dw_subcuota']->get_item(0, 'D_MONTO_CAPITAL_H');
		$monto_interes         	= $this->dws['dw_subcuota']->get_item(0, 'D_MONTO_INTERES_H');
		$monto_multa           	= $this->dws['dw_subcuota']->get_item(0, 'D_MONTO_MULTA_H');
		$monto_moneda          	= $this->dws['dw_subcuota']->get_item(0, 'D_MONTO_MONEDA_H');
		$monto_pesos           	= $this->dws['dw_subcuota']->get_item(0, 'D_MONTO_PESOS_H');
		$dscto_capital         	= $this->dws['dw_subcuota']->get_item(0, 'D_DSCTO_CAPITAL_H');
		$dscto_interes         	= $this->dws['dw_subcuota']->get_item(0, 'D_DSCTO_INTERES_H');
		$dscto_multa           	= $this->dws['dw_subcuota']->get_item(0, 'D_DSCTO_MULTA_H');
		$dscto_pesos           	= $this->dws['dw_subcuota']->get_item(0, 'D_DSCTO_PESOS_H');
		$cod_tipo_descuento		= $this->dws['dw_subcuota']->get_item(0, 'D_COD_TIPO_DESCUENTO');
		$fecha_primera_cuota   	= $this->dws['dw_tab_subcuota']->get_item(0, 'FECHA_PRIMERA_CUOTA');
		$cant                  	= $this->dws['dw_tab_subcuota']->get_item(0, 'CANT');
		$cartera_conocida		= $this->dws['dw_boleta']->get_item(0, 'CARTERA_CONOCIDA');

		$cod_boleta				= ($cod_boleta=='') ? "null" : $cod_boleta;
		$cod_plan_pago			= ($cod_plan_pago=='') ? "null" : $cod_plan_pago;
		$cod_tipo_descuento		= ($cod_tipo_descuento=='') ? "null" : $cod_tipo_descuento;
		$fecha_primera_cuota = $this->str2date($fecha_primera_cuota);
		
		$sp = 'spu_boleta';
		if ($this->is_new_record()){
			$operacion = 'INSERT';
		}
		else{
			$operacion = 'UPDATE';
		}

		$param = 	"'$operacion'
					,$cod_boleta
					,$cod_usuario
					,$cod_deuda
					,$cod_plan_pago
					,$monto_capital
					,$monto_interes
					,$monto_multa
					,$monto_moneda
					,$monto_pesos
					,$cod_tipo_descuento
					,$dscto_capital
					,$dscto_interes
					,$dscto_multa
					,$dscto_pesos
					,$fecha_primera_cuota
					,$cant
					,'$cartera_conocida'";

		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$cod_boleta = $db->GET_IDENTITY("boleta");
				$this->dws['dw_boleta']->set_item(0, 'COD_BOLETA', $cod_boleta);
			}
			
			for ($i=0; $i<$this->dws['dw_tab_subcuota_detalle']->row_count(); $i++) 
				$this->dws['dw_tab_subcuota_detalle']->set_item($i, 'COD_BOLETA', $cod_boleta);
			if (!$this->dws['dw_tab_subcuota_detalle']->update($db))
				return false;
		}
		return true;
	}
	function create_ingreso() {
		$cod_boleta = $this->dws['dw_boleta']->get_item(0, 'COD_BOLETA');
		session::set("CREADA_DESDE_BOLETA", $cod_boleta);
		$url = $this->root_url.'../../commonlib_seven/trunk/php/link_wi.php?modulo_origen=boleta&modulo_destino=ingreso&cod_modulo_destino='.K_NEW_RECORD.'&cod_item_menu=1515';
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

/////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wi_boleta.php";
if (file_exists($file_name)) 
	require_once($file_name);
else {
	class dw_detalle_plan_pago extends dw_detalle_plan_pago_base {
	    
	    function __construct() {
	        parent::__construct(); 
		}
	}
	class wi_boleta extends wi_boleta_base {
	    function __construct($cod_item_menu) {
	        parent::__construct($cod_item_menu); 
		}
	}
}
?>