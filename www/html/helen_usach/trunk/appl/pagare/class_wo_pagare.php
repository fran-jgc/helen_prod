<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_pagare extends w_output {
    function __construct() {
        $this->b_add_visible  = false;
        
        //////////////// CLASS_SQL = OUTPUT ///////////////
        $sql = sql_pagare::get_sql('OUTPUT');
        
        parent::__construct('pagare', $sql, $_REQUEST['cod_item_menu']);
        
        //formato numeros
        $RUT_S = new static_num('RUT');
        $MONTO_PESOS_S = new static_num('MONTO_PESOS');
        $MONTO_MONEDA_S = new static_num('MONTO_MONEDA',4);
        $this->dw->add_control($RUT_S);
        $this->dw->add_control($MONTO_PESOS_S);
        $this->dw->add_control($MONTO_MONEDA_S);
        
        // headers
        $COD_PAGARE = new header_text('COD_PAGARE', 'COD_PAGARE', 'COD_PAGARE');
        $FOLIO_PAGARE = new header_text('FOLIO_PAGARE', 'FOLIO_PAGARE', 'Folio');
        $FECHA_PAGARE = new header_date('FECHA_PAGARE', 'FECHA_PAGARE', 'F. Suscripción');
        $RUT = new header_rut('RUT', 'DD', 'Rut');
        $COD_DEUDA = new header_num('COD_DEUDA', 'D.COD_DEUDA', 'N° Deuda');
        $sql_tipo_deuda = sql_pagare::get_sql('DD_TIPO_DEUDA');
        $NOM_TIPO_DEUDA = new header_drop_down_string('NOM_TIPO_DEUDA', 'TD.COD_TIPO_DEUDA', 'Tipo Deuda', $sql_tipo_deuda);
        $MONTO_MONEDA = new header_num('MONTO_MONEDA', 'MONTO_MONEDA', 'Monto UTM',4);
        $MONTO_PESOS = new header_num('MONTO_PESOS', 'MONTO_PESOS', 'Monto Pesos');
        $FECHA_PROTESTO = new header_text('FECHA_PROTESTO', 'FECHA_PROTESTO', 'Fecha Protestado');
        $NOM_ESTADO_PAGARE = new header_text('NOM_ESTADO_PAGARE', 'EP.NOM_ESTADO_PAGARE', 'Estado');
        
        $this->add_header($COD_PAGARE);
        $this->add_header($FOLIO_PAGARE);
        $this->add_header($FECHA_PAGARE);
        $this->add_header($RUT);
        $this->add_header($COD_DEUDA);
        $this->add_header($NOM_TIPO_DEUDA);
        $this->add_header($MONTO_MONEDA);
        $this->add_header($MONTO_PESOS);
        $this->add_header($FECHA_PROTESTO);
        $this->add_header($NOM_ESTADO_PAGARE);
    }
}
?>