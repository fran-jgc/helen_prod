<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

//***********dibujo**************/////////
class print_devolucion_pagare_dos extends reporte {
    const K_PARAM_NOM_UNIVERSIDAD	= 5;
    const K_PARAM_DIRECCCION 		= 9;
    const K_PARAM_COMUNA 			= 11;
    const K_PARAM_CIUDAD 			= 12;
    const K_PARAM_PAIS 				= 16;
    const K_PARAM_TELEFONO			= 10;
    const K_PARAM_FAX 				= 15;
    const K_PARAM_E_MAIL 			= 13;
    const K_PARAM_SITIO_WEB 		= 14;
    function __construct($sql, $xml, $labels=array(), $titulo, $con_logo, $vuelve_a_presentacion=false) {
        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
        $sql_param = "select f_get_parametro(".self::K_PARAM_NOM_UNIVERSIDAD.")NOM_UNIVERSIDAD
							,f_get_parametro(".self::K_PARAM_DIRECCCION.")DIRECCION
							,f_get_parametro(".self::K_PARAM_COMUNA.")COMUNA
	                        ,f_get_parametro(".self::K_PARAM_CIUDAD.")CIUDAD
	                        ,f_get_parametro(".self::K_PARAM_PAIS.")PAIS
	                        ,f_get_parametro(".self::K_PARAM_TELEFONO.")TELEFONO
	                        ,f_get_parametro(".self::K_PARAM_E_MAIL.")EMAIL
	                        ,f_get_parametro(".self::K_PARAM_SITIO_WEB.")SITIO_WEB
	                 from DUAL";
        $result	= $db->build_results($sql_param);
        // reporte
        $labels = array();
        $labels['str_nom_universidad']  = $result[0]['NOM_UNIVERSIDAD'];
        $labels['str_direccion']        = $result[0]['DIRECCION'];
        $labels['str_comuna']           = $result[0]['COMUNA'];
        $labels['str_ciudad']           = $result[0]['CIUDAD'];
        $labels['str_pais']             = $result[0]['PAIS'];
        $labels['str_telefono']         = $result[0]['TELEFONO'];
        $labels['str_email']            = '';
        $labels['str_sitio_web']        = 'www.usach.cl';
        
        parent::__construct($sql, $xml, $labels, $titulo, $con_logo, $vuelve_a_presentacion);
    }
    function dibuja_uno(&$pdf, $result) {
        
        $pdf->SetFont('Helvetica','i',10);
        
        $pdf->Text( 370, 130,utf8_decode('ORD.:  N°__________'));
        $pdf->Text( 415, 130,$result['NRO_FOLIO']);
        $pdf->Text( 370, 145,'ANT.: Solicitud de deudor');
        $pdf->Text( 370, 160,utf8_decode('MAT.: Envía Pagaré'));
       //$pdf->Text( 400, 175,'________________________');
        
        $pdf->SetFont('Helvetica','b',10);
        $pdf->Text( 70, 240,'SANTIAGO, ');
        $pdf->Text( 70, 270,utf8_decode('DE:     ADMINISTRACIÓN GENERAL FONDO SOLIDARIO CRÉDITO UNIVERSITARIO'));
        
        if($result['SEXO']=='F'){
            $pdf->Text( 70, 295,'A:       SRA: '.utf8_decode($result['NOMBRE']));
        }else{
            $pdf->Text( 70, 295,'A:       SR: '.utf8_decode($result['NOMBRE']));
        }
        
        $pdf->Text( 100, 310,'Rut: '.number_format($result['RUT'], 0, ',', '.')."-".$result['DIG_VERIF']);
        $pdf->Text( 100, 325,utf8_decode('Dirección: '.$result['DIRECCION']));
        $pdf->Ln();
        $pdf->Line(50, 345,550, 345);
        
        $pdf->SetFont('Helvetica','',10);
        $pdf->SetXY(70 , 360);
        $pdf->MultiCell(450, 15,utf8_decode('De acuerdo a lo solicitado por usted, atendiendo que su deuda se encuentra pagada, adjunto envío '.$result['CANT_PAGARES'].' pagare(s) de '.$result['NOM_TIPO_DEUDA'].'. Agradeceré notificar vía email a marcia.zenteno@usach.cl la recepción de los pagarés.
        
        
        
Saluda atentamente a usted,'), 0, 'J',false);
        
       
        $pdf->SetFont('Helvetica','B','9');
		$pdf->Text( 251,620,$result['NOM_ADMIN']);
		$pdf->SetFont('Helvetica','','8,5');
		$pdf->Text( 254, 630,utf8_decode('ADMINISTRADOR GENERAL'));
		$pdf->Text( 212, 640,utf8_decode('FONDO SOLIDARIO DE CRÉDITO UNIVERSITARIO'));
		$pdf->Text( 258, 650,utf8_decode('DIRECCIÓN DE GESTIÓN'));
		$pdf->Text( 209, 660,utf8_decode('VICERRECTORÍA DE ADMINISTRACIÓN Y LOGÍSTICA'));
        

        $pdf->SetFont('Helvetica','i',5);
		$pdf->Text( 50, 680, 'WJM/MZV/lcj.' );
		$pdf->Text( 50, 690, utf8_decode('DISTRIBUCIÓN:-') );
		$pdf->Text( 50, 700, '1.- INTERESADO' );
		$pdf->Text( 50, 710, '1.- AGFCU' );
      
        
        $pdf->SetTextColor(200, 200, 200);
        $pdf->SetXY(5 , 710);
        $pdf->MultiCell(600,8,utf8_decode('ADMINISTRACIÓN GENERAL FONDO SOLIDARIO CRÉDITO UNIVERSITARIO '), 0, 'C',false);
        $pdf->SetXY(5 , 720);
        $pdf->MultiCell(600,8,utf8_decode('DIRECCIÓN DE GESTIÓN - VICERRECTORÍA DE ADMINISTRACIÓN Y LOGÍSTICA'), 0, 'C',false);
        $pdf->SetTextColor(0, 93, 255);
        $pdf->SetFont('Helvetica','',8);
    }
    function modifica_pdf(&$pdf) {
        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
        $result = $db->build_results($this->sql);
        
        for($i=0; $i<count($result); $i++) {
            $this->dibuja_uno($pdf, $result[$i]);
            if ($i < count($result) - 1)
                $pdf->AddPage();
        }
    }
}
?>