<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");

class dw_detalle_abono extends datawindow{
	function dw_detalle_abono() {
		
		$sql = sql_rendicion::get_sql('DW_DETALLE_ABONO');
		  		
		parent::datawindow($sql, 'DETALLE_ABONO', true, true);
		
		$this->add_control(new static_text('TIPO_DATO'));
		$this->add_control(new static_text('CODIGO'));
		$this->add_control(new static_text('FECHA'));
		$this->add_control(new static_num('RUT'));
		$this->add_control(new static_text('DIG_VERIF'));
		$this->add_control(new static_text('COD_DEUDA'));
		$this->add_control(new static_text('NOM_DEUDOR'));
		$this->add_control(new static_num('MONTO_MONEDA',4));
		$this->add_control(new static_num('MONTO_PESOS',0));
		$this->add_control(new static_num('PORC', 2));
		$this->add_control(new static_text('NOM_UNIVERSIDAD'));
		$this->add_control(new static_num('MONTO_RENDIDO_MONEDA',4));
		$this->add_control(new static_num('MONTO_RENDIDO_PESOS',0));
		
		$this->add_control(new edit_text('TIPO_DATO_H',10,10,'hidden'));
		$this->add_control(new edit_text('CODIGO_H',10,10,'hidden'));
		$this->add_control(new edit_text('MONTO_MONEDA_H',10,10,'hidden'));
		$this->add_control(new edit_text('MONTO_PESOS_H',10,10,'hidden'));
		$this->add_control(new edit_text('PORC_H',10,10,'hidden'));
		$this->add_control(new edit_text('COD_UNIVERSIDAD_H',10,10,'hidden'));
		$this->add_control(new edit_text('MONTO_RENDIDO_MONEDA_H',10,10,'hidden'));
		$this->add_control(new edit_text('MONTO_RENDIDO_PESOS_H',10,10,'hidden'));
		
		$this->set_first_focus('TIPO_DATO_H');
	}
	function get_fields_sin_where($sql) {
		// Se reimplementa esta funcion simplificando el select, porque la common 
		// no soporta selecect quye contengan union
		$sql = sql_rendicion::get_sql('DW_DETALLE_ABONO_NEWRECORD');
		return parent::get_fields_sin_where($sql);
	}
	function update($db){
		$sp = 'spu_detalle_abono';
		for ($i = 0; $i < $this->row_count(); $i++) {	
			$statuts = $this->get_status_row($i);
			if ($statuts == K_ROW_NOT_MODIFIED || $statuts == K_ROW_NEW)
					continue;
					
			$tipo_dato				= $this->get_item($i, 'TIPO_DATO_H');
			$cod_rendicion_abono	= $this->get_item($i, 'COD_RENDICION_ABONO');
			$cod_rendicion			= $this->get_item($i, 'COD_RENDICION');
			
			$cod_abono = "null";
			$cod_dscto = "null";
			if($tipo_dato == 'ABONO')
				$cod_abono = $this->get_item($i, 'CODIGO_H');
			if ($tipo_dato == 'DSCTO')
				$cod_dscto = $this->get_item($i, 'CODIGO_H');
			
			$monto_moneda			= $this->get_item($i, 'MONTO_MONEDA_H');
			$monto_pesos			= $this->get_item($i, 'MONTO_PESOS_H');
			$porc		        	= $this->get_item($i, 'PORC_H');
			$cod_universidad		= $this->get_item($i, 'COD_UNIVERSIDAD_H');
			$monto_rendido_moneda	= $this->get_item($i, 'MONTO_RENDIDO_MONEDA_H');
			$monto_rendido_pesos	= $this->get_item($i, 'MONTO_RENDIDO_PESOS_H');
			
			$cod_rendicion_abono	= ($cod_rendicion_abono =='') ? "null" : $cod_rendicion_abono;
			
			if ($statuts == K_ROW_NEW_MODIFIED)
				$operacion = 'INSERT';
			else if ($statuts == K_ROW_MODIFIED)
				$operacion = 'UPDATE';
			$param = "'$operacion'
						, $cod_rendicion_abono
						,$cod_rendicion
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
		}
		for ($i = 0; $i < $this->row_count('delete'); $i++){
			$statuts_d = $this->get_status_row($i, 'delete');
			if ($statuts_d == K_ROW_NEW || $statuts_d == K_ROW_NEW_MODIFIED){
				continue;
			}
			$cod_rendicion_abono   = $this->get_item($i, 'COD_RENDICION_ABONO', 'delete');

			if (!$db->EXECUTE_SP($sp, "'DELETE', $cod_rendicion_abono")){
				return false;
			}
		}
		return true;
	}
} 


/*
 * Clase: WI_RENDICION
 */
class wi_rendicion extends w_input{
	const K_ESTADO_RENDICION_SOLICITADA	= 1;
	const K_ESTADO_RENDICION_APROBADA	= 2;
	
	function wi_rendicion($cod_item_menu){
		parent::w_input('rendicion', $cod_item_menu);
		$this->b_delete_visible  = false;
		$this->b_print_visible   = true;
		
		$sql = sql_rendicion::get_sql('INPUT');

		$this->dws['dw_rendicion'] = new datawindow($sql);
		$this->dws['dw_detalle_abono'] = new dw_detalle_abono();
	
		//CONTROLES
		$this->dws['dw_rendicion']->add_control($control = new edit_date('FECHA_INICIO',10, 10));
		$control->set_onChange("borra_todos_abonos();");
		$this->dws['dw_rendicion']->add_control($control = new edit_date('FECHA_TERMINO',10, 10));
		$control->set_onChange("borra_todos_abonos();");
		
		if(K_CLIENTE == 'UV')
			$sql = "select COD_TIPO_DEUDA, COD_TIPO_DEUDA NOM_TIPO_DEUDA from TIPO_DEUDA where COD_TIPO_DEUDA in ('CS','CSR','CRPI','CR12C')";
		else
			$sql = "select COD_TIPO_DEUDA, COD_TIPO_DEUDA NOM_TIPO_DEUDA from TIPO_DEUDA where COD_TIPO_DEUDA in ('CS','CSR','CRPI','CR12')";
		
		$this->dws['dw_rendicion']->add_control($control = new drop_down_dw('COD_TIPO_DEUDA', $sql));
		$control->set_onChange("borra_todos_abonos();");
		
		$sql_cta_cte = sql_rendicion::get_sql('DD_COD_CUENTA_CORRIENTE');
		$this->dws['dw_rendicion']->add_control($control =new drop_down_dw('COD_CUENTA_CORRIENTE', $sql_cta_cte, 200));
		

		//////////////// CLASS_SQL = INPUT_ESTADO_SUSPENSION ///////////////
		$sql_er = sql_rendicion::get_sql('DD_ESTADO_RENDICION');
		$this->dws['dw_rendicion']->add_control(new drop_down_dw('COD_ESTADO_RENDICION', $sql_er, 140));
		
		$this->dws['dw_rendicion']->set_mandatory('FECHA_INICIO', 'Fecha Inicio');
		$this->dws['dw_rendicion']->set_mandatory('FECHA_TERMINO', 'Fecha Termino');
		$this->dws['dw_rendicion']->set_mandatory('COD_TIPO_DEUDA', 'Tipo deuda');
		
		//audoria
		$this->add_auditoria('COD_ESTADO_RENDICION');
	}
	function new_record() {	
		$this->dws['dw_rendicion']->insert_row();
		$this->dws['dw_rendicion']->set_item(0, 'FECHA_RENDICION', $this->current_date());
		$this->dws['dw_rendicion']->set_item(0, 'COD_USUARIO', $this->cod_usuario);
		$this->dws['dw_rendicion']->set_item(0, 'NOM_USUARIO', $this->nom_usuario);
		
		$this->dws['dw_rendicion']->set_item(0, 'COD_ESTADO_RENDICION', self::K_ESTADO_RENDICION_SOLICITADA);
		$this->dws['dw_rendicion']->set_entrable('COD_ESTADO_RENDICION', false);
	}
	function load_record() {	
		$cod_rendicion	= $this->get_item_wo($this->current_record, 'COD_RENDICION');
		$this->dws['dw_rendicion']->retrieve($cod_rendicion);
		$this->dws['dw_detalle_abono']->retrieve($cod_rendicion);
			
		$cod_estado_rendicion = $this->dws['dw_rendicion']->get_item(0, 'COD_ESTADO_RENDICION');
		if ($cod_estado_rendicion == self::K_ESTADO_RENDICION_SOLICITADA) {
			$this->dws['dw_rendicion']->set_entrable('COD_ESTADO_RENDICION', true);
			$this->b_no_save_visible = true;
			$this->b_save_visible 	 = true;
			$this->b_modify_visible	 = true;
			$this->b_delete_visible  = true;
			$this->b_print_visible   = false;
		}
		else if ($cod_estado_rendicion == self::K_ESTADO_RENDICION_APROBADA) {
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
		return $this->dws['dw_rendicion']->get_item(0, 'COD_RENDICION');
	}
	function save_record($db){
		$cod_rendicion 			= $this->dws['dw_rendicion']->get_item(0, 'COD_RENDICION');
		$cod_usuario			= $this->dws['dw_rendicion']->get_item(0, 'COD_USUARIO');
		$fecha_inicio			= $this->dws['dw_rendicion']->get_item(0, 'FECHA_INICIO');
		$fecha_termino			= $this->dws['dw_rendicion']->get_item(0, 'FECHA_TERMINO');
		$cod_estado_rendicion	= $this->dws['dw_rendicion']->get_item(0, 'COD_ESTADO_RENDICION');
		$cod_tipo_deuda			= $this->dws['dw_rendicion']->get_item(0, 'COD_TIPO_DEUDA');
		$fecha_inicio 			= $this->str2date($fecha_inicio); 
		$fecha_termino 			= $this->str2date($fecha_termino);
		$cod_cuenta_corriente	 = $this->dws['dw_rendicion']->get_item(0, 'COD_CUENTA_CORRIENTE');
		 
		$cod_rendicion			= ($cod_rendicion=='') ? "null" : $cod_rendicion;
		$cod_usuario			= ($cod_usuario=='') ? "null" : $cod_usuario;
		$cod_cuenta_corriente 	= ($cod_cuenta_corriente=='') ? "null" : $cod_cuenta_corriente;
		$sp = 'spu_rendicion';
		if ($this->is_new_record()){
			$operacion = 'INSERT';
		}
		else {
			$operacion = 'UPDATE';		
		}
		$param	= "'$operacion',
					$cod_rendicion,
					$cod_usuario,
					$fecha_inicio,
					$fecha_termino,
					$cod_estado_rendicion,
					'$cod_tipo_deuda',
					$cod_cuenta_corriente";
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$cod_rendicion = $db->GET_IDENTITY("rendicion");
				$this->dws['dw_rendicion']->set_item(0, 'COD_RENDICION', $cod_rendicion);
			}
			for ($i=0; $i < $this->dws['dw_detalle_abono']->row_count(); $i++){
				$this->dws['dw_detalle_abono']->set_item($i, 'COD_RENDICION', $cod_rendicion);}
			
			if (!$this->dws['dw_detalle_abono']->update($db)) 
				return false;
				
			return true;
		}
		return false;
	}
	function print_record() {
		$cod_rendicion = $this->get_key();
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql= sql_rendicion::get_sql('COUNT_UNIVERSIDAD', $cod_rendicion);
		$result_count = $db->build_results($sql);
		$count_rows = $db->count_rows();
		
		for($i=0;$i<$count_rows;$i++) {
			$cod_universidad = $result_count[$i]['COD_UNIVERSIDAD'];
			$nom_universidad = $result_count[$i]['NOM_UNIVERSIDAD'];
			
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$sql= sql_rendicion::get_sql('FILTRO_RENDICION', $cod_rendicion);
			$result = $db->build_results($sql);
			$labels = array();
			
			$fecha_rendicion 		= $result[0]['FECHA_RENDICION'];
			$nom_usuario 			= $result[0]['NOM_USUARIO'];
			$fecha_inicio 			= $result[0]['FECHA_INICIO'];
			$fecha_termino 			= $result[0]['FECHA_TERMINO'];
			$nom_estado_rendicion 	= $result[0]['NOM_ESTADO_RENDICION'];
			$cod_tipo_deuda		 	= $result[0]['COD_TIPO_DEUDA'];
				
			// report
			$labels['strCOD_RENDICION'] = $cod_rendicion;
			$labels['strFECHA_RENDICION'] = $fecha_rendicion;
			$labels['strNOM_USUARIO'] = $nom_usuario;
			$labels['strFECHA_INICIO'] = $fecha_inicio;
			$labels['strFECHA_TERMINO'] = $fecha_termino;
			$labels['strNOM_ESTADO_RENDICION'] = $nom_estado_rendicion;
			$labels['strCOD_TIPO_DEUDA'] = $cod_tipo_deuda;
			$labels['str_NOM_UNIVERSIDAD'] = $nom_universidad;
			
			$sql= sql_rendicion::get_sql('PRINT_RECORD', $cod_rendicion, $cod_universidad);
			// report
			$rpt = new reporte_helen($sql, $this->root_dir.'appl/rendicion/rendicion.xml', $labels, "Rendición".$i,1,'','','','L');
		}	
		$this->redraw();		
	}
	
	function export_excel() {		
		ini_set('max_execution_time', 300); //300 seconds = 5 minutes
		 
		require_once dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/php_writeexcel-0.3.0/class.writeexcel_workbook.inc.php";
		require_once dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/php_writeexcel-0.3.0/class.writeexcel_worksheet.inc.php";

		$cod_rendicion = $this->get_key();
		
		$fname = tempnam("/tmp", "export.xls");
		$workbook = &new writeexcel_workbook($fname);		
		
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql= sql_rendicion::get_sql('COUNT_UNIVERSIDAD', $cod_rendicion);
		$result_count = $db->build_results($sql);
		$count_rows = $db->count_rows();
				
		for($h=0;$h<$count_rows;$h++){
			
			$cod_universidad = $result_count[$h]['COD_UNIVERSIDAD'];
			$nom_universidad = $result_count[$h]['NOM_UNIVERSIDAD'];
		
			$sql= sql_rendicion::get_sql('PRINT_RECORD', $cod_rendicion, $cod_universidad);
			
			$res = $db->query($sql);
			
			$worksheet = $workbook->addworksheet('RENDICION '.substr($nom_universidad, 0, 18).' '.$h);
			
			// escribe encabezados		
			$header =& $workbook->addformat();
			$header->set_bold();
			$header->set_color('blue');	
			
			$text =& $workbook->addformat();
			$text->set_font("Verdana");
			$text->set_valign('vcenter');
			$text->set_bold();
			$text->set_color('blue');
			
			$worksheet->write(0, 0, 'Tipo Dato', $text);
			$worksheet->write(0, 1, 'Rut', $text);
			$worksheet->write(0, 2, 'año Cobro', $text);
			$worksheet->write(0, 3, 'N° Cuota', $text);
			$worksheet->write(0, 4, 'Monto Cuota', $text);
			$worksheet->write(0, 5, 'Fecha Pago', $text);
			$worksheet->write(0, 6, 'Capital UTM', $text);
			$worksheet->write(0, 7, 'Int. Penal UTM', $text);
			$worksheet->write(0, 8, 'Total UTM', $text);
			$worksheet->write(0, 9, 'Total Pesos', $text);
			$worksheet->write(0, 10, '% Universidad', $text);
			$worksheet->write(0, 11, 'Capital UTM', $text);
			$worksheet->write(0, 12, 'Int. Penal UTM', $text);
			$worksheet->write(0, 13, 'Total UTM', $text);
			$worksheet->write(0, 14, 'Total Pesos', $text);
			
			
			$i = 1;
			while($my_row = $db->get_row()){
				if ($worksheet->_datasize > 7000000)
					break;
				$worksheet->write($i, 0, $my_row['TIPO_DATO']);
				$worksheet->write($i, 1, $my_row['RUT']);
				$worksheet->write($i, 2, $my_row['ANO_COBRO']);
				$worksheet->write($i, 3, $my_row['NRO_CUOTA']);
				$worksheet->write($i, 4, $my_row['MONTO_CUOTA']);
				$worksheet->write($i, 5, $my_row['FECHA_PAGO']);
				$worksheet->write($i, 6, $my_row['CAPITAL_UTM']);
				$worksheet->write($i, 7, $my_row['INTERES_PENAL_UTM']);
				$worksheet->write($i, 8, $my_row['TOTAL_UTM']);
				$worksheet->write($i, 9, $my_row['TOTAL_PESOS']);
				$worksheet->write($i, 10, $my_row['PORC']);
				$worksheet->write($i, 11, $my_row['PORC_CAPITAL_UTM']);	
				$worksheet->write($i, 12, $my_row['PORC_INTERES_PENAL_UTM']);
				$worksheet->write($i, 13, $my_row['PORC_TOTAL_UTM']);
				$worksheet->write($i, 14, $my_row['PORC_TOTAL_PESOS']);					
				$i++;
			}
		
			if ($worksheet->_datasize > 7000000) {
				$worksheet->write($i, 0, 'No se completo la exportación de datos porque excede el máximo del tamaño de archivo 7 MB', $header);
			}
			
			if($db->database_type=="oci") {
				oci_free_statement($db->query_id);
				$db->query_id = false;
			}
			
		}
		$workbook->close();

		header("Content-Type: application/x-msexcel; name=\"RENDICION".$cod_rendicion."\"");
		header("Content-Disposition: inline; filename=\"RENDICION_".$cod_rendicion.".xls\"");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
	}	
	
	function procesa_event() {		
		if(isset($_POST['b_export_x'])) {
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$cod_rendicion = $this->get_key();			
			$this->export_excel();
		}		
		else
			parent::procesa_event();

	}
	
}
?>