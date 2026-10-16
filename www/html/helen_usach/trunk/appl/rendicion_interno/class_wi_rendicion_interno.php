<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");

class dw_detalle_abono extends datawindow{
    function __construct() {
		
		$sql = sql_rendicion_interno::get_sql('DW_DETALLE_ABONO');
		  		
		parent::__construct($sql, 'DETALLE_ABONO', true, true);
		
		$this->add_control(new static_text('TIPO_DATO'));
		$this->add_control(new static_text('CODIGO'));
		$this->add_control(new static_text('FECHA'));
		$this->add_control(new static_num('RUT'));
		$this->add_control(new static_text('DIG_VERIF'));
		$this->add_control(new static_text('COD_DEUDA'));
		$this->add_control(new edit_text('COD_DEUDA_H',10,10,'hidden'));
		$this->add_control(new static_text('NOM_DEUDOR'));
		$this->add_control(new static_num('MONTO_MONEDA',4));
		$this->add_control(new static_num('MONTO_PESOS',0));
		$this->add_control(new static_num('PORC', 2));
		$this->add_control(new static_text('NOM_ACREEDOR_INTERNO'));
		$this->add_control(new static_num('MONTO_RENDIDO_MONEDA',4));
		$this->add_control(new static_num('MONTO_RENDIDO_PESOS',0));
		
		$this->add_control($control = new edit_check_box('SELECCION', 'S', 'N'));
        $control->set_onChange("valida_cruzado(this);");
		$this->add_control(new edit_text('TIPO_DATO_H',10,10,'hidden'));
		$this->add_control(new edit_text('CODIGO_H',10,10,'hidden'));
		$this->add_control(new edit_text('MONTO_MONEDA_H',10,10,'hidden'));
		$this->add_control(new edit_text('MONTO_PESOS_H',10,10,'hidden'));
		$this->add_control(new edit_text('PORC_H',10,10,'hidden'));
		$this->add_control(new edit_text('COD_ACREEDOR_INTERNO_H',10,10,'hidden'));
		$this->add_control(new edit_text('MONTO_RENDIDO_MONEDA_H',10,10,'hidden'));
		$this->add_control(new edit_text('MONTO_RENDIDO_PESOS_H',10,10,'hidden'));
		$this->set_first_focus('TIPO_DATO_H');
	}
	function get_fields_sin_where($sql) {
		// Se reimplementa esta funcion simplificando el select, porque la common 
		// no soporta selecect quye contengan union
		$sql = sql_rendicion_interno::get_sql('DW_DETALLE_ABONO_NEWRECORD');
		return parent::get_fields_sin_where($sql);
	}
	function update($db){
		$sp = 'spu_rendicion_interno_abono';
		for ($i = 0; $i < $this->row_count(); $i++) {	
			$statuts = $this->get_status_row($i);
			if ($statuts == K_ROW_NOT_MODIFIED || $statuts == K_ROW_NEW)
					continue;
					
			$seleccion				= $this->get_item($i, 'SELECCION');
			$tipo_dato				= $this->get_item($i, 'TIPO_DATO_H');
			$cod_rendicion_interno_abono	= $this->get_item($i, 'COD_RENDICION_INTERNO_ABONO');
			$cod_rendicion_interno			= $this->get_item($i, 'COD_RENDICION_INTERNO');
			
			$cod_abono = "null";
			$cod_dscto = "null";
			if($tipo_dato == 'ABONO')
				$cod_abono = $this->get_item($i, 'CODIGO_H');
			if ($tipo_dato == 'DSCTO')
				$cod_dscto = $this->get_item($i, 'CODIGO_H');
			
			$monto_moneda			= $this->get_item($i, 'MONTO_MONEDA_H');
			$monto_pesos			= $this->get_item($i, 'MONTO_PESOS_H');
			$porc		        	= $this->get_item($i, 'PORC_H');
			$cod_universidad		= $this->get_item($i, 'COD_ACREEDOR_INTERNO_H');
			$monto_rendido_moneda	= $this->get_item($i, 'MONTO_RENDIDO_MONEDA_H');
			$monto_rendido_pesos	= $this->get_item($i, 'MONTO_RENDIDO_PESOS_H');
			
			$cod_rendicion_interno_abono	= ($cod_rendicion_interno_abono =='') ? "null" : $cod_rendicion_interno_abono;
			
			if ($statuts == K_ROW_NEW_MODIFIED)
				$operacion = 'INSERT';
			else if ($statuts == K_ROW_MODIFIED)
				$operacion = 'UPDATE';
				
			if($seleccion == 'S'){
				$param = "'$operacion'
							, $cod_rendicion_interno_abono
							,$cod_rendicion_interno
							,$cod_abono
							,$cod_dscto
							,$monto_moneda
							,$monto_pesos
							,$porc
							,$cod_universidad
							,$monto_rendido_moneda
							,$monto_rendido_pesos";
				
				if (!$db->EXECUTE_SP($sp,$param)){
					return false;
				}
			}else if(($seleccion == 'N') && ($operacion == 'UPDATE')){
				if (!$db->EXECUTE_SP($sp, "'DELETE', $cod_rendicion_interno_abono")){
					return false;
				}
			}
		}
		return true;
	}
} 


/*
 * Clase: WI_RENDICION_INTERNO
 */
class wi_rendicion_interno extends w_input{
	const K_ESTADO_RENDICION_INTERNO_SOLICITADA	= 1;
	const K_ESTADO_RENDICION_INTERNO_APROBADA	= 2;
	
	function __construct($cod_item_menu){
	    parent::__construct('rendicion_interno', $cod_item_menu);
		$this->b_delete_visible  = false;
		$this->b_print_visible   = false;
		
		$sql = sql_rendicion_interno::get_sql('INPUT');

		$this->dws['dw_rendicion_interno'] = new datawindow($sql);
		$this->dws['dw_detalle_abono'] = new dw_detalle_abono();
	
		//CONTROLES
		$this->dws['dw_rendicion_interno']->add_control($control = new edit_date('FECHA_INICIO',10, 10));
		$control->set_onChange("borra_todos_abonos();");
		$this->dws['dw_rendicion_interno']->add_control($control = new edit_date('FECHA_TERMINO',10, 10));
		$control->set_onChange("borra_todos_abonos();");
		
		if(K_CLIENTE == 'UV')
			$sql = "select COD_TIPO_DEUDA, COD_TIPO_DEUDA NOM_TIPO_DEUDA from TIPO_DEUDA where COD_TIPO_DEUDA in ('CS','CSR','CRI')";
		else
			$sql = "select COD_TIPO_DEUDA, COD_TIPO_DEUDA NOM_TIPO_DEUDA from TIPO_DEUDA where COD_TIPO_DEUDA in ('CS','CSR','CRI','CFR')";
		
		$this->dws['dw_rendicion_interno']->add_control($control = new drop_down_dw('COD_TIPO_DEUDA', $sql));
		$control->set_onChange("borra_todos_abonos();");
		
		$sql_cta_cte = sql_rendicion_interno::get_sql('DD_COD_CUENTA_CORRIENTE');
		$this->dws['dw_rendicion_interno']->add_control($control =new drop_down_dw('COD_CUENTA_CORRIENTE', $sql_cta_cte, 200));
		

		//////////////// CLASS_SQL = INPUT_ESTADO_SUSPENSION ///////////////
		$sql_er = sql_rendicion_interno::get_sql('DD_ESTADO_RENDICION');
		$this->dws['dw_rendicion_interno']->add_control(new drop_down_dw('COD_ESTADO_RENDICION', $sql_er, 140));
		
		$this->dws['dw_rendicion_interno']->set_mandatory('FECHA_INICIO', 'Fecha Inicio');
		$this->dws['dw_rendicion_interno']->set_mandatory('FECHA_TERMINO', 'Fecha Termino');
		$this->dws['dw_rendicion_interno']->set_mandatory('COD_TIPO_DEUDA', 'Tipo deuda');
		
		//audoria
		$this->add_auditoria('COD_ESTADO_RENDICION');
	}
	function new_record() {	
		$this->dws['dw_rendicion_interno']->insert_row();
		$this->dws['dw_rendicion_interno']->set_item(0, 'FECHA_RENDICION', $this->current_date());
		$this->dws['dw_rendicion_interno']->set_item(0, 'COD_USUARIO', $this->cod_usuario);
		$this->dws['dw_rendicion_interno']->set_item(0, 'NOM_USUARIO', $this->nom_usuario);
		
		$this->dws['dw_rendicion_interno']->set_item(0, 'COD_ESTADO_RENDICION', self::K_ESTADO_RENDICION_INTERNO_SOLICITADA);
		$this->dws['dw_rendicion_interno']->set_entrable('COD_ESTADO_RENDICION', false);
		$this->dws['dw_rendicion_interno']->set_item(0, 'DISPLAY_EXPORT', 'none');
	}
	function load_record() {	
		$cod_rendicion_interno	= $this->get_item_wo($this->current_record, 'COD_RENDICION_INTERNO');
		$this->dws['dw_rendicion_interno']->retrieve($cod_rendicion_interno);
		$this->dws['dw_detalle_abono']->retrieve($cod_rendicion_interno);
			
		$cod_estado_rendicion_interno = $this->dws['dw_rendicion_interno']->get_item(0, 'COD_ESTADO_RENDICION');
		
		if ($cod_estado_rendicion_interno == self::K_ESTADO_RENDICION_INTERNO_SOLICITADA) {
			$this->dws['dw_rendicion_interno']->set_entrable('COD_ESTADO_RENDICION', true);
			$this->b_no_save_visible = true;
			$this->b_save_visible 	 = true;
			$this->b_modify_visible	 = true;
			$this->b_delete_visible  = true;
			$this->b_print_visible   = false;
		}
		else if ($cod_estado_rendicion_interno == self::K_ESTADO_RENDICION_INTERNO_APROBADA) {
			$this->b_no_save_visible = false;
			$this->b_save_visible 	 = false;
			$this->b_modify_visible	 = false;
			$this->b_delete_visible  = false;
			$this->b_print_visible   = true;
		}
		else{
			$this->b_no_save_visible = false;
			$this->b_save_visible 	 = false;
			$this->b_modify_visible	 = false;
			$this->b_delete_visible  = false;
			$this->b_print_visible   = false;
		}
	}
	function get_key() {
		return $this->dws['dw_rendicion_interno']->get_item(0, 'COD_RENDICION_INTERNO');
	}
	function save_record($db){
		$cod_rendicion_interno 			= $this->dws['dw_rendicion_interno']->get_item(0, 'COD_RENDICION_INTERNO');
		$cod_usuario			= $this->dws['dw_rendicion_interno']->get_item(0, 'COD_USUARIO');
		$fecha_inicio			= $this->dws['dw_rendicion_interno']->get_item(0, 'FECHA_INICIO');
		$fecha_termino			= $this->dws['dw_rendicion_interno']->get_item(0, 'FECHA_TERMINO');
		$cod_estado_rendicion_interno	= $this->dws['dw_rendicion_interno']->get_item(0, 'COD_ESTADO_RENDICION');
		$cod_tipo_deuda			= $this->dws['dw_rendicion_interno']->get_item(0, 'COD_TIPO_DEUDA');
		$fecha_inicio 			= $this->str2date($fecha_inicio); 
		$fecha_termino 			= $this->str2date($fecha_termino);
		$cod_cuenta_corriente	 = $this->dws['dw_rendicion_interno']->get_item(0, 'COD_CUENTA_CORRIENTE');
		 
		$cod_rendicion_interno			= ($cod_rendicion_interno=='') ? "null" : $cod_rendicion_interno;
		$cod_usuario			= ($cod_usuario=='') ? "null" : $cod_usuario;
		$cod_cuenta_corriente 	= ($cod_cuenta_corriente=='') ? "null" : $cod_cuenta_corriente;
		$sp = 'spu_rendicion_interno';
		if ($this->is_new_record()){
			$operacion = 'INSERT';
		}
		else {
			$operacion = 'UPDATE';		
		}
		$param	= "'$operacion',
					$cod_rendicion_interno,
					$cod_usuario,
					$fecha_inicio,
					$fecha_termino,
					$cod_estado_rendicion_interno,
					'$cod_tipo_deuda',
					$cod_cuenta_corriente";
		
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$cod_rendicion_interno = $db->GET_IDENTITY("rendicion_interno");
				$this->dws['dw_rendicion_interno']->set_item(0, 'COD_RENDICION_INTERNO', $cod_rendicion_interno);
			}
			for ($i=0; $i < $this->dws['dw_detalle_abono']->row_count(); $i++){
				$this->dws['dw_detalle_abono']->set_item($i, 'COD_RENDICION_INTERNO', $cod_rendicion_interno);}
			
			if (!$this->dws['dw_detalle_abono']->update($db)) 
				return false;
				
			return true;
		}
		return false;
	}
	function print_record() {
		$cod_rendicion_interno = $this->get_key();
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql= sql_rendicion_interno::get_sql('COUNT_ACREEDOR_INTERNO', $cod_rendicion_interno);
		$result_count = $db->build_results($sql);
		$count_rows = $db->count_rows();
		
		for($i=0;$i<$count_rows;$i++) {
			$cod_acreedor_interno = $result_count[$i]['COD_ACREEDOR_INTERNO'];
			$nom_acreedor_interno = $result_count[$i]['NOM_ACREEDOR_INTERNO'];
			
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$sql= sql_rendicion_interno::get_sql('FILTRO_RENDICION_INTERNO', $cod_rendicion_interno);
			$result = $db->build_results($sql);
			$labels = array();
			
			$fecha_rendicion_interno 		= $result[0]['FECHA_RENDICION'];
			$nom_usuario 			= $result[0]['NOM_USUARIO'];
			$fecha_inicio 			= $result[0]['FECHA_INICIO'];
			$fecha_termino 			= $result[0]['FECHA_TERMINO'];
			$nom_estado_rendicion_interno 	= $result[0]['NOM_ESTADO_RENDICION'];
			$cod_tipo_deuda		 	= $result[0]['COD_TIPO_DEUDA'];
				
			// report
			$labels['str_COD_RENDICION_INTERNO'] = $cod_rendicion_interno;
			$labels['str_FECHA_RENDICION'] = $fecha_rendicion_interno;
			$labels['str_NOM_USUARIO'] = $nom_usuario;
			$labels['str_FECHA_INICIO'] = $fecha_inicio;
			$labels['str_FECHA_TERMINO'] = $fecha_termino;
			$labels['str_NOM_ESTADO_RENDICION'] = $nom_estado_rendicion_interno;
			$labels['str_COD_TIPO_DEUDA'] = $cod_tipo_deuda;
			$labels['str_NOM_UNIVERSIDAD'] = $nom_acreedor_interno;
			
			$sql= sql_rendicion_interno::get_sql('PRINT_RECORD', $cod_rendicion_interno, $cod_acreedor_interno);
			// report
			$rpt = new reporte_helen($sql, $this->root_dir.'appl/rendicion_interno/rendicion_interno.xml', $labels, "Rendicion".$i.'.pdf',1,'','','','L');
		}	
		$this->redraw();		
	}
	
	function export_excel() {
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 1800);//30 minutos

		require_once dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/libs/simplexlsxgen/src/SimpleXLSXGen.php";

		$cod_rendicion_interno = $this->get_key();

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = sql_rendicion_interno::get_sql('COUNT_ACREEDOR_INTERNO', $cod_rendicion_interno);
		$result_count = $db->build_results($sql);
		$count_rows = $db->count_rows();

		$xlsx = \Shuchkin\SimpleXLSXGen::create();

		for ($h = 0; $h < $count_rows; $h++) {
			$cod_acreedor_interno = $result_count[$h]['COD_ACREEDOR_INTERNO'];
			$nom_acreedor_interno = $result_count[$h]['NOM_ACREEDOR_INTERNO'];

			$sql = sql_rendicion_interno::get_sql('PRINT_RECORD', $cod_rendicion_interno, $cod_acreedor_interno);
			$res = $db->query($sql);

			$rows = [['<b>Tipo Dato</b>', '<b>Rut</b>', '<b>Dig Verif</b>', '<b>Origen Pago</b>', '<b>Año Cobro</b>', '<b>N° Cuota</b>', '<b>Monto Cuota</b>', '<b>Fecha Pago</b>', '<b>Total UTM Abono</b>', '<b>Total Pesos Abono</b>', '<b>% Universidad</b>', '<b>Total UTM Rendido</b>', '<b>Total Pesos Rendido</b>', '<b>Tipo Deuda</b>']];
			while ($my_row = $db->get_row()) {
				$rows[] = [$my_row['TIPO_DATO'], $my_row['RUT'], $my_row['DIG_VERIF'], $my_row['NOM_ORIGEN_PAGO'], $my_row['ANO_COBRO'], $my_row['NRO_CUOTA'], $my_row['MONTO_CUOTA'], $my_row['FECHA_PAGO'], $my_row['TOTAL_UTM'], $my_row['TOTAL_PESOS'], $my_row['PORC'], $my_row['MONTO_RENDIDO_MONEDA'], $my_row['MONTO_RENDIDO_PESOS'], $my_row['COD_TIPO_DEUDA']];
			}

			if ($db->database_type == "oci") {
				oci_free_statement($db->query_id);
				$db->query_id = false;
			}

			$xlsx->addSheet($rows, 'RENDICION '.substr($nom_acreedor_interno, 0, 18).' '.$h);
		}

		$xlsx->downloadAs('RENDICION_INTERNO_'.$cod_rendicion_interno.'.xlsx');
	}
	
	function procesa_event() {		
		if(isset($_POST['b_export_x'])) {
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$cod_rendicion_interno = $this->get_key();			
			$this->export_excel();
		}		
		else
			parent::procesa_event();

	}
	
}
?>