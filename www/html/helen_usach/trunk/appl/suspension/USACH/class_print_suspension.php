<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../common_appl/class_reporte_helen.php");
header('Content-Type: text/html; charset=UTF-8');
//***********dibujo**************/////////
class print_suspension extends reporte_helen {	
    
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
		$pdf->MultiCell(500, 15,utf8_decode('De acuerdo a los antecedentes  aportados por Ud., la Administración General del Fondo Solidario de Crédito Universitario de ésta Universidad, ha resuelto lo siguiente:

1.- Acoger su solicitud en orden a verificar su situación académica/socioeconómica.
2.- Informar a Ud. que se ha dispuesto suspender el cobro correspondiente a la(s) cuota(s) del '.$result['NOM_TIPO_DEUDA'].(' del(los) año(s) ').$result['ANO_SUSPENSION'].(', dado que presenta situación de ').($result['NOM_TIPO_SUSPENSION']).'.'.'
3.- La cobranza de su deuda continuará en cobro a partir del próximo año, conforme a las normas impartidas por la Ley 19.287 que establece suspensión por:
  3.1.- Estudios de Posgrados
       I.   Magíster hasta 3 años.
       II.  Doctorado hasta 4 años.
       III. Combinación de ambos hasta 4 años.
  3.2.- Cesantía sobreviniente, esto es, la producida en el período en que debe efectuarse el pago de la cuota correspondiente. Esta suspensión podría solicitarse por una sola vez y operará por un período máximo de doce meses. En este caso, el plazo máximo para servir la deuda se extenderá por el mismo número de meses que haya operado la suspensión.
		  
		 
Saluda atentamente a Ud.,'), 0, 'J');
	
	
		$pdf->SetFont('Helvetica','B','9');
		$pdf->Text( 251,620,$result['NOM_ADMIN']);
		$pdf->SetFont('Helvetica','','8,5');
		$pdf->Text( 254, 630,utf8_decode('ADMINISTRADOR GENERAL'));
		$pdf->Text( 212, 640,utf8_decode('FONDO SOLIDARIO DE CRÉDITO UNIVERSITARIO'));
		//$pdf->Text( 258, 650,utf8_decode('DIRECCIÓN DE GESTIÓN'));
		$pdf->Text( 222, 650,utf8_decode('VICERRECTORÍA DE FINANZAS Y LOGÍSTICA'));


		
		$pdf->SetFont('Helvetica','i',5);
		$pdf->Text( 50, 680, 'WJM/krp' );
		$pdf->Text( 50, 690, utf8_decode('DISTRIBUCIÓN:-') );
		$pdf->Text( 50, 700, '1.- INTERESADO' );
		$pdf->Text( 50, 710, '1.- AGFCU' );
		
		
		$pdf->SetFont('Helvetica','',8);
		$factor = 0.1;
	}
	function modifica_pdf(&$pdf) {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result = $db->build_results($this->sql);
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql= sql_suspension::get_sql('PRINT_PARAM');
		$result_universidad = $db->build_results($sql);		
		$nom_universidad = $result_universidad[0]['NOM_UNIVERSIDAD'];
		$mail_universidad = $result_universidad[0]['MAIL_UNIVERSIDAD'];
		$dir_universidad = utf8_decode($result_universidad[0]['DIR_UNIVERSIDAD']);
		
		for($i=0; $i<count($result); $i++) {
			$this->dibuja_uno($pdf, $result[$i], $nom_universidad, $mail_universidad, $dir_universidad);
			if ($i < count($result) - 1)
			$pdf->AddPage();
		}
	}		
}
?>