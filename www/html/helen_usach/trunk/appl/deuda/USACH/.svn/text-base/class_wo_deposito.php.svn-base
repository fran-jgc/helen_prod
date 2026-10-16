<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_deposito extends w_output {
	function __construct() {

		$sql = sql_deposito::get_sql('OUTPUT');

		parent::__construct('deposito', $sql, $_REQUEST['cod_item_menu']);
		
		$MONTO_DEPOSITADO_S = new static_num('MONTO_DEPOSITADO');
		$this->dw->add_control($MONTO_DEPOSITADO_S);
		// headers
		$NRO_DEPOSITO = new header_num('NRO_DEPOSITO', 'NRO_DEPOSITO', 'N° Depósito');
		$FECHA_DEPOSITO = new header_date('FECHA_DEPOSITO', 'FECHA_DEPOSITO', 'Fecha Depósito');
		$sql_banco = sql_deposito::get_sql('DD_COD_BANCO');
		$NOM_BANCO = new header_drop_down('NOM_BANCO', 'B.COD_BANCO', 'Banco', $sql_banco);
		$MONTO_DEPOSITADO = new header_num('MONTO_DEPOSITADO', 'f_doc_pago_get_monto_depos(COD_DEPOSITO)', 'Monto $');
		$CANTIDAD_DOCUMENTOS = new header_num('CANTIDAD_DOCUMENTOS', 'f_doc_pago_get_cant_doc(COD_DEPOSITO)', 'Cantidad Documentos');
		$sql_familia_deuda = sql_deposito::get_sql('DD_COD_FAMILIA_DEUDA');
		$NOM_FAMILIA_DEUDA = new header_drop_down('NOM_FAMILIA_DEUDA', 'FD.COD_FAMILIA_DEUDA', 'Familia Deuda', $sql_familia_deuda);
		$sql_estado_deposito = sql_deposito::get_sql('DD_COD_ESTADO_DEPOSITO');
		$NOM_ESTADO_DEPOSITO = new header_drop_down('NOM_ESTADO_DEPOSITO', 'ED.COD_ESTADO_DEPOSITO', 'Estado', $sql_estado_deposito); 
		
		$this->add_header($NRO_DEPOSITO);
		$this->add_header($FECHA_DEPOSITO);
		$this->add_header($NOM_BANCO);
		$this->add_header($MONTO_DEPOSITADO); 
		$this->add_header($CANTIDAD_DOCUMENTOS); 
		$this->add_header($NOM_FAMILIA_DEUDA);
		$this->add_header($NOM_ESTADO_DEPOSITO);
	}
}
?>