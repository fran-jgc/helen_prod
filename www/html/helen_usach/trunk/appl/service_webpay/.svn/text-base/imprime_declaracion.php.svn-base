<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../declaracion/USACH/class_print_declaracion.php");
require_once(dirname(__FILE__)."/../common_appl/class_MySQLRD_helen.php");

class print_declaracion_web extends print_declaracion_usach {
	const K_PARAM_NOM_UNIVERSIDAD		= 5;
	function print_declaracion_web() {
		// borra todos los archivos temporales que existan de pdf creados
		$this->clean_files(".", "pdf");
		
		$cod_declaracion = $_REQUEST['cod_declaracion'];
		$sql= "SELECT 	DR.RUT
										,DR.DIG_VERIF
										,DR.NOMBRE
										,DR.APELLIDO_PATERNO
										,DR.APELLIDO_MATERNO
										,D.COD_DECLARACION
										,D.FECHA_REGISTRO
										,U1.NOM_USUARIO
										,D.COD_DEUDOR
										,VD.NOM_VIA_DECLARACION
										,TO_CHAR(D.FECHA_CONFIRMA, 'dd/mm/yyyy') FECHA_CONFIRMA 
										,D.COD_ESTADO_DECLARACION
										,ED.NOM_ESTADO_DECLARACION
										,U2.NOM_USUARIO NOM_USUARIO_CONFIRMA
										,TO_CHAR(D.FECHA_DECLARACION, 'dd/mm/yyyy') FECHA_DECLARACION
										,D.ANO
										,D.ANO + 1 ANO_DECLARACION
										,D.CON_HIJO
										,D.COD_AFP
										,A.NOM_AFP
										,EC.NOM_ESTADO_CIVIL
										,D.COD_ESTADO_CIVIL
										,D.RUT_CONYUGE
										,D.DIG_VERIF_CONYUGE
										,D.NOM_CONYUGE
										,D.CONYUGE_ES_DEUDOR
										,D.CONYUGE_DECLARA
										,D.ENTREGA_F22
										,f_get_parametro(".self::K_PARAM_NOM_UNIVERSIDAD.") NOM_UNIVERSIDAD
										,to_char(FECHA_DECLARACION,'DD/MM/YYYY') FECHA_DECLARACION
										,'S' ES_WEB_DEUDOR
                    					,ENTREGA_F22_CONYUGE 
                    					,AC.NOM_AFP NOM_AFP_CONYUGE
								FROM 	DECLARACION D LEFT OUTER JOIN USUARIO U1 ON D.COD_USUARIO = U1.COD_USUARIO
											LEFT OUTER JOIN USUARIO U2 ON D.COD_USUARIO_CONFIRMA = U2.COD_USUARIO 
                      							LEFT OUTER JOIN AFP AC ON D.COD_AFP_CONYUGE = AC.COD_AFP,
										DEUDOR DR, VIA_DECLARACION VD, ESTADO_DECLARACION ED, AFP A, ESTADO_CIVIL EC
								WHERE 	D.COD_DECLARACION = $cod_declaracion
								AND 	D.COD_DEUDOR = DR.COD_DEUDOR
								AND 	D.COD_VIA_DECLARACION = VD.COD_VIA_DECLARACION
								AND 	D.COD_ESTADO_DECLARACION = ED.COD_ESTADO_DECLARACION
								AND 	D.COD_AFP = A.COD_AFP
								AND 	D.COD_ESTADO_CIVIL = EC.COD_ESTADO_CIVIL";//sql_declaracion::get_sql('PRINT_RECORD', $cod_declaracion);
						
		// reporte
		$labels = array();
		$xml = dirname(__FILE__)."/../declaracion/declaracion.xml";

		// VMC, 18-2-2011
		// No se llama al ancestro por enredos de autoload
		$this->sql = $sql;
		$this->xml = $xml;	// debe venir como la posicion relativa desde
		$this->labels = $labels;
		$this->titulo = "Declaracion_$cod_declaracion.pdf";
		$this->con_logo = 0;		
		$this->orientation = 'P';
		$this->unit = 'pt';
		$this->format = 'letter';
		
		$this->make_reporte();
	}
	function make_reporte() {
		$p = new ReportParser();
		$p->parseRP($this->xml);
		$rdata = new MySQLRD_helen($this->sql, '', '');

		$pdf = PDF::makePDF(array($p), array($this->labels), array($rdata), $this->con_logo,$this->orientation,$this->unit,$this->format);		
		$pdf->SetTitle($this->titulo);
		$this->modifica_pdf($pdf);

		// sacado de FAQ de FPDF para evitar problemas con IE
		//Determinar un nombre temporal de fichero en el directorio actual
		$this->clean_files("../../tmp", "pdf");
		$file = basename(tempnam("../../tmp", 'tmp'));
		rename("../../tmp/".$file, "../../tmp/".$file.'.pdf');
		$file .= '.pdf';
		//Guardar el PDF en un fichero
		$pdf->Output("../../tmp/".$file, 'F');
		//Redirección
		header('Location: '."../../tmp/".$file);
	}
}

new print_declaracion_web();
?>