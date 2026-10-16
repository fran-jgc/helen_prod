<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../common_appl/class_reporte_helen.php");

//***********dibujo**************/////////
class print_condonacion extends reporte_helen {	
    
	const K_PARAM_UNIVERSIDAD = 5;
	
	function __construct($sql, $xml, $labels=array(), $titulo, $con_logo, $vuelve_a_presentacion=false) {
	    parent::__construct($sql, $xml, $labels, $titulo, $con_logo, $vuelve_a_presentacion);			
	}
	function defuncion(&$pdf, $result, $nom_universidad, $mail_universidad, $dir_universidad){
	    $pdf->SetMargins(80, 0, 80);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->SetXY(260, 100);
        $pdf->MultiCell(280, 20, 'Informa Condonaciones artículo N°17, Ley N°19.287.');

        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->Text(260, 160, 'RESOLUCION EXENTA INTERNA Nº ');

        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Text(260, 185, 'Santiago, ' . $result['DIA'] . ' de ' . $result['MES'] . '  ' . $result['ANO'] . '');

        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->Text(83, 220, 'VISTOS:');
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Text(140, 220, 'Lo dispuesto en la Ley N°19.287 del 4 de febrero de 1994.');

        $html = "<b>CONSIDERANDO: </b> En cumplimiento de los requisitos establecidos en el artículo<br><br><br>N°17 de la Ley N°19.287, de fecha 24 de enero de 1994.";
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->SetXY(80, 250);
        $pdf->WriteHTML($html, 45);

        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->SetXY(80, 300);
        $pdf->Cell(0, 20, 'RESUELVO:', 0, '', 'C');

        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Text(80, 341, '1.');
        
        $txt = "Condónese el saldo deudor por concepto de Fondo Solidario de Crédito Universitario, al beneficiario de este crédito Sr(a) ";
        $txt .= $result['NOMBRE']." Rut: ".number_format($result['RUT'], 0, ',', '.')."-".$result['DIG_VERIF'].", por extinción de la deuda,";
        $txt .= " conforme a lo preceptuado en el artículo 17° de la ley N° 19.287. ";
        $pdf->SetXY(100, 330);
        $pdf->MultiCell(460, 15, $txt, 0, 'J');
        
        $txt2 = "El monto de Fondo Solidario de Crédito Universitario a condonar por fallecimiento del deudor asciende ".$result['UTM']." UTM.";
        $pdf->SetXY(100, 390);
        $pdf->MultiCell(460, 15, $txt2, 0, 'J');
        
        $txt3 = "De conformidad con lo dispuesto en el número precedente, condónese la deuda indicada en los respectivos registros contables.";
        $pdf->SetXY(80, 450);
        $pdf->MultiCell(460, 15, $txt3, 0, 'J');
	    
        $pdf->SetXY(80, 500);
        $pdf->Cell(460, 15, "Anótese, Regístrese y Comuníquese,", 0, 'J');
        
        $pdf->SetXY(300, 580);
        $pdf->MultiCell(250, 15, $result['NOM_ADMIN']."\nAdministrador General\nFondo Solidario de Crédito Universitario", 0, 'C');
        
        $pdf->SetFont('Helvetica', 'UB', 8);
        $pdf->Text(80, 680,"DISTRIBUCIÓN:");
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->Text(80, 690,"1. Interesado");
        $pdf->Text(80, 700,"2. AGFSCU");
	}

	function psml(&$pdf, $result, $nom_universidad, $mail_universidad, $dir_universidad){
        $pdf->SetMargins(80, 0, 80);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->SetXY(260, 100);
        $pdf->MultiCell(280, 20, 'Informa Condonaciones artículo N°8, Ley N°19.287.');
        
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->Text(260, 160, 'RESOLUCION EXENTA INTERNA Nº ');
        
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Text(260, 185, 'Santiago, ' . $result['DIA'] . ' de ' . $result['MES'] . '  ' . $result['ANO'] . '');
        
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->Text(83, 220, 'VISTOS:');
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Text(140, 220, 'Lo dispuesto en la Ley N°19.287 del 4 de febrero de 1994.');
        
        $html = "<b>CONSIDERANDO: </b> En cumplimiento de los requisitos establecidos en el artículo<br><br><br>N°8 de la Ley N°19.287, de fecha 24 de enero de 1994.";
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->SetXY(80, 250);
        $pdf->WriteHTML($html, 45);
        
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->SetXY(80, 300);
        $pdf->Cell(0, 20, 'RESUELVO:', 0, '', 'C');
        
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Text(80, 341, '1.');
        
        $txt = "Condónese el saldo deudor por concepto de Fondo Solidario de Crédito Universitario, al beneficiario de este crédito Sr(a) ";
        $txt .= $result['NOMBRE']." Rut: ".number_format($result['RUT'], 0, ',', '.')."-".$result['DIG_VERIF'].",";
        $txt .= " por haber cumplido fielmente con todas las obligaciones legales en un plazo de ".$result['ANOS']." años, conforme a los montos iniciales actualizados. ";
        $pdf->SetXY(100, 330);
        $pdf->MultiCell(460, 15, $txt, 0, 'J');
        
        $txt2 = "El monto de Fondo Solidario de Crédito Universitario a condonar por el solo ministerio de la Ley asciende ".$result['UTM']." UTM.";
        $pdf->SetXY(100, 420);
        $pdf->MultiCell(460, 15, $txt2, 0, 'J');
        
        $txt3 = "De conformidad con lo dispuesto en el número precedente, condónese la deuda indicada en los respectivos registros contables.";
        $pdf->SetXY(80, 480);
        $pdf->MultiCell(460, 15, $txt3, 0, 'J');
        
        $pdf->SetXY(80, 530);
        $pdf->Cell(460, 15, "Anótese, Regístrese y Comuníquese,", 0, 'J');
        
        $pdf->SetXY(300, 580);
        $pdf->MultiCell(250, 15, $result['NOM_ADMIN']."\nAdministrador General\nFondo Solidario de Crédito Universitario", 0, 'C');
        
        $pdf->SetFont('Helvetica', 'UB', 8);
        $pdf->Text(80, 680,"DISTRIBUCIÓN:");
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->Text(80, 690,"1. Interesado");
        $pdf->Text(80, 700,"2. AGFSCU");
    }

    function psml_cruzado(&$pdf, $result, $nom_universidad, $mail_universidad, $dir_universidad){
        $pdf->SetMargins(80, 0, 80);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->SetXY(260, 100);
        $pdf->MultiCell(280, 20, 'Informa Condonaciones artículo N°8, Ley N°19.287.');
        
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->Text(260, 160, 'RESOLUCION EXENTA INTERNA Nº ');
        
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Text(260, 185, 'Santiago, ' . $result['DIA'] . ' de ' . $result['MES'] . '  ' . $result['ANO'] . '');
        
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->Text(83, 220, 'VISTOS:');
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Text(140, 220, 'Lo dispuesto en la Ley N°19.287 del 4 de febrero de 1994.');
        
        $html = "<b>CONSIDERANDO: </b> En cumplimiento de los requisitos establecidos en el artículo<br><br><br>N°8 de la Ley N°19.287, de fecha 24 de enero de 1994.";
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->SetXY(80, 250);
        $pdf->WriteHTML($html, 45);
        
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->SetXY(80, 300);
        $pdf->Cell(0, 20, 'RESUELVO:', 0, '', 'C');
        
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Text(80, 341, '1.');
        
        $txt = "Condónese el saldo deudor por concepto de Fondo Solidario de Crédito Universitario, al beneficiario de este crédito Sr(a) ";
        $txt .= $result['NOMBRE']." Rut: ".number_format($result['RUT'], 0, ',', '.')."-".$result['DIG_VERIF'].",";
        $txt .= " por haber cumplido fielmente con todas las obligaciones legales en un plazo de ".$result['ANOS']." años, conforme a los montos iniciales actualizados. ";
        $pdf->SetXY(100, 330);
        $pdf->MultiCell(460, 15, $txt, 0, 'J');
        
        $txt2 = "El monto de Fondo Solidario de Crédito Universitario a condonar por el solo ministerio de la Ley asciende ".$result['UTM']." UTM, ";
        $txt2.= "del cual un ".$result['PORC_CRUZADO']."% corresponde a la Universidad de Santiago.";
        $pdf->SetXY(100, 420);
        $pdf->MultiCell(460, 15, $txt2, 0, 'J');
        
        $txt3 = "De conformidad con lo dispuesto en el número precedente, condónese la deuda indicada en los respectivos registros contables.";
        $pdf->SetXY(80, 500);
        $pdf->MultiCell(460, 15, $txt3, 0, 'J');
        
        $pdf->SetXY(80, 550);
        $pdf->Cell(460, 15, "Anótese, Regístrese y Comuníquese,", 0, 'J');
        
        $pdf->SetXY(300, 600);
        $pdf->MultiCell(250, 15, $result['NOM_ADMIN']."\nAdministrador General\nFondo Solidario de Crédito Universitario", 0, 'C');
        
        $pdf->SetFont('Helvetica', 'UB', 8);
        $pdf->Text(80, 680,"DISTRIBUCIÓN:");
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->Text(80, 690,"1. Interesado");
        $pdf->Text(80, 700,"2. AGFSCU");
    }

    function incapacidad(&$pdf, $result, $nom_universidad, $mail_universidad, $dir_universidad){
        $pdf->SetMargins(80, 0, 80);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->SetXY(260, 100);
        $pdf->MultiCell(280, 20, 'Informa Condonaciones artículo N°17, Ley N°19.287.');
        
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->Text(260, 160, 'RESOLUCION EXENTA INTERNA Nº ');
        
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Text(260, 185, 'Santiago, ' . $result['DIA'] . ' de ' . $result['MES'] . '  ' . $result['ANO'] . '');
        
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->Text(83, 220, 'VISTOS:');
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Text(140, 220, 'Lo dispuesto en la Ley N°19.287 del 4 de febrero de 1994.');
        
        $html = "<b>CONSIDERANDO: </b> En cumplimiento de los requisitos establecidos en el artículo<br><br><br>N°17 de la Ley N°19.287, de fecha 24 de enero de 1994.";
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->SetXY(80, 250);
        $pdf->WriteHTML($html, 45);
        
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->SetXY(80, 300);
        $pdf->Cell(0, 20, 'RESUELVO:', 0, '', 'C');
        
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Text(80, 341, '1.');
        
        $txt = "Condónese el saldo deudor por concepto de Fondo Solidario de Crédito Universitario, al beneficiario de este crédito Sr(a) ";
        $txt .= $result['NOMBRE']." Rut: ".number_format($result['RUT'], 0, ',', '.')."-".$result['DIG_VERIF'].", por encontrarse físicamente, incapacitado en forma permanente para trabajar,";
        $txt .= " conforme a lo preceptuado en el artículo 17° de la ley N° 19.287. ";
        $pdf->SetXY(100, 330);
        $pdf->MultiCell(460, 15, $txt, 0, 'J');
        
        $txt2 = "El monto de Fondo Solidario de Crédito Universitario a condonar por incapacidad física  del deudor asciende ".$result['UTM']." UTM.";
        $pdf->SetXY(100, 420);
        $pdf->MultiCell(460, 15, $txt2, 0, 'J');
        
        $txt3 = "De conformidad con lo dispuesto en el número precedente, condónese la deuda indicada en los respectivos registros contables.";
        $pdf->SetXY(80, 480);
        $pdf->MultiCell(460, 15, $txt3, 0, 'J');
        
        $pdf->SetXY(80, 530);
        $pdf->Cell(460, 15, "Anótese, Regístrese y Comuníquese,", 0, 'J');
        
        $pdf->SetXY(300, 580);
        $pdf->MultiCell(250, 15, $result['NOM_ADMIN']."\nAdministrador General\nFondo Solidario de Crédito Universitario", 0, 'C');
        
        $pdf->SetFont('Helvetica', 'UB', 8);
        $pdf->Text(80, 680,"DISTRIBUCIÓN:");
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->Text(80, 690,"1. Interesado");
        $pdf->Text(80, 700,"2. AGFSCU");
    }

    function dibuja_uno(&$pdf, $result, $nom_universidad, $mail_universidad, $dir_universidad)
    {
        $tipo_condonacion = $result['COD_TIPO_CONDONACION'];

        if ($tipo_condonacion == 1) {
            $this->psml($pdf, $result, $nom_universidad, $mail_universidad, $dir_universidad);
        } elseif ($tipo_condonacion == 2) {
            $this->defuncion($pdf, $result, $nom_universidad, $mail_universidad, $dir_universidad);
        } elseif ($tipo_condonacion == 3) {
            $this->incapacidad($pdf, $result, $nom_universidad, $mail_universidad, $dir_universidad);
        }elseif ($tipo_condonacion == 7) {
            $this->psml_cruzado($pdf, $result, $nom_universidad, $mail_universidad, $dir_universidad);
        }
// 		$pdf->SetFont('Helvetica','i',10);
		
// 		$pdf->Text( 360, 120,'ORD.     : NÂ° _______________/');
// 		$pdf->Text( 360, 130,'ANT.     :                    ');
// 		$pdf->Text( 360, 140,'MAT.     : Informa CondonaciÃ³n');
// 		$pdf->Text( 360, 145,'______________________________');
// 		$pdf->SetFont('Helvetica','B',12);
// 		$pdf->Text( 60, 180,'SANTIAGO,  '.$result['MES'].' '.$result['DIA'].'  del  '.$result['ANO'].'');		
// 		$pdf->SetFont('Helvetica','',12);
// 		$pdf->Text( 50, 210,'DE:  ADMINISTRACIÃ“N GRAL. FONDO SOLIDARIO CRÃ‰DITO UNIVERSITARIO');
// 		$pdf->Text( 50, 240,'A  :');
// 		$pdf->Text( 85, 240,utf8_decode($result['NOM_RECEPTOR']));
		
// 		$pdf->SetFont('Helvetica','',12);
// 		$pdf->SetXY(60 , 300);
// 		$pdf->MultiCell(500, 20,'Me permito informar a UD. que de acuerdo con los antecedentes que obran en poder de esta AdministraciÃ³n, se ha procedido a condonar la deuda del Fondo Solidario por '.utf8_decode($result['NOM_TIPO_CONDONACION']).'  al siguiente deudor(a):', 0, 'J');
		
// 	   $pdf->Text( 180, 420,'Rut:' );
// 		$pdf->Text( 220, 420,number_format($result['RUT'], 0, ',', '.')."-".$result['DIG_VERIF']);		
// 		$pdf->Text( 180, 400,'Sr(a):' );
// 		$pdf->Text( 220, 400,utf8_decode($result['NOMBRE']));

// 		$pdf->Text( 300, 520,'Saluda Atentamente a Ud.' );
// 		$pdf->Text( 290, 620,$result['NOM_ADMIN']);
// 		$pdf->Text( 290, 640,'ADMINISTRADOR GENERAL' );
		

// 		$pdf->SetFont('Helvetica','B',8);
// 		$pdf->Text( 50, 650, 'WJM/MZV' );
// 		$pdf->Text( 50, 660, 'DISTRIBUCIÃ“N:-' );
// 		$pdf->Text( 50, 670, '1.- INTERESADO' );
// 		$pdf->Text( 50, 680, '2.- AGFCU' );
		
// 		$pdf->SetFont('Helvetica','',8);

	}
	
	function modifica_pdf(&$pdf) {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result = $db->build_results($this->sql);
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql= sql_suspension::get_sql('PRINT_PARAM');
		$result_universidad = $db->build_results($sql);		
		$nom_universidad = $result_universidad[0]['NOM_UNIVERSIDAD'];
		$mail_universidad = $result_universidad[0]['MAIL_UNIVERSIDAD'];
		$dir_universidad = $result_universidad[0]['DIR_UNIVERSIDAD'];
		
		for($i=0; $i<count($result); $i++) {
			$this->dibuja_uno($pdf, $result[$i], $nom_universidad, $mail_universidad, $dir_universidad);
			if ($i < count($result) - 1)
				$pdf->AddPage();
		}
	}		
}
?>