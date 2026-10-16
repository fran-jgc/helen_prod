<?php 
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once("class_print_cambio_documento.php");

class dw_documento_pago_base extends datawindow{
	var $is_new_record = false;
	
	function dw_documento_pago_base() {

		$sql = sql_cambio_documento::get_sql('DW_DOCUMENTO_PAGO_NUEVO');
		
		parent::datawindow($sql, 'DOCUMENTO_PAGO', true, true);

		$this->add_control(new edit_text('COD_DOCUMENTO_PAGO_H', 2,2, 'hidden'));
		$this->add_control(new edit_text('COD_DOCUMENTO_NUEVO', 2,2, 'hidden'));

		$sql = sql_cambio_documento::get_sql('DD_TIPO_DOCUMENTO_PAGO');
		$this->add_control($control = new drop_down_dw('COD_TIPO_DOCUMENTO_PAGO', $sql, 180));
		$control->set_onChange("f_banco_deposito();");

		$this->add_control($control = new edit_num('NRO_DOCUMENTO_PAGO'));
		$control->set_onChange("cambia_nro_documento(this);");

		$sql = sql_cambio_documento::get_sql('DD_BANCO');
		$this->add_control($control = new drop_down_dw('COD_BANCO', $sql, 120));
		$control->set_onChange("cambia_banco(this);");
		
		$sql = sql_cambio_documento::get_sql('DD_PLAZA');
		$this->add_control($control = new drop_down_dw('COD_PLAZA', $sql, 120));
		$control->set_onChange("cambia_plaza(this);");
		
		$this->add_control($control = new edit_date('FECHA_DOCUMENTO_PAGO', 10, 10));
		$control->set_onChange("cambia_fecha_docto(this);");
		//$control->set_onChange("cambia_fecha_docto();");
		
		$this->add_control(new edit_num('MONTO'));

		$this->set_computed('MONTO_C', '[MONTO]');
		$this->controls['MONTO_C']->type = 'hidden';
		$this->accumulate('MONTO_C');

		//MANDATORY
		$this->set_mandatory('COD_TIPO_DOCUMENTO_PAGO', 'Tipo Documento');
		$this->set_mandatory('MONTO', 'Monto');
	}
	
	/*
	 * del_line('DOCUMENTO_PAGO_0', 'cambio_documento');*/
	function fill_record(&$temp, $record) {
		parent::fill_record($temp, $record);
		
		if (!$this->is_new_record) {
        	$eliminar = '<img src="../../../../commonlib_seven/trunk/images/b_delete_line_d.jpg"/>';
        	$temp->setVar($this->label_record.".ELIMINAR_".strtoupper($this->label_record), $eliminar);
        	
        	$this->b_add_line_visible = false;
        	$agregar = '<img src="../../../../commonlib_seven/trunk/images/b_add_line_d.jpg"/>';
			$temp->setVar("AGREGAR_".strtoupper($this->label_record), $agregar);
		}
	}
    
	function update($db,$COD_CAMBIO_DOCUMENTO, $COD_DEUDOR, $COD_FAMILIA_DEUDA){
		$sp = 'spu_documento_nuevo';
		for ($i = 0; $i < $this->row_count(); $i++){
			$statuts = $this->get_status_row($i);
			if ($statuts == K_ROW_NOT_MODIFIED){
				continue;
			}
			// siempre es INSERT
			//$operacion	= 'INSERT';
			$statuts = $this->get_status_row($i);
			
			if ($statuts == K_ROW_NEW_MODIFIED)
				$operacion = 'INSERT';
			elseif ($statuts == K_ROW_MODIFIED)
				$operacion = 'UPDATE';
				
			$kl_deposito				= 3;
			$kl_efectivo				= 4;
			$kl_estado_doc_por_depos	= 1;
			$kl_estado_doc_depositado	= 2;
			
			$cod_documento_nuevo 		= $this->get_item($i, 'COD_DOCUMENTO_NUEVO');
			$cod_cambio_documento 		= $this->get_item($i, 'COD_CAMBIO_DOCUMENTO');
			$cod_documento_pago         = $this->get_item($i, 'COD_DOCUMENTO_PAGO_H');
			$cod_tipo_documento_pago    = $this->get_item($i, 'COD_TIPO_DOCUMENTO_PAGO');
			$nro_documento_pago         = $this->get_item($i, 'NRO_DOCUMENTO_PAGO');
			$cod_banco                  = $this->get_item($i, 'COD_BANCO');
			$cod_plaza                  = $this->get_item($i, 'COD_PLAZA');
			$fecha_documento_pago       = $this->get_item($i, 'FECHA_DOCUMENTO_PAGO');
			$monto                      = $this->get_item($i, 'MONTO');
				
			$cod_documento_nuevo	= ($cod_documento_nuevo =='') ? "null" : $cod_documento_nuevo;
			$cod_documento_pago		= ($cod_documento_pago =='') ? "null" : $cod_documento_pago;
			$nro_documento_pago		= ($nro_documento_pago =='') ? "null" : $nro_documento_pago;
			$cod_banco				= ($cod_banco =='') ? "null" : $cod_banco;
			$cod_plaza				= ($cod_plaza =='') ? "null" : $cod_plaza;
			$fecha_documento_pago	= ($fecha_documento_pago =='') ? "null" : "$fecha_documento_pago";
			$monto					= ($monto =='') ? 0 : "$monto";

			if($cod_tipo_documento_pago ==  $kl_efectivo){
				$cod_banco 				= 'NULL';
				$cod_plaza 				= 'NULL';
				$fecha_documento_pago	= 'NULL';
				$nro_documento_pago		= 'NULL';
			}
			else if ($cod_tipo_documento_pago !=  $kl_efectivo){/*en caso de que el tipo de doc sea distinto de efectivo a la fecha doc la deja con ''*/
				if (K_TIPO_BD =="mssql"){
					$fecha_documento_pago	= $this->str2date($fecha_documento_pago);
				}
				else if(K_TIPO_BD =="oci"){
					if ($fecha_documento_pago != "null" && $fecha_documento_pago != "sysdate"){
						$fecha_documento_pago = "TO_DATE('".$fecha_documento_pago."','dd/mm/yyyy')";
					}
				}
			}
			
			$param = "	'$operacion'
						,$cod_documento_nuevo
						,$cod_cambio_documento
						,$cod_documento_pago
						,$cod_tipo_documento_pago
						,$nro_documento_pago
						,$cod_banco
						,$cod_plaza
						,$fecha_documento_pago
						,$monto
						,$COD_DEUDOR
						,$COD_FAMILIA_DEUDA
						,$this->cod_usuario";
			
			if (!$db->EXECUTE_SP($sp, $param)){
				return false;
			}
		}
		for ($i = 0; $i < $this->row_count('delete'); $i++){
			$statuts_d = $this->get_status_row($i, 'delete');
			if ($statuts_d == K_ROW_NEW || $statuts_d == K_ROW_NEW_MODIFIED){
				continue;
			}
			$cod_documento_pago   = $this->get_item($i, 'COD_DOCUMENTO_PAGO_H', 'delete');

			if (!$db->EXECUTE_SP($sp, "'DELETE', $cod_documento_pago")){
				return false;
			}
		}
		return true;
	}
}

class dw_documento_original extends datawindow{
	function dw_documento_original() {

		$sql= sql_cambio_documento::get_sql('DW_DOCUMENTO_ORIGINAL');

		parent::datawindow($sql, 'DOCUMENTO_ORIGINAL', true, true);

		$this->add_control($control = new edit_check_box('SELECCION', 'S', 'N'));
		$control->set_onChange("seleccion_doc(this);static_result()");
		$this->add_control(new edit_text_upper('COD_DOCUMENTO_PAGO_DO_H', 10, 10, 'hidden'));
		$this->add_control(new edit_text_upper('COD_DOCUMENTO_ORIGINAL', 10, 10, 'hidden'));
		$this->add_control(new static_text('NOM_ESTADO_DOCUMENTO_PAGO'));
		$this->add_control(new edit_text('COD_FAMILIA_DEUDA_H', 10, 10, 'hidden'));
		$this->add_control(new edit_text('COD_DEUDOR_DOC_ORI', 10, 10, 'hidden'));
		
		$this->add_control(new static_text('NOM_TIPO_DOCUMENTO_PAGO'));
		$this->add_control(new static_num('NRO_DOCUMENTO_PAGO_DO'));
		$this->add_control(new static_text('NOM_BANCO'));
		$this->add_control(new static_text('NOM_PLAZA'));
		$this->add_control(new static_text('FECHA_DOCUMENTO_PAGO_DO'));
		
		/*lo que hace las siguientes lineas
		- cuando se selecciona un doc, en campos ocultos deja el valor en un acummulate
		- si se selecciona el campo SELECCION_V pasa a ser 1 y por ende se multiplica con MONTO_DO
		- si no se selecciona el campo SELECCION_V queda en 0 y se muliplica con MONTO_DO, resultado 0*/
		$this->add_control(new computed('MONTO_DO', 0));
		$this->add_control(new edit_text('SELECCION_V', 10, 10, 'hidden'));
		$this->set_computed('MONTO_SELECCIONADO', '[SELECCION_V] * [MONTO_DO]');
		$this->controls['MONTO_SELECCIONADO']->type = 'hidden';

		$this->accumulate('MONTO_SELECCIONADO');
		//$this->add_control(new edit_text('SUM_MONTO_SELECCIONADO', 10,10, 'hidden'));
		
		//MANDATORY
		$this->set_mandatory('SELECCION', 'Selecci�n');
	}
	
	function update($db) {
		for ($i = 0; $i < $this->row_count(); $i++){
			$SELECCION = $this->get_item($i, 'SELECCION');
			if ($SELECCION == 'S'){
				$COD_DOCUMENTO_ORIGINAL = $this->get_item($i, 'COD_DOCUMENTO_ORIGINAL');
				$COD_CAMBIO_DOCUMENTO 	= $this->get_item($i, 'COD_CAMBIO_DOCUMENTO');
				$COD_DOCUMENTO_PAGO 	= $this->get_item($i, 'COD_DOCUMENTO_PAGO_DO_H');
				
				$COD_DOCUMENTO_ORIGINAL 	= ($COD_DOCUMENTO_ORIGINAL == '') ? "null" : $COD_DOCUMENTO_ORIGINAL;
				
				if($COD_DOCUMENTO_ORIGINAL == "null"){
					if (!$db->EXECUTE_SP('spu_documento_original', "'INSERT', $COD_DOCUMENTO_ORIGINAL, $COD_CAMBIO_DOCUMENTO, $COD_DOCUMENTO_PAGO"))
						return false;
				}
			}
		}
		return true;
	}
}

class wi_cambio_documento_base extends w_input{

	function wi_cambio_documento_base($cod_item_menu){
		parent::w_input('cambio_documento', $cod_item_menu);

		$sql = sql_cambio_documento::get_sql('INPUT');
		$this->dws['dw_cambio_documento'] = new datawindow($sql);
		$this->dws['dw_cambio_documento']->add_control(new static_num('COD_CAMBIO_DOCUMENTO'));
		$this->dws['dw_cambio_documento']->add_control(new static_text('NOM_USUARIO'));
		$this->dws['dw_cambio_documento']->add_control(new edit_text('COD_USUARIO',10,10, 'hidden'));
		
		$this->dws['dw_cambio_documento']->add_control($control = new edit_rut('RUT'));
		$control->set_onChange("obtiene_datos(); obtiene_docs_original();");
		
		$this->dws['dw_cambio_documento']->add_control(new static_text('DIG_VERIF'));
		$this->dws['dw_cambio_documento']->add_control(new static_text('NOMBRE'));
		$this->dws['dw_cambio_documento']->add_control(new edit_text('COD_DEUDOR',10,10, 'hidden'));
		$this->dws['dw_cambio_documento']->add_control(new edit_text('COD_DEUDA',10,10, 'hidden'));
		
		$this->dws['dw_cambio_documento']->add_control($control = new edit_num('RECARGO',10,10));
		$control->set_onChange("recargo_descuento(this);static_result()");
		$this->dws['dw_cambio_documento']->add_control($control = new edit_num('DESCUENTO',10,10));
		$control->set_onChange("recargo_descuento(this);static_result()");
		
		$this->dws['dw_cambio_documento']->add_control(new static_num('STATIC_RESULT'));
		
		$sql_familia_deuda = sql_cambio_documento::get_sql('DD_FAMILIA_DEUDA');
		$this->dws['dw_cambio_documento']->add_control($control = new drop_down_dw('COD_FAMILIA_DEUDA', $sql_familia_deuda, 0, '', false));
		$control->set_onChange("change_familia_deuda(this);");
		
		//***** DW_DOCUMENTO_ORIGINAL *****//
		$this->dws['dw_documento_original'] = new dw_documento_original();
		//***** DW_DOCUMENTO_PAGO ****//
		$this->dws['dw_documento_pago'] = new dw_documento_pago();
		
		
		//MANDATORY
		$this->dws['dw_cambio_documento']->set_mandatory('RUT', 'Rut');
		
		//FIRST FOCUS
		$this->set_first_focus('RUT');
		
		
	}
	function new_record(){
		$this->dws['dw_cambio_documento']->insert_row();
		$this->dws['dw_cambio_documento']->set_item(0, 'COD_USUARIO', $this->cod_usuario);
		$this->dws['dw_cambio_documento']->set_item(0, 'NOM_USUARIO', $this->nom_usuario);
		$this->dws['dw_cambio_documento']->set_item(0, 'FECHA_CAMBIO_DOCUMENTO', $this->current_date());
		
		$this->dws['dw_cambio_documento']->set_item(0, 'RECARGO', 0);
		$this->dws['dw_cambio_documento']->set_item(0, 'DESCUENTO', 0);
		$this->dws['dw_cambio_documento']->set_item(0, 'STATIC_RESULT', 0);
		
		$this->dws['dw_documento_pago']->is_new_record = true;
		
	}
	function load_record(){
		$cod_cambio_documento = $this->get_item_wo($this->current_record, 'COD_CAMBIO_DOCUMENTO');
		$this->dws['dw_cambio_documento']->retrieve($cod_cambio_documento);
		
		$this->dws['dw_documento_original']->retrieve($cod_cambio_documento);
		$this->dws['dw_documento_pago']->retrieve($cod_cambio_documento);
		
		$recargo = $this->dws['dw_cambio_documento']->get_item(0, 'RECARGO'); 
		$descuento = $this->dws['dw_cambio_documento']->get_item(0, 'DESCUENTO');
		
		$db 		= new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql 		= "SELECT	SUM(MONTO) MONTO
						FROM	DOCUMENTO_ORIGINAL DO, DOCUMENTO_PAGO DP
						WHERE	DO.COD_DOCUMENTO_PAGO = DP.COD_DOCUMENTO_PAGO
						AND		DO.COD_CAMBIO_DOCUMENTO = $cod_cambio_documento";
		$result 	= $db->build_results($sql);
		$monto = $result[0]['MONTO'];
		
		$static_result = $monto + ($recargo - $descuento); 
		$this->dws['dw_cambio_documento']->set_item(0, 'STATIC_RESULT', $static_result);
		
		$cod_documento_original = $this->dws['dw_documento_original']->get_item(0, 'COD_DOCUMENTO_ORIGINAL');
		
		$this->dws['dw_cambio_documento']->set_entrable_dw(false);
		$this->dws['dw_documento_original']->set_entrable_dw(false);
		
		$this->b_print_visible 		= true;
		$this->b_delete_visible  	= false;	
		
		//14-oct CAMBIO DOCUMENTO No permite anular Cambio de documento
		//La idea es que el usuario no anular� el documento. Si no que sea modificable 
		//los campos Nro doc, fecha doc, plaza y banco.
		$this->dws['dw_documento_pago']->set_entrable('COD_TIPO_DOCUMENTO_PAGO', false);
		$this->dws['dw_documento_pago']->set_entrable('MONTO', false);		
	}
	
	function get_key(){
		return $this->dws['dw_cambio_documento']->get_item(0, 'COD_CAMBIO_DOCUMENTO');
	}

	function save_record($db){
		$COD_CAMBIO_DOCUMENTO	= $this->dws['dw_cambio_documento']->get_item(0, 'COD_CAMBIO_DOCUMENTO');
		$COD_USUARIO			= $this->dws['dw_cambio_documento']->get_item(0, 'COD_USUARIO');
		$COD_DEUDOR				= $this->dws['dw_cambio_documento']->get_item(0, 'COD_DEUDOR');
		$RECARGO				= $this->dws['dw_cambio_documento']->get_item(0, 'RECARGO');
		$DESCUENTO				= $this->dws['dw_cambio_documento']->get_item(0, 'DESCUENTO');
		$COD_FAMILIA_DEUDA		= $this->dws['dw_cambio_documento']->get_item(0, 'COD_FAMILIA_DEUDA');

		$COD_DEUDOR				= ($COD_DEUDOR =='') ? "null" : "$COD_DEUDOR";
		$RECARGO				= ($RECARGO =='') ? 0 : "$RECARGO";
		$DESCUENTO				= ($DESCUENTO =='') ? 0 : "$DESCUENTO";
		$COD_CAMBIO_DOCUMENTO 	= ($COD_CAMBIO_DOCUMENTO == '') ? "null" : $COD_CAMBIO_DOCUMENTO;

		$sp = 'spu_cambio_documento';
		if ($this->is_new_record()){
			$operacion = 'INSERT';
		}
		else{
			$operacion = 'UPDATE';
		}
		$param	= "'$operacion'
					,$COD_CAMBIO_DOCUMENTO
					,$COD_USUARIO
					,$COD_DEUDOR
					,$RECARGO
					,$DESCUENTO
					,$COD_FAMILIA_DEUDA";
					
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$COD_CAMBIO_DOCUMENTO = $db->GET_IDENTITY("cambio_documento");
				$this->dws['dw_cambio_documento']->set_item(0, 'COD_CAMBIO_DOCUMENTO', $COD_CAMBIO_DOCUMENTO);
			}
			for ($i=0; $i<$this->dws['dw_documento_original']->row_count(); $i++)
				$this->dws['dw_documento_original']->set_item($i, 'COD_CAMBIO_DOCUMENTO', $COD_CAMBIO_DOCUMENTO);

			if (!$this->dws['dw_documento_original']->update($db)) 
				return false;
			
			for ($i=0; $i<$this->dws['dw_documento_pago']->row_count(); $i++)
				$this->dws['dw_documento_pago']->set_item($i, 'COD_CAMBIO_DOCUMENTO', $COD_CAMBIO_DOCUMENTO);	
				
			if (!$this->dws['dw_documento_pago']->update($db,$COD_CAMBIO_DOCUMENTO, $COD_DEUDOR, $COD_FAMILIA_DEUDA))
				return false;	
			
			if ($this->is_new_record()) {
				if (!$db->EXECUTE_SP($sp, "'COMPROBANTE', $COD_CAMBIO_DOCUMENTO, $COD_USUARIO, $COD_DEUDOR, $RECARGO, $DESCUENTO, $COD_FAMILIA_DEUDA"))
					return false;
			}
			
			return true;
		}
		return false;
	}
	function print_record() {
		$cod_cambio_documento = $this->get_key();
		$sql= sql_cambio_documento::get_sql('PRINT_RECORD', $cod_cambio_documento);
		// reporte
		$labels = array();
		$labels['strCOD_CAMBIO_DOCUMENTO'] = $cod_cambio_documento;
		$rpt = new print_cambio_documento($sql, $this->root_dir.'appl/cambio_documento/cambio_documento.xml', $labels, "Cambio de Documento".$cod_cambio_documento,1);
		$this->redraw();
				
	}
}
/////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wi_cambio_documento.php";
if (file_exists($file_name)) 
	require_once($file_name);
else {
	class dw_documento_pago extends dw_documento_pago_base {
		function dw_documento_pago() {
			parent::dw_documento_pago_base(); 
		}
	}
	class wi_cambio_documento extends wi_cambio_documento_base {
		function wi_cambio_documento($cod_item_menu) {
			parent::wi_cambio_documento_base($cod_item_menu); 
		}
	}
}
?>