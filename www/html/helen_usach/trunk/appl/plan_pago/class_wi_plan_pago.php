<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_print_plan_pago.php");
/*
 Clase : WI_PLAN_PAGO
 */
class wi_plan_pago extends w_input {
	const K_ESTADO_PLAN_MOROSO = 4;
	const K_COD_ORIGEN_PAGO_CAJA = 1;
	
	function __construct($cod_item_menu) {
	    parent::__construct('plan_pago', $cod_item_menu);
			$this->b_delete_visible  = false;
			
			///////////////// CLASS_SQL = INPUT ///////////////
			$sql = sql_plan_pago::get_sql('INPUT');
			
			$this->dws['dw_plan_pago'] = new datawindow($sql);
			$this->dws['dw_plan_pago']->add_control(new static_num('RUT'));
			
			$sql_emp_cobr =sql_plan_pago::get_sql('DD_COD_EMPRESA_COBRANZA');
			$this->dws['dw_plan_pago']->add_control(new drop_down_dw('COD_EMPRESA_COBRANZA', $sql_emp_cobr, 140));
			
			$this->dws['dw_plan_pago']->add_control(new edit_check_box('PUBLICA_DICOM', 'S', 'N'));
			$this->dws['dw_plan_pago']->add_control(new edit_text_upper('MONTO_CAPITAL_H', 10, 10, 'hidden'));
			$this->dws['dw_plan_pago']->add_control(new edit_text_upper('MONTO_INTERES_H', 10, 10, 'hidden'));
			$this->dws['dw_plan_pago']->add_control(new edit_text_upper('COD_CONCEPTO_COBRO_H', 10, 10, 'hidden')); 
			
			$this->dws['dw_plan_pago_resumen'] = new dw_plan_pago_resumen($sql);
			$this->dws['dw_pp_abono'] = new dw_pp_abono($sql);
			$this->dws['dw_pp_descuento'] = new dw_pp_descuento($sql);
			$this->dws['dw_pp_detalle_pago'] = new dw_pp_detalle_pago($sql);
			//	$this->dws['dw_pp_descuento_ttal'] = new dw_pp_descuento_ttal($sql);
		}
		
		function new_record() {
			//$this->dws['dw_plan_pago']->insert_row();
		}
		
		function load_record() {
			$cod_plan_pago	= $this->get_item_wo($this->current_record, 'COD_PLAN_PAGO');
			$this->dws['dw_plan_pago']->retrieve($cod_plan_pago);
			$this->dws['dw_plan_pago_resumen']->retrieve($cod_plan_pago);
			$this->dws['dw_pp_abono']->retrieve($cod_plan_pago);
			$this->dws['dw_pp_descuento']->retrieve($cod_plan_pago);
			if(K_TIPO_BD == "oci"){
				$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
				$param = "$cod_plan_pago, ".self::K_COD_ORIGEN_PAGO_CAJA.", 'DETALLE_PAGO'";
				$db->EXECUTE_SP('sp_input_dw_pp_detalle_pago', $param);
			}
						
			$this->dws['dw_pp_detalle_pago']->retrieve($cod_plan_pago);
			$this->b_print_visible		= true;
			
			$cod_estado_plan_pago = $this->dws['dw_plan_pago']->get_item(0, 'COD_ESTADO_PLAN_PAGO');
		}
		
		function get_key() {
			return $this->dws['dw_plan_pago']->get_item(0, 'COD_PLAN_PAGO');
		}
		
		function save_record($db) {
			//sin sentencias
			
            $cod_plan_pago			= $this->dws['dw_plan_pago']->get_item(0, 'COD_PLAN_PAGO');
			$cod_deuda				= $this->dws['dw_plan_pago']->get_item(0, 'COD_DEUDA');
			$nro_cuota				= $this->dws['dw_plan_pago']->get_item(0, 'NRO_CUOTA');
			$ano_cobro				= $this->dws['dw_plan_pago']->get_item(0, 'ANO_COBRO');
			$monto_capital			= $this->dws['dw_plan_pago']->get_item(0, 'MONTO_CAPITAL_H');
			$monto_interes			= $this->dws['dw_plan_pago']->get_item(0, 'MONTO_INTERES_H');
			$fecha_vencto			= $this->dws['dw_plan_pago']->get_item(0, 'FECHA_VENCTO');
			$cod_concepto_cobro		= $this->dws['dw_plan_pago']->get_item(0, 'COD_CONCEPTO_COBRO_H');
			$cod_estado_plan_pago	= $this->dws['dw_plan_pago']->get_item(0, 'COD_ESTADO_PLAN_PAGO');
			$cod_empresa_cobranza	= $this->dws['dw_plan_pago']->get_item(0, 'COD_EMPRESA_COBRANZA');
			$publica_dicom			= $this->dws['dw_plan_pago']->get_item(0, 'PUBLICA_DICOM');
			
			$cod_concepto_cobro		= ($cod_concepto_cobro =='') ? "NULL" : "$cod_concepto_cobro";
			$cod_estado_plan_pago	= ($cod_estado_plan_pago =='') ? "NULL" : "$cod_estado_plan_pago";
			$cod_empresa_cobranza	= ($cod_empresa_cobranza ==0) ? "NULL" : "$cod_empresa_cobranza";
			$publica_dicom			= ($publica_dicom =='') ? "NULL" : "'$publica_dicom'";
			
			if ($fecha_vencto != "null"){
                $fecha_vencto = $this->str2date($fecha_vencto);
			}
			
			$sp = 'spu_plan_pago';
			if ($this->is_new_record()){
				$operacion = 'INSERT';
			}
			else{
				$operacion = 'UPDATE';
			}
			$param	= "'$operacion'
					,$cod_plan_pago
					,$cod_deuda
					,$nro_cuota
					,$ano_cobro
					,$monto_capital
					,$monto_interes
					,$fecha_vencto
					,$cod_concepto_cobro
					,$cod_estado_plan_pago
					,$cod_empresa_cobranza
					,$publica_dicom";

			if ($db->EXECUTE_SP($sp, $param)){
				if ($this->is_new_record()) {
					$cod_plan_pago = $db->GET_IDENTITY('plan_pago');
					$this->dws['dw_plan_pago']->set_item(0, 'COD_PLAN_PAGO', $cod_plan_pago);
				}
			}
			
			return true;		
		
		}
		
		function print_record() {
			$cod_plan_pago = $this->get_key();
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			
			$param = "$cod_plan_pago, ".self::K_COD_ORIGEN_PAGO_CAJA.", 'PRINT'";
			$db->EXECUTE_SP('sp_input_dw_pp_detalle_pago', $param);
			
		 	$sql= sql_plan_pago::get_sql('PRINT_RECORD');
		 	
		 	$result = $db->build_results($sql);
		 	//print_r($result); return false;
			// reporte
			$labels = array();
			$file_name = $this->find_file('plan_pago', 'plan_pago.xml');					
			$rpt = new print_plan_pago($cod_plan_pago, $sql, $file_name, $labels, "Plan Pago".$cod_plan_pago.'.pdf', 0);										
			//$rpt = new reporte($cod_plan_pago, $sql, $file_name, $labels, "Plan Pago".$cod_plan_pago, 0);
			$this->_load_record();
		}
	}
/*
class print_plan_pago extends reporte {
	const K_PARAM_UNIVERSIDAD	= 5;
	const K_ESTADO_SIMULACION	= 1000;
	const K_COD_ORIGEN_PAGO_CAJA =1;
	const K_FONDO_SOLIDARIO      = 1;
    const K_CREDITO_INSTITUCIONAL = 2;
	var $cod_plan_pago;
	
	function __construct ($cod_plan_pago, $sql, $xml, $labels=array(), $titulo, $con_logo, $vuelve_a_presentacion=false) {
    	$this->cod_plan_pago = $cod_plan_pago; 
    	
    	parent::__construct($sql, $xml, $labels, $titulo, $con_logo, $vuelve_a_presentacion);

    }
    
	function modifica_pdf(&$pdf) {

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result = $db->build_results($this->sql);
		print_r($result); echo 'lalalal'; return false;
        // PRINT UACH: FS, CI
        $cod_deuda = $result[0]['COD_DEUDA'];
        $db_familia = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
        $cod_deuda	= $result[0]['COD_DEUDA'];
       	$sql_familia_deuda = "SELECT FD.COD_FAMILIA_DEUDA
							FROM FAMILIA_DEUDA FD, DEUDA D, TIPO_DEUDA TD
					   		WHERE D.COD_DEUDA = $cod_deuda
					    	AND D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
					    	AND TD.COD_FAMILIA_DEUDA = FD.COD_FAMILIA_DEUDA";

		$result_familia = $db_familia->build_results($sql_familia_deuda);
		$cod_familia_deuda = $result_familia[0]['COD_FAMILIA_DEUDA'];
        
		if(K_TIPO_BD == "oci"){
			$param = "$this->cod_plan_pago, ".self::K_COD_ORIGEN_PAGO_CAJA.", 'PRINT'";
			$db->EXECUTE_SP('sp_input_dw_pp_detalle_pago', $param);
			}

		$db_uni = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
        $sql_uni= sql_ingreso::get_sql('PRINT_PARAM',self::K_PARAM_UNIVERSIDAD);
        $result_uni = $db_uni->build_results($sql_uni);

		//para el caso de la UV el encabezado es distinto
		if(session::get('K_CLIENTE') == 'UV'){
			$factor = 0.45;
			$pdf->Image(dirname(__FILE__)."/../../images_appl/".session::get('K_CLIENTE')."/logo_universidad.jpg", 40, 20, $factor * 286, $factor * 147);
			$pdf->SetFont('Helvetica','B',8);
			$pdf->Text(200, 765,'FONDO SOLIDARIO DE CRÉDITO UNIVERSITARIO');
		//EL LOGO DE LA ULAGOS ES = AL DE LA UV POR LO CUAL CAMBIE EL TAMaño ASIGNADO.	
		}else if(session::get('K_CLIENTE') == 'ULAGOS'){
			$factor = 0.45;
			$pdf->Image(dirname(__FILE__)."/../../images_appl/".session::get('K_CLIENTE')."/logo_universidad.jpg", 40, 20, $factor * 286, $factor * 147);
			$pdf->SetFont('Helvetica','B',8);
			$pdf->Text(200, 42,'FONDO SOLIDARIO DE CRÉDITO UNIVERSITARIO');
	        $pdf->Text(200, 51,$result_uni[0]['NOM_UNIVERSIDAD']);
		}else if(session::get('K_CLIENTE') == 'UACH'){
         	// PRINT UACH: FS, CI
         	$factor = 0.45;
			$pdf->Image(dirname(__FILE__)."/../../images_appl/".session::get('K_CLIENTE')."/logo_universidad.jpg", 40, 20, $factor * 200, $factor * 180);
			if($cod_familia_deuda == self::K_FONDO_SOLIDARIO){
			$pdf->SetFont('Helvetica','B',12);
			$pdf->Text($iniX + 140, $iniY + 60,'FONDO SOLIDARIO DE CRÉDITO UNIVERSITARIO');
			}else if($cod_familia_deuda == self::K_CREDITO_INSTITUCIONAL){ 
			$pdf->SetFont('Helvetica','B',12);
			$pdf->Text($iniX + 140, $iniY + 60,'CRÉDITO INSTITUCIONAL');	
			}
			$pdf->Text($iniX + 140, $iniY + 73,'UNIVERSIDAD AUSTRAL DE CHILE');
		}else{
			$factor = 0.2;
        	$pdf->Image(dirname(__FILE__)."/../../images_appl/".session::get('K_CLIENTE')."/logo_universidad.jpg", 40, 20, $factor * 400, $factor * 457);
			
	        $pdf->SetFont('Helvetica','',8);
	        $pdf->Text(130, 42,'FONDO SOLIDARIO DE CRÉDITO UNIVERSITARIO');
	        $pdf->Text(130, 51,$result_uni[0]['NOM_UNIVERSIDAD']);
		}
		
        //titulo de impresion
        $pdf->SetFont('Helvetica','B',20);
        $pdf->Text(220, 140,'Detalle Pago');
        
        $pdf->SetFont('Helvetica','B',20);
        $pdf->Text(490, 60,'N°:');
        $pdf->SetFont('Helvetica','',20);
        $cod_plan_pago = $result[0]['COD_PLAN_PAGO'];
        $pdf->Text(520, 60, $cod_plan_pago);

        $pdf->SetFont('Helvetica','B',9);
        $pdf->Text(50, 190,'Rut');
        $pdf->Text(105, 190,':');
        $pdf->SetFont('Helvetica','',8);
        $pdf->Text(110, 190,number_format($result [0]['RUT'], 0, ',', '.').' - '.$result[0]['DIG_VERIF']);

        $pdf->SetFont('Helvetica','B',9);
        $pdf->Text(50, 205,'Nombre');
        $pdf->Text(105, 205,':');
        $pdf->SetFont('Helvetica','',8);
        $pdf->Text(110, 205,$result[0]['APELLIDO_PATERNO'].' '.$result[0]['APELLIDO_MATERNO'].' '.$result[0]['NOMBRE']);
        
        $pdf->SetFont('Helvetica','B',9);
        $pdf->Text(50, 220,'Nro. Deuda');
        $pdf->Text(105, 220,':');
        $pdf->SetFont('Helvetica','',8);
        $pdf->Text(110, 220, $result[0]['COD_DEUDA']);
        
        $pdf->SetFont('Helvetica','B',9);
        $pdf->Text(160, 220,'Tipo Deuda');
        $pdf->Text(215, 220,':');
        $pdf->SetFont('Helvetica','',8);
        $pdf->Text(225, 220, $result[0]['COD_TIPO_DEUDA']);
        
        $pdf->SetFont('Helvetica','B',9);
        $pdf->Text(270, 220,'año');
        $pdf->Text(293, 220,':');
        $pdf->SetFont('Helvetica','',8);
        $pdf->Text(305, 220, $result[0]['ANO_COBRO']);
        
        $pdf->SetFont('Helvetica','B',9);
        $pdf->Text(350, 220,'Nro. Cuota');
        $pdf->Text(400, 220,':');
        $pdf->SetFont('Helvetica','',8);
        $pdf->Text(410, 220, $result[0]['NRO_CUOTA']);
        
        //TITULOS  para Detalle Pago.
        $pdf->SetFont('Helvetica','B',7);
        $pdf->Text(50, 255, 'FORMA PAGO');
        $pdf->SetFont('Helvetica','B',8);
		$pdf->SetXY(50, 265);
		$pdf->Cell(45, 15, 'F. Abono', 'LBRT', '','C');
		$pdf->SetXY(95, 265);
		$pdf->Cell(80, 15, 'Tipo Docto.', 'BRT', '','C');
		$pdf->SetXY(175, 265);
		$pdf->Cell(45, 15, 'F. Docto.', 'BRT', '','C');
		$pdf->SetXY(220, 265);
		$pdf->Cell(100, 15, 'Banco', 'BRT', '','C');
		$pdf->SetXY(320, 265);
		$pdf->Cell(60, 15, 'N° Docto.', 'BRT', '','C');
		$pdf->SetXY(380, 265);
		$pdf->Cell(80, 15, 'Plaza', 'BRT', '','C');
		$pdf->SetXY(460, 265);
		$pdf->Cell(60, 15, 'Monto Docto.', 'BRT', '','C');
		$pdf->SetXY(520, 265);
		$pdf->Cell(60, 15, 'Tot. Abono $', 'BRT', '','C');
		
		
		//TABLA DE DETALLE PAGO 
		$SUM_MONTO_DOCUMENTO = 0;
		$SUM_MONTO_PESOS_ABONO = 0;
		for ($i=0, $y=0; $i < count($result); $i++,$y++) {
			$fecha_abono		= $result[$i]['FECHA_ABONO_DETALLE_PAGO'];
			$nom_doc_pago		= $result[$i]['NOM_TIPO_DOCUMENTO_PAGO'];
			$fecha_doc			= $result[$i]['FECHA_DOCUMENTO_PAGO'];
			$nom_banco			= $result[$i]['NOM_BANCO'];
			$nro_documento_pago	= $result[$i]['NRO_DOCUMENTO_PAGO'];
			$nom_plaza			= $result[$i]['NOM_PLAZA'];
			$monto_documento	= number_format($result[$i]['MONTO_DOCUMENTO'], 0, ',', '.');
			$monto_pesos_abono	= number_format($result[$i]['MONTO_PESOS_ABONO'], 0, ',', '.');
			
			$pdf->SetFont('Helvetica','',7);
			$pdf->SetXY(50, 280 +(15 * $y));
			$pdf->Cell(45, 15, $fecha_abono, 'LBR', '','C');
			
			$pdf->SetXY(95, 280 +(15 * $y));
			$pdf->Cell(80, 15, $nom_doc_pago, 'BR', '','C');
			
			$pdf->SetXY(175, 280 +(15 * $y));
			$pdf->Cell(45, 15, $fecha_doc, 'BR', '','C');
			
			$pdf->SetXY(220, 280 +(15 * $y));
			$pdf->Cell(100, 15, $nom_banco, 'BR', '','L');
			
			$pdf->SetXY(320, 280 +(15 * $y));
			$pdf->Cell(60, 15, $nro_documento_pago, 'BR', '','C');
			
			$pdf->SetXY(380, 280 +(15 * $y));
			$pdf->Cell(80, 15, $nom_plaza, 'BR', '','L');
			
			$pdf->SetXY(460, 280 +(15 * $y));
			$pdf->Cell(60, 15, $monto_documento, 'BR', '','R');
			$SUM_MONTO_DOCUMENTO	+= $result[$i]['MONTO_DOCUMENTO'];
			
			$pdf->SetXY(520, 280 +(15 * $y));
			$pdf->Cell(60, 15, $monto_pesos_abono, 'BR', '','R');
			$SUM_MONTO_PESOS_ABONO	+= $result[$i]['MONTO_PESOS_ABONO'];
			
		}
		//totales de deuda
		$pdf->SetFont('Helvetica','B',7);
		$pdf->SetXY(380, 280 +(15 * $y));
		$pdf->Cell(80, 15, 'Total:', 'LBR', '','R');
		$pdf->SetFont('Helvetica','B',8);
		$SUM_MONTO_DOCUMENTO = number_format($SUM_MONTO_DOCUMENTO,0, ',', '.');
		$pdf->SetXY(460, 280 +(15 * $y));
		$pdf->Cell(60, 15, $SUM_MONTO_DOCUMENTO, 'BR', '','R');
		$SUM_MONTO_PESOS_ABONO = number_format($SUM_MONTO_PESOS_ABONO,0, ',', '.');
		$pdf->SetXY(520, 280 +(15 * $y));
		$pdf->Cell(60, 15, $SUM_MONTO_PESOS_ABONO, 'BR', '','R');
		
        
    }
    function make_reporte() {
        
        $p = new ReportParser();
        $p->parseRP($this->xml);
        $rdata = new MySQLRD_helen($this->sql, $this->sp, $this->param);
        
        $pdf = PDF::makePDF(array($p), array($this->labels), array($rdata), $this->con_logo,$this->orientation,$this->unit,$this->format);
        $pdf->SetTitle($this->titulo);
        $this->modifica_pdf($pdf);
        $pdf->Output($this->titulo, 'I');
    }
}  */  	
class dw_plan_pago_resumen extends datawindow{
    function __construct() {
			
			///////////////// CLASS_SQL = INPUT_DW_PLAN_PAGO ///////////////
			$sql = sql_plan_pago::get_sql("INPUT_DW_PLAN_PAGO"); 
			
			parent::__construct($sql, 'PLAN_PAGO_RESUMEN', true, true);
			
			$this->add_control(new static_num('MONTO_CAPITAL',4));
			$this->add_control(new static_num('MONTO_INTERES',4));
			$this->add_control(new static_num('MULTA',4));
			$this->add_control(new static_num('TOTAL_PP',4));
			$this->add_control(new static_num('ABONO_CAPITAL',4));
			$this->add_control(new static_num('ABONO_INTERES',4));
			$this->add_control(new static_num('ABONO_MULTA',4));
			$this->add_control(new static_num('TOTAL_ABONO',4));
			$this->add_control(new static_num('DSCTO_CAPITAL',4));
			$this->add_control(new static_num('DSCTO_INTERES',4));
			$this->add_control(new static_num('DSCTO_MULTA',4));
			$this->add_control(new static_num('TOTAL_DSCTO',4));
			$this->add_control(new static_num('SALDO_CAPITAL',4));
			$this->add_control(new static_num('SALDO_INTERES',4));
			$this->add_control(new static_num('SALDO_MULTA',4));
			$this->add_control(new static_num('TOTAL_SALDO',4));
		}
	}

class dw_pp_abono extends datawindow{
    const K_ESTADO_PAGO_VIGENTE = 1;
	function __construct() {			
		$sql ="SELECT 	 A.COD_ABONO
						, TO_CHAR(FECHA_ABONO,'dd/mm/yyyy') FECHA_ABONO
						, A.MONTO_CAPITAL MONTO_CAPITAL_ABO
						, A.MONTO_INTERES MONTO_INTERES_ABO
						, A.MONTO_MULTA MONTO_MULTA_ABO
						, A.MONTO_MONEDA MONTO_MONEDA_ABO
						, A.MONTO_PESOS MONTO_PESOS_ABO
						, OP.NOM_ORIGEN_PAGO
						, EP.NOM_ESTADO_PAGO
						, CD.NOM_COMPONENTE_DEUDA
						, SB.COD_BOLETA
						, DECODE(A.COD_ESTADO_PAGO, ".self::K_ESTADO_PAGO_VIGENTE.", A.MONTO_CAPITAL, 0) MONTO_CAPITAL_ABO_V
						, DECODE(A.COD_ESTADO_PAGO, ".self::K_ESTADO_PAGO_VIGENTE.", A.MONTO_INTERES, 0) MONTO_INTERES_ABO_V
						, DECODE(A.COD_ESTADO_PAGO, ".self::K_ESTADO_PAGO_VIGENTE.", A.MONTO_MULTA, 0) MONTO_MULTA_ABO_V
						, DECODE(A.COD_ESTADO_PAGO, ".self::K_ESTADO_PAGO_VIGENTE.", A.MONTO_MONEDA, 0) MONTO_MONEDA_ABO_V
						, DECODE(A.COD_ESTADO_PAGO, ".self::K_ESTADO_PAGO_VIGENTE.", A.MONTO_PESOS, 0) MONTO_PESOS_ABO_V
						,row_number() OVER (ORDER BY FECHA_ABONO asc) ORDER_FECHA --SOLO PARA ORACLE, PARA ORDENAR CAMPOS FECHA
                FROM	ABONO A
						, ORIGEN_PAGO OP
						, ESTADO_PAGO EP
						, COMPONENTE_DEUDA CD
						, PLAN_PAGO PP
        				, SUB_BOLETA SB
				WHERE	A.COD_ORIGEN_PAGO = OP.COD_ORIGEN_PAGO
				AND		A.COD_ESTADO_PAGO = EP.COD_ESTADO_PAGO
				AND		A.COD_COMPONENTE_DEUDA = CD.COD_COMPONENTE_DEUDA
				AND		A.COD_PLAN_PAGO = PP.COD_PLAN_PAGO (+)
  				AND 	SB.COD_SUB_BOLETA (+)= A.COD_SUB_BOLETA
				AND		PP.COD_PLAN_PAGO = {KEY1}
              	UNION
              	SELECT   NULL COD_ABONO
                   		, TO_CHAR(FECHA_ABONO,'dd/mm/yyyy') FECHA_ABONO
                   		, MONTO_CAPITAL MONTO_CAPITAL_ABO
						, MONTO_INTERES MONTO_INTERES_ABO
						, MONTO_MULTA MONTO_MULTA_ABO
						, MONTO_MONEDA MONTO_MONEDA_ABO
						, MONTO_PESOS MONTO_PESOS_ABO
                   		, decode(FOLIO_CARGA, 'REND_INTERNO', 'COMPLEMENTO INTERNO'
						  										, 'COMPL OTRA UES') NOM_ORIGEN_PAGO
						,(SELECT ESTADO_PAGO.NOM_ESTADO_PAGO FROM ESTADO_PAGO, ABONO WHERE ABONO.COD_ESTADO_PAGO = ESTADO_PAGO.COD_ESTADO_PAGO AND ABONO.COD_ABONO = ABONO_FICTICIO.COD_ABONO)NOM_ESTADO_PAGO
						, NULL NOM_COMPONENTE_DEUDA
						, NULL COD_BOLETA
                   		, MONTO_CAPITAL MONTO_CAPITAL_ABO_V
						, MONTO_INTERES MONTO_INTERES_ABO_V
						, MONTO_MULTA MONTO_MULTA_ABO_V
						, MONTO_MONEDA MONTO_MONEDA_ABO_V
						, MONTO_PESOS MONTO_PESOS_ABO_V
						,row_number() OVER (ORDER BY FECHA_ABONO asc) ORDER_FECHA --SOLO PARA ORACLE, PARA ORDENAR CAMPOS FECHA
                FROM abono_ficticio
              	WHERE cod_plan_pago = {KEY1}
              	ORDER BY FECHA_ABONO, COD_ABONO DESC";
        parent::__construct($sql, 'PP_ABONO');
		
        $this->add_control(new static_link('COD_ABONO', '../../../../commonlib_seven/trunk/php/link_wi.php?modulo_origen=plan_pago&modulo_destino=abono&cod_modulo_destino=[COD_ABONO]&cod_item_menu=1025'));
		$this->add_control(new static_num('MONTO_CAPITAL_ABO',4));
		$this->add_control(new static_num('MONTO_INTERES_ABO',4));
		$this->add_control(new static_num('MONTO_MULTA_ABO',4));
		$this->add_control(new static_num('MONTO_MONEDA_ABO',4));
		$this->add_control(new static_num('MONTO_PESOS_ABO',0));
		
		// Se agregan estos controls, pero no es necesario agregarlos al htm
		$this->add_control(new static_num('MONTO_CAPITAL_ABO_V',4));
		$this->add_control(new static_num('MONTO_INTERES_ABO_V',4));
		$this->add_control(new static_num('MONTO_MULTA_ABO_V',4));
		$this->add_control(new static_num('MONTO_MONEDA_ABO_V',4));
		$this->add_control(new static_num('MONTO_PESOS_ABO_V',0));
		
		$this->accumulate('MONTO_CAPITAL_ABO_V', '', false);
		$this->accumulate('MONTO_INTERES_ABO_V', '', false);
		$this->accumulate('MONTO_MULTA_ABO_V', '', false);
		$this->accumulate('MONTO_MONEDA_ABO_V', '', false);
		$this->accumulate('MONTO_PESOS_ABO_V', '', false);
	}
}

class dw_pp_descuento extends datawindow{
    const K_ESTADO_PAGO_VIGENTE = 1;
    function __construct() {
        $sql = "SELECT	 DSCTO.COD_DESCUENTO COD_DESCUENTO_AUX --si no existe campo falla select por los ficticios COD_DESCUENTO = null
						,DSCTO.COD_DESCUENTO
						, TO_CHAR(FECHA_DESCUENTO,'dd/mm/yyyy') FECHA_DESCUENTO
						, DSCTO.MONTO_CAPITAL MONTO_CAPITAL_DSCTO
						, DSCTO.MONTO_INTERES MONTO_INTERES_DSCTO
						, DSCTO.MONTO_MULTA MONTO_MULTA_DSCTO
						, DSCTO.MONTO_MONEDA MONTO_MONEDA_DSCTO
						, DSCTO.MONTO_PESOS MONTO_PESOS_DSCTO
						, OP.NOM_ORIGEN_PAGO
						, CD.NOM_COMPONENTE_DEUDA
						, EP.NOM_ESTADO_PAGO
						, SB.COD_BOLETA
						, decode(DSCTO.COD_ESTADO_PAGO, ".self::K_ESTADO_PAGO_VIGENTE.", DSCTO.MONTO_CAPITAL, 0) MONTO_CAPITAL_DSCTO_V
						, decode(DSCTO.COD_ESTADO_PAGO, ".self::K_ESTADO_PAGO_VIGENTE.", DSCTO.MONTO_INTERES, 0) MONTO_INTERES_DSCTO_V
						, decode(DSCTO.COD_ESTADO_PAGO, ".self::K_ESTADO_PAGO_VIGENTE.", DSCTO.MONTO_MULTA, 0) MONTO_MULTA_DSCTO_V
						, decode(DSCTO.COD_ESTADO_PAGO, ".self::K_ESTADO_PAGO_VIGENTE.", DSCTO.MONTO_MONEDA, 0) MONTO_MONEDA_DSCTO_V
						, decode(DSCTO.COD_ESTADO_PAGO, ".self::K_ESTADO_PAGO_VIGENTE.", DSCTO.MONTO_PESOS, 0) MONTO_PESOS_DSCTO_V
				FROM	DESCUENTO DSCTO
						, ORIGEN_PAGO OP
						, COMPONENTE_DEUDA CD
						, ESTADO_PAGO EP
						, PLAN_PAGO PP
						, SUB_BOLETA SB
				WHERE	DSCTO.COD_ORIGEN_PAGO = OP.COD_ORIGEN_PAGO
				AND		DSCTO.COD_COMPONENTE_DEUDA = CD.COD_COMPONENTE_DEUDA
				AND		DSCTO.COD_ESTADO_PAGO = EP.COD_ESTADO_PAGO
				AND		DSCTO.COD_PLAN_PAGO = PP.COD_PLAN_PAGO (+)
				AND		SB.COD_SUB_BOLETA (+)= DSCTO.COD_SUB_BOLETA
				AND		PP.COD_PLAN_PAGO  = {KEY1}
                UNION
				SELECT	COD_DESCUENTO_FICTICIO COD_DESCUENTO_AUX --si no existe campo falla select por los ficticios COD_DESCUENTO = null
						,NULL COD_DESCUENTO
						, TO_CHAR(FECHA_DESCUENTO,'dd/mm/yyyy') FECHA_DESCUENTO
						, MONTO_CAPITAL MONTO_CAPITAL_DSCTO
						, MONTO_INTERES MONTO_INTERES_DSCTO
						, MONTO_MULTA MONTO_MULTA_DSCTO
						, MONTO_MONEDA MONTO_MONEDA_DSCTO
						, MONTO_PESOS MONTO_PESOS_DSCTO
						, 'COMPL OTRA UES' NOM_ORIGEN_PAGO
						, NULL NOM_COMPONENTE_DEUDA
						, 'VIGENTE' NOM_ESTADO_PAGO
						, NULL COD_BOLETA
						, MONTO_CAPITAL MONTO_CAPITAL_DSCTO_V
						, MONTO_INTERES MONTO_INTERES_DSCTO_V
						, MONTO_MULTA MONTO_MULTA_DSCTO_V
						, MONTO_MONEDA MONTO_MONEDA_DSCTO_V
						, MONTO_PESOS MONTO_PESOS_DSCTO_V
				FROM DESCUENTO_FICTICIO
				WHERE COD_PLAN_PAGO  = {KEY1}
				ORDER BY FECHA_DESCUENTO, COD_DESCUENTO DESC";
        parent::__construct($sql, 'PP_DESCUENTO', true, true);

        $this->add_control(new static_link('COD_DESCUENTO', '../../../../commonlib_seven/trunk/php/link_wi.php?modulo_origen=plan_pago&modulo_destino=descuento&cod_modulo_destino=[COD_DESCUENTO]&cod_item_menu=1030'));
        $this->add_control(new static_num('MONTO_CAPITAL_DSCTO',4));
        $this->add_control(new static_num('MONTO_INTERES_DSCTO',4));
        $this->add_control(new static_num('MONTO_MULTA_DSCTO',4));
        $this->add_control(new static_num('MONTO_MONEDA_DSCTO',4));
        $this->add_control(new static_num('MONTO_PESOS_DSCTO',0));
		    
        // Se agregan estos controls, pero no es necesario agregarlos al htm
        $this->add_control(new static_num('MONTO_CAPITAL_DSCTO_V',4));
        $this->add_control(new static_num('MONTO_INTERES_DSCTO_V',4));
        $this->add_control(new static_num('MONTO_MULTA_DSCTO_V',4));
        $this->add_control(new static_num('MONTO_MONEDA_DSCTO_V',4));
        $this->add_control(new static_num('MONTO_PESOS_DSCTO_V',0));
        
        $this->accumulate('MONTO_CAPITAL_DSCTO_V', '', false);
        $this->accumulate('MONTO_INTERES_DSCTO_V', '', false);
        $this->accumulate('MONTO_MULTA_DSCTO_V', '', false);
        $this->accumulate('MONTO_MONEDA_DSCTO_V', '', false);
        $this->accumulate('MONTO_PESOS_DSCTO_V', '', false);
    }
}

class dw_pp_detalle_pago extends datawindow{

	const K_COD_ORIGEN_PAGO_CAJA = 1;		
	function __construct() {
		///////////////// CLASS_SQL = INPUT_DW_PP_DETALLE_PAGO ///////////////
		$sql = sql_plan_pago::get_sql("INPUT_DW_PP_DETALLE_PAGO", self::K_COD_ORIGEN_PAGO_CAJA);
		
		parent::__construct($sql, 'PP_DETALLE_PAGO', true, true);
		$this->add_control(new static_num('MONTO_DOCUMENTO',0));		
		$this->add_control(new static_num('MONTO_PESOS_ABONO',0));
		
		$this->accumulate('MONTO_DOCUMENTO', '', false);
		$this->accumulate('MONTO_PESOS_ABONO', '', false);
	}
}
?>