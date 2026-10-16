<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");

//ini_set('display_errors', 'ON');
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 1800);//30 minutos


class wi_inf_abonos extends w_param_informe_helen {
    function __construct($cod_item_menu) {
        $session_id = session_id();
       // echo $session_id.'$session_id<br>';
		$xml = session::get('K_ROOT_DIR').'appl/inf_abonos/inf_abonos.xml';
		
		$sql_informe = sql_inf_abonos::get_sql('INPUT',$session_id);		
		//echo $sql_informe;
		$fecha = getdate();
		$ano   = $fecha["year"];
		
		parent::__construct('inf_abonos', $cod_item_menu, 'Informe Abonos.pdf', $xml, $sql_informe, 'spi_abono');
		
		$sql = sql_inf_abonos::get_sql('INPUT_FECHA', $ano);
		
		$this->dws['dw_param'] = new datawindow($sql);
		
		
		$sql1 = "select COD_FAMILIA_DEUDA 
                		, NOM_FAMILIA_DEUDA
                from 	 FAMILIA_DEUDA
                order by COD_FAMILIA_DEUDA";
		
		$this->dws['dw_param']->add_control($control = new drop_down_dw('COD_FAMILIA_DEUDA', $sql1));
		$control->set_drop_down_dependiente('inf_abonos', 'COD_TIPO_DEUDA');
	
		
		$sql2 = "select 'TODAS'	COD_TIPO_DEUDA
						,'TODAS' NOM_TIPO_DEUDA				
				FROM DUAL UNION
                select COD_TIPO_DEUDA 
                		, NOM_TIPO_DEUDA
                from 	 TIPO_DEUDA
                WHERE COD_FAMILIA_DEUDA={KEY1}
                order by COD_TIPO_DEUDA";
		
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_TIPO_DEUDA', $sql2));
		$this->dws['dw_param']->add_control(new edit_date('FECHA_INICIAL', 10, 10));
		$this->dws['dw_param']->add_control(new edit_date('FECHA_FINAL', 10, 10));

		// mandatorys		
		$this->dws['dw_param']->set_mandatory('COD_TIPO_DEUDA', 'Tipo Deuda');	
		$this->dws['dw_param']->set_mandatory('FECHA_INICIAL', 'Fecha Inicial');
		$this->dws['dw_param']->set_mandatory('FECHA_FINAL', 'Fecha Final');	
	}
	function make_filtro() {
	    $cod_familia_deuda = $this->dws['dw_param']->get_item(0, 'COD_FAMILIA_DEUDA');
		$cod_tipo_deuda = $this->dws['dw_param']->get_item(0, 'COD_TIPO_DEUDA');
		$fecha_inicial    = $this->dws['dw_param']->get_item(0, 'FECHA_INICIAL');
		$fecha_final      = $this->dws['dw_param']->get_item(0, 'FECHA_FINAL');
		$session_id = session_id();
		// Filtro
		$nom_familia_deuda = $this->dws['dw_param']->controls['COD_FAMILIA_DEUDA']->get_label_from_value($cod_familia_deuda);
		$nom_tipo_deuda = $this->dws['dw_param']->controls['COD_TIPO_DEUDA']->get_label_from_value($cod_tipo_deuda);
		$this->filtro = "Familia Deuda = $nom_familia_deuda\n";
		$this->filtro = "Tipo Deuda = $nom_tipo_deuda\n";
		$this->filtro .= "Fecha Inicial = $fecha_inicial; Fecha final = $fecha_final\n";
		
		// Arma los parametros para el SP
		//$fecha_inicial = $this->str2date($fecha_inicial);
		//$fecha_final = $this->str2date($fecha_final, '23:59:59');
		$this->param = "'$session_id','$cod_tipo_deuda','$fecha_inicial', '$fecha_final',$cod_familia_deuda";
		//$this->sp = 'spi_abono';
		

	}	
	function genera_xls() {
	    ini_set('memory_limit', '-1');
	    ini_set('max_execution_time', 1800);//30 minutos

	    require_once dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/libs/simplexlsxgen/src/SimpleXLSXGen.php";

	    //echo $this->sp .''.$this->param;
	    $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	    $session_id = session_id();
	    $this->sp = "spi_abono";
	    if (K_TIPO_BD=="mssql") {
	        $sql = "exec ".$this->sp." ".$this->param;
	    }
	    elseif (K_TIPO_BD=="oci") {
	        $db->EXECUTE_SP($this->sp, $this->param);
	        
	     
	      
	        $sql = "SELECT      RUT
								  ,DV
								
								  ,TIPO_DEUDA
								  , to_char(INICIO_COBRO, 'dd/mm/yyyy') INICIO_COBRO
								  ,CARTERA
								  ,UNIVERSIDAD
								  ,ESTADO_DEUDA
								  ,DE.COD_DEUDA
								  
								  ,FOLIO
								  ,FOLIO_ORSAN
								  ,COMPONENTE_DEUDA
								  
								  ,NRO_CUOTA
								  ,ANO_CUOTA
								  ,to_char(FECHA_VENCTO, 'dd/mm/yyyy') FECHA_VENCTO
								  ,NOM_CONCEPTO
								  
								  , to_char(FECHA_PAGO, 'dd/mm/yyyy') FECHA_PAGO
								  , to_char(FECHA_REGISTRO_ABONO, 'dd/mm/yyyy') FECHA_REGISTRO_ABONO
								  , to_char(FECHA_DEPOSITO, 'dd/mm/yyyy') FECHA_DEPOSITO
								  ,ENTIDAD
								  ,TIPO_DOCUMENTO_PAGO
								  ,CAP_UTM
								  ,INT_UTM
								  ,INT_MULTA_UTM
								  ,TOTAL_UTM
								  ,CAP_PESOS
								  ,INT_PESOS
								  ,INT_MULTA_PESOS
								  ,TOTAL_PESOS
								  ,UTM_MES_PAGO
								  ,TIPO_ABONO
								  ,ORDEN_COMPRA
								
								  ,ESTADO_EXCEDENTE
								  , to_char(FECHA_EXCEDENTE, 'dd/mm/yyyy') FECHA_EXCEDENTE
								  ,MONTO_EXCEDENTE
								  ,MONTO_EXCEDENTE_PESOS
								  
								  , to_char(FECHA_REGISTRO_DESCUENTO, 'dd/mm/yyyy') FECHA_REGISTRO_DESCUENTO
								  , to_char(FECHA_DESCUENTO, 'dd/mm/yyyy') FECHA_DESCUENTO
								  ,ESTADO_DESCUENTO
								  , ORIGEN_PAGO
								  , CAP_UTM_DESCUENTO
								  , INT_UTM_DESCUENTO
								  , MULTA_UTM_DESCUENTO
								  , TOTAL_UTM_DESCUENTO
								  , CAP_PESOS_DESCUENTO
								  , INT_PESOS_DESCUENTO
								  , INT_MULTA_PESOS_DESCUENTO
								  , TOTAL_PESOS_DESCUENTO
								  , UTM_MES_PAGO_DESCUENTO
								
								  , PORC_USACH
								  , to_char(FECHA_CONSOLIDACION, 'dd/mm/yyyy') FECHA_CONSOLIDACION
								  , PORC_FONDO
								  , to_char(FECHA_CONSOLIDACION_INT, 'dd/mm/yyyy') FECHA_CONSOLIDACION_INT
								  , PORC_CU
								  , PORC_CF
								  , COMPROBANTE
								  , COMPLEMENTO_OTRAS_UES
								  , ABONO_TOTAL
                            FROM INF_ABONO IA , ESTADO_PAGO EP, DEUDA DE, CARTERA C, UNIVERSIDAD U
                            WHERE IA.COD_ESTADO_PAGO=EP.COD_ESTADO_PAGO
                            AND IA.COD_DEUDA = DE.COD_DEUDA
                            AND DE.COD_CARTERA = C.COD_CARTERA
                            AND U.COD_UNIVERSIDAD = DE.COD_UNIVERSIDAD
                            and SESSION_ID = '$session_id'
                            ORDER BY FECHA_PAGO ASC";
	    }
	    $res = $db->query($sql);

	    $rows = [['<b>RUT</b>', '<b>DV</b>', '<b>TIPO DEUDA</b>', '<b>INICIO COBRO</b>', '<b>CARTERA</b>', '<b>UNIVERSIDAD</b>', '<b>ESTADO DEUDA</b>', '<b>CODIGO DEUDA</b>', '<b>FOLIO A CODIGO DE ABONO</b>', '<b>FOLIO ORSAN A FOLIO OTRA ENTIDAD</b>', '<b>COMPONENTE DEUDA</b>', '<b>NRO CUOTA</b>', '<b>AÑO CUOTA</b>', '<b>FECHA VENCTO</b>', '<b>CONCEPTO COBRO</b>', '<b>FECHA ABONO</b>', '<b>FECHA REGISTRO</b>', '<b>FECHA DEPÓSITO</b>', '<b>ENTIDAD</b>', '<b>TIPO DOC. PAGO</b>', '<b>CAPITAL. UTM</b>', '<b>INTERÉS. UTM</b>', '<b>MULTA. UTM</b>', '<b>MONTO MONEDA UTM</b>', '<b>CAPITAL. PESOS</b>', '<b>INTERÉS. PESOS</b>', '<b>MULTA. PESOS</b>', '<b>MONTO MONEDA PESOS</b>', '<b>UTM DEL MES PAGO</b>', '<b>TIPO ABONO</b>', '<b>ORDEN COMPRA TRANSBANK</b>', '<b>ESTADO EXCEDENTE</b>', '<b>FECHA EXCEDENTE</b>', '<b>MONTO EXCEDENTE UTM</b>', '<b>MONTO EXCEDENTE PESOS</b>', '<b>FECHA DE REGISTRO</b>', '<b>FECHA DE DESCUENTO</b>', '<b>ESTADO DESCUENTO</b>', '<b>ORIGEN PAGO</b>', '<b>CAPITAL. UTM</b>', '<b>INTERÉS. UTM</b>', '<b>MULTA. UTM</b>', '<b>MONTO MONEDA UTM</b>', '<b>CAPITAL. PESOS</b>', '<b>INTERÉS. PESOS</b>', '<b>MULTA. PESOS</b>', '<b>MONTO MONEDA PESOS</b>', '<b>UTM DEL MES PAGO</b>', '<b>PORCEJATE USACH</b>', '<b>FECHA CONSOLIDACION USACH</b>', '<b>PORCEJATE FONDO A  PORCEJATE INTERNO</b>', '<b>FECHA CONSOLIDACION INTERNO</b>', '<b>PORCENTAJE CU</b>', '<b>PORCENTAJE CF</b>', '<b>COMPROBANTE</b>', '<b>COMPLEMENTO_OTRAS_UES</b>', '<b>ABONO_TOTAL</b>']];
	    while ($my_row = $db->get_row()) {
	        $rows[] = [$my_row[0], $my_row[1], $my_row[2], $my_row[3], $my_row[4], $my_row[5], $my_row[6], $my_row[7], $my_row[8], $my_row[9], $my_row[10], $my_row[11], $my_row[12], $my_row[13], $my_row[14], $my_row[15], $my_row[16], $my_row[17], $my_row[18], $my_row[19], $my_row[20], $my_row[21], $my_row[22], $my_row[23], $my_row[24], $my_row[25], $my_row[26], $my_row[27], $my_row[28], $my_row[29], $my_row[30], $my_row[31], $my_row[32], $my_row[33], $my_row[34], $my_row[35], $my_row[36], $my_row[37], $my_row[38], $my_row[39], $my_row[40], $my_row[41], $my_row[42], $my_row[43], $my_row[44], $my_row[45], $my_row[46], $my_row[47], $my_row[48], $my_row[49], $my_row[50], $my_row[51], $my_row[52], $my_row[53], $my_row[54], $my_row[55], $my_row[56]];
	    }

	    if ($db->database_type == "oci") {
	        oci_free_statement($db->query_id);
	        $db->query_id = false;
	    }

	    \Shuchkin\SimpleXLSXGen::fromArray($rows, 'Informe Abonos')->downloadAs($this->nom_tabla.'.xlsx');
	}
}
?>