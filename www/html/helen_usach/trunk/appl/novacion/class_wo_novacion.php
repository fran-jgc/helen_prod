<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_novacion extends w_output {
    function __construct() {
        
        $sql = sql_novacion::get_sql('OUTPUT');
        
        parent::__construct('novacion', $sql, $_REQUEST['cod_item_menu']);
        
        //formato
        $this->dw->add_control(new static_num('RUT'));
        
        // headers
        $COD_NOVACION = new header_num('COD_NOVACION', 'COD_NOVACION', 'N° Novación');
        $FECHA_NOVACION = new header_date('FECHA_NOVACION', 'FECHA_NOVACION', 'Fecha');
        $RUT = new header_rut('RUT', 'DEUDOR', 'Rut');
        $NOMBRE = new header_text('NOMBRE', 'NOMBRE', 'Nombre');
        $APELLIDO_PATERNO = new header_text('APELLIDO_PATERNO', 'APELLIDO_PATERNO', 'Paterno');
        $APELLIDO_MATERNO = new header_text('APELLIDO_MATERNO', 'APELLIDO_MATERNO', 'Materno');
        $COD_DEUDA_ANTIGUA = new header_num('COD_DEUDA_ANTIGUA', 'COD_DEUDA_ANTIGUA', 'N° Deuda Original');
        $COD_DEUDA_NUEVA = new header_num('COD_DEUDA_NUEVA', 'COD_DEUDA_NUEVA', 'N° Deuda Nueva');
        
        $this->add_header($COD_NOVACION);
        $this->add_header($FECHA_NOVACION);
        $this->add_header($RUT);
        $this->add_header($NOMBRE);
        $this->add_header($APELLIDO_PATERNO);
        $this->add_header($APELLIDO_MATERNO);
        $this->add_header($COD_DEUDA_ANTIGUA);
        $this->add_header($COD_DEUDA_NUEVA);
        
        $sql_estado_novacion = sql_novacion::get_sql('DD_ESTADO_NOVACION');
        $NOM_ESTADO_NOVACION = new header_drop_down('NOM_ESTADO_NOVACION', 'EN.COD_ESTADO_NOVACION', 'Estado', $sql_estado_novacion);
        $this->add_header($NOM_ESTADO_NOVACION);
        
    }
}
?>