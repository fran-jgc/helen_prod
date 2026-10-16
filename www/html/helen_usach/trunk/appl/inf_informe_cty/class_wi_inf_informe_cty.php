<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");

class wi_inf_informe_cty extends w_param_informe_helen {
    var $cod_tipo_deuda;
    function __construct($cod_item_menu) {
        $xml = session::get('K_ROOT_DIR').'appl/inf_informe_cty/inf_informe_cty.xml';
        $sql_informe = sql_inf_informe_cty::get_sql('INPUT');
        parent::__construct('inf_informe_cty', $cod_item_menu, 'Informe CTY', $xml, $sql_informe, 'spi_informe_cty');
        
        $sql = sql_inf_informe_cty::get_sql('CARTERA');
        
        $this->dws['dw_param'] = new datawindow($sql);
        
        $sql = sql_inf_informe_cty::get_sql('DD_COD_CARTERA');
        $this->dws['dw_param']->add_control(new drop_down_dw('COD_CARTERA', $sql));
        $sql = sql_inf_informe_cty::get_sql('DD_COD_TIPO_DEUDA');
        $this->dws['dw_param']->add_control(new drop_down_dw('COD_TIPO_DEUDA', $sql));
        $this->dws['dw_param']->add_control(new edit_num('ANO', 4, 4));
        
        // mandatorys
        $this->dws['dw_param']->set_mandatory('COD_CARTERA', 'Cartera');
        $this->dws['dw_param']->set_mandatory('COD_TIPO_DEUDA', 'Tipo deuda');
        $this->dws['dw_param']->set_mandatory('ANO', 'Año');
    }
    function make_filtro() {
        $cod_cartera = $this->dws['dw_param']->get_item(0, 'COD_CARTERA');
        $cod_tipo_deuda = $this->dws['dw_param']->get_item(0, 'COD_TIPO_DEUDA');
        $ano   		 = $this->dws['dw_param']->get_item(0, 'ANO');
        
        // Filtro
        $nom_cartera = $this->dws['dw_param']->controls['COD_CARTERA']->get_label_from_value($cod_cartera);
        $this->filtro = "Cartera = $nom_cartera; Tipo deuda  = '$cod_tipo_deuda'\n";
        $this->filtro .= "Año = $ano\n";
        $this->cod_tipo_deuda = "$cod_tipo_deuda";
        // Arma los parametros para el SP
        $this->param = "'$cod_tipo_deuda', $cod_cartera, $ano";
    }
    function genera_xls() {
        require_once dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/libs/simplexlsxgen/src/SimpleXLSXGen.php";
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 1800);//30 minutos

        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

        if ($this->cod_tipo_deuda == "CSR") {
            $this->sp = "spi_informe_cty_csr";
            if (K_TIPO_BD == "mssql") {
                $sql = "exec ".$this->sp." ".$this->param;
            } elseif (K_TIPO_BD == "oci") {
                $db->EXECUTE_SP($this->sp, $this->param);
                $sql = "SELECT  RUT
                            ,ANO
                            ,FECHA_VENCTO
                            ,MONTO_INGRESO_DEUDOR
                            ,MONTO_INGRESO_CONYUGE
                            ,CUOTA
                            ,NOM_CONCEPTO_COBRO
                            ,CUOTA_FIJA
                            ,CUOTA_VARIABLE
                            ,MONTO_PEC
                        FROM TEMP_INFORME_CTY_CSR
                        ORDER BY RUT";
            }
            $res = $db->query($sql);
            $rows = [['<b>RUT</b>', '<b>AÑO</b>', '<b>FECHA VENCTO</b>', '<b>MONTO INGRESO DEUDOR</b>', '<b>MONTO INGRESO CONYUGE</b>', '<b>CUOTA</b>', '<b>NOMBRE CONCEPTO COBRO</b>', '<b>CUOTA FIJA</b>', '<b>CUOTA VARIABLE</b>', '<b>MONTO PEC</b>']];
            while ($my_row = $db->get_row()) {
                $rows[] = [$my_row['RUT'], $my_row['ANO'], $my_row['FECHA_VENCTO'], $my_row['MONTO_INGRESO_DEUDOR'], $my_row['MONTO_INGRESO_CONYUGE'], $my_row['CUOTA'], $my_row['NOM_CONCEPTO_COBRO'], $my_row['CUOTA_FIJA'], $my_row['CUOTA_VARIABLE'], $my_row['MONTO_PEC']];
            }
        } else {
            if (K_TIPO_BD == "mssql") {
                $sql = "exec ".$this->sp." ".$this->param;
            } elseif (K_TIPO_BD == "oci") {
                $db->EXECUTE_SP($this->sp, $this->param);
                $sql = "SELECT   RUT
                            ,ANO
                            ,FECHA_VENCTO
                            ,MONTO_INGRESO_DEUDOR
                            ,MONTO_INGRESO_CONYUGE
                            ,NOM_CONCEPTO_COBRO
                            ,MONTO_CTY
                            ,MONTO_CAT
                            ,MONTO_PEC
                        FROM TEMP_INFORME_CTY
                        ORDER BY RUT";
            }
            $res = $db->query($sql);
            $rows = [['<b>RUT</b>', '<b>AÑO</b>', '<b>FECHA VENCTO</b>', '<b>MONTO INGRESO DEUDOR</b>', '<b>MONTO INGRESO CONYUGE</b>', '<b>NOMBRE CONCEPTO COBRO</b>', '<b>MONTO CTY</b>', '<b>MONTO CAT</b>', '<b>MONTO PEC</b>']];
            while ($my_row = $db->get_row()) {
                $rows[] = [$my_row['RUT'], $my_row['ANO'], $my_row['FECHA_VENCTO'], $my_row['MONTO_INGRESO_DEUDOR'], $my_row['MONTO_INGRESO_CONYUGE'], $my_row['NOM_CONCEPTO_COBRO'], $my_row['MONTO_CTY'], $my_row['MONTO_CAT'], $my_row['MONTO_PEC']];
            }
        }

        if ($db->database_type == "oci") {
            oci_free_statement($db->query_id);
            $db->query_id = false;
        }

        \Shuchkin\SimpleXLSXGen::fromArray($rows, 'inf_cty')->downloadAs('inf_cty.xlsx');
    }
}
?>