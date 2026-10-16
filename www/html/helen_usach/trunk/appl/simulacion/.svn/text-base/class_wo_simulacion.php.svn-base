<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../appl.ini");

class wo_simulacion extends w_output {
    
    function __construct() {
        $sql = sql_simulacion::get_sql('OUTPUT');
        
        parent::__construct('simulacion', $sql, $_REQUEST['cod_item_menu']);
        
        // formato
        $this->dw->add_control(new static_num('RUT'));
        
        //header
        $COD_SIMULACION = new header_num('COD_SIMULACION', 'COD_INGRESO', 'N° Simulación');
        $FECHA_INGRESO = new header_date('FECHA_INGRESO', 'FECHA_INGRESO', 'Fecha');
        $RUT = new header_rut('RUT', 'DE', 'Rut');
        $NOMBRE = new header_text('NOMBRE', 'NOMBRE', 'Nombre');
        $APELLIDO_PATERNO = new header_text('APELLIDO_PATERNO', 'APELLIDO_PATERNO', 'Paterno');
        $APELLIDO_MATERNO = new header_text('APELLIDO_MATERNO', 'APELLIDO_MATERNO', 'Materno');
        
        $this->add_header($COD_SIMULACION);
        $this->add_header($FECHA_INGRESO);
        $this->add_header($RUT);
        $this->add_header($NOMBRE);
        $this->add_header($APELLIDO_PATERNO);
        $this->add_header($APELLIDO_MATERNO);
    }
}
?>