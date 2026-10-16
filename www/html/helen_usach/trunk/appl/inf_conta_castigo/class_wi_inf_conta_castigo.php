<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

ini_set('memory_limit', '480M');
ini_set('max_execution_time', 900); //900 seconds = 15 minutes
class wi_inf_conta_castigo extends w_param_informe_helen {
    function __construct($cod_item_menu) {
		$sql_informe = sql_inf_conta_castigo::get_sql('INPUT');
		parent::__construct('inf_conta_castigo', $cod_item_menu, 'Castigo', $sql_informe, 'spi_conta_castigo');

		$sql = sql_inf_conta_castigo::get_sql('FECHA'); 

		$this->dws['dw_param'] = new datawindow($sql);

		$sql = sql_inf_conta_castigo::get_sql('DD_FAMILIA_DEUDA');
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_FAMILIA_DEUDA', $sql));
		$sql = sql_inf_conta_castigo::get_sql('DD_TIPO_DEUDA');
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_TIPO_DEUDA', $sql));
		$this->dws['dw_param']->add_control(new edit_num('ANO', 4, 4));
		$this->dws['dw_param']->add_control(new drop_down_list('CASTIGO_AGOSTO', array('N', 'S'), array('Marzo','Agosto')));
		
		// mandatorys		
		$this->dws['dw_param']->set_mandatory('COD_FAMILIA_DEUDA', 'Familia Deuda');	
		$this->dws['dw_param']->set_mandatory('COD_TIPO_DEUDA', 'Tipo Deuda');
		$this->dws['dw_param']->set_mandatory('ANO', 'Año');	
	}
	function make_filtro() {
		$cod_familia	= $this->dws['dw_param']->get_item(0, 'COD_FAMILIA_DEUDA'); 
		$ano 			= $this->dws['dw_param']->get_item(0, 'ANO');
		$castigo_agosto = $this->dws['dw_param']->get_item(0, 'CASTIGO_AGOSTO');
		// Filtro
		$this->filtro = "Familia Deuda = $cod_familia;\nAño = $ano";
	}
	function genera_xls() {
		ini_set('memory_limit', '128M');
		ini_set('max_execution_time', 300);
		require_once dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/libs/simplexlsxgen/src/SimpleXLSXGen.php";

		$cod_familia_deuda = $_POST['COD_FAMILIA_DEUDA_0'];
		$cod_tipo_deuda    = $_POST['COD_TIPO_DEUDA_0'];
		$ano               = $_POST['ANO_0'];
		$castigo_agosto    = $_POST['CASTIGO_AGOSTO_0'];
		$this->param = "$cod_familia_deuda,'$cod_tipo_deuda', $ano, '$castigo_agosto', $this->cod_usuario";
		$this->sp    = "spi_conta_castigo";

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$db->EXECUTE_SP($this->sp, $this->param);

		$sql_meta = "SELECT ANO_CONTABLE, HASTA_AGOSTO, COD_TIPO_DEUDA
		             FROM TEMP_CONTA_CASTIGO
		             where COD_USUARIO = $this->cod_usuario
		             and rownum = 1";
		$meta         = $db->build_results($sql_meta);
		$ano_contable = $meta[0]['ANO_CONTABLE'];
		$tipo_deuda   = $meta[0]['COD_TIPO_DEUDA'];
		$hasta_agosto = $meta[0]['HASTA_AGOSTO'];

		$ano_ant = $ano_contable - 1;
		if ($hasta_agosto == 'S')
			$meses = 'JUN-AGO '.$ano;
		else
			$meses = 'ENE-MAR '.$ano;

		$sql = "SELECT RUT, DV, COD_DEUDA, COD_TIPO_DEUDA,
		               SALDO_ANT, SALDO_ANT_PESOS, ABONO_MORA_CP, ABONO_MORA_CP_PESOS,
		               SALDO_CUOTA, SALDO_CUOTA_PESOS,
		               to_char(FECHA_VENCTO_CUOTA, 'dd/mm/yyyy') FECHA_VENCTO_CUOTA,
		               NOM_CARTERA, PORC, DESCUENTO_CR12, DESCUENTO_CR12_PESOS,
		               PORC_INTERNO, PORC_CF, PORC_CU
		        FROM TEMP_CONTA_CASTIGO
		        where COD_USUARIO = $this->cod_usuario
		        ORDER BY COD_TIPO_DEUDA, RUT";
		$res = $db->query($sql);

		if ($tipo_deuda == 'CR12') {
			$header_row = ['<b>RUT</b>', '<b>DV</b>', '<b>COD DEUDA</b>', '<b>TIPO DEUDA</b>',
			               '<b>SALDO ANT '.$ano_ant.'</b>', '<b>SALDO ANT '.$ano_ant.' PESOS</b>',
			               '<b>ABONO '.$meses.'</b>', '<b>ABONO '.$meses.' PESOS</b>',
			               '<b>SALDO '.$ano_ant.'</b>', '<b>SALDO '.$ano_ant.' PESOS</b>',
			               '<b>VENCTO CUOTA</b>', '<b>NOM CARTERA</b>', '<b>PORC USACH</b>',
			               '<b>DESCTO CR12 OPORTUNO</b>', '<b>DESCTO CR12 OPORTUNO PESOS</b>',
			               '<b>PORC INTERNO</b>', '<b>PORC CF</b>', '<b>PORC CU</b>'];
			$rows = [$header_row];
			while ($my_row = $db->get_row()) {
				$rows[] = [$my_row['RUT'], $my_row['DV'], $my_row['COD_DEUDA'], $my_row['COD_TIPO_DEUDA'],
				           $my_row['SALDO_ANT'], $my_row['SALDO_ANT_PESOS'],
				           $my_row['ABONO_MORA_CP'], $my_row['ABONO_MORA_CP_PESOS'],
				           $my_row['SALDO_CUOTA'], $my_row['SALDO_CUOTA_PESOS'],
				           $my_row['FECHA_VENCTO_CUOTA'], $my_row['NOM_CARTERA'],
				           $my_row['PORC'], $my_row['DESCUENTO_CR12'], $my_row['DESCUENTO_CR12_PESOS'],
				           $my_row['PORC_INTERNO'], $my_row['PORC_CF'], $my_row['PORC_CU']];
			}
		} else {
			$header_row = ['<b>RUT</b>', '<b>DV</b>', '<b>COD DEUDA</b>', '<b>TIPO DEUDA</b>',
			               '<b>SALDO ANT '.$ano_ant.'</b>', '<b>SALDO ANT '.$ano_ant.' PESOS</b>',
			               '<b>ABONO '.$meses.'</b>', '<b>ABONO '.$meses.' PESOS</b>',
			               '<b>SALDO '.$ano_ant.'</b>', '<b>SALDO '.$ano_ant.' PESOS</b>',
			               '<b>VENCTO CUOTA</b>', '<b>NOM CARTERA</b>', '<b>PORC USACH</b>',
			               '<b>PORC INTERNO</b>', '<b>PORC CF</b>', '<b>PORC CU</b>'];
			$rows = [$header_row];
			while ($my_row = $db->get_row()) {
				$rows[] = [$my_row['RUT'], $my_row['DV'], $my_row['COD_DEUDA'], $my_row['COD_TIPO_DEUDA'],
				           $my_row['SALDO_ANT'], $my_row['SALDO_ANT_PESOS'],
				           $my_row['ABONO_MORA_CP'], $my_row['ABONO_MORA_CP_PESOS'],
				           $my_row['SALDO_CUOTA'], $my_row['SALDO_CUOTA_PESOS'],
				           $my_row['FECHA_VENCTO_CUOTA'], $my_row['NOM_CARTERA'],
				           $my_row['PORC'],
				           $my_row['PORC_INTERNO'], $my_row['PORC_CF'], $my_row['PORC_CU']];
			}
		}

		if ($db->database_type == "oci") {
			oci_free_statement($db->query_id);
			$db->query_id = false;
		}

		\Shuchkin\SimpleXLSXGen::fromArray($rows, 'inf_conta_castigo')->downloadAs('inf_conta_castigo.xlsx');
	}
}
?>