<?php 
	require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
	require_once(dirname(__FILE__)."/TCPDF-master/tcpdf.php");
	ini_set('display_errors', 'Off');
	$voucher                   = $_REQUEST["voucher"];
	$monto = $_REQUEST["monto"];
	$nt = $_REQUEST["nt"];
	$n_cuota = $_REQUEST["n_cuota"];
	$fecha = $_REQUEST["fecha"];
	$autorizacion = $_REQUEST["autorizacion"];
	$nro_cuota  = $_REQUEST["nro_cuota"];
	$deuda = $_REQUEST["deuda"];
	$deudor = $_REQUEST["deudor"];
	$nom_comercio = "UNIVERSIDAD DE SANTIAGO DE CHILE DEPARTAMENTO DE FINANZAS";
	
	if($n_cuota == '0'){
	    $tipo_pago = 'Débito';
	}else if($n_cuota != '0'){
	    $tipo_pago = 'Crédito';
	}
	$descripcion = "Pago de la cuota N° $nro_cuota del Credito Institucional";
	

	

    //$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	//$result = $db->build_results($sql);

	class MYPDF extends TCPDF {
	    var $dataArray = array();

		var $W_I 	=	1;	//$W_ITEM
		var $W_NP 	=	1;	//$W_NOM_PRODUCTO
		var $W_CP 	=	1;	//$W_COD_PRODUCTO
		var $W_C 	=	1;	//$W_CANTIDAD
		var $W_PU 	=	1;	//$W_PRECIO_UNITARIO
		var $W_MD 	=	1;	//$W_MONTO_DESCUENTO
		var $W_PCD  =	1;	//$W_PRECIO_CON_DESCUENTO
		var $W_T 	=	1; 	//$W_TOTAL

	    public function Header() {
        	if (!empty($this->dataArray)){
            	$bMargin = $this->getBreakMargin();
	            // GET CURRENT AUTO-PAGE-BREAK MODE
	            $auto_page_break = $this->getAutoPageBreak();
	            // DISABLE AUTO-PAGE-BREAK
	            $this->SetAutoPageBreak(false, 0);
	            
	            // RESTORE AUTO-PAGE-BREAK STATUS
	            $this->SetAutoPageBreak($auto_page_break, $bMargin);
	            
        	}
    	}
	    
	}

	///// INI GENERA OBJETO PDF ////
    $pdf = new MYPDF('P', 'pt', 'LETTER', true, 'UTF-8', false);
	
	$pdf->SetCreator('SetCreator');
	$pdf->SetAuthor('SetAuthor');
	$pdf->SetTitle('SetTitle');
	$pdf->SetSubject('SetSubject');
	$pdf->SetKeywords('SetKeywords');
    $pdf->setPrintHeader(true);//PARA EVITAR QUE SE DIBUJE UNA LINEA EN LA CABECERA
	$pdf->setPrintFooter(false);//PARA EVITAR QUE SE DIBUJE UNA LINEA EN EL PIE DE PAGINA
	$pdf->SetFooterMargin(0);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->SetLineWidth(0.3);
	$pdf->AddPage();
	///// FIN GENERA OBJETO PDF ////

	//// INI CAMPOS FIJOS PRIMERA PAGINA ////
	$M_LEFT = 22;
	$M_TOP = 95;
    $pdf->SetTextColor(0,0,0);	    
    $pdf->SetFont('helvetica','B',18);
	$pdf->Text(22, 70,'Comprobante de pago #'.$voucher);
	$pdf->SetFont('helvetica','B',12);
	$pdf->Ln(30);
	// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
	$pdf->MultiCell(170, 17, 'MONTO COMPRA', 1, 'L', 0, 0, '', '', true, 0, false, true, 0);
	$pdf->SetFont('helvetica','',11);
	$pdf->MultiCell(385, 17, number_format("$monto", 0, ",", "."), 1, 'L', 0, 1, '', '', true, 0, false, true, 0);
	$pdf->SetFont('helvetica','B',12);
	$pdf->MultiCell(170, 17, 'NUMERO DE CUOTAS', 1, 'L', 0, 0, '', '', true, 0, false, true, 0);
	$pdf->SetFont('helvetica','',11);
	$pdf->MultiCell(385, 17, $n_cuota, 1, 'L', 0, 1, '', '', true, 0, false, true, 0);
	$pdf->SetFont('helvetica','B',12);
	$pdf->MultiCell(170, 17, 'TIPO DE PAGO', 1, 'L', 0, 0, '', '', true, 0, false, true, 0);
	$pdf->SetFont('helvetica','',11);
	$pdf->MultiCell(385, 17, $tipo_pago, 1, 'L', 0, 1, '', '', true, 0, false, true, 0);
	$pdf->SetFont('helvetica','B',12);
	$pdf->MultiCell(170, 17, 'FECHA TRANSACCION', 1, 'L', 0, 0, '', '', true, 0, false, true, 0);
	$pdf->SetFont('helvetica','',11);
	$pdf->MultiCell(385, 17, $fecha, 1, 'L', 0, 1, '', '', true, 0, false, true, 0);
	$pdf->SetFont('helvetica','B',12);
	$pdf->MultiCell(170, 17, 'CODIGO AUTORIZACION', 1, 'L', 0, 0, '', '', true, 0, false, true, 0);
	$pdf->SetFont('helvetica','',11);
	$pdf->MultiCell(385, 17, $autorizacion, 1, 'L', 0, 1, '', '', true, 0, false, true, 0);
	$pdf->SetFont('helvetica','B',12);
	$pdf->MultiCell(170, 17, 'NUMERO TARJETA', 1, 'L', 0, 0, '', '', true, 0, false, true, 0);
	$pdf->SetFont('helvetica','',11);
	$pdf->MultiCell(385, 17, '************'.$nt, 1, 'L', 0, 1, '', '', true, 0, false, true, 0);
	$pdf->SetFont('helvetica','B',12);
	$pdf->MultiCell(170, 17, 'MONEDA', 1, 'L', 0, 0, '', '', true, 0, false, true, 0);
	$pdf->SetFont('helvetica','',11);
	$pdf->MultiCell(385, 17, 'PESOS', 1, 'L', 0, 1, '', '', true, 0, false, true, 0);
	$pdf->SetFont('helvetica','B',12);
	$pdf->MultiCell(170, 17, 'NUMERO DE OPERACION', 1, 'L', 0, 0, '', '', true, 0, false, true, 0);
	$pdf->SetFont('helvetica','',11);
	$pdf->MultiCell(385, 17, $voucher, 1, 'L', 0, 1, '', '', true, 0, false, true, 0);
	
	$pdf->SetFont('helvetica','B',12);
	$pdf->MultiCell(170, 17, 'NUMERO DE DEUDA', 1, 'L', 0, 0, '', '', true, 0, false, true, 0);
	$pdf->SetFont('helvetica','',11);
	$pdf->MultiCell(385, 17, $deuda, 1, 'L', 0, 1, '', '', true, 0, false, true, 0);
	
	$pdf->SetFont('helvetica','B',12);
	$pdf->MultiCell(170, 17, 'DEUDOR', 1, 'L', 0, 0, '', '', true, 0, false, true, 0);
	$pdf->SetFont('helvetica','',11);
	$pdf->MultiCell(385, 17, $deudor, 1, 'L', 0, 1, '', '', true, 0, false, true, 0);
	
	
	
	$pdf->SetFont('helvetica','B',12);
	$pdf->MultiCell(170, 17, 'DESCRIPCION DEL PAGO', 1, 'L', 0, 0, '', '', true, 0, false, true, 0);
	$pdf->SetFont('helvetica','',11);
	$pdf->MultiCell(385, 17, $descripcion, 1, 'L', 0, 1, '', '', true, 0, false, true, 0);
	$pdf->SetFont('helvetica','B',12);
	$pdf->MultiCell(170, 17, 'COMERCIO', 1, 'L', 0, 0, '', '', true, 0, false, true, 0);
	$pdf->SetFont('helvetica','',11);
	$pdf->MultiCell(385, 17, $nom_comercio, 1, 'L', 0, 1, '', '', true, 0, false, true, 0);
		
		
	$pdf->SetTitle('Comprobante '.$voucher);
		


	$pdf->Output("Comprobante ".$voucher.".PDF", 'I');

?>