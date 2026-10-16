<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_conciliacion extends w_output {
    function __construct() {
        $sql = sql_conciliacion::get_sql('OUTPUT');
        
        parent::__construct('conciliacion', $sql, $_REQUEST['cod_item_menu']);
        
        // headers
        $COD_CONCILIACION = new header_num('COD_CONCILIACION', 'COD_CONCILIACION', 'N° Conciliación');
        $sql_usuario_conciliacion = sql_conciliacion::get_sql('DD_COD_USUARIO');
        $NOM_USUARIO = new header_drop_down('NOM_USUARIO', 'COD_USUARIO', 'Usuario', $sql_usuario_conciliacion);
        $FECHA_CONCILIACION = new header_date('FECHA_CONCILIACION', 'FECHA_CONCILIACION', 'Fecha');
        $sql_ctacte_conciliacion = sql_conciliacion::get_sql('DD_CTACTE');
        $NOM_CUENTA_CORRIENTE = new header_drop_down('NOM_CUENTA_CORRIENTE', 'COD_CUENTA_CORRIENTE', 'Cuenta Corriente', $sql_ctacte_conciliacion);
        $MES = new header_num('MES', 'MES', 'Mes Conciliación');
        $ANO = new header_num('ANO', 'ANO', 'Año Conciliación');
        
        $this->add_header($COD_CONCILIACION);
        $this->add_header($NOM_USUARIO);
        $this->add_header($FECHA_CONCILIACION);
        $this->add_header($NOM_CUENTA_CORRIENTE);
        $this->add_header($MES);
        $this->add_header($ANO);
    }
}
?>