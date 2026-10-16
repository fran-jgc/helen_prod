<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

ini_set('memory_limit', '480M');
ini_set('max_execution_time', 900); //900 seconds = 15 minutes 
class wi_inf_conta_svp extends w_param_informe_helen {
	function wi_inf_conta_svp($cod_item_menu) {
		$xml = session::get('K_ROOT_DIR').'appl/inf_conta_svp.xml';
		$sql_informe = sql_inf_conta_svp::get_sql('INPUT');
		parent::w_param_informe('inf_conta_svp', $cod_item_menu, 'Sin Vencimiento Pactado', $xml, $sql_informe, 'spi_conta_svp');

		$sql = sql_inf_conta_svp::get_sql('FECHA');

		$this->dws['dw_param'] = new datawindow($sql);

		$sql =	sql_inf_conta_svp::get_sql('DD_COD_TIPO_DEUDA');
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_TIPO_DEUDA', $sql));
		$this->dws['dw_param']->add_control(new edit_num('ANO', 4, 4));
		$sql = sql_inf_flujo_exigible::get_sql('DD_COD_CARTERA');
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_CARTERA', $sql, 0, '', false));

		// mandatorys
		$this->dws['dw_param']->set_mandatory('COD_TIPO_DEUDA', 'Tipo Deuda');
		$this->dws['dw_param']->set_mandatory('ANO', 'Año');
	}
	
	function make_filtro() {
		$cod_tipo_deuda = $this->dws['dw_param']->get_item(0, 'COD_TIPO_DEUDA');
		$ano 			= $this->dws['dw_param']->get_item(0, 'ANO');
		$cod_cartera 	= $this->dws['dw_param']->get_item(0, 'COD_CARTERA'); 

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
		$this->filtro = "Tipo Deuda = $cod_tipo_deuda; Cartera = $nom_cartera \nAño = $ano";
		$this->ano = $ano;
		// Arma los paramaetros para el SP
		$this->param = "'$cod_tipo_deuda', $ano, $cod_cartera";
	}
	
	function genera_xls() {
		ini_set('memory_limit', '128M');
		ini_set('max_execution_time', 300);
		require_once dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/libs/simplexlsxgen/src/SimpleXLSXGen.php";

		$ano = $this->dws['dw_param']->get_item(0, 'ANO');

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		if (K_TIPO_BD == "mssql") {
			$sql = "exec ".$this->sp." ".$this->param;
		} elseif (K_TIPO_BD == "oci") {
			$db->EXECUTE_SP($this->sp, $this->param);
			$sql = $this->sql_informe;
		}
		$res = $db->query($sql);

		$rows = [['<b>RUT</b>', '<b>NOMBRE</b>', '<b>SALDO 01/01/'.$ano.'</b>', '<b>REAJUSTE 2%</b>', '<b>ABONOS '.$ano.'</b>', '<b>DESCTOS '.$ano.'</b>', '<b>SALDO 31/12/'.$ano.'</b>', '<b>INI_COBRO</b>']];
		while ($my_row = $db->get_row()) {
			$rows[] = [$my_row[0], $my_row[1], $my_row[2], $my_row[3], $my_row[4], $my_row[5], $my_row[6], $my_row[7]];
		}

		if ($db->database_type == "oci") {
			oci_free_statement($db->query_id);
			$db->query_id = false;
		}

		\Shuchkin\SimpleXLSXGen::fromArray($rows, 'Informe SVP')->downloadAs($this->nom_tabla.'.xlsx');
	}
}
?>