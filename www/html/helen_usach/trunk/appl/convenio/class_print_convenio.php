<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class print_convenio extends reporte {
	const K_PARAM_TIPO_DEUDA_RENEG = 29;
	var $cod_convenio;	
	
	function __construct($cod_convenio, $xml, $labels=array(), $titulo, $con_logo, $vuelve_a_presentacion=false) {
		$this->cod_convenio = $cod_convenio;
		$sql_tipo = sql_convenio::get_sql('SQL_TIPO');
		parent::__construct($sql_tipo, $xml, $labels, $titulo, 1, $vuelve_a_presentacion);
	}
	function dibuja_uno(&$pdf, $result, $anios, $dir_universidad, $fono_universidad, $nom_universidad, $comuna_universidad, $ciudad_universidad, $fax_universidad, $administrador_universidad, $interes_convenio, $pago_sugerido_convenio){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		
		$pdf->SetFont('Helvetica','B',11);
		$pdf->SetFont('Helvetica','B',11);
		$pdf->Text( 250, 120,'CONVENIO DE PAGO');
		
		$pdf->SetFont('Helvetica','',10);
		$pdf->Text( 450, 50,'FOLIO: '.$result[0]['COD_CONVENIO']);
		
		$pdf->SetXY(50 , 150);
		$pdf->MultiCell(500, 15,'     '.$ciudad_universidad.', '.$result[0]['DIA'].' de '.$result[0]['MES'].' de '.$result[0]['ANIO'].', entre don '.utf8_decode($administrador_universidad).', en su calidad de Administrador General del Fondo de Crédito Universitario de la'.$nom_universidad.', ubicada en '.utf8_decode($dir_universidad).', '.utf8_decode($ciudad_universidad).'.', 0, 'J',false);
		
		$CY=$pdf->GetY();
		
		
		$pdf->SetXY(50 , $CY);
		$pdf->MultiCell(500, 15,'     Y don(ña) '.$result[0]['NOM_DEUDOR'].', RUT: '.number_format($result[0]['RUT'], 0, ',', '.').'-'.$result[0]['DIG_VERIF'].', con domicilio en: '.utf8_decode($result[0]['DIRECCION_DEUDOR']).', deudor(a) del '.$result[0]['NOM_TIPO_DEUDA'].'.', 0, 'J',false);
		
		$CY=$pdf->GetY()+15;
		$pdf->SetXY(50 , $CY);
		$pdf->MultiCell(500, 15,'     Han convenido lo siguiente:', 0, 'J',false);
		
		$CY=$pdf->GetY()+15;
		$pdf->SetXY(50 , $CY);
		$pdf->Cell(100,15,'1.-','','C');
		$pdf->SetXY(65 , $CY);
		$pdf->MultiCell(485, 15,'Don(ña) '.utf8_decode($result[0]['NOM_DEUDOR']).', adeuda a la '.$nom_universidad.', la cantidad de '.number_format($result[0]['MONTO_CONVENIO_MONEDA'], 4, ',', '.').' UTM calculados al '.$result[0]['FECHA_CONVENIO'].', correspondiente saldo Crédito Universitario '.$result[0]['NOM_TIPO_DEUDA'].', cuotas(s) año(s) '.$anios.'.', 0, 'J',false);

		$CY=$pdf->GetY()+15;
		$pdf->SetXY(50 , $CY);
		$pdf->Cell(100,15,'2.-','','C');
		$pdf->SetXY(65 , $CY);
		$pdf->MultiCell(485, 15,'Por este acto las partes convienen en liquidar la deuda de la siguiente forma:', 0, 'J',false);

		$pdf->SetFont('Helvetica','B',10);
		//for de las cuotas
		$cantidad_cuotas = $result[0]['CANT_CUOTA']; 
		$CY=$pdf->GetY()+10;
		$pdf->SetXY(200 , $CY);
		$pdf->Cell(50,10,'Cuotas','LBRT','','C');
		$pdf->SetXY(250 , $CY);
		$pdf->Cell(100,10,'Fecha Vencimiento','LBRT','','C');
		$pdf->SetXY(350 , $CY);
		$pdf->Cell(50,10,'Monto','LBRT','','C');
		$CY=$pdf->GetY()+10;
		$cuota=1;
		$cuota_mensual = number_format($result[0]['CUOTA_MENSUAL_MONEDA'], 4, ',', '.');
				
		for($i=0;$i<$cantidad_cuotas;$i++){
			$sql_fechas = sql_convenio::get_sql('ANO_CUOTAS',$this->cod_convenio,$i);
			$result_fechas = $db->build_results($sql_fechas);
			$pdf->SetFont('Helvetica','',10);
			$pdf->SetXY(200 , $CY);
			$pdf->Cell(50,10,$cuota,'LBRT','','C');
			$pdf->SetXY(250 , $CY);
			$pdf->Cell(100,10,$result_fechas[0]['FECHA'],'LBRT','','C');
			$pdf->SetXY(350 , $CY);
			$pdf->Cell(50,10,$cuota_mensual,'LBRT','','C');
			
			$cuota++;
			$CY=$pdf->GetY()+10;
		}
		
		$pdf->SetFont('Helvetica','',10);
		$CY=$pdf->GetY()+15;
		$pdf->SetXY(50 , $CY);
		$pdf->Cell(100,15,'3.-','','C');
		$pdf->SetXY(65 , $CY);
		$pdf->MultiCell(485, 15,'Durante el periodo en Convenio, el saldo de la deuda se incrementará en '.$interes_convenio.'% mensual, no existiendo otro recargo por mora. La cuota fijada incluye los incrementos que afectan a la deuda base, segun este Convenio.', 0, 'J',false);
		
		$CY=$pdf->GetY()+15;
		$pdf->SetXY(50 , $CY);
		$pdf->Cell(100,15,'4.-','','C');
		$pdf->SetXY(65 , $CY);
		$pdf->MultiCell(485, 15,'Quien suscribe en calidad de deudor(a) queda apercibido que el incuplimiento del presente convenio, en cualquiera de sus partes liberará al Administrador General de todo compromiso respecto de éste, dejando constancia que las medidas posteriores de cobro consideran el registro de deuda morosa en los servicios de información comercial y de cobranza externa.', 0, 'J',false);
		
		$CY=$pdf->GetY()+15;
		$pdf->SetXY(50 , $CY);
		$pdf->Cell(100,15,'5.-','','C');
		$pdf->SetXY(65 , $CY);
		$pdf->MultiCell(485, 15,'El pago se hará mediante depósito en la cuenta corriente N° '.$result[0]['NRO_CUENTA_CORRIENTE'].' '.utf8_decode($result[0]['NOM_CUENTA_CORRIENTE']).' a nombre de la '.$nom_universidad.'. El comprobante deberá ser faxeado al número '.$fax_universidad.' o enviar una fotocopia a la Oficina de Crédito Universitario, para los efectos de Identificación del pago.', 0, 'J',false);
		
		
		$pdf->SetFont('Helvetica','B',11);
		$pdf->SetXY(100 , 700);
		$pdf->Cell(200,15,utf8_decode($result[0]['NOM_DEUDOR']),'','','C');
		$pdf->SetXY(100 , 715);
		$pdf->Cell(200,15,'RUT '.number_format($result[0]['RUT'], 0, ',', '.').'-'.$result[0]['DIG_VERIF'],'','','C');
		
		$pdf->SetXY(400 , 700);
		$pdf->Cell(100,15,$administrador_universidad,'','','C');
		$pdf->SetXY(400 , 715);
		$pdf->Cell(100,15,'Administrador General','','','C');
		
		$pdf->SetFont('Helvetica','',7);
		$pdf->SetXY(400 , 700);
		$factor = 0.2;
		//$pdf->Image(dirname(__FILE__)."/../../images_appl/".K_CLIENTE."/logo_reporte.jpg", 280, 25, $factor * 220, $factor * 220);
	
		
	}
	function modifica_pdf(&$pdf) {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = sql_convenio::get_sql('PRINT_CONVENIO',$this->cod_convenio);
		$result = $db->build_results($sql);

		$sql = sql_convenio::get_sql('PRINT_PARAMETROS',$this->cod_convenio);
		$result_universidad = $db->build_results($sql);	
		
		$dir_universidad = $result_universidad[0]['DIRECCION'];
		$fono_universidad = $result_universidad[0]['TELEFONO'];
		$nom_universidad = $result_universidad[0]['NOM_UNIVERSIDAD'];
		$comuna_universidad = $result_universidad[0]['COMUNA'];
		$ciudad_universidad = $result_universidad[0]['CIUDAD'];
		$fax_universidad = $result_universidad[0]['FAX'];
		$administrador_universidad = $result_universidad[0]['ADMINISTRADOR'];
		$interes_convenio = $result_universidad[0]['INTERES_CONVENIO'];
		$pago_sugerido_convenio = $result_universidad[0]['PAGO_SUGERIDO_CONVENIO'];
		
		$sql = sql_convenio::get_sql('ANO_COBRO',$this->cod_convenio);
		$result_anios = $db->build_results($sql);
		$count = count($result_anios);
		$anios ='';
		for($i=0;$i<$count;$i++){
			if($anios==''){
				$anios = $result_anios[$i]['ANO_COBRO'];
			}
			else{
				$anios.= ', '.$result_anios[$i]['ANO_COBRO'];
			}
		}
		
		
		$this->dibuja_uno($pdf, $result, $anios, $dir_universidad, $fono_universidad,$nom_universidad, $comuna_universidad, $ciudad_universidad, $fax_universidad, $administrador_universidad, $interes_convenio, $pago_sugerido_convenio);
	}
}
?>