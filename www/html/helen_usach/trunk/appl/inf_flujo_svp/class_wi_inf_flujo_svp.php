<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

ini_set('memory_limit', '480M');
ini_set('max_execution_time', 900); //900 seconds = 15 minutes 
class wi_inf_flujo_svp extends w_param_informe_helen {
    
	function __construct($cod_item_menu) {
		$xml = session::get('K_ROOT_DIR').'appl/inf_flujo_svp/inf_flujo_svp.xml';
		$sql_informe = sql_inf_flujo_svp::get_sql('INPUT');
		parent::__construct('inf_flujo_svp', $cod_item_menu, 'Flujo deudas SVP', $xml, $sql_informe, 'spi_flujo_svp');

		$sql = sql_inf_flujo_svp::get_sql('FECHA');

		$this->dws['dw_param'] = new datawindow($sql);
		$this->dws['dw_param']->add_control(new edit_num('ANO', 4, 4));

		// mandatorys
		$this->dws['dw_param']->set_mandatory('ANO', 'Año');
	}
	function make_filtro() {
		$ano 			= $this->dws['dw_param']->get_item(0, 'ANO');

		// Filtro
		$this->filtro = "Año = $ano";

		// Arma los paramaetros para el SP
		$this->param = "$ano";
	}
	function genera_xls() {
		ini_set('memory_limit', '128M');
		ini_set('max_execution_time', 300);
		require_once dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/libs/simplexlsxgen/src/SimpleXLSXGen.php";

		$ano_sp = $_POST['ANO_0'];
		$this->param = "$ano_sp, $this->cod_usuario";
		$this->sp    = "spi_flujo_svp";
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$db->EXECUTE_SP($this->sp, $this->param);

		$sql = "SELECT RUT, DIG_VERIF, TIPO_DEUDA, COD_DEUDA,
		               to_char(FECHA_INICIO_COBRO, 'yyyy') FECHA_INICIO_COBRO,
		               CANT_CUOTA, ESTADO_DEUDA, UNIVERSIDAD, CARTERA, MONTO_DEUDA,
		               MONTO_ANO1, MONTO_ANO2, MONTO_ANO3, MONTO_ANO4, MONTO_ANO5,
		               MONTO_ANO6, MONTO_ANO7, MONTO_ANO8, MONTO_ANO9, MONTO_ANO10,
		               MONTO_ANO11, MONTO_FINAL, ANO_CONTABLE, ES_CRUZADO, PORC_USACH,
		               CON_PRORROGA, COD_CARRERA, NOM_CARRERA
		        FROM TEMP_FLUJO_SVP
		        where COD_USUARIO = $this->cod_usuario
		        ORDER BY RUT";
		$res = $db->query($sql);

		$ano = $this->dws['dw_param']->get_item(0, 'ANO');
		$rows = [[
			'<b>RUT</b>', '<b>DV</b>', '<b>TIPO DEUDA</b>', '<b>COD DEUDA</b>',
			'<b>FECHA INICIO COBRO</b>', '<b>CANT CUOTA</b>', '<b>ESTADO DEUDA</b>',
			'<b>UNIVERSIDAD</b>', '<b>CARTERA</b>', '<b>MONTO DEUDA</b>',
			'<b>MONTO '.($ano+1).'</b>', '<b>MONTO '.($ano+2).'</b>', '<b>MONTO '.($ano+3).'</b>',
			'<b>MONTO '.($ano+4).'</b>', '<b>MONTO '.($ano+5).'</b>', '<b>MONTO '.($ano+6).'</b>',
			'<b>MONTO '.($ano+7).'</b>', '<b>MONTO '.($ano+8).'</b>', '<b>MONTO '.($ano+9).'</b>',
			'<b>MONTO '.($ano+10).'</b>', '<b>MONTO '.($ano+11).'</b>',
			'<b>MONTO FINAL</b>', '<b>ANO CONTABLE</b>', '<b>ES CRUZADO</b>', '<b>PORC USACH</b>',
			'<b>CON PRORROGA</b>', '<b>COD CARRERA</b>', '<b>NOM CARRERA</b>',
		]];
		while ($my_row = $db->get_row()) {
			$rows[] = [
				$my_row['RUT'], $my_row['DIG_VERIF'], $my_row['TIPO_DEUDA'], $my_row['COD_DEUDA'],
				$my_row['FECHA_INICIO_COBRO'], $my_row['CANT_CUOTA'], $my_row['ESTADO_DEUDA'],
				$my_row['UNIVERSIDAD'], $my_row['CARTERA'], $my_row['MONTO_DEUDA'],
				$my_row['MONTO_ANO1'], $my_row['MONTO_ANO2'], $my_row['MONTO_ANO3'],
				$my_row['MONTO_ANO4'], $my_row['MONTO_ANO5'], $my_row['MONTO_ANO6'],
				$my_row['MONTO_ANO7'], $my_row['MONTO_ANO8'], $my_row['MONTO_ANO9'],
				$my_row['MONTO_ANO10'], $my_row['MONTO_ANO11'],
				$my_row['MONTO_FINAL'], $my_row['ANO_CONTABLE'], $my_row['ES_CRUZADO'],
				$my_row['PORC_USACH'], $my_row['CON_PRORROGA'], $my_row['COD_CARRERA'],
				$my_row['NOM_CARRERA'],
			];
		}

		if ($db->database_type == "oci") {
			oci_free_statement($db->query_id);
			$db->query_id = false;
		}

		\Shuchkin\SimpleXLSXGen::fromArray($rows, 'Detalle')->downloadAs('inf_flujo_svp.xlsx');
	}
}
?>