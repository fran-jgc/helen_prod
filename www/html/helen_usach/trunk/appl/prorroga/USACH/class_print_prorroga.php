<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../common_appl/class_reporte_helen.php");
header('Content-Type: text/html; charset=UTF-8');
//***********dibujo**************/////////
class print_prorroga extends reporte_helen {	
	function __construct($sql, $xml, $labels=array(), $titulo, $con_logo, $vuelve_a_presentacion=false) {
	    parent::__construct($sql, $xml, $labels, $titulo, $con_logo, $vuelve_a_presentacion);		
	}
	function dibuja_uno(&$pdf, $result, $nom_universidad, $mail_universidad, $dir_universidad) {
		
		$pdf->SetFont('Helvetica','i',10);

		$pdf->Text( 360, 100,utf8_decode('ORD.     : N° ___________/'));
		$pdf->Text( 360, 115,'ANT.     : Solicitud deudor');
		$pdf->Text( 360, 130,'MAT.     : Da respuesta.	');
		$pdf->Text( 360, 145,'_____________________');
		$pdf->SetFont('Helvetica','',10);
		$pdf->Text( 50, 180,'SANTIAGO,  '.$result['MES'].' '.$result['DIA'].' del '.$result['ANO'].'');		
		$pdf->SetFont('Helvetica','B',10);
		$pdf->Text( 50, 210,utf8_decode('DE:  ADMINISTRACIÓN GRAL. FONDO SOLIDARIO CRÉDITO UNIVERSITARIO'));
		$pdf->Text( 50, 240,'A  :');
		$pdf->SetFont('Helvetica','',10);		
		$pdf->Text( 70, 240,utf8_decode($result['NOMBRE']));
		$pdf->Text( 350, 240,'Rut:');
		$pdf->Text( 380, 240,number_format($result['RUT'], 0, ',', '.')."-".$result['DIG_VERIF']);
		$pdf->Text( 70, 252,utf8_decode($result['DIRECCION']));
		
		$pdf->SetFont('Helvetica','',10);

		$pdf->SetXY(50 , 275);
		$pdf->MultiCell(500, 14,utf8_decode('De acuerdo a los antecedentes  aportados por usted, la Administración General del Fondo Solidario de Crédito Universitario de ésta Universidad, ha resuelto lo siguiente:
		
1.- Acoger su solicitud en orden a verificar su situación académica.
2.- Informar a usted que se a dispuesto no hacer efectivo el cobro correspondiente al '.$result['NOM_TIPO_DEUDA'].', dado que registra como última matrícula en el año '.$result['ULT_MAT'].' en la '.$result['NOM_UNIVERSIDAD'].'.
3.- La cobranza de su deuda empezaría a regir 2 años después que usted haya dejado de ser alumno estando o no en posesión de su título universitario.
4.- Consecuente con lo anterior, usted debería presentar una solicitud y certificado que acredite que es alumno regular de una Institución de Estudios Superiores, a la que se refiere la Ley N°18.591, en caso de requerir una nueva prórroga.
		
		Saluda atentamente a Ud.,'), 0, 'J');





		
		

	

		$pdf->SetFont('Helvetica','B','9');
		$pdf->Text( 252,620,$result['NOM_ADMIN']);
		$pdf->SetFont('Helvetica','','8,5');
		$pdf->Text( 254, 630,utf8_decode('ADMINISTRADOR GENERAL'));
		$pdf->Text( 212, 640,utf8_decode('FONDO SOLIDARIO DE CRÉDITO UNIVERSITARIO'));
		//$pdf->Text( 257, 650,utf8_decode('DIRECCIÓN DE GESTIÓN'));
		$pdf->Text( 222, 650,utf8_decode('VICERRECTORÍA DE FINANZAS Y LOGÍSTICA'));




		$pdf->SetFont('Helvetica','i',5);
		$pdf->Text( 50, 680, 'WJM/krp' );
		$pdf->Text( 50, 690, utf8_decode('DISTRIBUCIÓN:-') );
		$pdf->Text( 50, 700, '1.- INTERESADO' );
		$pdf->Text( 50, 710, '1.- AGFCU' );
		
		
		$pdf->SetFont('Helvetica','',8);
		$factor = 0.1;
		//$pdf->Image(dirname(__FILE__)."/../../../images_appl/".K_CLIENTE."/logo_universidad.jpg", 500, 35, $factor * 300, $factor * 450);
		//$pdf->SetFont('Helvetica','B',22);
		//$pdf->SetTextColor(0, 0, 128);
		//$pdf->Text( 500, 130, session::get('K_CLIENTE'));
	}
	function modifica_pdf(&$pdf) {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result = $db->build_results($this->sql);
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql= sql_prorroga::get_sql('PRINT_PARAM');
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