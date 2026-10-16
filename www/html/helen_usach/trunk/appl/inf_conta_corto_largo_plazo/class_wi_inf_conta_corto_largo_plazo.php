<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

ini_set('memory_limit', '480M');
ini_set('max_execution_time', 900); //900 seconds = 15 minutes 

class wi_inf_conta_corto_largo_plazo extends w_param_informe_helen {
	var $cod_tipo_deuda;
	
	function wi_inf_conta_corto_largo_plazo($cod_item_menu) {
		$xml = session::get('K_ROOT_DIR').'appl/inf_conta_corto_largo_plazo.xml';
		$sql_informe = sql_inf_conta_corto_largo_plazo::get_sql('INPUT');
		parent::w_param_informe('inf_conta_corto_largo_plazo', $cod_item_menu,'Corto y Largo Plazo', $xml, $sql_informe, 'spi_conta_corto_largo_plazo');

		$sql = sql_inf_conta_corto_largo_plazo::get_sql('FECHA');

		$this->dws['dw_param'] = new datawindow($sql);

		$sql = sql_inf_conta_corto_largo_plazo::get_sql('DD_COD_TIPO_DEUDA');
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_TIPO_DEUDA', $sql));
		$this->dws['dw_param']->add_control(new edit_num('ANO', 4, 4));
		$sql = sql_inf_conta_corto_largo_plazo::get_sql('DD_COD_CARTERA');
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_CARTERA', $sql, 0, '', false));

		// mandatorys		
		$this->dws['dw_param']->set_mandatory('COD_TIPO_DEUDA', 'Tipo Deuda');	
		$this->dws['dw_param']->set_mandatory('ANO', 'Año');	
	}
	function make_filtro() {
		$cod_tipo_deuda = $this->dws['dw_param']->get_item(0, 'COD_TIPO_DEUDA'); 
		$this->cod_tipo_deuda = $cod_tipo_deuda;
		$ano 			= $this->dws['dw_param']->get_item(0, 'ANO');
		$cod_cartera	= $this->dws['dw_param']->get_item(0, 'COD_CARTERA');

		if ($cod_cartera==0)
			$nom_cartera = 'TODAS'; 
		else {
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$sql = "select NOM_CARTERA
					from CARTERA
					where COD_CARTERA = $cod_cartera";
			$result = $db->build_results($sql);
			$nom_cartera = $result[0]['NOM_CARTERA']; 
		}
		// Filtro
		$this->filtro = "Tipo Deuda = $cod_tipo_deuda; Cartera = $nom_cartera \nAñoo = $ano";

		// Arma los paramaetros para el SP
		$this->param = "'$cod_tipo_deuda', $ano, $cod_cartera";	
	}
	
	function genera_xls() {
		ini_set('memory_limit', '128M');
		ini_set('max_execution_time', 300);
		require_once dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/libs/simplexlsxgen/src/SimpleXLSXGen.php";

		$ano = $this->dws['dw_param']->get_item(0, 'ANO');
		$ano_sig = $ano + 1;

		$header_row = [
			'<b>RUT</b>', '<b>NOMBRE</b>', '<b>CARTERA</b>', '<b>INICIO COBRO</b>',
			'<b>EMITIDA '.$ano.'</b>', '<b>ABONO '.$ano.'</b>', '<b>DESCTOS '.$ano.'</b>', '<b>CR</b>', '<b>SALDO '.$ano.'</b>',
			'<b>CONCEPTO</b>',
			'<b>EMITIDA '.$ano_sig.'</b>', '<b>ABONO '.$ano_sig.'</b>', '<b>DESCTOS '.$ano_sig.'</b>', '<b>CR '.$ano_sig.'</b>', '<b>SALDO '.$ano_sig.'</b>',
			'<b>CUOTA LP</b>', '<b>EMITIDA LP</b>', '<b>ABONO LP</b>', '<b>DESCTOS LP</b>', '<b>CR LP</b>', '<b>SALDO LP</b>',
			'<b>CAT</b>',
		];
		if ($this->cod_tipo_deuda == 'CSR') {
			$header_row[] = '<b>UV_PORC</b>';
			$header_row[] = '<b>UV_EMITIDA '.$ano.'</b>';
			$header_row[] = '<b>UV_ABONO '.$ano.'</b>';
			$header_row[] = '<b>UV_DESCTOS '.$ano.'</b>';
			$header_row[] = '<b>UV_CR</b>';
			$header_row[] = '<b>UV_SALDO '.$ano.'</b>';
			$header_row[] = '<b>UV_EMITIDA '.$ano_sig.'</b>';
			$header_row[] = '<b>UV_ABONO '.$ano_sig.'</b>';
			$header_row[] = '<b>UV_DESCTOS '.$ano_sig.'</b>';
			$header_row[] = '<b>UV_CR '.$ano_sig.'</b>';
			$header_row[] = '<b>UV_SALDO '.$ano_sig.'</b>';
			$header_row[] = '<b>UV_CUOTA LP</b>';
			$header_row[] = '<b>UV_EMITIDA LP</b>';
			$header_row[] = '<b>UV_ABONO LP</b>';
			$header_row[] = '<b>UV_DESCTOS LP</b>';
			$header_row[] = '<b>UV_CR LP</b>';
			$header_row[] = '<b>UV_SALDO LP</b>';
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

		\Shuchkin\SimpleXLSXGen::fromArray($rows, 'Corto y Largo Plazo')->downloadAs($this->nom_tabla.'.xlsx');
	}
}
?>