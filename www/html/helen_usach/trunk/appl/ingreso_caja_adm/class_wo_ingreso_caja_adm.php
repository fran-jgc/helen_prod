<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_ingreso_caja_adm extends w_output {
	function __construct() {
		$sql = sql_ingreso_caja_adm::get_sql('OUTPUT');

		parent::__construct('ingreso_caja_adm', $sql, $_REQUEST['cod_item_menu']);
		
		// formato
		$this->dw->add_control(new static_num('RUT'));
		$this->dw->add_control(new static_num('MONTO_PESOS'));
		$this->dw->add_control(new static_num('MONTO_MONEDA',4));
		
		//header
		$COD_INGRESO_CAJA_ADM = new header_num('COD_INGRESO_CAJA_ADM', 'COD_INGRESO_CAJA_ADM', 'N° Ingreso');
		$FECHA_INGRESO_CAJA_ADM = new header_date('FECHA_INGRESO_CAJA_ADM', 'FECHA_INGRESO_CAJA_ADM', 'Fecha');
		$RUT = new header_rut('RUT', 'DEUDOR', 'Rut');
		$COD_DEUDA = new header_num('COD_DEUDA', 'DEUDA.COD_DEUDA', 'N° Deuda');
		$sql_tipo_deuda = sql_ingreso_caja_adm::get_sql('DD_TIPO_DEUDA');
		$NOM_TIPO_DEUDA = new header_drop_down_string('NOM_TIPO_DEUDA', 'TD.COD_TIPO_DEUDA', 'Tipo Deuda', $sql_tipo_deuda);
		$ANO_COBRO = new header_num('ANO_COBRO', 'ANO_COBRO', 'Año');
		$COD_BOLETA = new header_num('COD_BOLETA', 'B.COD_BOLETA', 'N° Boleta');
		$NRO_SUB_BOLETA = new header_num('NRO_SUB_BOLETA', 'NRO_SUB_BOLETA', 'N° Sub-Boleta');
		$MONTO_MONEDA = new header_num('MONTO_MONEDA', 'MONTO_MONEDA', 'Monto UTM');
		$MONTO_PESOS = new header_num('MONTO_PESOS', 'MONTO_PESOS', 'Monto $');
		
		$this->add_header($COD_INGRESO_CAJA_ADM);
		$this->add_header($FECHA_INGRESO_CAJA_ADM);
		$this->add_header($RUT);
		$this->add_header($COD_DEUDA);
		$this->add_header($NOM_TIPO_DEUDA);
		$this->add_header($ANO_COBRO);
		$this->add_header($COD_BOLETA);
		$this->add_header($NRO_SUB_BOLETA);
		$this->add_header($MONTO_MONEDA);
		$this->add_header($MONTO_PESOS);
	}
}
?>