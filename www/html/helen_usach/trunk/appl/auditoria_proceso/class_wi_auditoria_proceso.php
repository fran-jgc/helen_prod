<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 1800);//30 minutos

class dw_detalle_error_proceso extends datawindow{
	function __construct() {
		$sql = sql_auditoria_proceso::get_sql('DW_DETALLE_ERROR_PROCESO');
		parent::__construct($sql, 'DETALLE_ERROR_PROCESO');
	}
	function retrieve($cod_ejecucion_proceso) {
	   
	   $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT COUNT(*) AS CANT FROM ERROR_PROCESO WHERE COD_EJECUCION_PROCESO = $cod_ejecucion_proceso";		 	
		$result = $db->build_results($sql);
		$cant = $result[0]['CANT'];		
				
		if ($cant < 100)
		    datawindow::retrieve($cod_ejecucion_proceso);
	}
}

class wi_auditoria_proceso extends w_input {
    function __construct($cod_item_menu) {
        parent::__construct('auditoria_proceso', $cod_item_menu);
		$this->b_delete_visible  = false;

		$sql = sql_auditoria_proceso::get_sql("INPUT");
		
		$this->dws['dw_auditoria_proceso'] 					= new datawindow($sql);
		$this->dws['dw_detalle_error_proceso'] 				= new dw_detalle_error_proceso($sql);
		
		// asigna los formatos			
		$this->dws['dw_auditoria_proceso']->add_control(new static_num('COD_EJECUCION_PROCESO'));
		$this->dws['dw_auditoria_proceso']->add_control(new static_text('NOM_PROCESO', 80, 100));
		$this->dws['dw_auditoria_proceso']->add_control(new static_text('FECHA_EJECUCION_PROCESO', 80, 100));
		$this->dws['dw_auditoria_proceso']->add_control(new static_text('FECHA_TERMINO', 80, 100));
		$this->dws['dw_auditoria_proceso']->add_control(new static_text('DURACION', 80, 100));
		$this->dws['dw_auditoria_proceso']->add_control(new static_num('ACTUAL_REGISTRO'));
		$this->dws['dw_auditoria_proceso']->add_control(new static_num('CANT_ERROR'));
		$this->dws['dw_auditoria_proceso']->add_control(new static_num('CANT_WARNING'));
	}

	function load_record(){
		$COD_EJECUCION_PROCESO = $this->get_item_wo($this->current_record, 'COD_EJECUCION_PROCESO');
		$this->dws['dw_auditoria_proceso']->retrieve($COD_EJECUCION_PROCESO);
		$this->dws['dw_detalle_error_proceso']->retrieve($COD_EJECUCION_PROCESO);
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = sql_auditoria_proceso::get_sql('CANT_DETALLE_ERROR', $COD_EJECUCION_PROCESO);		 	
		$result = $db->build_results($sql);
		$cant_detalle_error = $result[0]['CANT_DETALLE_ERROR'];
				 
		if ($cant_detalle_error < 100){			
			$this->dws['dw_auditoria_proceso']->set_item(0, 'DISPLAY_DETALLE_ERROR', '');
			$this->dws['dw_auditoria_proceso']->set_item(0, 'DISPLAY_MENSAJE_DETALLE_ERROR', 'none');
		}
		else {
			$this->dws['dw_auditoria_proceso']->set_item(0, 'DISPLAY_DETALLE_ERROR', 'none');
			$this->dws['dw_auditoria_proceso']->set_item(0, 'DISPLAY_MENSAJE_DETALLE_ERROR', '');			
		}
		
		$this->b_no_save_visible = false;
		$this->b_save_visible 	 = false;
		$this->b_modify_visible	 = false;
	}
	function get_key() {
		return $this->dws['dw_auditoria_proceso']->get_item(0, 'COD_EJECUCION_PROCESO');
	}
	function export_excel() {
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 1800);//30 minutos
		require_once dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/libs/simplexlsxgen/src/SimpleXLSXGen.php";

		$COD_EJECUCION_PROCESO = $this->get_key();

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = $this->dws['dw_detalle_error_proceso']->get_sql();
		$sql = $this->replace_KEYS($sql, array($COD_EJECUCION_PROCESO));
		$res = $db->query($sql);

		$rows = [['<b>COD ERROR PROCESO</b>', '<b>COD EJECUCION PROCESO</b>', '<b>TIPO ERROR</b>', '<b>COD ERROR BD</b>', '<b>MENSAJE ERROR BD</b>', '<b>MENSAJE ERROR</b>']];
		while ($my_row = $db->get_row()) {
			$rows[] = [$my_row['COD_ERROR_PROCESO'], $my_row['COD_EJECUCION_PROCESO_DW'], $my_row['TIPO_ERROR'], $my_row['ERROR_CODE_BD'], $my_row['ERROR_MESSAGE_BD'], $my_row['ERROR_MESSAGE']];
		}

		if ($db->database_type == "oci") {
			oci_free_statement($db->query_id);
			$db->query_id = false;
		}

		\Shuchkin\SimpleXLSXGen::fromArray($rows, 'DETALLE_ERROR_PROCESO'.$COD_EJECUCION_PROCESO)->downloadAs('DETALLE_ERROR_PROCESO_'.$COD_EJECUCION_PROCESO.'.xlsx');
	}
	function procesa_event() {		
		if(isset($_POST['b_export_x'])) {
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$COD_EJECUCION_PROCESO = $this->get_key();			
			$this->export_excel();
		}		
		else
			parent::procesa_event();

	}
}
?>