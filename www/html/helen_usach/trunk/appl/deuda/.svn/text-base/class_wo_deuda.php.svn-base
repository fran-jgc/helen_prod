<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
class wo_deuda extends w_output {
    const K_EN_COBRANZA_NORMAL = 2;
    
    function __construct() {
        $this->b_add_visible  = false;
        $sql = sql_deuda::get_sql('OUTPUT');
        
        parent::__construct('deuda', $sql, $_REQUEST['cod_item_menu']);
        
        $RUT_S = new static_num('RUT');
        $this->dw->add_control($RUT_S);
        
        // headers
        $COD_DEUDA = new header_num('COD_DEUDA', 'COD_DEUDA', 'N° Deuda');
        $RUT = new header_rut('RUT', 'DO', 'Rut');
        $NOMBRE = new header_text('NOMBRE', 'NOMBRE', 'Nombre');
        $APELLIDO_PATERNO = new header_text('APELLIDO_PATERNO', 'APELLIDO_PATERNO', 'Paterno');
        $APELLIDO_MATERNO = new header_text('APELLIDO_MATERNO', 'APELLIDO_MATERNO', 'Materno');
        
        $sql_tipo_deuda = sql_deuda::get_sql('DD_COD_TIPO_DEUDA');
        $NOM_TIPO_DEUDA = new header_drop_down_string('NOM_TIPO_DEUDA', 'TD.COD_TIPO_DEUDA', 'Tipo Deuda', $sql_tipo_deuda);
        
        $sql_estado_deuda = sql_deuda::get_sql('DD_COD_ESTADO_DEUDA');
        $NOM_ESTADO_DEUDA = new header_drop_down('NOM_ESTADO_DEUDA', 'ED.COD_ESTADO_DEUDA', 'Estado', $sql_estado_deuda);
        
        $sql = sql_deuda::get_sql('DD_COD_UNIVERSIDAD');
        $NOM_UNIVERSIDAD = new header_drop_down('NOM_UNIVERSIDAD', 'DA.COD_UNIVERSIDAD', 'Universidad', $sql);
        $NOM_CARTERA = new header_text('NOM_CARTERA', 'CA.NOM_CARTERA', 'Cartera');
        
        $this->add_header($COD_DEUDA);
        $this->add_header($RUT);
        $this->add_header($NOMBRE);
        $this->add_header($APELLIDO_PATERNO);
        $this->add_header($APELLIDO_MATERNO);
        $this->add_header($NOM_TIPO_DEUDA);
        $this->add_header($NOM_ESTADO_DEUDA);
        $this->add_header($NOM_UNIVERSIDAD);
        $this->add_header($NOM_CARTERA);
        
        $NOM_ESTADO_DEUDA->valor_filtro = self::K_EN_COBRANZA_NORMAL;
        $this->make_filtros();
    }
}
?>