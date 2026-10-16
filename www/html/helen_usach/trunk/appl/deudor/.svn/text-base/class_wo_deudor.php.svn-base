<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_deudor extends w_output {
    function __construct() {
        $this->b_add_visible  = false;
        
        $sql = sql_deudor::get_sql('OUTPUT');
        
        parent::__construct('deudor', $sql, $_REQUEST['cod_item_menu']);
        
        //formato numeros
        $rut = new static_num('RUT');
        $this->dw->add_control($rut);
        
        // headers
        $cod_deudor = new header_num('COD_DEUDOR', 'COD_DEUDOR', 'Código');
        $rut = new header_rut('RUT', 'DEUDOR', 'Rut');
        $nombre = new header_text('NOMBRE', 'NOMBRE', 'Nombre');
        $APELLIDO_PATERNO = new header_text('APELLIDO_PATERNO', 'APELLIDO_PATERNO', 'Paterno');
        $APELLIDO_MATERNO = new header_text('APELLIDO_MATERNO', 'APELLIDO_MATERNO', 'Materno');
        
        $this->add_header($cod_deudor);
        $this->add_header($rut);
        $this->add_header($nombre);
        $this->add_header($APELLIDO_PATERNO);
        $this->add_header($APELLIDO_MATERNO);
    }
}
?>