<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

ini_set('display_errors', 'Off');
class wi_inf_periodo_gracia_base extends w_param_informe_helen {
	var $cod_tipo_deuda;
	var $ano;
	
	function __construct($cod_item_menu) {
		$xml = session::get('K_ROOT_DIR').'appl/inf_periodo_gracia/inf_periodo_gracia.xml';
		$sql_informe = sql_inf_periodo_gracia::get_sql('INPUT');
		parent::__construct('inf_periodo_gracia', $cod_item_menu, 'Informe CTY', $xml, $sql_informe, 'spi_periodo_gracia');
		
		$sql = sql_inf_periodo_gracia::get_sql('INPUT_CONTROL');
		$this->dws['dw_param'] = new datawindow($sql);
		
		$sql = sql_inf_periodo_gracia::get_sql('DD_COD_TIPO_DEUDA');
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_TIPO_DEUDA', $sql));
		$this->dws['dw_param']->add_control(new edit_num('ANO', 4, 4));
		
		// mandatorys	
		$this->dws['dw_param']->set_mandatory('COD_TIPO_DEUDA', 'Tipo deuda');	
		$this->dws['dw_param']->set_mandatory('ANO', 'Año');	
	}
	function make_filtro() {
		$cod_tipo_deuda = $this->dws['dw_param']->get_item(0, 'COD_TIPO_DEUDA');
		$ano   		 = $this->dws['dw_param']->get_item(0, 'ANO');

		// Filtro
		$nom_tipo_deuda = $this->dws['dw_param']->controls['COD_TIPO_DEUDA']->get_label_from_value($cod_tipo_deuda);
		$this->filtro = "Tipo deuda  = '$nom_tipo_deuda'\n";
		$this->filtro = "Año = $ano\n";
		$this->cod_tipo_deuda = "$cod_tipo_deuda";
		$this->ano = $ano;
		// Arma los parametros para el SP
		$this->param = "'$cod_tipo_deuda', $ano";
		}
	function genera_xls() {
		ini_set('memory_limit', '128M');
		ini_set('max_execution_time', 300); //300 seconds = 5 minutes

		require_once dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/php_writeexcel-0.3.0/class.writeexcel_workbook.inc.php";
		require_once dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/php_writeexcel-0.3.0/class.writeexcel_worksheet.inc.php";
		
		$fname = tempnam("/tmp", "export.xls");
		$workbook = new writeexcel_workbook($fname);
		$worksheet = $workbook->addworksheet('DEUDORES PERIODO GRACIA');
		
		// escribe encabezados
		# Create a format for the column headings
		$header =& $workbook->addformat();
		$header->set_bold();
		$header->set_color('blue');

		$worksheet->write(0, $col++,  'RUT', $header);
		$worksheet->write(0, $col++,  'NOMBRE', $header);
		$worksheet->write(0, $col++,  'TIPO DEUDA', $header);
		$worksheet->write(0, $col++,  'CODIGO DEUDA', $header);
		$worksheet->write(0, $col++,  'FECHA INICIO COBRO', $header);
		$worksheet->write(0, $col++,  'MONTO INICIO COBRO', $header);
		$worksheet->write(0, $col++,  'SALDO A LA FECHA', $header);
		$worksheet->write(0, $col++,  'DIRECCION', $header);
		$worksheet->write(0, $col++,  'COMUNA', $header);
		$worksheet->write(0, $col++,  'CIUDAD', $header);
		$worksheet->write(0, $col++,  'REGION', $header);
		$worksheet->write(0, $col++,  'TELEFONO', $header);
		$worksheet->write(0, $col++,  'MAIL', $header);
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

		$this->sp = "spi_periodo_gracia";
		if (K_TIPO_BD=="mssql") {
			$sql = "exec ".$this->sp." ".$this->param;
		}
		elseif (K_TIPO_BD=="oci") {
			$db->EXECUTE_SP($this->sp, $this->param);
			$sql = "SELECT   RUT
							,NOMBRE
							,TIPO_DEUDA
							,COD_DEUDA
							,FECHA_INICIO_COBRO
							,MONTO_INICIO_COBRO
							,SALDO_ACTUAL_DEUDA
							,DIRECCION
							,COMUNA
							,CIUDAD
							,REGION
							,TELEFONO
							,MAIL          
					FROM	TEMP_PERIODO_GRACIA
					ORDER BY RUT";
		}
		$res = $db->query($sql);
			
		$i = 0;
		while($my_row = $db->get_row()){
			if ($worksheet->_datasize > 7000000)
				break;
				
			$columna = 0;
			for ($j=0; $j <  $col; $j++) {		
				$worksheet->write($i + 1, $columna, $my_row[$j]);
				$columna++;
			}
			$i++;
		}
		if ($worksheet->_datasize > 7000000) {
			$worksheet->write($i + 1, 0, 'No se completo la exportación de datos porque excede el máximo del tamaño de archivo 7 MB', $header);
		}
		
		if($db->database_type=="oci") {
			oci_free_statement($db->query_id);
			$db->query_id = false;
		}
		
		$workbook->close();
		
		header("Content-Type: application/x-msexcel; name=\"$this->nom_tabla\"");
		header("Content-Disposition: inline; filename=\"$this->nom_tabla.xls\"");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
	}
}

// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wi_inf_periodo_gracia.php";
if (file_exists($file_name)) {
	require_once($file_name);
}
else {
	class wi_inf_periodo_gracia extends wi_inf_periodo_gracia_base {
	    function __construct($cod_item_menu) {
	        parent::__construct($cod_item_menu); 
		}
	}
}
?>