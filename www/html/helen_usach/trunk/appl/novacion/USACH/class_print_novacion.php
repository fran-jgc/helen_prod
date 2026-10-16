<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");

//***********dibujo**************/////////
class print_novacion extends reporte {
	var $cod_novacion;
	
	function __construct($cod_novacion, $xml, $labels=array(), $titulo, $con_logo, $vuelve_a_presentacion=false) {
		$this->cod_novacion = $cod_novacion;
		parent::__construct("select DUMMY from DUAL", $xml, $labels, $titulo, $con_logo, $vuelve_a_presentacion);
	}
	 
	function dibuja_uno(&$pdf, $result, $monto_pagare, $monto_moneda) {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$K_PARAM_COD_UNIVERSIDAD_DEFECTO = 3;
		
		$sql_universidad = "SELECT VALOR 
          	  				FROM PARAMETRO
          	  				WHERE COD_PARAMETRO =".$K_PARAM_COD_UNIVERSIDAD_DEFECTO;	
		$result_universidad_defecto = $db->build_results($sql_universidad);
		
		$cod_universidad_defecto = $result_universidad_defecto[0]['VALOR'];
		$cod_universidad_pagare = $result['COD_UNIVERSIDAD'];
		
		$sql_nombre_universidad ="SELECT NOM_UNIVERSIDAD
								  FROM UNIVERSIDAD
								  WHERE COD_UNIVERSIDAD=".$cod_universidad_pagare;
		$result_nombre_universidad = $db->build_results($sql_nombre_universidad);
		
		if($cod_universidad_defecto == $cod_universidad_pagare){
			
			$pdf->SetFont('Helvetica','',16);
			$pdf->Text( 230, 100,'PAGARÉ');
			$pdf->Text( 300, 100,$result['COD_NOVACION']);
			$pdf->SetFont('Helvetica','',11);
		
			$pdf->Rect( 320, 110, 240, 30, 'f');
			$pdf->Text( 330, 123,'FOLIO N° :');
			$pdf->Text( 440, 123,$result['COD_NOVACION']);
			$pdf->Text( 330, 138,'RUT DEUDOR N° :');
			$pdf->Text( 440, 138,number_format($result['RUT'], 0, ',', '.')."-".$result['DIG_VERIF']);
		}
		else{
			$pdf->SetFont('Helvetica','',16);
			$pdf->Text( 230, 100,'PAGARÉ');
			$pdf->SetFont('Helvetica','',11);
			
			$pdf->Rect( 320, 110, 240, 30, 'f');
			$pdf->Text( 330, 123,'FOLIO N° :');

			$pdf->Text( 330, 138,'RUT DEUDOR N° :');
			$pdf->Text( 440, 138,number_format($result['RUT'], 0, ',', '.')."-".$result['DIG_VERIF']);
		}
		
		$pdf->SetFont('Helvetica','I',11);
		$pdf->Rect( 40, 150, 520, 85, 'f');
		$pdf->Text( 50, 165,'YO ');
		$pdf->Text( 160, 165,$result['NOMBRE']);
		$pdf->Text( 50, 180,'CED. IDENT.: ');
		$pdf->Text( 160, 180,number_format($result['RUT'], 0, ',', '.')."-".$result['DIG_VERIF']);
		$pdf->Text( 50, 195,'DOMICILIADO EN: ');

		$pdf->Text( 160, 195,utf8_decode($result['DIRECCION'].', '.$result['NOM_COMUNA']));
		$pdf->Text( 50, 210,'TELÉFONO ');
		$pdf->Text( 160, 210,$result['TELEFONO']);
		$pdf->SetFont('Helvetica','I',11);

		$pdf->SetFont('Helvetica','I',11);
		$pdf->Text( 50, 255,'Por el presente instrumento, me obligo a pagar a la '.$result_nombre_universidad[0]['NOM_UNIVERSIDAD'].':');

		$pdf->SetXY(50 , 280);
		$pdf->MultiCell(500, 15,'De acuerdo a lo establecido en la Ley N°19.287, del 04 de febrero de 1994, la suma consolidada al 31 de Diciembre de '.$result['ANO_CONSOLIDA'].'  de que soy deudor(a), equivalente a '.number_format($monto_moneda, 4, ',', '.').' ('.$result['MONTO_MONEDA_PALABRAS'].'), (En Palabras). el inicio del pago de la(s) '.$result['CANT_CUOTA'].' cuota(s) anuales y sucesívas corresponderí al año  '.$result['ANO_INICIO'].'. ', 0, 'J',false);

		$pdf->SetXY(50 , 347);
		$pdf->MultiCell(500, 15,'El pago de la obligación que asumo conforme al presente Pagaré, lo hará en la forma y condiciones establecidas en la Ley N° 19287 del 04 de Febrero de 1994, y sus Reglamentos, normativas que declaro conocer y me obligo a aceptar en todas sus partes.', 0, 'J',false);

		$pdf->SetXY(50 , 400);
		$pdf->MultiCell(500, 15,'La suma adeudada conforme al presente instrumento, la pagaré en el equivalente a Moneda Nacional, según el valor de la Unidad Tributaria Mensual vigente a la época del pago efectivo, en '.$result['CANT_CUOTA'].'  CUOTA(S), anuales, iguales de  '.number_format($result['MONTO_CUOTA'], 4, ',', '.').' UTM devengándose un interés equivalente a un '.$result['TASA_INTERES_CREDITO'].'%  anual sobre el capital reajustado.', 0, 'J',false);

		$pdf->SetXY(50 , 475);
		$pdf->MultiCell(500, 15,'En caso de incumplimiento del pago anual correspondiente, éste devengará un Interés Penal de  '.$result['TASA_INTERES_PENAL'].'%  por cada mes o fracción del mes, en que se retrase su cumplimiento.', 0, 'J',false);

		$pdf->SetXY(50 , 520);	
		$pdf->MultiCell(500, 15,'El pago deberé efectuarse en las oficinas correspondientes a la Universidad o en los lugares que ella señale.', 0,'J',false);

		$pdf->SetXY(50 , 565);
		$pdf->MultiCell(500, 15,'Para todos los efectos del presente Pagaré, tanto el deudor, así como la Universidad, fijan su domicilio en la ciudad de Santiago, sometiéndose a la jurisdicción de sus Tribunales Ordinarios.', 0, 'J',false);

		$pdf->SetXY(50 , 610);
		$pdf->MultiCell(500, 15,'Para efectos de las obligaciones contraídas en virtud al presente Pagaré, el deudor se considera plenamente capaz.', 0, 'J',false);

		$pdf->SetXY(50 , 655);
		$pdf->MultiCell(500, 15,$result_nombre_universidad[0]['NOM_UNIVERSIDAD'].', quedan liberados de la obligación de protestar el presente pagaré respecto de todos los obligados  a su pago.', 0, 'J',false);

		$pdf->SetFont('Helvetica','I',8);
		$pdf->Line( 100,  740,  200,  740);
		$pdf->Text( 130, 750,'NOTARIO');
		$pdf->Line( 350,  740,  460,  740);
		$pdf->Text( 370, 750,'FIRMA DEL DEUDOR');
		$pdf->Text( 50, 780,'SANTIAGO, Diciembre 31 de '.$result['ANO_CONSOLIDA']);
		$pdf->Text( 50, 780,'SANTIAGO,');
		
		$pdf->SetFont('Helvetica','',7);
		$factor = 0.2;
			
		$sql= sql_novacion::get_sql('PRINT_PARAM');
		$result_universidad = $db->build_results($sql);	
		$nom_universidad = $result_universidad[0]['NOM_UNIVERSIDAD'];
		$mail_universidad = $result_universidad[0]['MAIL_UNIVERSIDAD'];
		$dir_universidad = $result_universidad[0]['DIR_UNIVERSIDAD'];
		
		if($cod_universidad_defecto == $cod_universidad_pagare){
		 	$pdf->Text( 100, 35, $nom_universidad);
			$pdf->Text( 100, 45, 'VICE-RECTORIA DE ASUNTOS ECONÓMICOS Y ADMINISTRATIVOS' );
			$pdf->Text( 100, 55, 'ADMINISTRACIÓN GRAL. FONDO SOLIDARIO CRÉDITO UNIVERSITARIO' );
			$pdf->Text( 100, 65, $dir_universidad.' '.$mail_universidad);
			$pdf->Image(dirname(__FILE__)."/../../../images_appl/".K_CLIENTE."/logo_universidad.jpg", 45, 25, $factor * 220, $factor * 220);
		}
	}
	
	function modifica_pdf(&$pdf){
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql= sql_novacion::get_sql('PRINT_NOVACION',$this->cod_novacion);
		$result = $db->build_results($sql);
		$monto_pagare='';
		for($j=0; $j<2 ; $j++){
			for($i=0; $i<count($result); $i++){
				$monto_moneda = $result[$i]['MONTO_MONEDA'];
				if($monto_moneda != ''){
					$separado = explode('.', $monto_moneda);
					$saldo_entero = $separado[0];
					$saldo_decimal = $separado[1];
				}else{
					$saldo_entero = 0;
					$saldo_decimal = 0;
				}
				
				$entero_en_palabras = Numbers_Words::toWords($saldo_entero,"es");
				$decimal_en_palabras = Numbers_Words::toWords($saldo_decimal,"es");
		
				$result[$i]['MONTO_MONEDA_PALABRAS'] = $entero_en_palabras." coma ".$decimal_en_palabras." UTM" ;
				
				$this->dibuja_uno($pdf, $result[$i],$monto_pagare,$monto_moneda);
				if($i < count($result)-1)
					$pdf->AddPage();
			}
			if($j < 2-1){
				$pdf->AddPage();
			}	
			$pdf->SetFont('Helvetica','B',15);
			$pdf->SetXY($iniX+460,$iniY + 50);
			$pdf->Cell(100,$h_cell,'COPIA', '', '','L');	
		}	
	}
}
?>