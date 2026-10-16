<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../boleta/USACH/class_print_boleta.php");
require_once(dirname(__FILE__)."/../common_appl/class_MySQLRD_helen.php");

class print_boleta_web extends print_boleta {
	function print_boleta_web() {
		// borra todos los archivos temporales que existan de pdf creados
		$this->clean_files(".", "pdf");
		
		$cod_boleta = $_REQUEST['cod_boleta'];
		$sql = "SELECT $cod_boleta COD_BOLETA FROM DUAL";
				
		// reporte
		$labels = array();
		$xml = dirname(__FILE__)."/../boleta/boleta.xml";

		// VMC, 18-2-2011
		// No se llama al ancestro por enredos de autoload
		$this->sql = $sql;
		$this->xml = $xml;	// debe venir como la posicion relativa desde
		$this->labels = $labels;
		$this->titulo = "Boleta_$cod_boleta.pdf";
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

new print_boleta_web();
?>