<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../appl.ini");

class wo_repro_rcs_base extends w_output {
    const K_Emitida = 1;
    function __construct()
    {
        $sql = sql_repro_rcs::get_sql('OUTPUT');
        parent::__construct('repro_rcs', $sql, $_REQUEST['cod_item_menu']);
        
        // formato
        $this->dw->add_control(new static_num('RUT'));
        $this->dw->add_control(new static_num('MONTO_PAGO_INICIAL',4));
        $this->dw->add_control(new static_num('MONTO_A_REPROGRAMAR',4));
        $this->dw->add_control(new static_num('MONTO_CUOTA',4));
        
        
        //header
        $COD_REPRO_RCS = new header_num('COD_REPRO_RCS', 'R.COD_REPRO_RCS', 'Código');
        $NRO_REPRO_RCS = new header_num('NRO_REPRO_RCS', 'R.NRO_REPRO_RCS', 'Nro17Bis');
        $FECHA_REPRO_RCS = new header_date('FECHA_REPRO_RCS', 'R.FECHA_REPRO_RCS', 'Fecha');
        $sql = "select ER.COD_ESTADO_repro_rcs ,ER.NOM_ESTADO_repro_rcs NOM_ESTADO_REPRO from ESTADO_repro_rcs ER ORDER BY ER.COD_ESTADO_repro_rcs";
        $NOM_ESTADO_REPRO = new header_drop_down('NOM_ESTADO_REPRO', 'ER.COD_ESTADO_repro_rcs', 'Estado', $sql);
        $RUT = new header_rut('RUT', 'D', 'Rut');
        $NOMBRE = new header_text('NOMBRE', 'NOMBRE', 'Nombre');
        $MONTO_PAGO_INICIAL = new header_num('MONTO_PAGO_INICIAL', 'MONTO_PAGO_INICIAL', 'Monto Ini UTM');
        $MONTO_A_REPROGRAMAR = new header_num('MONTO_A_REPROGRAMAR', 'MONTO_A_REPROGRAMAR', 'Monto Repro UTM');
        $CANT_CUOTA = new header_num('CANT_CUOTA', 'CANT_CUOTA', 'Cant Cuota');
        $MONTO_CUOTA = new header_num('MONTO_CUOTA', 'MONTO_CUOTA', 'Monto Cuota UTM');
        
        $this->add_header($COD_REPRO_RCS);
        $this->add_header($NRO_REPRO_RCS);
        $this->add_header($FECHA_REPRO_RCS);
        $this->add_header($NOM_ESTADO_REPRO);
        $this->add_header($RUT);
        $this->add_header($NOMBRE);
        $this->add_header($MONTO_PAGO_INICIAL);
        $this->add_header($MONTO_A_REPROGRAMAR);
        $this->add_header($CANT_CUOTA);
        $this->add_header($MONTO_CUOTA);
        $NOM_ESTADO_REPRO->valor_filtro = self::K_Emitida;
        $this->make_filtros();
    }
}

/////////////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wo_repro_rcs.php";

if (file_exists($file_name)){
    require_once($file_name);
} else {
    class wo_repro_rcs extends wo_repro_rcs_base {
        function __construct() {
            parent::__construct();
        }
    }
}
?>