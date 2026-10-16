<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class print_cambio_documento extends reporte {
	const K_PARAM_NOM_UNIVERSIDAD	= 5;
	const K_PARAM_DIRECCCION 		= 9;
	const K_PARAM_COMUNA 			= 11;
	const K_PARAM_CIUDAD 			= 12;
	const K_PARAM_PAIS 				= 16;
	const K_PARAM_TELEFONO			= 10;
	const K_PARAM_FAX 				= 15;
	const K_PARAM_E_MAIL 			= 13;
	const K_PARAM_SITIO_WEB 		= 14;
	const K_TERMINO_DOC				= 580;
	var $pos_sig					= 0;

	
	function __construct($sql, $xml, $labels=array(), $titulo, $con_logo='N', $vuelve_a_presentacion=false,$orientation='P',$unit='pt',$format='letter'){
				
		if(K_TIPO_BD == "oci"){
			$sql_param = "select to_char(sysdate, 'dd/mm/yyyy hh:mm') FECHA_HORA
							,f_get_parametro(".self::K_PARAM_NOM_UNIVERSIDAD.")NOM_UNIVERSIDAD
							,f_get_parametro(".self::K_PARAM_DIRECCCION.")DIRECCION
							,f_get_parametro(".self::K_PARAM_COMUNA.")COMUNA
	                        ,f_get_parametro(".self::K_PARAM_CIUDAD.")CIUDAD
	                        ,f_get_parametro(".self::K_PARAM_PAIS.")PAIS
	                        ,f_get_parametro(".self::K_PARAM_TELEFONO.")TELEFONO
	                        ,f_get_parametro(".self::K_PARAM_FAX.")FAX
	                        ,f_get_parametro(".self::K_PARAM_E_MAIL.")EMAIL
	                        ,f_get_parametro(".self::K_PARAM_SITIO_WEB.")SITIO_WEB
	                 from DUAL";
		}
		else if(K_TIPO_BD == "mssql"){
			$sql_param = "select convert(varchar(20),getdate(), 103) + ' ' + convert(varchar(20), datepart(hh,getdate()))+':'+ Convert(varchar(2), DatePart(mi, GetDate())) FECHA_HORA
							,dbo.f_get_parametro(".self::K_PARAM_NOM_UNIVERSIDAD.")NOM_UNIVERSIDAD
							,dbo.f_get_parametro(".self::K_PARAM_DIRECCCION.")DIRECCION
							,dbo.f_get_parametro(".self::K_PARAM_COMUNA.")COMUNA
	                        ,dbo.f_get_parametro(".self::K_PARAM_CIUDAD.")CIUDAD
	                        ,dbo.f_get_parametro(".self::K_PARAM_PAIS.")PAIS
	                        ,dbo.f_get_parametro(".self::K_PARAM_TELEFONO.")TELEFONO
	                        ,dbo.f_get_parametro(".self::K_PARAM_FAX.")FAX
	                        ,dbo.f_get_parametro(".self::K_PARAM_E_MAIL.")EMAIL
	                        ,dbo.f_get_parametro(".self::K_PARAM_SITIO_WEB.")SITIO_WEB";
		}
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result = $db->build_results($sql_param);
		
		$labels['str_fecha_hora'] = $result[0]['FECHA_HORA'];
		$labels['str_nom_universidad'] = $result[0]['NOM_UNIVERSIDAD'];
		$labels['str_direccion'] = $result[0]['DIRECCION'];
		$labels['str_comuna'] = $result[0]['COMUNA'];
		$labels['str_ciudad'] = $result[0]['CIUDAD'];
		$labels['str_pais'] = $result[0]['PAIS'];
		$labels['str_telefono'] = $result[0]['TELEFONO'];
		$labels['str_fax'] = $result[0]['FAX'];
		$labels['str_email'] = $result[0]['EMAIL'];
		$labels['str_sitio_web'] = $result[0]['SITIO_WEB'];
		
		parent::__construct($sql, $xml, $labels, $titulo, $con_logo, $vuelve_a_presentacion,$orientation,$unit,$format);	
	}
	function dibuja_uno(&$pdf, $result, $iniX, $iniY, $copia){
		
		/* crea una cuadricula
		$x=0;
		$y=0;
		$count = 0;
		$pdf->SetLineWidth(0.01);
		$pdf->SetDrawColor( 256, 0 , 0);
		$l=0;
		for($x=0;$x<=640;$x=$x+20){
			$pdf->line($x,0,$x,800);
			$pdf->SetFont('Helvetica','',4);
			$pdf->Text($x + 1, 0 + 3,$x);
			
		}
		for($y=0;$y<=800;$y=$y+20){
			$pdf->line(0,$y,640,$y);
			$pdf->Text(3,$y + 2,$y);
		}
		*/
		
		$pdf->SetFont('Helvetica','',7);
		//$pdf->Text($iniX + 560, $iniY + 40,'Pag '.$pdf->PageNo().'/{nb}');
		$pdf->SetFont('Helvetica','B',19);
		$pdf->Text($iniX + 125, $iniY + 90,'Comprobante Cambio Documento');
		
		$pdf->SetLineWidth(0.2);

		$pdf->SetFont('Helvetica','B',10);
		$pdf->SetXY(30,136);
		$pdf->Cell(60,20,'Rut         :', '', '','L');
		$pdf->SetFont('Helvetica','',10);
		$pdf->SetXY(80,136);
		$pdf->Cell(75,20,number_format($result['RUT'], 0, ',', '.').' - '.$result['DIG_VERIF'], 'B', '','L');
		
		
		$pdf->SetFont('Helvetica','B',10);
		$pdf->SetXY(160,136);
		$pdf->Cell(60,20,'Familia Deuda   :', '', '','L');
		$pdf->SetFont('Helvetica','',10);
		$pdf->SetXY(245,136);
		$pdf->Cell(155,20,substr($result['FAMILIA'], 0,26), 'B', '','L');

		
		$pdf->SetFont('Helvetica','B',10);
		$pdf->SetXY(405,136);
		$pdf->Cell(60,20,'N° Cambio Doc. :', '', '','L');
		$pdf->SetFont('Helvetica','',10);
		$pdf->SetXY(490,136);
		$pdf->Cell(90,20,$result['COD_CAMBIO_DOCUMENTO'], 'B', '','L');
		
		
		$pdf->SetFont('Helvetica','B',10);
		$pdf->SetXY(30,161);
		$pdf->Cell(60,20,'Nombre :', '', '','L');
		$pdf->SetFont('Helvetica','',10);
		$pdf->SetXY(80,161);
		$pdf->Cell(500,20,utf8_decode($result['NOMBRE']), 'B', '','L');
		

		$pdf->SetFont('Helvetica','B',10);
		$pdf->SetXY(30,186);
		$pdf->Cell(60,20,'Fecha    :', '', '','L');
		$pdf->SetFont('Helvetica','',10);
		$pdf->SetXY(80,186);
		$pdf->Cell(75,20,$result['FECHA_CAMBIO_DOCUMENTO'], 'B', '','L');
		
		$pdf->SetFont('Helvetica','B',10);
		$pdf->SetXY(160,186);
		$pdf->Cell(60,20,'Monto Recargo :', '', '','L');
		$pdf->SetFont('Helvetica','',10);
		$pdf->SetXY(245,186);
		$pdf->Cell(105,20,number_format($result['RECARGO'], 0, ',', '.'), 'B', '','L');

		$pdf->SetFont('Helvetica','B',10);
		$pdf->SetXY(360,186);
		$pdf->Cell(60,20,'Monto Descuento :', '', '','L');
		$pdf->SetFont('Helvetica','',10);
		$pdf->SetXY(455,186);
		$pdf->Cell(125,20,number_format($result['DESCUENTO'], 0, ',', '.'), 'B', '','L');

	}
	
	function dibuja_dos(&$pdf, $result, $iniX, $iniY, $copia){

		GLOBAL $pos_sig;
		$cant = count($result);
		$pos_sig = 220;
		
		$pdf->SetFont('Helvetica','B',10);
		$pdf->Text($iniX + 30, $iniY + $pos_sig,'Documento Original');
		$pdf->SetFont('Helvetica','B',8);
		$pos_sig = $pos_sig + 20;
		$pdf->SetXY(30,$pos_sig);
		$pdf->Cell(90,18,'Tipo Doc. Pago', 'LBTR', 'B','C');
		$pdf->SetXY(120,$pos_sig);
		$pdf->Cell(50,18,'N° Doc.', 'LBTR', 'B','C');
		$pdf->SetXY(170,$pos_sig);
		$pdf->Cell(160,18,'Banco', 'LBTR', 'B','C');
		$pdf->SetXY(330,$pos_sig);
		$pdf->Cell(135,18,'Plaza', 'LBTR', 'B','C');
		$pdf->SetXY(465,$pos_sig);
		$pdf->Cell(45,18,'Fecha Doc.', 'LBTR', 'B','C');
		$pdf->SetXY(510,$pos_sig);
		$pdf->Cell(70,18,'Monto', 'LBTR', 'B','C');
		$pos_sig = $pos_sig + 18;
		

		for($i=0;$i<$cant;$i++){
			$pdf->SetFont('Helvetica','',6);
			if($pos_sig > self::K_TERMINO_DOC){
				
				$pdf->AddPage();
				$pdf->SetFont('Helvetica','',7);
				//$pdf->Text($iniX + 560, $iniY + 40,'Pag '.$pdf->PageNo().'/{nb}');
				$pdf->SetFont('Helvetica','B',22);
				$pdf->Text($iniX + 120, $iniY + 120,'Comprobante Cambio Documento');
				$pos_sig = 176;
				
				$pdf->SetFont('Helvetica','B',10);
				$pdf->Text($iniX + 30, $iniY + $pos_sig,'Documento Original');
				$pdf->SetFont('Helvetica','B',8);
				$pos_sig = $pos_sig + 20;
				$pdf->SetXY(30,$pos_sig);
				$pdf->Cell(90,18,'Tipo Doc. Pago', 'LBTR', 'B','C');
				$pdf->SetXY(120,$pos_sig);
				$pdf->Cell(50,18,'N° Doc.', 'LBTR', 'B','C');
				$pdf->SetXY(170,$pos_sig);
				$pdf->Cell(160,18,'Banco', 'LBTR', 'B','C');
				$pdf->SetXY(330,$pos_sig);
				$pdf->Cell(135,18,'Plaza', 'LBTR', 'B','C');
				$pdf->SetXY(465,$pos_sig);
				$pdf->Cell(45,18,'Fecha Doc.', 'LBTR', 'B','C');
				$pdf->SetXY(510,$pos_sig);
				$pdf->Cell(70,18,'Monto', 'LBTR', 'B','C');
				$pos_sig = $pos_sig + 18;
				
				$pdf->SetFont('Helvetica','',6);
				
				$pdf->SetXY(100,580);
			}
			
			$pdf->SetFont('Helvetica','',8);
			$pdf->SetXY(30,$pos_sig);
			$pdf->Cell(90,15,$result[$i]['NOM_TIPO_DOCUMENTO_PAGO'], 'LBTR', '','L');
			$pdf->SetXY(120,$pos_sig);
			$pdf->Cell(50,15,$result[$i]['NRO_DOCUMENTO_PAGO'], 'LBTR', '','R');
			$pdf->SetXY(170,$pos_sig);
			$pdf->Cell(160,15,substr($result[$i]['NOM_BANCO_BDO'],0,30), 'LBTR', '','L');
			$pdf->SetXY(330,$pos_sig);
			$pdf->Cell(135,15,$result[$i]['NOM_PLAZA'], 'LBTR', '','L');
			$pdf->SetXY(465,$pos_sig);
			$pdf->Cell(45,15,$result[$i]['FECHA_DOCUMENTO_PAGO'], 'LBTR', '','L');
			$pdf->SetXY(510,$pos_sig);
			$pdf->Cell(70,15,number_format($result[$i]['MONTO'], 0, ',', '.'), 'LBTR', '','R');
			
			$pos_sig = $pos_sig + 20;
		}
	}
	
	function dibuja_tres(&$pdf, $result, $iniX, $iniY, $copia){
		
		GLOBAL $pos_sig;
		$cant = count($result);
		$pos_sig = $pos_sig + 18;
		
		if($pos_sig > self::K_TERMINO_DOC){
				
				$pdf->SetFont('Helvetica','',6);
				$pdf->AddPage();
				$pdf->SetFont('Helvetica','',7);
				//$pdf->Text($iniX + 560, $iniY + 40,'Pag '.$pdf->PageNo().'/{nb}');
				$pdf->SetFont('Helvetica','B',22);
				$pdf->Text($iniX + 120, $iniY + 120,'Comprobante Cambio Documento');
				$pos_sig = 176;
				
				$pdf->SetFont('Helvetica','',6);
		}
		
		$pdf->SetFont('Helvetica','B',10);
		$pdf->Text($iniX + 30, $iniY + $pos_sig,'Documento Nuevo');
		
		$pdf->SetFont('Helvetica','B',8);
		$pos_sig = $pos_sig + 18;
		
		$pdf->SetXY(30,$pos_sig);
		$pdf->Cell(90,18,'Tipo Doc. Pago', 'LBTR', 'B','C');
		$pdf->SetXY(120,$pos_sig);
		$pdf->Cell(50,18,'N° Doc.', 'LBTR', 'B','C');
		$pdf->SetXY(170,$pos_sig);
		$pdf->Cell(160,18,'Banco', 'LBTR', 'B','C');
		$pdf->SetXY(330,$pos_sig);
		$pdf->Cell(135,18,'Plaza', 'LBTR', 'B','C');
		$pdf->SetXY(465,$pos_sig);
		$pdf->Cell(45,18,'Fecha Doc.', 'LBTR', 'B','C');
		$pdf->SetXY(510,$pos_sig);
		$pdf->Cell(70,18,'Monto', 'LBTR', 'B','C');
		$pos_sig = $pos_sig + 18;
		
		for($i=0;$i<$cant;$i++){
			
			if($pos_sig > self::K_TERMINO_DOC){
				
				$pdf->SetFont('Helvetica','',6);
				$pdf->AddPage();
				//$pdf->Text($iniX + 560, $iniY + 40,'Pag '.$pdf->PageNo().'/{nb}');
				$pdf->SetFont('Helvetica','B',22);
				$pdf->Text($iniX + 120, $iniY + 120,'Comprobante Cambio Documento');
				$pos_sig = 176;
				
				$pdf->SetFont('Helvetica','B',10);
				$pdf->Text($iniX + 30, $iniY + $pos_sig,'Documento Nuevo');
		
				$pos_sig = $pos_sig + 18;
				$pdf->SetFont('Helvetica','B',8);
				
				$pdf->SetXY(30,$pos_sig);
				$pdf->Cell(90,18,'Tipo Doc. Pago', 'LBTR', 'B','C');
				$pdf->SetXY(120,$pos_sig);
				$pdf->Cell(50,18,'N° Doc.', 'LBTR', 'B','C');
				$pdf->SetXY(170,$pos_sig);
				$pdf->Cell(160,18,'Banco', 'LBTR', 'B','C');
				$pdf->SetXY(330,$pos_sig);
				$pdf->Cell(135,18,'Plaza', 'LBTR', 'B','C');
				$pdf->SetXY(465,$pos_sig);
				$pdf->Cell(45,18,'Fecha Doc.', 'LBTR', 'B','C');
				$pdf->SetXY(510,$pos_sig);
				$pdf->Cell(70,18,'Monto', 'LBTR', 'B','C');
				$pos_sig = $pos_sig + 18;
				
				$pdf->SetFont('Helvetica','',6);
				
			}

		
			$pdf->SetFont('Helvetica','',8);
			$pdf->SetXY(30,$pos_sig);
			$pdf->Cell(90,15,$result[$i]['NOM_TIPO_DOCUMENTO_PAGO'], 'LBTR', '','L');
			$pdf->SetXY(120,$pos_sig);
			$pdf->Cell(50,15,$result[$i]['NRO_DOCUMENTO_PAGO'], 'LBTR', '','R');
			$pdf->SetXY(170,$pos_sig);
			$pdf->Cell(160,15,substr($result[$i]['NOM_BANCO_BDO'],0,30), 'LBTR', '','L');
			$pdf->SetXY(330,$pos_sig);
			$pdf->Cell(135,15,utf8_decode($result[$i]['NOM_PLAZA']), 'LBTR', '','L');
			$pdf->SetXY(465,$pos_sig);
			$pdf->Cell(45,15,$result[$i]['FECHA_DOCUMENTO_PAGO'], 'LBTR', '','L');
			$pdf->SetXY(510,$pos_sig);
			$pdf->Cell(70,15,number_format($result[$i]['MONTO'], 0, ',', '.'), 'LBTR', '','R');
				
			$pos_sig = $pos_sig + 15;
	
		}
		$pdf->SetXY(100,660);
		$pdf->Cell(150,20,'Funcionario', 'T', 'B','C');
		
		$pdf->SetXY(350,660);
		$pdf->Cell(150,20,'Deudor', 'T', 'B','C');
	}

	function modifica_pdf(&$pdf) {
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result = $db->build_results($this->sql);

		for($i=0; $i<=1; $i++) {
			$cod_documento_cambio = $result[0]['COD_CAMBIO_DOCUMENTO'];
		
			$sql_encabezado= sql_cambio_documento::get_sql('PRINT_ENCABEZADO', $cod_documento_cambio);
			$result = $db->build_results($sql_encabezado);
			
			$this->dibuja_uno($pdf, $result[0],0, 10, '');
	
			$sql_contenido = sql_cambio_documento::get_sql('PRINT_CONTENIDO', $cod_documento_cambio);
			$result_contenido = $db->build_results($sql_contenido);
			$this->dibuja_dos($pdf, $result_contenido,0, 10,'');
			
			$sql_final = sql_cambio_documento::get_sql('PRINT_FINAL', $cod_documento_cambio);						
			$result_final = $db->build_results($sql_final);
			$this->dibuja_tres($pdf, $result_final,0, 10, '');
			
			if ($i < 1)
				$pdf->AddPage();
		}
		
		
	}
}
?>