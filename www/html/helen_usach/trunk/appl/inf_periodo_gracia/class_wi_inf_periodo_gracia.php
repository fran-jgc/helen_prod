<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

ini_set('display_errors', 'Off');
class wi_inf_periodo_gracia_base extends w_param_informe_helen {
    var $cod_tipo_deuda;
    var $ano;
    
    function __construct($cod_item_menu) {
        $xml = session::get('K_ROOT_DIR').'appl/inf_periodo_gracia/inf_periodo_gracia.xml';
        $sql_informe = sql_inf_periodo_gracia::get_sql('INPUT');
        parent::__construct('inf_periodo_gracia', $cod_item_menu, 'Informe CTY', $xml, $sql_informe, 'spi_periodo_gracia');
        
        $sql = sql_inf_periodo_gracia::get_sql('INPUT_CONTROL');
        $this->dws['dw_param'] = new datawindow($sql);
        
        $sql = sql_inf_periodo_gracia::get_sql('DD_COD_TIPO_DEUDA');
        $this->dws['dw_param']->add_control(new drop_down_dw('COD_TIPO_DEUDA', $sql));
        $this->dws['dw_param']->add_control(new edit_num('ANO', 4, 4));
        
        // mandatorys
        $this->dws['dw_param']->set_mandatory('COD_TIPO_DEUDA', 'Tipo deuda');
        $this->dws['dw_param']->set_mandatory('ANO', 'Año');
    }
    function make_filtro() {
        $cod_tipo_deuda = $this->dws['dw_param']->get_item(0, 'COD_TIPO_DEUDA');
        $ano   		 = $this->dws['dw_param']->get_item(0, 'ANO');
        
        // Filtro
        $nom_tipo_deuda = $this->dws['dw_param']->controls['COD_TIPO_DEUDA']->get_label_from_value($cod_tipo_deuda);
        $this->filtro = "Tipo deuda  = '$nom_tipo_deuda'\n";
        $this->filtro = "Año = $ano\n";
        $this->cod_tipo_deuda = "$cod_tipo_deuda";
        $this->ano = $ano;
        // Arma los parametros para el SP
        $this->param = "'$cod_tipo_deuda', $ano";
    }
    function genera_xls() {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 1800);//30 minutos

        require_once dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/libs/simplexlsxgen/src/SimpleXLSXGen.php";

        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

        $this->sp = "spi_periodo_gracia";
        if (K_TIPO_BD == "mssql") {
            $sql = "exec ".$this->sp." ".$this->param;
        } elseif (K_TIPO_BD == "oci") {
            $db->EXECUTE_SP($this->sp, $this->param);
            $sql = "SELECT   RUT
                            ,NOMBRE
                            ,TIPO_DEUDA
                            ,COD_DEUDA
                            ,FECHA_INICIO_COBRO
                            ,MONTO_INICIO_COBRO
                            ,SALDO_ACTUAL_DEUDA
                            ,DIRECCION
                            ,COMUNA
                            ,CIUDAD
                            ,REGION
                            ,TELEFONO
                            ,MAIL
                    FROM	TEMP_PERIODO_GRACIA
                    ORDER BY RUT";
        }
        $res = $db->query($sql);

        $rows = [['<b>RUT</b>', '<b>NOMBRE</b>', '<b>TIPO DEUDA</b>', '<b>CODIGO DEUDA</b>', '<b>FECHA INICIO COBRO</b>', '<b>MONTO INICIO COBRO</b>', '<b>SALDO A LA FECHA</b>', '<b>DIRECCION</b>', '<b>COMUNA</b>', '<b>CIUDAD</b>', '<b>REGION</b>', '<b>TELEFONO</b>', '<b>MAIL</b>']];
        while ($my_row = $db->get_row()) {
            $rows[] = [$my_row[0], $my_row[1], $my_row[2], $my_row[3], $my_row[4], $my_row[5], $my_row[6], $my_row[7], $my_row[8], $my_row[9], $my_row[10], $my_row[11], $my_row[12]];
        }

        if ($db->database_type == "oci") {
            oci_free_statement($db->query_id);
            $db->query_id = false;
        }

        \Shuchkin\SimpleXLSXGen::fromArray($rows, 'DEUDORES PERIODO GRACIA')->downloadAs($this->nom_tabla.'.xlsx');
    }
}

// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wi_inf_periodo_gracia.php";
if (file_exists($file_name)) {
    require_once($file_name);
}
else {
    class wi_inf_periodo_gracia extends wi_inf_periodo_gracia_base {
        function __construct($cod_item_menu) {
            parent::__construct($cod_item_menu);
        }
    }
}
?>