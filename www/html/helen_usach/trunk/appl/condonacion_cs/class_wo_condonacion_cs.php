<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_condonacion_cs extends w_output{
    
    const K_EMITIDA = 1;
    
    function __construct()
    {
        $sql = sql_condonacion_cs::get_sql('OUTPUT');
        
        parent::__construct('condonacion_cs', $sql, $_REQUEST['cod_item_menu']);
        
        //formato numeros
        $this->dw->add_control(new static_num('RUT'));
        $this->dw->add_control(new static_num('MONTO',4));
        
        // headers
        $COD_CONDONACION_CS = new header_num('COD_CONDONACION_CS', 'COD_CONDONACION_CS', 'N° Cond. CS');
        $FECHA_CONDONACION_CS = new header_text('FECHA_CONDONACION_CS', 'FECHA_CONDONACION_CS', 'Fecha');
        $COD_DEUDA = new header_num('COD_DEUDA', 'DA.COD_DEUDA', 'Deuda');
        $RUT = new header_rut('RUT', 'DR');
        $NOMBRE = new header_text('NOMBRE', 'NOMBRE', 'Nombre');
        $APELLIDO_PATERNO = new header_text('APELLIDO_PATERNO', 'APELLIDO_PATERNO', 'Paterno');
        $APELLIDO_MATERNO = new header_text('APELLIDO_MATERNO', 'APELLIDO_MATERNO', 'Materno');
        $MONTO = new header_num('MONTO', 'MONTO', 'Saldo a Condonar');
        
        $this->add_header($COD_CONDONACION_CS);
        $this->add_header($FECHA_CONDONACION_CS);
        $this->add_header($COD_DEUDA);
        $this->add_header($RUT);
        $this->add_header($NOMBRE);
        $this->add_header($APELLIDO_PATERNO);
        $this->add_header($APELLIDO_MATERNO);
        $this->add_header($MONTO);
        
        $sql_estado_ccs = sql_condonacion_cs::get_sql('DD_COD_ESTADO_CONDONACION_CS');
        $NOM_ESTADO_CONDONACION_CS = new header_drop_down('NOM_ESTADO_CONDONACION_CS', 'ECCS.COD_ESTADO_CONDONACION_CS', 'Est. Cond. CS', $sql_estado_ccs);
        $this->add_header($NOM_ESTADO_CONDONACION_CS);
        
        
        
        
        $NOM_ESTADO_CONDONACION_CS->valor_filtro = self::K_EMITIDA;
        $this->make_filtros();
        
    }
}
?>