<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");

//***********dibujo**************/////////
class print_suspension extends reporte {	
    function __construct($sql, $xml, $labels=array(), $titulo, $con_logo, $vuelve_a_presentacion=false) {
        parent::__construct($sql, $xml, $labels, $titulo, $con_logo, $vuelve_a_presentacion);			
	}
	function dibuja_uno(&$pdf, $result, $nom_universidad, $mail_universidad, $dir_universidad) {
		
		$pdf->SetFont('Helvetica','i',10);
		
		$pdf->Text( 360, 100,'ORD  N°__________/');
		$pdf->Text( 360, 115,'ANT.: Solicitud deudor');
		$pdf->Text( 360, 130,'MAT.: Da respuesta.	');
		$pdf->Text( 360, 145,'_________________/');
		$pdf->SetFont('Helvetica','',10);
		$pdf->Text( 50, 180,'SANTIAGO, '.$result['MES'].' '.$result['DIA'].' del '.$result['ANO_SYS'].'');		
		$pdf->SetFont('Helvetica','',10);
		$pdf->Text( 50, 240,'DE:  ADMINISTRACIÓN GRAL. FONDO SOLIDARIO CRÉDITO UNIVERSITARIO');
		$pdf->Text( 50, 240,'A:');
		$pdf->Text( 70, 240,utf8_decode($result['NOMBRE']));
		$pdf->Text( 350, 240,'Rut:');
		$pdf->Text( 380, 240,number_format($result['RUT'], 0, ',', '.')."-".$result['DIG_VERIF']);
		$pdf->Text( 70, 255,utf8_decode($result['DIRECCION']));
		
		$pdf->SetFont('Helvetica','',10);
		$pdf->Text( 207, 350,'1.-	Acoger su solicitud en orden a verificar su situación académica.');
		$pdf->SetXY(50 , 290);
		$pdf->MultiCell(500, 15,'                                                    	De acuerdo a los antecedentes  aportados por Ud., la Administación General del Fondo Solidario de Crédito Universitario de esta Universidad, ha resuelto lo siguiente:', 0, 'J',false);
		
		$pdf->SetXY(50 , 380);
		$pdf->MultiCell(500, 15,'                                               2.-	Informar a Ud. que se a dispuesto suspender el cobro. correspondiente a la (s) cuota (s) del '.$result['NOM_TIPO_DEUDA'].' del (los) año (s) '.$result['ANO_SUSPENSION'].' dado que presenta la siguiente situación '.$result['NOM_TIPO_SUSPENSION'].'. ', 0, 'J',false);
		
		$pdf->SetXY(50 , 455);
		$pdf->MultiCell(500, 15,'                                             3.-	La cobranza de su deuda continuara en cobro a partir del próximo año, conforme a las normas impartidas por la ley por la Ley 19.287 que establece suspensión hasta por tres años, cuando el deudor estudia Magíster y Hasta cuatro años cuando estudia Doctorado. En caso de estudiar Magíster y Doctorado, suspensión se puede aceptar solo hasta por cuatro años.', 0, 'J',false);
		
		$pdf->Text( 230, 560,'Saluda atentamente a Ud.,');
		$pdf->Text( 235, 620,utf8_decode($result['NOM_ADMIN']));
		$pdf->SetFont('Helvetica','B',8);
		$pdf->Text( 245, 632,'ADMINISTRADOR GENERAL');
		
		$pdf->SetFont('Helvetica','B',8);
		$pdf->Text( 50, 740, $nom_universidad);
		$pdf->Text( 50, 750, 'VICERRECTORIA DE ASUNTOS ECONOMICOS Y ADMINISTRATIVOS' );
		$pdf->Text( 50, 760, 'ADMINISTRACION GRAL. FONDO SOLIDARIO CREDITO UNIVERSITARIO');
		$pdf->Text( 50, 770, $dir_universidad.' '.$mail_universidad);
		
		$pdf->SetFont('Helvetica','',8);
		$factor = 0.2;
		$pdf->Image(dirname(__FILE__)."/../../../images_appl/".K_CLIENTE."/logo_universidad.jpg", 498, 30, $factor * 350, $factor * 400);
		$pdf->SetFont('Helvetica','B',22);
		$pdf->SetTextColor(0, 0, 128);
		$pdf->Text( 500, 130, session::get('K_CLIENTE'));
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