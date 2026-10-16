<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

ini_set('memory_limit', '480M');
ini_set('max_execution_time', 900); //900 seconds = 15 minutes

class wi_inf_flujo_exigible extends w_param_informe_helen {
    
	function __construct($cod_item_menu) {
		$xml = session::get('K_ROOT_DIR').'appl/inf_flujo_exigible/inf_flujo_exigible.xml';
		$sql_informe = "";
		parent::__construct('inf_flujo_exigible', $cod_item_menu, 'Flujo deudas EXIGIBLE', $xml, $sql_informe, 'spi_flujo_exigible');

		$sql = "SELECT	To_Char(SYSDATE,'YYYY') ANO
						,null COD_CARTERA
				FROM 	DUAL";
		
		$this->dws['dw_param'] = new datawindow($sql);
		$this->dws['dw_param']->add_control(new edit_num('ANO', 4, 4));
		$sql = "SELECT	0 			COD_CARTERA
						,'Todas' 	NOM_CARTERA
				from DUAL
				union
				SELECT		COD_CARTERA
							, NOM_CARTERA
				FROM 		CARTERA
				ORDER BY	COD_CARTERA";
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_CARTERA', $sql, 0, '', false));

		// mandatorys		
		$this->dws['dw_param']->set_mandatory('ANO', 'Año');	
	}
	function make_filtro() {
		$ano 			= $this->dws['dw_param']->get_item(0, 'ANO');
		$cod_cartera 	= $this->dws['dw_param']->get_item(0, 'COD_CARTERA'); 

		// Arma los paramaetros para el SP
		$this->param = "$ano, $cod_cartera,$this->cod_usuario";	
	}
	function genera_pdf($labels = array(), $con_logo = true,$orientation='L',$unit='pt',$format='letter') {
		$ano = $this->dws['dw_param']->get_item(0, 'ANO');
		$ano = $ano+2;
		for ($i=0; $i<13; $i++){
			$labels['str_ano'.($i+1)] = $ano + $i;
		}
		parent::genera_pdf($labels, $con_logo,$orientation, $unit,$format);
	}
	
	function genera_xls() {
		ini_set('memory_limit', '128M');
		ini_set('max_execution_time', 300);
		require_once dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/libs/simplexlsxgen/src/SimpleXLSXGen.php";

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$ano_sp      = $_POST['ANO_0'];
		$cod_cartera = $_POST['COD_CARTERA_0'];
		$this->param = "$ano_sp, $cod_cartera, $this->cod_usuario";
		$this->sp    = "spi_flujo_exigible";
		$db->EXECUTE_SP($this->sp, $this->param);

		$sql = "SELECT RUT, DIG_VERIF, COD_DEUDA, TIPO_DEUDA,
		               to_char(FECHA_INICIO_COBRO, 'dd/mm/yyyy') FECHA_INICIO_COBRO,
		               ANOS_PACTADOS, PORC_USACH, POR_FONDO, ESTADO_DEUDA,
		               NOMBRE_UNIVERSIDAD, NOMBRE_CARTERA,
		               MONTO_ANO1, MONTO_ANO2, MONTO_ANO3, MONTO_ANO4, MONTO_ANO5,
		               MONTO_ANO6, MONTO_ANO7, MONTO_ANO8, MONTO_ANO9, MONTO_ANO10,
		               MONTO_ANO11, MONTO_ANO12, MONTO_ANO13, MONTO_ANO14, MONTO_ANO15,
		               MONTO_ANO16, MONTO_ANO17, MONTO_9999,
		               CTY, CAT, CRUZADO, CRUZADO_INTERNO, PRORROGA, SUSPENSION,
		               FECHA_CONDONACION, CUOTA_FIJA, COD_CARRERA, NOM_CARRERA
		        FROM TEMP_FLUJO_EXIGIBLE
		        where COD_USUARIO = $this->cod_usuario
		        ORDER BY RUT";
		$res = $db->query($sql);

		$ano = $this->dws['dw_param']->get_item(0, 'ANO');
		$rows = [[
			'<b>RUT</b>', '<b>DV</b>', '<b>COD DEUDA</b>', '<b>TIPO DEUDA</b>',
			'<b>FECHA INICIO COBRO</b>', '<b>ANOS PACTADOS</b>', '<b>PORC USACH</b>',
			'<b>POR FONDO</b>', '<b>ESTADO DEUDA</b>', '<b>NOMBRE UNIVERSIDAD</b>', '<b>NOMBRE CARTERA</b>',
			'<b>MONTO '.($ano+0).'</b>', '<b>MONTO '.($ano+1).'</b>', '<b>MONTO '.($ano+2).'</b>',
			'<b>MONTO '.($ano+3).'</b>', '<b>MONTO '.($ano+4).'</b>', '<b>MONTO '.($ano+5).'</b>',
			'<b>MONTO '.($ano+6).'</b>', '<b>MONTO '.($ano+7).'</b>', '<b>MONTO '.($ano+8).'</b>',
			'<b>MONTO '.($ano+9).'</b>', '<b>MONTO '.($ano+10).'</b>', '<b>MONTO '.($ano+11).'</b>',
			'<b>MONTO '.($ano+12).'</b>', '<b>MONTO '.($ano+13).'</b>', '<b>MONTO '.($ano+14).'</b>',
			'<b>MONTO '.($ano+15).'</b>', '<b>MONTO '.($ano+16).'</b>', '<b>MONTO 9999</b>',
			'<b>CTY</b>', '<b>CAT</b>', '<b>CRUZADO</b>', '<b>CRUZADO INTERNO</b>',
			'<b>PRORROGA</b>', '<b>SUSPENSION</b>', '<b>FECHA CONDONACION</b>',
			'<b>CUOTA FIJA</b>', '<b>COD CARRERA</b>', '<b>NOM CARRERA</b>',
		]];
		while ($my_row = $db->get_row()) {
			$rows[] = [
				$my_row['RUT'], $my_row['DIG_VERIF'], $my_row['COD_DEUDA'], $my_row['TIPO_DEUDA'],
				$my_row['FECHA_INICIO_COBRO'], $my_row['ANOS_PACTADOS'], $my_row['PORC_USACH'],
				$my_row['POR_FONDO'], $my_row['ESTADO_DEUDA'], $my_row['NOMBRE_UNIVERSIDAD'],
				$my_row['NOMBRE_CARTERA'],
				$my_row['MONTO_ANO1'], $my_row['MONTO_ANO2'], $my_row['MONTO_ANO3'],
				$my_row['MONTO_ANO4'], $my_row['MONTO_ANO5'], $my_row['MONTO_ANO6'],
				$my_row['MONTO_ANO7'], $my_row['MONTO_ANO8'], $my_row['MONTO_ANO9'],
				$my_row['MONTO_ANO10'], $my_row['MONTO_ANO11'], $my_row['MONTO_ANO12'],
				$my_row['MONTO_ANO13'], $my_row['MONTO_ANO14'], $my_row['MONTO_ANO15'],
				$my_row['MONTO_ANO16'], $my_row['MONTO_ANO17'], $my_row['MONTO_9999'],
				$my_row['CTY'], $my_row['CAT'], $my_row['CRUZADO'], $my_row['CRUZADO_INTERNO'],
				$my_row['PRORROGA'], $my_row['SUSPENSION'], $my_row['FECHA_CONDONACION'],
				$my_row['CUOTA_FIJA'], $my_row['COD_CARRERA'], $my_row['NOM_CARRERA'],
			];
		}

		if ($db->database_type == "oci") {
			oci_free_statement($db->query_id);
			$db->query_id = false;
		}

		\Shuchkin\SimpleXLSXGen::fromArray($rows, 'Detalle')->downloadAs('inf_flujo_exigible.xlsx');
	}
}
?>