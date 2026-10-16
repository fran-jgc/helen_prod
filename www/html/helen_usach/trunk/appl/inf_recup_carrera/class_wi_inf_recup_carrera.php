<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");
ini_set('memory_limit', '480M');
ini_set('max_execution_time', 900); //900 seconds = 15 minutes


class wi_inf_recup_carrera extends w_param_informe_helen {
	var $orientation = 'P';
	
	function __construct($cod_item_menu) {
		$xml = session::get('K_ROOT_DIR').'appl/inf_recup_carrera/inf_recup_carrera.xml';
		
		$sql_informe = sql_inf_recup_carrera::get_sql('INPUT');
		
		parent::__construct('inf_recup_carrera', $cod_item_menu, 'Recuperación por Carrera', $xml, $sql_informe, 'spi_recup_carrera');

		$sql = sql_inf_recup_carrera::get_sql('YEAR');
		
		$this->dws['dw_param'] = new datawindow($sql);

		$sql = sql_inf_recup_carrera::get_sql('INPUT_DEUDA');
		
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_FAMILIA_DEUDA', $sql));
		$this->dws['dw_param']->add_control(new edit_num('ANO', 4, 4));

		
		$this->dws['dw_param']->add_control(new edit_radio_button('RESUMEN', 'RESUMEN', 'DETALLE','Resumen', 'FORMATO'));
		$this->dws['dw_param']->add_control(new edit_radio_button('DETALLE', 'DETALLE', 'RESUMEN','Detalle', 'FORMATO'));
		
		// mandatorys		
		$this->dws['dw_param']->set_mandatory('COD_FAMILIA_DEUDA', 'Familia Deuda');	
		$this->dws['dw_param']->set_mandatory('ANO', 'Año');		
		
		$this->dws['dw_param']->add_control(new static_text('FECHA_FLUJO_SVP',10,10));
		$this->dws['dw_param']->add_control(new static_text('FECHA_FLUJO_EXIGIBLE',10,10));
			
	}
	
	function make_filtro() {
	 	$cod_familia_deuda = $this->dws['dw_param']->get_item(0, 'COD_FAMILIA_DEUDA');
		$ano = $this->dws['dw_param']->get_item(0, 'ANO'); 
		$resumen 		= $this->dws['dw_param']->get_item(0, 'RESUMEN');
		$detalle 		= $this->dws['dw_param']->get_item(0, 'DETALLE');
		
		if($resumen =='RESUMEN'){
			// Filtro
			$nom_familia = $this->dws['dw_param']->controls['COD_FAMILIA_DEUDA']->get_label_from_value($cod_familia_deuda);
			$this->filtro = "Familia Deuda = $nom_familia\n";
			
			$this->filtro .= "Año = $ano\n";
			$this->orientation = 'P';
			
			// Arma los paramaetros para el SP
			$this->sp = "spi_recup_carrera";
			$this->param = "$cod_familia_deuda, $ano, $this->cod_usuario";
			$this->sql_informe = "SELECT	COD_CARRERA
							        		,NOM_CARRERA
							        		,CANT_DEUDOR
							        		,MONTO_DEUDA_VIGENTE
							        		,MONTO_ABONO_VIGENTE
							        		,MONTO_ABONO_VENCIDA
							        		,MONTO_ABONO_ANTICIPADO
							        		,MONTO_ABONO_OTRO
							        		,MONTO_TOTAL
									FROM	TEMP_RECUP_CARRERA
									where COD_USUARIO = $this->cod_usuario
									ORDER BY	COD_CARRERA";
			
			$this->xml = session::get('K_ROOT_DIR').'appl/inf_recup_carrera/inf_recup_carrera.xml';
		}
		else if($detalle =='DETALLE'){
			$nom_familia = $this->dws['dw_param']->controls['COD_FAMILIA_DEUDA']->get_label_from_value($cod_familia_deuda);
			$this->filtro = "Familia Deuda = $nom_familia\n";
			
			$this->filtro .= "Año = $ano\n";
			
			$this->orientation = 'L';
			
			$this->sp = "spi_recup_carrera";
			$this->param = "$cod_familia_deuda, $ano, $this->cod_usuario";
			$this->sql_informe = "SELECT  COD_DEUDOR
									  	,NOM_DEUDOR
									  	,RUT_DEUDOR
									  	,DIG_VERIF
									  	,COD_CARRERA
									    ,NOM_CARRERA
									    ,MONTO_DEUDA_VIGENTE
									    ,MONTO_ABONO_VIGENTE_UTM
									    ,MONTO_ABONO_VIGENTE_PESOS
									    ,MONTO_ABONO_VENCIDA_UTM
									    ,MONTO_ABONO_VENCIDA_PESOS
									    ,MONTO_ABONO_ANTICIPADO_UTM
									    ,MONTO_ABONO_ANTICIPADO_PESOS
									    ,MONTO_ABONO_OTRO_UTM
									    ,MONTO_ABONO_OTRO_PESOS
									    ,MONTO_ABONO_TOTAL_UTM
									    ,MONTO_ABONO_TOTAL_PESOS
									FROM TEMP_RECUP_CARRERA_DETALLE
									where COD_USUARIO = $this->cod_usuario";
			
			$this->xml = session::get('K_ROOT_DIR').'appl/inf_recup_carrera/inf_recup_carrera_detalle.xml';
		}		
	}
	
	function genera_detalle_xls() {
		require_once dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/libs/simplexlsxgen/src/SimpleXLSXGen.php";
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 1800);//30 minutos

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$db->EXECUTE_SP($this->sp, $this->param);
		$sql = $this->sql_informe;
		$res = $db->query($sql);

		$rows = [['<b>CODIGO</b>', '<b>DEUDOR</b>', '<b>RUT</b>', '<b>DV</b>',
		          '<b>COD CARRERA</b>', '<b>NOMBRE CARRERA</b>', '<b>MONTO DEUDA VIGENTE</b>',
		          '<b>MONTO ABONO VIGENTE UTM</b>', '<b>MONTO ABONO VIGENTE PESOS</b>',
		          '<b>MONTO ABONO VENCIDA UTM</b>', '<b>MONTO ABONO VENCIDA PESOS</b>',
		          '<b>MONTO ABONO ANTICIPADO UTM</b>', '<b>MONTO ABONO ANTICIPADO PESOS</b>',
		          '<b>MONTO ABONO OTRO UTM</b>', '<b>MONTO ABONO OTRO PESOS</b>',
		          '<b>MONTO ABONO TOTAL UTM</b>', '<b>MONTO ABONO TOTAL PESOS</b>']];
		while ($my_row = $db->get_row()) {
			$rows[] = [$my_row['COD_DEUDOR'], $my_row['NOM_DEUDOR'], $my_row['RUT_DEUDOR'],
			           $my_row['DIG_VERIF'], $my_row['COD_CARRERA'], $my_row['NOM_CARRERA'],
			           $my_row['MONTO_DEUDA_VIGENTE'], $my_row['MONTO_ABONO_VIGENTE_UTM'],
			           $my_row['MONTO_ABONO_VIGENTE_PESOS'], $my_row['MONTO_ABONO_VENCIDA_UTM'],
			           $my_row['MONTO_ABONO_VENCIDA_PESOS'], $my_row['MONTO_ABONO_ANTICIPADO_UTM'],
			           $my_row['MONTO_ABONO_ANTICIPADO_PESOS'], $my_row['MONTO_ABONO_OTRO_UTM'],
			           $my_row['MONTO_ABONO_OTRO_PESOS'], $my_row['MONTO_ABONO_TOTAL_UTM'],
			           $my_row['MONTO_ABONO_TOTAL_PESOS']];
		}

		if ($db->database_type == "oci") {
			oci_free_statement($db->query_id);
			$db->query_id = false;
		}

		\Shuchkin\SimpleXLSXGen::fromArray($rows, 'Detalle')->downloadAs('inf_recuperacion_carrera.xlsx');
	}
	function genera_resumen_xls() {
		require_once dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/libs/simplexlsxgen/src/SimpleXLSXGen.php";
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 1800);//30 minutos

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$db->EXECUTE_SP($this->sp, $this->param);
		$sql = $this->sql_informe;
		$res = $db->query($sql);

		$rows = [['<b>CODIGO CARRERA</b>', '<b>NOMBRE CARRERA</b>', '<b>CANT DEUDOR</b>',
		          '<b>MONTO DEUDA VIGENTE</b>', '<b>MONTO ABONO VIGENTE</b>',
		          '<b>MONTO ABONO VENCIDA</b>', '<b>MONTO ABONO ANTICIPADO</b>',
		          '<b>MONTO ABONO OTRO</b>', '<b>MONTO TOTAL</b>']];
		while ($my_row = $db->get_row()) {
			$rows[] = [$my_row['COD_CARRERA'], $my_row['NOM_CARRERA'],
			           $my_row['CANT_DEUDOR'], $my_row['MONTO_DEUDA_VIGENTE'],
			           $my_row['MONTO_ABONO_VIGENTE'], $my_row['MONTO_ABONO_VENCIDA'],
			           $my_row['MONTO_ABONO_ANTICIPADO'], $my_row['MONTO_ABONO_OTRO'],
			           $my_row['MONTO_TOTAL']];
		}

		if ($db->database_type == "oci") {
			oci_free_statement($db->query_id);
			$db->query_id = false;
		}

		\Shuchkin\SimpleXLSXGen::fromArray($rows, 'Resumen')->downloadAs('inf_recuperacion_carrera.xlsx');
	}
	function genera_xls() {
		$resumen 		= $this->dws['dw_param']->get_item(0, 'RESUMEN');
		$detalle 		= $this->dws['dw_param']->get_item(0, 'DETALLE');
		
		if($detalle =='DETALLE'){
			$this->genera_detalle_xls();
		}else{
			$this->genera_resumen_xls();
		}
	}
}
?>