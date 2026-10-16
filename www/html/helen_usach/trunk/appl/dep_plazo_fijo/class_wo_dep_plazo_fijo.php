<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_dep_plazo_fijo extends w_output {
    
    function __construct() {
        
        $sql = "SELECT COD_DEP_PLAZO_FIJO
                      ,EPF.NOM_ESTADO_DEP_PLAZO_FIJO
                      ,To_Char(FECHA_DEP_PLAZO_FIJO, 'DD/MM/YYYY' ) FECHA
                      ,DESCRIPCION
                FROM DEP_PLAZO_FIJO PF, ESTADO_DEP_PLAZO_FIJO EPF
                WHERE PF.COD_ESTADO_DEP_PLAZO_FIJO = EPF.COD_ESTADO_DEP_PLAZO_FIJO
                ORDER BY COD_DEP_PLAZO_FIJO DESC";
        
        parent::__construct('dep_plazo_fijo', $sql, $_REQUEST['cod_item_menu']);
        
        // headers
        $COD_DEP_PLAZO_FIJO = new header_num('COD_DEP_PLAZO_FIJO', 'COD_DEP_PLAZO_FIJO', 'Código');
        $FECHA_REGISTRO = new header_date('FECHA', 'FECHA_DEP_PLAZO_FIJO', 'Fecha Dép.');
        
        $sql_estado = "SELECT   COD_ESTADO_DEP_PLAZO_FIJO, NOM_ESTADO_DEP_PLAZO_FIJO
								FROM   ESTADO_DEP_PLAZO_FIJO
								ORDER BY COD_ESTADO_DEP_PLAZO_FIJO";
        $NOM_ESTADO_DEP_PLAZO_FIJO = new header_drop_down('NOM_ESTADO_DEP_PLAZO_FIJO', 'EDP.COD_ESTADO_DEP_PLAZO_FIJO', 'Estado DEP.', $sql_estado );
        
        $DESCRIPCION = new header_text('DESCRIPCION', 'DESCRIPCION', 'Descripción');
        
        $this->add_header($COD_DEP_PLAZO_FIJO);
        $this->add_header($FECHA_REGISTRO);
        $this->add_header($NOM_ESTADO_DEP_PLAZO_FIJO);
        $this->add_header($DESCRIPCION);
    }
}
?>