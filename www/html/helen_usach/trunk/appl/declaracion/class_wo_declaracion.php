<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
ini_set('memory_limit', '500M');
class wo_declaracion extends w_output {
    const K_SOLICITADA = 1;
    
    function __construct() {
        $this->b_add_visible = (session::get("COD_PERFIL") == 1);
        
        $sql = sql_declaracion::get_sql('OUTPUT');
        
        parent::__construct('declaracion', $sql, $_REQUEST['cod_item_menu']);
        
        $this->dw->add_control(new static_num('RUT'));
        $this->dw->add_control(new static_num('INGRESO_PROM_PESOS'));
        
        // headers
        $COD_DECLARACION = new header_num('COD_DECLARACION', 'COD_DECLARACION', 'Código');
        
        $sql = sql_declaracion::get_sql('DD_COD_ESTADO_DECLARACION_1');
        $NOM_ESTADO_DECLARACION = new header_drop_down_string('NOM_ESTADO_DECLARACION', 'ED.COD_ESTADO_DECLARACION', 'Estado', $sql);
        $ANO = new header_num('ANO', 'ANO', 'Año DJ');
        $FECHA_DECLARACION = new header_date('FECHA_DECLARACION', 'FECHA_DECLARACION', 'Fecha DJ');
        $RUT = new header_rut('RUT', 'DEU', 'Rut');
        $NOMBRE = new header_text('NOMBRE', 'DEU.NOMBRE', 'Deudor');
        $APELLIDO_PATERNO = new header_text('APELLIDO_PATERNO', 'DEU.APELLIDO_PATERNO', 'Paterno');
        $APELLIDO_MATERNO = new header_text('APELLIDO_MATERNO', 'DEU.APELLIDO_MATERNO', 'Materno');
        $NOMBRE = new header_text('NOMBRE', 'NOMBRE', 'Deudor');
        
        $sql_via_declaracion  = sql_declaracion::get_sql('DD_COD_VIA_DECLARACION_1');
        $NOM_VIA_DECLARACION = new header_drop_down_string('NOM_VIA_DECLARACION', 'VD.COD_VIA_DECLARACION', 'Via DJ', $sql_via_declaracion);
        $INGRESO_PROM_PESOS = new header_num('INGRESO_PROM_PESOS', '(select AVG(MONTO_DEUDOR_PESOS) from DECLARACION_MES DM where DM.COD_DECLARACION = DEC.COD_DECLARACION)', 'Ingreso Promedio');
        $this->add_header(new header_text('ARCHIVOS_SIN_REVISAR', 'f_docs_dj_sin_revisar(DEC.COD_DECLARACION)', 'Tiene docs sin revisar'));
        $this->add_header($COD_DECLARACION);
        $this->add_header($NOM_ESTADO_DECLARACION);
        $this->add_header($ANO);
        $this->add_header($FECHA_DECLARACION);
        $this->add_header($RUT);
        $this->add_header($NOMBRE);
        $this->add_header($APELLIDO_PATERNO);
        $this->add_header($APELLIDO_MATERNO);
        $this->add_header($NOMBRE);
        $this->add_header($NOM_VIA_DECLARACION);
        $this->add_header($INGRESO_PROM_PESOS);
        
        
        $NOM_ESTADO_DECLARACION->valor_filtro = self::K_SOLICITADA;
        $this->make_filtros();
        
    }
}
?>