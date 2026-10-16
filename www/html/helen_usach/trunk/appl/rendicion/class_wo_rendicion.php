<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_rendicion extends w_output {
    function __construct() {
        $this->b_add_visible  = true;
        
        ///////////////// CLASS_SQL = OUTPUT///////////////
        $sql = sql_rendicion::get_sql('OUTPUT');
        
        parent::__construct('rendicion', $sql, $_REQUEST['cod_item_menu']);
        
        // formato
        $this->dw->add_control(new static_num('MONTO_PESOS'));
        $this->dw->add_control(new static_num('MONTO_MONEDA',4));
        
        $COD_RENDICION = new header_num('COD_RENDICION', 'COD_RENDICION', 'N° Rendición');
        $FECHA_RENDICION = new header_date('FECHA_RENDICION', 'FECHA_RENDICION', 'Fecha');
        $sql = "select COD_TIPO_DEUDA, COD_TIPO_DEUDA NOM_TIPO_DEUDA from TIPO_DEUDA where COD_TIPO_DEUDA in ('CS','CSR','CRI','CSN')";
        $COD_TIPO_DEUDA = new header_drop_down('COD_TIPO_DEUDA', 'R.COD_TIPO_DEUDA', 'Tipo deuda', $sql);
        $FECHA_INICIO = new header_date('FECHA_INICIO', 'FECHA_INICIO', 'Inicio');
        $FECHA_TERMINO = new header_date('FECHA_TERMINO', 'FECHA_TERMINO', 'Termino');
        $MONTO_MONEDA = new header_num('MONTO_MONEDA', 'MONTO_MONEDA', 'Total  UTM', 4);
        $MONTO_PESOS = new header_num('MONTO_PESOS', 'MONTO_PESOS', 'Total $', 0);
        $sql = "select COD_ESTADO_RENDICION, NOM_ESTADO_RENDICION from ESTADO_RENDICION order by COD_ESTADO_RENDICION";
        $NOM_ESTADO_RENDICION = new header_drop_down('NOM_ESTADO_RENDICION', 'R.COD_ESTADO_RENDICION', 'Estado', $sql);
        
        $this->add_header($COD_RENDICION);
        $this->add_header($FECHA_RENDICION);
        $this->add_header($COD_TIPO_DEUDA);
        $this->add_header($FECHA_INICIO);
        $this->add_header($FECHA_TERMINO);
        $this->add_header($MONTO_MONEDA);
        $this->add_header($MONTO_PESOS);
        $this->add_header($NOM_ESTADO_RENDICION);
    }
    function procesa_event() {
        if(isset($_POST['b_add_x'])) {
            $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
            $sql = sql_rendicion::get_sql('CUENTA_RENDICIONES');
            $result = $db->build_results($sql);
            if ($result == true) {
                $this->_redraw();
                $this->alert('Ya existe una Rendición en estado "Solicitada". Para crear otra debe "Aprobar" o "Anular" la actual.');
            }
            else
                $this->add();
        }
        else
            parent::procesa_event();
    }
}
?>