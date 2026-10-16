<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_cambio_documento extends w_output {
    function __construct() {		
		$sql = sql_cambio_documento::get_sql('OUTPUT'); 

		parent::__construct('cambio_documento', $sql, $_REQUEST['cod_item_menu']);
		
		//formato numeros
		$this->dw->add_control(new static_num('RUT'));
		
		// headers
		$COD_CAMBIO_DOCUMENTO = new header_num('COD_CAMBIO_DOCUMENTO', 'COD_CAMBIO_DOCUMENTO', 'N° Cambio');
		$FECHA_CAMBIO_DOCUMENTO = new header_date('FECHA_CAMBIO_DOCUMENTO', 'FECHA_CAMBIO_DOCUMENTO', 'Fecha');
		$RUT = new header_rut('RUT', 'D', 'Rut');
		$NOMBRE = new header_text('NOMBRE', 'NOMBRE', 'Nombre');
		$APELLIDO_PATERNO = new header_text('APELLIDO_PATERNO', 'APELLIDO_PATERNO', 'Paterno');
		$APELLIDO_MATERNO = new header_text('APELLIDO_MATERNO', 'APELLIDO_MATERNO', 'Materno');
		
		$this->add_header($COD_CAMBIO_DOCUMENTO);
		$this->add_header($FECHA_CAMBIO_DOCUMENTO);
		$this->add_header($RUT);
		$this->add_header($NOMBRE);
		$this->add_header($APELLIDO_PATERNO);
		$this->add_header($APELLIDO_MATERNO);
		
	}
}
?>