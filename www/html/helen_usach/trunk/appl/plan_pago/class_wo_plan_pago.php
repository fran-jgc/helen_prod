<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class header_empresa_cobranza extends header_drop_down {
    function __construct() {
        $sql_cobranza = sql_plan_pago::get_sql('DD_COD_EMPRESA_COBRANZA');
        parent::__construct('NOM_EMPRESA_COBRANZA', 'EC.COD_EMPRESA_COBRANZA', 'Nom. Cobranza', $sql_cobranza);
    }
    function make_filtro() {
        if ($this->valor_filtro=='0')
            return "(".$this->field_bd." is null) and ";
            else
                return parent::make_filtro();
    }
}
class wo_plan_pago extends w_output {
    function __construct() {
        $this->b_add_visible  = false;
        
        ///////////////// CLASS_SQL = OUTPUT ///////////////
        $sql = sql_plan_pago::get_sql('OUTPUT');
        
        parent::__construct('plan_pago', $sql, $_REQUEST['cod_item_menu']);
        
        //formato numeros
        $RUT_S = new static_num('RUT');
        $SALDO_K_S = new static_num('SALDO_K',4);
        $SALDO_VENCIDO_S = new static_num('SALDO_VENCIDO',4);
        
        $this->dw->add_control($RUT_S);
        $this->dw->add_control($SALDO_K_S);
        $this->dw->add_control($SALDO_VENCIDO_S);
        
        // headers
        $COD_PLAN_PAGO = new header_num('COD_PLAN_PAGO', 'PP.COD_PLAN_PAGO', 'Código');
        $VALOR_CUOTA = new header_num('VALOR_CUOTA', '(PP.MONTO_CAPITAL + PP.MONTO_INTERES)', 'Valor Cuota');
        $RUT = new header_rut('RUT', 'DR', 'Rut');
        $COD_DEUDA = new header_num('COD_DEUDA', 'DE.COD_DEUDA', 'N° Deuda');
        
        $sql_tipo_deuda = sql_plan_pago::get_sql('OUTPUT_TIPO_DEUDA');
        $NOM_TIPO_DEUDA = new header_drop_down_string('NOM_TIPO_DEUDA', 'TD.COD_TIPO_DEUDA', 'Tipo Deuda', $sql_tipo_deuda);
        $ANO_COBRO = new header_num('ANO_COBRO', 'ANO_COBRO', 'Año Cobro');
        $NRO_CUOTA = new header_num('NRO_CUOTA', 'NRO_CUOTA', 'N° Cuota');
        $FECHA_VENCTO = new header_date('FECHA_VENCTO', 'FECHA_VENCTO', 'Fecha Vencto.');
        
        $sql_estado = sql_plan_pago::get_sql('OUTPUT_DD_ESTADO_PP');
        $NOM_ESTADO_PLAN_PAGO = new header_drop_down_string('NOM_ESTADO_PLAN_PAGO', 'EPP.COD_ESTADO_PLAN_PAGO', 'Estado', $sql_estado);
        
        $COD_EMPRESA_COBRANZA = new header_num('COD_EMPRESA_COBRANZA', 'PP.COD_EMPRESA_COBRANZA', 'Cod. Cobranza');
        $CUOTA_ANUAL = new header_num('CUOTA_ANUAL', '(PP.MONTO_CAPITAL + PP.MONTO_INTERES)', 'Cuota Anual');
        $MONTO_ABONO = new header_num('MONTO_ABONO', 'f_pp_monto_abono(COD_PLAN_PAGO, "MONEDA")', 'Monto Abono');
        $ULTIMO_ABONO = new header_num('ULTIMO_ABONO', 'convert(varchar,(select max(FECHA_ABONO) from ABONO where COD_PLAN_PAGO = PP.COD_PLAN_PAGO and cod_ESTADO_PAGO = 1),103)', 'Último Abono');
        $header_empresa_cobranza = new header_empresa_cobranza();
        
        $ES_CRUZADO = new header_text('ES_CRUZADO', 'f_pp_es_cruzado(PP.COD_PLAN_PAGO)', 'Es Cruzado');
        $this->add_header($ES_CRUZADO);
        
        $this->add_header($COD_PLAN_PAGO);
        $this->add_header($VALOR_CUOTA);
        $this->add_header($RUT);
        $this->add_header($COD_DEUDA);
        $this->add_header($NOM_TIPO_DEUDA);
        $this->add_header($ANO_COBRO);
        $this->add_header($NRO_CUOTA);
        $this->add_header($FECHA_VENCTO);
        $this->add_header($NOM_ESTADO_PLAN_PAGO);
        $this->add_header($header_empresa_cobranza);
        $this->add_header($COD_EMPRESA_COBRANZA);
        $this->add_header($CUOTA_ANUAL);
        $this->add_header($MONTO_ABONO);
        
        $this->add_header($ULTIMO_ABONO);
    }
    
    function habilita_boton(&$temp, $boton, $habilita) {
        if ($boton=='load' && $habilita)
            $temp->setVar("WO_".strtoupper($boton), '<input name="b_'.$boton.'" id="b_'.$boton.'" src="../../../../commonlib_seven/trunk/images/b_'.$boton.'.jpg" type="image" '.
                'onMouseDown="MM_swapImage(\'b_'.$boton.'\',\'\',\'../../../../commonlib_seven/trunk/images/b_'.$boton.'_click.jpg\',1)" '.
                'onMouseUp="MM_swapImgRestore()" onMouseOut="MM_swapImgRestore()" '.
                'onMouseOver="MM_swapImage(\'b_'.$boton.'\',\'\',\'../../../../commonlib_seven/trunk/images/b_'.$boton.'_over.jpg\',1)" '.
                'onClick="return validate_load();" />');
            else
                parent::habilita_boton($temp, $boton, $habilita);
    }
    
    function redraw_item($temp, $ind, $record) {
        // Se reimplementa para calcular las columnas SALDO_K, SALDO_VENCIDO
        $cod_plan_pago = $this->dw->get_item($record, 'COD_PLAN_PAGO');
        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
        
        if (K_TIPO_BD=="oci")
            $sql = "select Nvl(f_pp_saldo_capital(COD_PLAN_PAGO) + f_pp_saldo_interes_normal(COD_PLAN_PAGO),0) SALDO_K
							,Nvl(f_pp_saldo_mora(COD_PLAN_PAGO) + f_pp_saldo_multa(COD_PLAN_PAGO), 0) SALDO_VENCIDO
					from PLAN_PAGO
					where COD_PLAN_PAGO = $cod_plan_pago";
            else if (K_TIPO_BD=="mssql")
                $sql = "select isnull(dbo.f_pp_saldo_capital(COD_PLAN_PAGO, getdate()) + dbo.f_pp_saldo_interes_normal(COD_PLAN_PAGO, getdate()),0) SALDO_K
						, isnull(dbo.f_pp_saldo_mora(COD_PLAN_PAGO, getdate()) + dbo.f_pp_saldo_multa(COD_PLAN_PAGO, getdate()), 0) SALDO_VENCIDO
					from PLAN_PAGO
					where COD_PLAN_PAGO = $cod_plan_pago";
                
                $result = $db->build_results($sql);
                $this->dw->set_item($record, 'SALDO_K', $result[0]['SALDO_K']);
                $this->dw->set_item($record, 'SALDO_VENCIDO', $result[0]['SALDO_VENCIDO']);
                parent::redraw_item($temp, $ind, $record);
    }
    function redraw($temp) {
        parent::redraw($temp);
        $this->habilita_boton($temp, 'load', true);
    }
    
    function load_cobranza() {
        $columnas = array('COD_PLAN_PAGO' 		=> array('TIPO' => 'NUMBER', 'FORMATO' => '10'	,'NULL' => 'N')
            ,'COD_EMPRESA_COBRANZA' => array('TIPO' => 'NUMBER', 'FORMATO' => '10'	,'NULL' => 'S'));
        
        $archivo = new archivo_cobranza('FILE_NAME', "../../tmp/", $columnas);
        $u = $archivo->upLoadFile();
        
        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
        $sp = 'sp_pp_empresa_cobranza';
        
        $db->BEGIN_TRANSACTION();
        $error = false;
        // procesa el archivo
        $archivo->open();
        $i = 1;
        while (!$archivo->feof()) {
            if (count($archivo->error) == $archivo->K_MAX_ERROR) {
                $this->alert('Demasiados errores, no se seguira cargando el archivo.');
                break;
            }
            $record = $archivo->read_record($i);
            if (is_array($record)) {	// Si es array no existe error
                $cod_plan_pago = $record['COD_PLAN_PAGO'];
                $cod_empresa_cobranza = $record['COD_EMPRESA_COBRANZA'];
                $cod_empresa_cobranza	= ($cod_empresa_cobranza =='') ? "NULL" : "$cod_empresa_cobranza";
                $cod_usuario = $this->cod_usuario;
                $param = "$cod_plan_pago
						,$cod_empresa_cobranza
						,$cod_usuario";
                if (!$db->EXECUTE_SP($sp, $param)) {
                    $error = true;
                    $db->ROLLBACK_TRANSACTION();
                    $error_sp = $db->GET_ERROR();
                    $this->alert('No se pudo grabar el registro.\n\n'.$db->make_msg_error_bd());
                    break;
                }
            }
            $i++;
        }
        $archivo->close();
        if (!$error) {
            $db->COMMIT_TRANSACTION();
        }
        $archivo->delFile();
        $this->retrieve();
        $archivo->display_error();	// Muestra los errores si existen
    }
    function export() {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 1800);//30 minutos

        require_once dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/libs/simplexlsxgen/src/SimpleXLSXGen.php";

        $header_row = [];
        for ($j = 0; $j < count($this->dw->fields); $j++) {
            $field = $this->dw->fields[$j]->name;
            if ($field == 'SALDO_K') {
                $header_row[] = '<b>Saldo Capital</b>';
            } elseif ($field == 'SALDO_VENCIDO') {
                $header_row[] = '<b>Saldo Vencido</b>';
            } elseif (isset($this->headers[$field])) {
                $header_row[] = '<b>'.$this->headers[$field]->nom_header.'</b>';
            }
        }

        $rows = [$header_row];

        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
        $this->make_filtros();
        $sql = $this->dw->get_sql();

        // Reemplazar los placeholders null con los cálculos reales para evitar N+1 queries
        if (K_TIPO_BD == "oci") {
            $sql = str_replace('null SALDO_K',
                'Nvl(f_pp_saldo_capital(PP.COD_PLAN_PAGO) + f_pp_saldo_interes_normal(PP.COD_PLAN_PAGO),0) SALDO_K', $sql);
            $sql = str_replace('null SALDO_VENCIDO',
                'Nvl(f_pp_saldo_mora(PP.COD_PLAN_PAGO) + f_pp_saldo_multa(PP.COD_PLAN_PAGO), 0) SALDO_VENCIDO', $sql);
        } else if (K_TIPO_BD == "mssql") {
            $sql = str_replace('null SALDO_K',
                'isnull(dbo.f_pp_saldo_capital(PP.COD_PLAN_PAGO, getdate()) + dbo.f_pp_saldo_interes_normal(PP.COD_PLAN_PAGO, getdate()),0) SALDO_K', $sql);
            $sql = str_replace('null SALDO_VENCIDO',
                'isnull(dbo.f_pp_saldo_mora(PP.COD_PLAN_PAGO, getdate()) + dbo.f_pp_saldo_multa(PP.COD_PLAN_PAGO, getdate()), 0) SALDO_VENCIDO', $sql);
        }

        $res = $db->query($sql);
        $i = 0;
        while ($my_row = $db->get_row()) {
            $data_row = [];
            for ($j = 0; $j < count($this->dw->fields); $j++) {
                $field = $this->dw->fields[$j]->name;
                if ($field == 'SALDO_K' || $field == 'SALDO_VENCIDO') {
                    $data_row[] = $my_row[$field];
                } elseif (isset($this->headers[$field])) {
                    if ($field == 'ROW')
                        $data_row[] = $i + 1;
                    else
                        $data_row[] = $my_row[$field];
                }
            }
            $rows[] = $data_row;
            $i++;
        }

        if ($db->database_type == "oci") {
            oci_free_statement($db->query_id);
            $db->query_id = false;
        }

        \Shuchkin\SimpleXLSXGen::fromArray($rows, $this->nom_tabla)->downloadAs($this->nom_tabla.'.xlsx');
    }
    function procesa_event() {
        if(isset($_POST['b_load_x']))
            $this->load_cobranza();
            else
                parent::procesa_event();
    }
}

class archivo_cobranza extends archivo_datos {
    function __construct($file, $dir, $columnas) {
        parent::__construct($file, $dir, $columnas);
    }
    function valida_record($record, $num_linea) {
        $record = parent::valida_record($record, $num_linea);
        if (!is_array($record))
            return $record;
            
            $cod_plan_pago = $record['COD_PLAN_PAGO'];
            $cod_empresa_cobranza = $record['COD_EMPRESA_COBRANZA'];
            
            $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
            
            //valida que exista el plan pago
            $sql_pp = "select count(*) COUNT_PP from PLAN_PAGO where COD_PLAN_PAGO = $cod_plan_pago";
            $result_pp = $db->build_results($sql_pp);
            $count_pp = $result_pp[0]['COUNT_PP'];
            if ($count_pp == 0)
                return $this->registra_error('Linea '.$num_linea.': Cod. Plan Pago inexistente: '.$cod_plan_pago);
                
                //valida que exista la empresa de cobranza
                if($cod_empresa_cobranza != ''){//se es '' es porque no tiene empresa de cobranza
                    $sql_ec = "select count(*) COUNT_EC from EMPRESA_COBRANZA where COD_EMPRESA_COBRANZA = $cod_empresa_cobranza";
                    $result_ec = $db->build_results($sql_ec);
                    $count_ec = $result_ec[0]['COUNT_EC'];
                    if ($count_ec == 0)
                        return $this->registra_error('Linea '.$num_linea.': Cod. Empresa Cobranza inexistente: '.$cod_empresa_cobranza);
                }
                return $record;
    }
}
?>