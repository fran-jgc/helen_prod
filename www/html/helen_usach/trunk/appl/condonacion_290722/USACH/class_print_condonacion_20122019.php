<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../common_appl/class_reporte_helen.php");

//***********dibujo**************/////////
class print_condonacion extends reporte_helen {	
    
	const K_PARAM_UNIVERSIDAD = 5;
	
	function __construct($sql, $xml, $labels=array(), $titulo, $con_logo, $vuelve_a_presentacion=false) {
	    parent::__construct($sql, $xml, $labels, $titulo, $con_logo, $vuelve_a_presentacion);			
	}
	function dibuja_uno(&$pdf, $result, $nom_universidad, $mail_universidad, $dir_universidad) {
		
		$pdf->SetFont('Helvetica','i',10);
		
		$pdf->Text( 360, 120,'ORD.     : N° _______________/');
		$pdf->Text( 360, 130,'ANT.     :                    ');
		$pdf->Text( 360, 140,'MAT.     : Informa Condonación');
		$pdf->Text( 360, 145,'______________________________');
		$pdf->SetFont('Helvetica','B',12);
		$pdf->Text( 60, 180,'SANTIAGO,  '.$result['MES'].' '.$result['DIA'].'  del  '.$result['ANO'].'');		
		$pdf->SetFont('Helvetica','',12);
		$pdf->Text( 50, 210,'DE:  ADMINISTRACIÓN GRAL. FONDO SOLIDARIO CRÉDITO UNIVERSITARIO');
		$pdf->Text( 50, 240,'A  :');
		$pdf->Text( 85, 240,$result['NOM_RECEPTOR']);
		
		$pdf->SetFont('Helvetica','',12);
		$pdf->SetXY(60 , 300);
		$pdf->MultiCell(500, 20,'Me permito informar a UD. que de acuerdo con los antecedentes que obran en poder de esta Administración, se ha procedido a condonar la deuda del Fondo Solidario por '.utf8_decode($result['NOM_TIPO_CONDONACION']).'  al siguiente deudor(a):', 0, 'J');
		
	   $pdf->Text( 180, 420,'Rut:' );
		$pdf->Text( 220, 420,number_format($result['RUT'], 0, ',', '.')."-".$result['DIG_VERIF']);		
		$pdf->Text( 180, 400,'Sr(a):' );
		$pdf->Text( 220, 400,utf8_decode($result['NOMBRE']));

		$pdf->Text( 300, 520,'Saluda Atentamente a Ud.' );
		$pdf->Text( 290, 620,$result['NOM_ADMIN']);
		$pdf->Text( 290, 640,'ADMINISTRADOR GENERAL' );
		

		$pdf->SetFont('Helvetica','B',8);
		$pdf->Text( 50, 650, 'WJM/MZV' );
		$pdf->Text( 50, 660, 'DISTRIBUCIÓN:-' );
		$pdf->Text( 50, 670, '1.- INTERESADO' );
		$pdf->Text( 50, 680, '2.- AGFCU' );
		
		$pdf->SetFont('Helvetica','',8);

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