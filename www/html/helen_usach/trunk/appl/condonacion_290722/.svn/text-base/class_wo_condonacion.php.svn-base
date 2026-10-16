<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_condonacion extends w_output {
    function __construct() {
        
        $sql = sql_condonacion::get_sql('OUTPUT');
        
        parent::__construct('condonacion', $sql, $_REQUEST['cod_item_menu']);
        
        //formato
        $this->dw->add_control(new static_num('RUT'));
        
        // headers
        
        $COD_CONDONACION = new header_num('COD_CONDONACION', 'COD_CONDONACION', 'N° Condonación');
        $NRO_DECRETO = new header_num('NRO_DECRETO', 'NRO_DECRETO', 'N° Decreto');
        $sql_tipo_condonacion = sql_condonacion::get_sql('DD_TIPO_CONDONACION');
        $NOM_TIPO_CONDONACION = new header_drop_down('NOM_TIPO_CONDONACION', 'TC.COD_TIPO_CONDONACION', 'Tipo Condonación', $sql_tipo_condonacion);
        $RUT = new header_rut('RUT', 'D', 'Rut');
        $NOMBRE = new header_text('NOMBRE', 'NOMBRE', 'Nombre');
        $APELLIDO_PATERNO = new header_text('APELLIDO_PATERNO', 'APELLIDO_PATERNO', 'Paterno');
        $APELLIDO_MATERNO = new header_text('APELLIDO_MATERNO', 'APELLIDO_MATERNO', 'Materno');
        $sql_estado_condonacion = sql_condonacion::get_sql('DD_ESTADO_CONDONACION');
        $NOM_ESTADO_CONDONACION = new header_drop_down('NOM_ESTADO_CONDONACION', 'EC.COD_ESTADO_CONDONACION', 'Estado', $sql_estado_condonacion);
        
        $this->add_header($COD_CONDONACION);
        $this->add_header($NRO_DECRETO);
        $this->add_header($NOM_TIPO_CONDONACION);
        $this->add_header($RUT);
        $this->add_header($NOMBRE);
        $this->add_header($APELLIDO_PATERNO);
        $this->add_header($APELLIDO_MATERNO);
        $this->add_header($NOM_ESTADO_CONDONACION);
    }
}
?>