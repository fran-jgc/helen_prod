<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_documento extends w_output {
    
    function __construct() {
        
        $this->b_add_visible  = false;
        
        $sql = sql_documento::get_sql('OUTPUT');
        
        parent::__construct('documento', $sql, $_REQUEST['cod_item_menu']);
        
        //formato
        $this->dw->add_control(new static_num('RUT'));
        $this->dw->add_control(new static_num('MONTO'));
        $this->dw->add_control(new edit_text_upper('COD_DOCUMENTO_PAGO', 10, 10, 'hidden'));
        
        // headers
        $field_bd = 'f_doc_nro_ingreso(DP.COD_DOCUMENTO_PAGO)';
        
        
        $NRO_INGRESO	= new header_text('NRO_INGRESO', $field_bd, 'N° Ingreso');
        $NRO_DOCUMENTO_PAGO = new header_num('NRO_DOCUMENTO_PAGO', 'NRO_DOCUMENTO_PAGO', 'N° Doc');
        $FECHA_REGISTRO = new header_date('FECHA_REGISTRO', 'FECHA_REGISTRO', 'Fecha Recep.');
        $FECHA_DOCUMENTO_PAGO = new header_date('FECHA_DOCUMENTO_PAGO', 'FECHA_DOCUMENTO_PAGO', 'Fecha Doc.');
        $RUT = new header_rut('RUT', 'D', 'Rut');
        $NOMBRE = new header_text('NOMBRE', 'NOMBRE', 'Nombre');
        $sql_banco = sql_documento::get_sql('DD_COD_BANCO');
        $NOM_BANCO = new header_drop_down('NOM_BANCO', 'B.COD_BANCO', 'Banco', $sql_banco);
        $MONTO = new header_num('MONTO', 'MONTO', 'Monto');
        $sql_tipo_doc_pago = sql_documento::get_sql('DD_COD_TIPO_DOCUMENTO_PAGO');
        $NOM_TIPO_DOCUMENTO_PAGO = new header_drop_down('NOM_TIPO_DOCUMENTO_PAGO', 'TDP.COD_TIPO_DOCUMENTO_PAGO', 'Tipo Doc.', $sql_tipo_doc_pago);
        $sql_estado_doc_pago = sql_documento::get_sql('DD_COD_ESTADO_DOCUMENTO_PAGO');
        $NOM_ESTADO_DOCUMENTO_PAGO = new header_drop_down('NOM_ESTADO_DOCUMENTO_PAGO', 'EDP.COD_ESTADO_DOCUMENTO_PAGO', 'Estado Doc.', $sql_estado_doc_pago );
        $FECHA_DEPOSITO = new header_date('FECHA_DEPOSITO', 'FECHA_DEPOSITO', 'Fecha Depósito');
        
        $this->add_header($NRO_INGRESO);
        $this->add_header($NRO_DOCUMENTO_PAGO);
        $this->add_header($FECHA_REGISTRO);
        $this->add_header($FECHA_DOCUMENTO_PAGO);
        $this->add_header($RUT);
        $this->add_header($NOMBRE);	// Esta columna no esta en HTML es usada solo para export
        $this->add_header($NOM_BANCO);
        $this->add_header($MONTO);
        $this->add_header($NOM_TIPO_DOCUMENTO_PAGO);
        $this->add_header($NOM_ESTADO_DOCUMENTO_PAGO);
        $this->add_header($FECHA_DEPOSITO);
    }
}
?>