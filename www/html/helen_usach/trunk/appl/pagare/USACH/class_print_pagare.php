<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");

//***********dibujo**************/////////
class print_pagare extends reporte {	
    function __construct($sql, $xml, $labels=array(), $titulo, $con_logo, $vuelve_a_presentacion=false) {
        parent::__construct($sql, $xml, $labels, $titulo, $con_logo, $vuelve_a_presentacion);			
	}
	function dibuja_uno(&$pdf, $result, $nom_universidad, $mail_universidad, $dir_universidad) {
		
		
		$cod_deudor = $result['COD_DEUDOR'];
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql_dir = "SELECT  (SELECT   D.DIRECCION 
							FROM DIRECCION D 
							where  f_get_direccion_deudor(DEUDOR.COD_DEUDOR ,'P')=D.COD_DIRECCION 
						)DIRECCION
						,( SELECT NOM_CIUDAD FROM CIUDAD CI, DIRECCION D 
							WHERE D.COD_DEUDOR =  DEUDOR.COD_DEUDOR  
							AND D.COD_CIUDAD = CI.COD_CIUDAD
							and f_get_direccion_deudor(DEUDOR.COD_DEUDOR ,'P')=D.COD_DIRECCION)	CIUDAD
						,( SELECT C.NOM_COMUNA FROM COMUNA C, DIRECCION D 
							WHERE D.COD_DEUDOR =  DEUDOR.COD_DEUDOR  
							AND D.COD_COMUNA = C.COD_COMUNA
							and f_get_direccion_deudor(DEUDOR.COD_DEUDOR ,'P')=D.COD_DIRECCION)	COMUNA
						,( SELECT EMAIL FROM  DIRECCION D 
							WHERE D.COD_DEUDOR =  DEUDOR.COD_DEUDOR  
							and f_get_direccion_deudor(DEUDOR.COD_DEUDOR ,'P')=D.COD_DIRECCION)	EMAIL
						,( SELECT TELEFONO FROM  DIRECCION D 
							WHERE D.COD_DEUDOR =  DEUDOR.COD_DEUDOR  
							and f_get_direccion_deudor(DEUDOR.COD_DEUDOR ,'P')=D.COD_DIRECCION)	TELEFONO
							,( SELECT CELULAR FROM  DIRECCION D 
							WHERE D.COD_DEUDOR =  DEUDOR.COD_DEUDOR  
							and f_get_direccion_deudor(DEUDOR.COD_DEUDOR ,'P')=D.COD_DIRECCION)	CELULAR
						,(SELECT NOM_REGION FROM REGION 
						   WHERE COD_REGION = (SELECT COD_REGION FROM CIUDAD 
						   					   WHERE COD_CIUDAD = (SELECT COD_CIUDAD FROM DIRECCION
												              	   WHERE COD_DEUDOR = $cod_deudor
												              	   AND TIPO_DIRECCION = 'P'
												              	   AND ES_VALIDA = 'S'
												                   AND ROWNUM = 1 ) 
							                  ) AND ROWNUM = 1 ) NOM_REGION
						from DEUDOR
						where COD_DEUDOR = $cod_deudor";
		$result_dir = $db->build_results($sql_dir);	
		
		$cod_deuda = $result['COD_DEUDA'];
		$cod_univ  = $result['COD_UNIVERSIDAD'];
		$K_EMITIDA = 1;
		$K_ABONADA = 2;
		$K_MOROSA = 4;
		$ano_suspension = $result['ANO_SUSPENSION'];
		
		$sql_count_cruzado = "select * from cruzado where cod_deuda= $cod_deuda";
		$result_cruzado = $db->build_results($sql_count_cruzado);
		
		if(count($result_cruzado) > 0){
			//Segï¿½n correo enviado por Fabiola el 23-01-2015, el Pagarï¿½ debe quedar sin el descto. por pago oportuno
			//pero el descto. debe quedar en el sistema y se aplica cuando el deudor paga la cuota.
			$sql_pp = "SELECT  round((NVL(pp.monto_capital + pp.monto_interes, 0)) * (cr.porc / 100), 4) MONTO_CUOTA
							  ,to_char(pp.fecha_vencto, 'dd-mm-yyyy') FECHA_VENCTO
					   FROM PLAN_PAGO pp, CRUZADO cr
					   WHERE pp.cod_deuda= cr.cod_deuda
					   and pp.cod_deuda = $cod_deuda
					   AND cr.cod_universidad = $cod_univ
					   AND pp.cod_estado_plan_pago in ($K_EMITIDA,$K_ABONADA, $K_MOROSA)
					   and pp.ano_cobro > $ano_suspension
					   ORDER BY pp.nro_cuota";
			$result_pp = $db->build_results($sql_pp);	
		}
		else{
			$sql_pp = "SELECT  NVL(monto_capital + monto_interes, 0) MONTO_CUOTA
							  ,to_char(fecha_vencto, 'dd-mm-yyyy') FECHA_VENCTO
					   FROM PLAN_PAGO
					   WHERE cod_deuda = $cod_deuda
					   AND cod_estado_plan_pago in ($K_EMITIDA,$K_ABONADA, $K_MOROSA)
					   and ano_cobro > $ano_suspension
					   ORDER BY nro_cuota";
			$result_pp = $db->build_results($sql_pp);
		}
		
		$y=50;
		$pdf->SetXY(20 , $y+=10);
		$pdf->SetFontSize(10);
		$pdf->SetFont('Arial','B');
		
		$pdf->MultiCell(500,10,$result['NOM_UNIVERSIDAD'],0,'C');
		$pdf->MultiCell(500,10,'FONDO SOLIDARIO DE CRÉDITO UNIVERSITARIO',0,'C');
		$pdf->ln();
		$pdf->ln();
		$pdf->MultiCell(500,10,'PAGARÉ DE REPROGRAMACIÓN SEGUN LEY N°20.572/2012 CON VENCIMIENTOS',0,'C');
		$pdf->MultiCell(500,10,'MODIFICADOS PRO ARTÍCULOS 16 Y 17 DEL DECRETO SUPREMO N°114/12',0,'C');
		$pdf->ln();
		$pdf->ln();
		$pdf->ln();
		$pdf->ln();
		
		
		$pdf->SetFontSize(8);
		$pdf->SetFont('Arial', 'B');
		$pdf->SetX(400);
		$pdf->MultiCell(500,10,'Folio N° '.$result['FOLIO_PAGARE'],0,'L');
		
		$pdf->ln();
		$pdf->ln();
		$pdf->ln();
		$pdf->ln();
		$pdf->ln();
		$pdf->ln();
		$pdf->ln();
		$pdf->ln();
		$pdf->SetX(50);
		$pdf->SetFont('Arial');
		$parrafo1 = 'En .........................................,a............de........................de......................,yo '.utf8_decode($result['NOMBRE'].' '.$result['APELLIDO_PATERNO'].' '.$result['APELLIDO_MATERNO']).
					', Cédula de Identidad N° '.number_format($result['RUT'], 0, '', '.').'-'.$result['DIG_VERIF'].
					' chileno(a), domiciliado(a) en '.utf8_decode($result_dir[0]['DIRECCION']).' Comuna de '.utf8_decode($result_dir[0]['COMUNA']).', '.utf8_decode($result_dir[0]['NOM_REGION']).'.'."\n".
					'En Conformidad a las disposiciones de la ley 20.572/12, y su respectivo Reglamento, Decreto Supremo N°114, de 2012, del Ministerio de Educación,'.
					' normativa que forma parte integrante del presente documento para todos los efectos legales y que me obligo a aceptar en todas sus partes. declaro que:';
		$pdf->MultiCell(500,10, $parrafo1 ,0, 'J',false);
		
		$pdf->ln();
		$pdf->SetX(50);
		$parrafo2 = 'Debo y pagaré a la '.$result['NOM_UNIVERSIDAD'].', domiciliada en '.utf8_decode($result['DIRECCION_UNIVERSIDAD']).', a su '.
					'ordern o a sus cesionarios, la suma de '.str_replace('.',',',$result['MONTO_MONEDA']).' UTM(Unidades Tributarias Mensuales) '.
					'o a la unidad fijada por ley que la remplace, equivalentes a la fecha de suscripción del presente pagaré a la suma en moneda '.
					'nacional de $'.number_format($result['MONTO_PESOS'], 0, '', '.').' correspondientes a los créditos otorgados por la universidad '.
					'según el siguiente detalle:';
		$pdf->MultiCell(500,10, $parrafo2 ,0, 'J',false);
		
		$pdf->ln();
		$pdf->SetX(50);
		$parrafo3 = 'Dicha suma me obligo a pagarla en la forma, época y condiciones siguientes:';
		$pdf->MultiCell(500,10, $parrafo3 ,0, 'J',false);
		
		$pdf->ln();
		$pdf->SetX(50);
		$pdf->SetFont('Arial', 'B');
		$parrafo4 = 'En cumplimiento de lo dispuesto en los artículos 16 y 17 del Decreto Supremo N°114 de la Ley de Reprogramación, y concurriendo la '.
					'suspensión en el pago de la cuota anual N°1, fecha de vencimiento '.$result['FECHA_VENCTO'].' se modifican los vencimientos estipulados '.
					'en pagaré folio '.$result['FOLIO_PAGARE_SUSPENSION'].', suscrito con fecha '.$result['FECHA_PAGARE_SUSPENSION'].', a partir de la '.
					'cuota anteriormente señalada y las sucesivas anuales a partir de ésta, manteniéndose las cuotas devengadas y morosas con sus respectivas '.
					'fechas de vencimiento en el pagaré antes individualizado, el cual producirá todos sus efectos legales con respecto a dichas cuotas, '.
					'modificando los siguientes vencimientos para las cuotas señaladas a continuación:';
		$pdf->MultiCell(500,10, $parrafo4 ,0, 'J',false);
		
		$pdf->ln();
		$pdf->ln();
		$pdf->ln();
		$pdf->SetX(50);
		$parrafo5 = 'Plan de Pago:'.number_format($result['MONTO_MONEDA'], 4, ',', '.').' UTM';
		$pdf->MultiCell(500,10, $parrafo5 ,0, 'J',false);
		
		$pdf->ln();
		$pdf->SetFont('Arial');
		$pdf->SetX(50);
		$pdf->Cell(44,20,'N° Cuotas',1,'L','C');
		$pdf->Cell(62,20,'Fecha Vencto.' ,1,'L','C');
		$pdf->Cell(60,20,'Cuotas en UTM',1,'L','C');
		$pdf->Cell(44,20,'N° Cuotas',1,'L','C');
		$pdf->Cell(62,20,'Fecha Vencto.' ,1,'L','C');
		$pdf->Cell(60,20,'Cuotas en UTM',1,'L','C');
		$pdf->Cell(44,20,'N° Cuotas',1,'L','C');
		$pdf->Cell(62,20,'Fecha Vencto.' ,1,'L','C');
		$pdf->Cell(60,20,'Cuotas en UTM',1,'L','C');
		
		
		$pdf->ln();
		$pdf->SetX(50);
		$pdf->Cell(44,15,1 ,1,'L','C');
		$pdf->Cell(62,15,$result_pp[0]['FECHA_VENCTO'],1,'L','C');
		$pdf->Cell(60,15,str_replace('0,0000','',number_format($result_pp[0]['MONTO_CUOTA'], 4, ',', '.')) ,1,'L','C');
		$pdf->Cell(44,15,6,1,'L','C');
		$pdf->Cell(62,15,$result_pp[5]['FECHA_VENCTO'] ,1,'L','C');
		$pdf->Cell(60,15,str_replace('0,0000','',number_format($result_pp[5]['MONTO_CUOTA'], 4, ',', '.')),1,'L','C');
		$pdf->Cell(44,15,11,1,'L','C');
		$pdf->Cell(62,15,$result_pp[10]['FECHA_VENCTO'] ,1,'L','C');
		$pdf->Cell(60,15,str_replace('0,0000','',number_format($result_pp[10]['MONTO_CUOTA'], 4, ',', '.')),1,'L','C');
		
		$pdf->ln();
		$pdf->SetX(50);
		$pdf->Cell(44,15,2 ,1,'L','C');
		$pdf->Cell(62,15,$result_pp[1]['FECHA_VENCTO'] ,1,'L','C');
		$pdf->Cell(60,15,str_replace('0,0000','',number_format($result_pp[1]['MONTO_CUOTA'], 4, ',', '.')),1,'L','C');
		$pdf->Cell(44,15,7,1,'L','C');
		$pdf->Cell(62,15,$result_pp[6]['FECHA_VENCTO'] ,1,'L','C');
		$pdf->Cell(60,15,str_replace('0,0000','',number_format($result_pp[6]['MONTO_CUOTA'], 4, ',', '.')),1,'L','C');
		$pdf->Cell(44,15,12,1,'L','C');
		$pdf->Cell(62,15,$result_pp[11]['FECHA_VENCTO'] ,1,'L','C');
		$pdf->Cell(60,15,str_replace('0,0000','',number_format($result_pp[11]['MONTO_CUOTA'], 4, ',', '.')),1,'L','C');
		
		
		$pdf->ln();
		$pdf->SetX(50);
		$pdf->Cell(44,15,3 ,1,'L','C');
		$pdf->Cell(62,15,$result_pp[2]['FECHA_VENCTO'] ,1,'L','C');
		$pdf->Cell(60,15,str_replace('0,0000','',number_format($result_pp[2]['MONTO_CUOTA'], 4, ',', '.')),1,'L','C');
		$pdf->Cell(44,15,8,1,'L','C');
		$pdf->Cell(62,15,$result_pp[7]['FECHA_VENCTO'] ,1,'L','C');
		$pdf->Cell(60,15,str_replace('0,0000','',number_format($result_pp[7]['MONTO_CUOTA'], 4, ',', '.')),1,'L','C');
		$pdf->Cell(44,15,13,1,'L','C');
		$pdf->Cell(62,15,$result_pp[12]['FECHA_VENCTO'] ,1,'L','C');
		$pdf->Cell(60,15,str_replace('0,0000','',number_format($result_pp[12]['MONTO_CUOTA'], 4, ',', '.')),1,'L','C');
		
		$pdf->ln();
		$pdf->SetX(50);
		$pdf->Cell(44,15,4 ,1,'L','C');
		$pdf->Cell(62,15,$result_pp[3]['FECHA_VENCTO'] ,1,'L','C');
		$pdf->Cell(60,15,str_replace('0,0000','',number_format($result_pp[3]['MONTO_CUOTA'], 4, ',', '.')),1,'L','C');
		$pdf->Cell(44,15,9,1,'L','C');
		$pdf->Cell(62,15,$result_pp[8]['FECHA_VENCTO'] ,1,'L','C');
		$pdf->Cell(60,15,str_replace('0,0000','',number_format($result_pp[8]['MONTO_CUOTA'], 4, ',', '.')),1,'L','C');
		$pdf->Cell(44,15,14,1,'L','C');
		$pdf->Cell(62,15,$result_pp[13]['FECHA_VENCTO'] ,1,'L','C');
		$pdf->Cell(60,15,str_replace('0,0000','',number_format($result_pp[13]['MONTO_CUOTA'], 4, ',', '.')),1,'L','C');
		
		$pdf->ln();
		$pdf->SetX(50);
		$pdf->Cell(44,15,5 ,1,'L','C');
		$pdf->Cell(62,15,$result_pp[4]['FECHA_VENCTO'] ,1,'L','C');
		$pdf->Cell(60,15,str_replace('0,0000','',number_format($result_pp[4]['MONTO_CUOTA'], 4, ',', '.')),1,'L','C');
		$pdf->Cell(44,15,10,1,'L','C');
		$pdf->Cell(62,15,$result_pp[9]['FECHA_VENCTO'] ,1,'L','C');
		$pdf->Cell(60,15,str_replace('0,0000','',number_format($result_pp[9]['MONTO_CUOTA'], 4, ',', '.')),1,'L','C');
		$pdf->Cell(44,15,15,1,'L','C');
		$pdf->Cell(62,15,$result_pp[14]['FECHA_VENCTO'] ,1,'L','C');
		$pdf->Cell(60,15,str_replace('0,0000','',number_format($result_pp[14]['MONTO_CUOTA'], 4, ',', '.')),1,'L','C');
		
		$pdf->ln();
		$pdf->ln();
		$pdf->SetX(50);
		$parrafo6 = '- El cumplimiento oportuno de cada una de las cuotas de este plan pago, producirá la condonación automática y parcial del '.
					'saldo de los intereses penales al momento del pago de las cuotas pactadas. Para estos efectos, de acuerdo al inciso 3 del '.
					'artículo 4 de la ley N°20.572/12 y artículo 6 del reglamento, la condonación de los intereses penales será equivalente al doble '.
					'del porcentaje que represente cada cuota pagada respecto de la deuda consolidada, sin contar los intereses penales.'."\n".
				    '- La suma adeudada conforme la presente instrumento, la pagará en el equivalente en moneda nacional según el valor de la '.
					'Unidad Tributaria Mensual vigente a la época del pago efectivo, devengándose, además un interés equivalente a un 2% anual '.
					'sobre el capital reajustado.';
		$pdf->MultiCell(490,10, $parrafo6 ,0, 'J',false);
		
		$pdf->Text(300,750,'Pag. 1/2');
		
		$pdf->AddPage();
		$pdf->SetXY(50 , 70);
		$parrafo8 = '- En caso de mora o simple retardo, la deuda estará afecta al interés penal mensual del 1% por cada mes o fracción de mes de '.
					'retardo en el pago y hasta su pago efectivo, de acuerdo a lo dispuesto en el artículo 15 de la ley 19.287/94, modificada por la '.
					'ley N°20.572.'."\n".
					'- El pago deberá efectuarse en el domicilio de la Universidad o en los lugares que ella señale.'."\n".
					'- Para todos los efectos del presente pagaré, tanto el deudor, así como el beneficiario del presente documento, fijan su '.
					'domicilio en la comuna de SANTIAGO, sometiéndose a la jurisdicción de sus tribunales ordinarios.'."\n".
					'- En caso de mora o simple retardo en el pago de una o más cuotas del plan de pago, el beneficiario del presente pagaré tendrá '.
					'la facultad, de exigir el pago anticipado del total de la obligación, como si fuera de plazo vencido.'."\n".
					'- Libero al acreedor de la obligación de protesto, pero si éste optare por la realización de dicha diligencia, podrá hacerla, a su '.
					'libre elección, en forma bancaria, notarial o por el funcionario público que corresponda. En todo caso, en el evento del '.
					'protesto, me obligo a pagar los gastos y cargos de cualquier tipo que se devenguen.'."\n".
					'- El suscriptor de este pagaré, en atención al artículo 4  de la ley 19.628/99, faculta a la '.$result['NOM_UNIVERSIDAD'].', para publicar en el '.
					'Boletín Comercial, las deudas que se respalden con el presente pagaré, que a su fechade vencimiento, no hubiere pagado, sin '.
					'necesidad de protestar el presente documento.'."\n".
					'- La suscripción de este documento no importa novación';
		$pdf->MultiCell(490,10, $parrafo8 ,0, 'J',false);
		
		
		$pdf->ln();
		$pdf->ln();
		$pdf->SetX(50);
		$parrafo9 = 'Firma Suscriptor pagaré folio '.$result['FOLIO_PAGARE'].'............................................'."\n".
					'Cédula de Identidad '.number_format($result['RUT'], 0, '', '.').'-'.$result['DIG_VERIF'];
		$pdf->MultiCell(500,10, $parrafo9 ,0, 'J',false);
		
		$pdf->ln();
		$pdf->ln();
		$pdf->SetX(50);
		$parrafo10 = 'Según lo dispuesto en el artículo 23 N°3 del Decreto Ley 3475, este pagaré se encuentra Exento de impuestos.';
		$pdf->MultiCell(500,10, $parrafo10 ,0, 'J',false);
		
		$pdf->ln();
		$pdf->ln();
		$pdf->SetX(50);
		$pdf->SetFont('Arial', 'B');
		$parrafo11 = 'MANDATO ESPECIAL';
		$pdf->MultiCell(500,10, $parrafo11 ,0, 'J',false);
		
		$pdf->ln();
		$pdf->ln();
		$pdf->SetX(50);
		$pdf->SetFont('Arial');
		$parrafo12 = 'El suscriptor del presente pagaré, en su calidad de beneficiario de la ley N°20.572/12, sobre Reprogramación del Crédito '.
					 'Universitario, y en cumplimiento del artículo 5° de dicha ley, viene en otorgar PODER ESPECIAL, pero tan amplio como en '.
					 'Derecho se requiera, de carácter irrevocable en los términos del artículo 241 del Código de Comercio, al Administrador del '.
					 'Fondo Solidario de Crédito Universitario de la '.$result['NOM_UNIVERSIDAD'].'; a fin de que este, en su nombre y representación, '.
					 'requiera de su empleador para que descuente mensualmente de sus remuneraciones, el monto de las cuotas fijadas en el '.
					 'Pagaré suscrito conjuntamente con este Mandato, para materializar la reprogramación de su deuda, prevista por la Ley N°20.572/12.';
		$pdf->MultiCell(500,10, $parrafo12 ,0, 'J',false);
		
		$pdf->ln();
		$pdf->ln();
		$pdf->ln();
		$pdf->SetX(50);
		$parrafo13 = 'Firma Suscriptor pagaré folio '.$result['FOLIO_PAGARE'].' y Mandante...............................................................................';
		$pdf->MultiCell(500,10, $parrafo13 ,0, 'J',false);
		
		
		$pdf->ln();
		$pdf->SetFont('Arial');
		$pdf->SetX(50);
		$pdf->Cell(100,15,utf8_decode($result['APELLIDO_MATERNO']),0,'L','C');
		$pdf->Cell(100,15,utf8_decode($result['APELLIDO_PATERNO']) ,0,'L','C');
		$pdf->Cell(100,15,utf8_decode($result['NOMBRE']),0,'L','C');
		
		$pdf->ln();
		$pdf->SetX(50);
		$pdf->Cell(100,15,'Apellido materno' ,0,'L','C');
		$pdf->Cell(100,15,'Apellido paterno' ,0,'L','C');
		$pdf->Cell(100,15,'Nombres',0,'L','C');
		
		
		$pdf->ln();
		$pdf->ln();
		$pdf->ln();
		$pdf->SetX(50);
		$parrafo14 = 'Cédula de Identidad '.number_format($result['RUT'], 0, '', '.').'-'.$result['DIG_VERIF']."\n\n".
					 'Teléfonos..................................................'."\n\n".
					 'Correo.....................................................'."\n\n".
				     'Ciudad.....................................................';
		$pdf->MultiCell(500,10, $parrafo14 ,0, 'J',false);
		
		$pdf->ln();
		$pdf->ln();
		$pdf->ln();
		$pdf->ln();
		$pdf->ln();
		$pdf->ln();
		$pdf->ln();
		$pdf->ln();
		$pdf->SetX(300);
		$parrafo15 = '                                FIRMA NOTARIO'."\n".
					 'Autorización Notarial de la firma del suscriptor y Mandante';
		$pdf->MultiCell(500,10, $parrafo15 ,0, 'J',false);
		$pdf->Text(300,750,'Pag. 2/2');
		
		
	}
	function modifica_pdf(&$pdf) {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result = $db->build_results($this->sql);
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql= sql_pagare::get_sql('PRINT_PARAM');
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