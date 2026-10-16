<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
ini_set('display_errors', 'Off');
class print_reprogramacion extends reporte {
	const K_NOM_UNIVERSIDAD = 5;
	const K_ADM_UNIVERSIDAD = 19;
	const K_PORTAL_UNIVERSIDAD = 26;
	
	function __construct($cod_reprogramacion, $xml, $labels=array(), $titulo, $con_logo, $vuelve_a_presentacion=false) {
		$this->cod_reprogramacion = $cod_reprogramacion;
		
		if(K_TIPO_BD=="oci")
			$sql = "select 1 from dual";
		else if (K_TIPO_BD=="mssql")
			$sql = "select 1";
			
			parent::__construct($sql, $xml, $labels, $titulo, $con_logo, $vuelve_a_presentacion);
	}
	
	function dibuja_uno(&$pdf, $result) 
	{
		$factor = 0.3;
		if (K_CLIENTE == 'ULAGOS')
			$pdf->Image(dirname(__FILE__)."/../../images_appl/".K_CLIENTE."/logo_universidad.jpg", 45, 20, 286 * $factor, 147 * $factor);
		else if (K_CLIENTE == 'UDA')
			$pdf->Image(dirname(__FILE__)."/../../images_appl/".K_CLIENTE."/logo_universidad.jpg", 45, 20, 316 * $factor, 315 * $factor);
		else if (K_CLIENTE == 'SURCOB_UACH')
			$pdf->Image(dirname(__FILE__)."/../../images_appl/".K_CLIENTE."/logo_universidad.jpg", 45, 20, 271 * $factor, 322 * $factor);
		else if (K_CLIENTE == 'UACH')
			$pdf->Image(dirname(__FILE__)."/../../images_appl/".K_CLIENTE."/logo_universidad.jpg", 45, 20, 271 * $factor, 322 * $factor);
		else if (K_CLIENTE == 'UCM')
			$pdf->Image(dirname(__FILE__)."/../../images_appl/".K_CLIENTE."/logo_universidad.jpg", 45, 20, 200 * $factor, 257 * $factor);
		else if (K_CLIENTE == 'UCT')
			$pdf->Image(dirname(__FILE__)."/../../images_appl/".K_CLIENTE."/logo_uct.jpg", 45, 20, 160 * $factor, 160 * $factor);
		else if (K_CLIENTE == 'UMCE')
			$pdf->Image(dirname(__FILE__)."/../../images_appl/".K_CLIENTE."/logo_universidad.jpg", 45, 20, 329 * $factor, 319 * $factor);
		else if (K_CLIENTE == 'USACH')
			$pdf->Image(dirname(__FILE__)."/../../images_appl/".K_CLIENTE."/logo_universidad.jpg", 45, 20, 200 * $factor, 257 * $factor);
		else if (K_CLIENTE == 'UTEM')
			$pdf->Image(dirname(__FILE__)."/../../images_appl/".K_CLIENTE."/logo_universidad.jpg", 45, 20, 339 * $factor, 84 * $factor);
		else if (K_CLIENTE == 'UV')
			$pdf->Image(dirname(__FILE__)."/../../images_appl/".K_CLIENTE."/logo_universidad.jpg", 45, 20, 286 * $factor, 147 * $factor);
		else 
			$pdf->Image(dirname(__FILE__)."/../../images_appl/".K_CLIENTE."/logo_universidad.jpg", 55, 20, 286, 147);
		
		
		//////Titulo//////
		$pdf->SetFont('Helvetica','B');
		$pdf->Text(175, 190,'Certificado de Reprogramación Ley 20.572');
		
		//////Fecha y Datos Cliente//////
		$pdf->SetFont('Helvetica');
		$pdf->SetXY(55, 260);
		$pdf->MultiCell(500, 15,'Mes de '.$result[0]['MES']. ', Año '.$result[0]['ANO'].'.', 0, 'J', FALSE);
		$pdf->SetXY(55, 290);
		$pdf->MultiCell(500, 15,'Por el presente documento, certificamos que el Sr.(a): '.utf8_decode($result[0]['NOMBRE'].' '.$result[0]['APELLIDO_PATERNO'].' '.$result[0]['APELLIDO_MATERNO']).'. Rut: '.$rut = number_format($result[0]['RUT'],0,',','.').'-'.$result[0]['DIG_VERIF']. ' ha entregado la documentación y efectuado los pagos o compromisos, requeridos y  dispuestos en la Ley N° 20.572. Por lo tanto, a partir de esta fecha se considera Deudor Reprogramado, debiendo cumplir con lo dispuesto en ley señalada.', 0, 'J', FALSE);
		//////Tecto//////
		
		$pdf->SetXY(55, 455);
		$pdf->MultiCell(500, 15,' ', 0, 'J', FALSE);
		$pdf->SetXY(55, 470);
		$pdf->MultiCell(500, 15,$result[0][''], 0, 'J', FALSE);
		
		$pdf->SetXY(55, 390);
		$pdf->MultiCell(500, 15,'Se despide cordialmente,', 0, 'J', FALSE);
		$pdf->SetXY(55, 530);
		$pdf->SetFont('Helvetica','B');
		$pdf->MultiCell(500, 15,utf8_decode($result[0]['ADM_UNIVERSIDAD']), 0, 'C', FALSE);

		$pdf->SetFont('Helvetica');
		$pdf->SetXY(55, 545);
		$pdf->MultiCell(500, 15,'ADMINISTRADOR GENERAL', 0, 'C', FALSE);
		$pdf->SetXY(55, 560);
		$pdf->MultiCell(500, 15,'FONDO SOLIDARIO DE CRÉDITO UNIVERSITARIO', 0, 'C', FALSE);
		$pdf->SetXY(55, 575);
		$pdf->MultiCell(500, 15,$result[0]['NOM_UNIVERSIDAD'], 0, 'C', FALSE);
		
		$pdf->SetFontSize(9);
		$pdf->SetXY(55, 420);
		//$pdf->MultiCell(500, 15,$result[0]['PORTAL_UNIVERSIDAD'], 0, 'J', FALSE);
		$pdf->SetXY(145, 20);
		$pdf->MultiCell(240, 15,'FONDO SOLIDARIO DE CRÉDITO UNIVERSITARIO '.$result[0]['NOM_UNIVERSIDAD'], 0, 'J', FALSE);
		
	}
	
	function modifica_pdf(&$pdf) {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
				if (K_TIPO_BD=="oci"){
					$sql="SELECT DR.NOMBRE
									,DR.APELLIDO_PATERNO
									,DR.APELLIDO_MATERNO
									,DR.RUT
									,DR.DIG_VERIF
									,RE.COD_REPROGRAMACION
									,f_get_nom_mes (To_Char(FECHA_CONFIRMA,'MM')) MES
									,RE.COD_REPROGRAMACION
									,TO_CHAR(fecha_confirma,'yyyy') ANO
									,(SELECT f_get_parametro(".self::K_NOM_UNIVERSIDAD.")FROM dual) NOM_UNIVERSIDAD
									,(SELECT f_get_parametro(".self::K_ADM_UNIVERSIDAD.")FROM dual) ADM_UNIVERSIDAD
									,(SELECT f_get_parametro(".self::K_PORTAL_UNIVERSIDAD.")FROM dual) PORTAL_UNIVERSIDAD 		
							FROM REPROGRAMACION RE
								,DEUDOR_REPROGRAMACION DR
							WHERE RE.COD_REPROGRAMACION = DR.COD_REPROGRAMACION
							AND RE.COD_REPROGRAMACION = $this->cod_reprogramacion";
							}
				else if (K_TIPO_BD=="mssql"){
					$sql ="SELECT DR.NOMBRE
									,DR.APELLIDO_PATERNO
									,DR.APELLIDO_MATERNO
									,DR.RUT
									,DR.DIG_VERIF
									,RE.COD_REPROGRAMACION
									,dbo.f_get_nom_mes(MONTH(FECHA_CONFIRMA)) MES
									,RE.COD_REPROGRAMACION
									,YEAR(fecha_confirma) ANO
									,(SELECT dbo.f_get_parametro(".self::K_NOM_UNIVERSIDAD.")) NOM_UNIVERSIDAD
									,(SELECT dbo.f_get_parametro(".self::K_ADM_UNIVERSIDAD.")) ADM_UNIVERSIDAD
									,(SELECT dbo.f_get_parametro(".self::K_PORTAL_UNIVERSIDAD.")) PORTAL_UNIVERSIDAD 		
							FROM REPROGRAMACION RE
								,DEUDOR_REPROGRAMACION DR
							WHERE RE.COD_REPROGRAMACION = DR.COD_REPROGRAMACION
							AND RE.COD_REPROGRAMACION = $this->cod_reprogramacion";
							
							}
												
		$result = $db->build_results($sql);

		$count = count($result);
		
		$this->dibuja_uno($pdf, $result);
	}
}
?>