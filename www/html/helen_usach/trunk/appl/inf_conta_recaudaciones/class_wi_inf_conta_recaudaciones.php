<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

ini_set('memory_limit', '480M');
ini_set('max_execution_time', 900); //900 seconds = 15 minutes

class wi_inf_conta_recaudaciones extends w_param_informe_helen {
	var $cod_tipo_deuda;

	function wi_inf_conta_recaudaciones($cod_item_menu) {
		$xml = session::get('K_ROOT_DIR').'appl/inf_conta_recaudaciones/inf_conta_recaudaciones.xml';
		$sql_informe = sql_inf_conta_recaudaciones::get_sql('INPUT');
		parent::w_param_informe('inf_conta_recaudaciones', $cod_item_menu, 'Recaudaciones', $xml, $sql_informe, 'spi_conta_recaudaciones');

		$sql = sql_inf_conta_recaudaciones::get_sql('FECHA'); 

		$this->dws['dw_param'] = new datawindow($sql);

		$sql = sql_inf_conta_recaudaciones::get_sql('DD_TIPO_DEUDA');
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_TIPO_DEUDA', $sql));
		$this->dws['dw_param']->add_control(new edit_num('ANO', 4, 4));

		// mandatorys		
		$this->dws['dw_param']->set_mandatory('COD_TIPO_DEUDA', 'Tipo Deuda');	
		$this->dws['dw_param']->set_mandatory('ANO', 'Año');	
	}
	function make_filtro() {
		$cod_tipo_deuda = $this->dws['dw_param']->get_item(0, 'COD_TIPO_DEUDA'); 
		$this->cod_tipo_deuda = $cod_tipo_deuda;
		$ano 			= $this->dws['dw_param']->get_item(0, 'ANO');
		// Filtro
		$this->filtro = "Tipo Deuda = $cod_tipo_deuda;\nAño = $ano";

		// Arma los paramaetros para el SP
		$this->param = "'$cod_tipo_deuda', $ano";	
	}
	
	function genera_xls() {
		ini_set('memory_limit', '128M');
		ini_set('max_execution_time', 300);
		require_once dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/libs/simplexlsxgen/src/SimpleXLSXGen.php";

		$header_row = [
			'<b>RUT</b>', '<b>NOMBRE</b>', '<b>FECHA</b>', '<b>COMP.</b>', '<b>FOLIO</b>', '<b>FORMA PAGO</b>',
			'<b>AÃ‘O CUOTA</b>', '<b>SVP</b>', '<b>SVP $</b>', '<b>VENCIDA</b>', '<b>VENCIDA $</b>',
			'<b>CP</b>', '<b>CP $</b>', '<b>LP</b>', '<b>LP $</b>',
		];
		if ($this->cod_tipo_deuda == 'CSR') {
			$header_row[] = '<b>UV_PORC</b>';
			$header_row[] = '<b>UV_SVP</b>';
			$header_row[] = '<b>UV_SVP $</b>';
			$header_row[] = '<b>UV_VENCIDA</b>';
			$header_row[] = '<b>UV_VENCIDA $</b>';
			$header_row[] = '<b>UV_CP</b>';
			$header_row[] = '<b>UV_CP $</b>';
			$header_row[] = '<b>UV_LP</b>';
			$header_row[] = '<b>UV_LP $</b>';
		}
		$col = count($header_row);

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		if (K_TIPO_BD == "mssql") {
			$sql = "exec ".$this->sp." ".$this->param;
		} elseif (K_TIPO_BD == "oci") {
			$db->EXECUTE_SP($this->sp, $this->param);
			$sql = $this->sql_informe;
		}
		$res = $db->query($sql);

		$rows = [$header_row];
		while ($my_row = $db->get_row()) {
			$data_row = [];
			for ($j = 0; $j < $col; $j++) {
				$data_row[] = $my_row[$j];
			}
			$rows[] = $data_row;
		}

		if ($db->database_type == "oci") {
			oci_free_statement($db->query_id);
			$db->query_id = false;
		}

		\Shuchkin\SimpleXLSXGen::fromArray($rows, 'Recaudaciones')->downloadAs($this->nom_tabla.'.xlsx');
	}

}
?>