<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../appl.ini");

class wo_comprobante_base extends w_output {
    function __construct() {
        
        $sql = sql_comprobante::get_sql('OUTPUT');
        
        parent::__construct('comprobante', $sql, $_REQUEST['cod_item_menu']);
        
        //formato numeros
        $this->dw->add_control(new static_num('MONTO'));
        
        // headers
        $COD_COMPROBANTE = new header_num('COD_COMPROBANTE', 'COD_COMPROBANTE', 'Cod. Comprob');
        $NRO_COMPROBANTE = new header_num('NRO_COMPROBANTE', 'NRO_COMPROBANTE', 'N° Comprob.');
        $FECHA_COMPROBANTE = new header_date('FECHA_COMPROBANTE', 'FECHA_COMPROBANTE', 'Fecha');
        $sql_estado_comp = sql_comprobante::get_sql('DD_COD_ESTADO_COMPROBANTE');
        $NOM_ESTADO_COMPROBANTE = new header_drop_down('NOM_ESTADO_COMPROBANTE', 'EC.COD_ESTADO_COMPROBANTE', 'Estado', $sql_estado_comp);
        $sql_familia_deuda = sql_comprobante::get_sql('DD_COD_FAMILIA_DEUDA_1');
        $NOM_FAMILIA_DEUDA = new header_drop_down('NOM_FAMILIA_DEUDA', 'FM.COD_FAMILIA_DEUDA', 'Familia Deuda', $sql_familia_deuda);
        $NOM_COMPROBANTE = new header_text('NOM_COMPROBANTE', 'NOM_COMPROBANTE', 'Glosa');
        $sql_tipo = sql_comprobante::get_sql('DD_COD_TIPO_COMPROBANTE_1');
        $NOM_TIPO_COMPROBANTE = new header_drop_down('NOM_TIPO_COMPROBANTE', 'TC.COD_TIPO_COMPROBANTE', 'Tipo', $sql_tipo);
        $sql_usuario = sql_comprobante::get_sql('DD_COD_USUARIO');
        $NOM_USUARIO = new header_drop_down('NOM_USUARIO', 'U.COD_USUARIO', 'Usuario', $sql_usuario);
        $MONTO = new header_num('MONTO', 'f_comp_get_monto_debe(C.COD_COMPROBANTE)', 'Monto');
        
        $this->add_header($COD_COMPROBANTE);
        $this->add_header($NRO_COMPROBANTE);
        $this->add_header($FECHA_COMPROBANTE);
        $this->add_header($NOM_ESTADO_COMPROBANTE);
        $this->add_header($NOM_FAMILIA_DEUDA);
        $this->add_header($NOM_COMPROBANTE);
        $this->add_header($NOM_TIPO_COMPROBANTE);
        $this->add_header($NOM_USUARIO);
        $this->add_header($MONTO);
        
        $MONTO->field_bd_order = 'MONTO';
    }
}

// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wo_comprobante.php";
if (file_exists($file_name)) {
    require_once($file_name);
}
else {
    class wo_comprobante extends wo_comprobante_base {
        function __construct() {
            parent::__construct();
        }
    }
}
?>