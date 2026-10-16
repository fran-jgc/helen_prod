<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_convenio extends w_output {
    function __construct() {
        $this->b_add_visible  = true;
        
        ///////////////// CLASS_SQL = OUTPUT///////////////
        $sql = sql_convenio::get_sql('OUTPUT');
        
        parent::__construct('convenio', $sql, $_REQUEST['cod_item_menu']);
        
        // formato
        $this->dw->add_control(new static_num('RUT'));
        
        //HEADERS
        $COD_CONVENIO = new header_num('COD_CONVENIO', 'COD_CONVENIO', 'N° Convenio');
        $FECHA_CONVENIO = new header_date('FECHA_CONVENIO', 'FECHA_CONVENIO', 'Fecha');
        $RUT = new header_rut('RUT', 'DR');
        $NOMBRE = new header_text('NOMBRE', 'NOMBRE', 'Nombre');
        $APELLIDO_PATERNO = new header_text('APELLIDO_PATERNO', 'APELLIDO_PATERNO', 'Paterno');
        $APELLIDO_MATERNO = new header_text('APELLIDO_MATERNO', 'APELLIDO_MATERNO', 'Materno');
        $COD_DEUDA = new header_num('COD_DEUDA', 'COD_DEUDA', 'N° Deuda', 0);
        $NOM_USUARIO = new header_text('NOM_USUARIO', 'NOM_USUARIO', 'Usuario');
        $sql = "select COD_TIPO_DEUDA, NOM_TIPO_DEUDA from TIPO_DEUDA";
        $COD_TIPO_DEUDA = new header_drop_down('COD_TIPO_DEUDA', 'DA.COD_TIPO_DEUDA', 'Tipo Deuda', $sql);
        $sql_estado = sql_convenio::get_sql("OUTPUT_DD_COD_ESTADO_CONVENIO");
        $NOM_ESTADO_CONVENIO = new header_drop_down('NOM_ESTADO_CONVENIO', 'EC.COD_ESTADO_CONVENIO', 'Estado', $sql_estado);
        $sql_autoriza = sql_convenio::get_sql('DD_AUTORIZADO');
        $AUTORIZADO = new header_drop_down_string('AUTORIZADO', 'C.AUTORIZADO', 'Autorizado', $sql_autoriza);
        
        $this->add_header($COD_CONVENIO);
        $this->add_header($FECHA_CONVENIO);
        $this->add_header($RUT);
        $this->add_header($NOMBRE);
        $this->add_header($APELLIDO_PATERNO);
        $this->add_header($APELLIDO_MATERNO);
        $this->add_header($COD_DEUDA);
        $this->add_header($NOM_USUARIO);
        $this->add_header($COD_TIPO_DEUDA);
        $this->add_header($NOM_ESTADO_CONVENIO);
        $this->add_header($AUTORIZADO);
    }
}
?>
