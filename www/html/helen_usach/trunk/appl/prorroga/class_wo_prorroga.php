<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_prorroga extends w_output {

    const K_SOLICITADA = 1;
    function __construct() {
        $this->b_add_visible  = true;
        
        ///////////////// CLASS_SQL = OUTPUT///////////////
        $sql = sql_prorroga::get_sql('OUTPUT');
        
        parent::__construct('prorroga', $sql, $_REQUEST['cod_item_menu']);
        //formato
        $this->dw->add_control(new static_num('RUT'));
        // headers
        $COD_PRORROGA = new header_num('COD_PRORROGA', 'COD_PRORROGA', 'N°');
        $FECHA_PRORROGA = new header_date('FECHA_PRORROGA', 'FECHA_PRORROGA', 'Fecha');
        $FECHA_INICIO_COBRO = new header_date('FECHA_INICIO_COBRO', 'P.FECHA_INICIO_COBRO', 'Inicio Cobro');
        $FECHA_SOLICITUD_TRAMITE = new header_date('FECHA_SOLICITUD_TRAMITE', 'FECHA_SOLICITUD_TRAMITE', 'Fecha Tramite');
        $RUT = new header_rut('RUT', 'DR', 'Rut');
        $NOMBRE = new header_text('NOMBRE', 'NOMBRE', 'Nombre');
        $APELLIDO_PATERNO = new header_text('APELLIDO_PATERNO', 'APELLIDO_PATERNO', 'Paterno');
        $APELLIDO_MATERNO = new header_text('APELLIDO_MATERNO', 'APELLIDO_MATERNO', 'Materno');
        $MONTO_DEUDA = new header_num('MONTO_DEUDA', 'dbo.f_monto_deuda_prorroga(P.COD_DEUDA, P.COD_COMPROBANTE)', 'Monto Deuda');


        
        
        $this->add_header($COD_PRORROGA);
        $this->add_header($FECHA_PRORROGA);
        $this->add_header($FECHA_INICIO_COBRO);
        $this->add_header($FECHA_SOLICITUD_TRAMITE);
        
        $this->add_header($RUT);
        $this->add_header($NOMBRE);
        $this->add_header($APELLIDO_PATERNO);
        $this->add_header($APELLIDO_MATERNO);
        $this->add_header($MONTO_DEUDA);
        
        ///////////////// CLASS_SQL = OUTPUT_DD_COD_ESTADO ///////////////
        $sql_estado = sql_prorroga::get_sql("OUTPUT_DD_COD_ESTADO");
        $NOM_ESTADO_PRORROGA = new header_drop_down('NOM_ESTADO_PRORROGA', 'EP.COD_ESTADO_PRORROGA', 'Estado', $sql_estado);
        $this->add_header($NOM_ESTADO_PRORROGA);

        $NOM_ESTADO_PRORROGA->valor_filtro = self::K_SOLICITADA;

        $this->make_filtros();
    }
}
?>