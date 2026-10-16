<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../appl.ini");
require_once(dirname(__FILE__)."/../web_service/class_ws_cliente.php");

class wo_reprogramacion_base extends w_output {
    const K_REPRO_EMITIDO = 1;
    
    function __construct() {
        $sql = sql_reprogramacion::get_sql('OUTPUT');
        
        parent::__construct('reprogramacion', $sql, $_REQUEST['cod_item_menu']);
        
        // formato
        $this->dw->add_control(new static_num('RUT'));
        
        //header
        $COD_REPROGRAMACION = new header_num('COD_REPROGRAMACION', 'RE.COD_REPROGRAMACION', 'Código');
        $FECHA_REPROGRAMACION = new header_date('FECHA_REPROGRAMACION', 'RE.FECHA_REPROGRAMACION', 'Fecha');
        $RUT = new header_rut('RUT', 'DR', 'Rut');
        $NOMBRE = new header_text('NOMBRE', 'NOMBRE', 'Nombre');
        $APELLIDO_PATERNO = new header_text('APELLIDO_PATERNO', 'APELLIDO_PATERNO', 'Paterno');
        $APELLIDO_MATERNO = new header_text('APELLIDO_MATERNO', 'APELLIDO_MATERNO', 'Materno');
        $sql = "select COD_ESTADO_REPROGRAMACION, NOM_ESTADO_REPROGRAMACION from ESTADO_REPROGRAMACION order by COD_ESTADO_REPROGRAMACION";
        $NOM_ESTADO_REPROGRAMACION = new header_drop_down('NOM_ESTADO_REPROGRAMACION', 'RE.COD_ESTADO_REPROGRAMACION', 'Estado', $sql);
        $K_CLIENTE = new header_text('K_CLIENTE', 'RE.K_CLIENTE', 'Emisor');
        
        $this->add_header($COD_REPROGRAMACION);
        $this->add_header($FECHA_REPROGRAMACION);
        $this->add_header($RUT);
        $this->add_header($NOMBRE);
        $this->add_header($APELLIDO_PATERNO);
        $this->add_header($APELLIDO_MATERNO);
        $this->add_header($NOM_ESTADO_REPROGRAMACION);
        $this->add_header($K_CLIENTE);
        
        $NOM_ESTADO_REPROGRAMACION->valor_filtro = self::K_REPRO_EMITIDO;
        $this->make_filtros();
    }
    
    function habilita_boton(&$temp, $boton, $habilita) {
        if ($boton=='create' && $habilita)
            $temp->setVar("WO_ADD_DESDE_REPROGRAMACION", '<input name="b_'.$boton.'" id="b_'.$boton.'" src="../../../../commonlib_seven/trunk/images/b_'.$boton.'.jpg" type="image" '.
                'onMouseDown="MM_swapImage(\'b_'.$boton.'\',\'\',\'../../../../commonlib_seven/trunk/images/b_'.$boton.'_click.jpg\',1)" '.
                'onMouseUp="MM_swapImgRestore()" onMouseOut="MM_swapImgRestore()" '.
                'onMouseOver="MM_swapImage(\'b_'.$boton.'\',\'\',\'../../../../commonlib_seven/trunk/images/b_'.$boton.'_over.jpg\',1)" '.
                'onClick="request_crear_desde();  return false;" />');
            else
                parent::habilita_boton($temp, $boton, $habilita);
    }
    
    function crear_desde_WS($request) {
        require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/nusoap-0.7.3/lib/nusoap.php");
        $K_PARAM_RUT_UNI = 4;
        $K_PARAM_MINEDUC = 33;
        $K_REPRO_EMITIDA = 1;
        $K_REPRO_APROBADA = 2;
        $array_request = explode('|', $request);
        $rut_deudor = $array_request[0];
        $nro_folio = $array_request[1];
        
        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
        
        $sql =	sql_reprogramacion::get_sql('AJAX_RUT_REPRO', $rut_deudor);
        $result = $db->build_results($sql);
        if ($result[0]['CANT']==0){
            $this->alert("El Deudor No existe.");
            $this->_redraw();
        }
        else{
            ///////////////// CLASS_SQL = AJAX_COD_REPROGRAMACION ///////////////
            $sql1 =	sql_reprogramacion::get_sql('AJAX_COD_REPROGRAMACION', $rut_deudor, $K_REPRO_APROBADA);
            $resultado = $db->build_results($sql1);
            ///////////////// CLASS_SQL = AJAX_VALIDA_REPRO_CONFIRMADA ///////////////
            $sql =	sql_reprogramacion::get_sql('AJAX_VALIDA_REPRO_CONFIRMADA', $rut_deudor,$K_REPRO_APROBADA);
            $result = $db->build_results($sql);
            if ($result[0]['CANT']>0){
                $this->alert("El Deudor tiene una Reprogramación en estado Confirmada con el Codigo: ".$resultado[0]['COD_REPROGRAMACION']."");
                $this->_redraw();
            }else{
                ///////////////// CLASS_SQL = AJAX_COD_REPROGRAMACION ///////////////
                $sql1 =	sql_reprogramacion::get_sql('AJAX_COD_REPROGRAMACION', $rut_deudor, $K_REPRO_EMITIDA);
                $resultado = $db->build_results($sql1);
                
                ///////////////// CLASS_SQL = AJAX_VALIDA_REPRO_SOLICITADA ///////////////
                $sql =	sql_reprogramacion::get_sql('AJAX_VALIDA_REPRO_SOLICITADA', $rut_deudor, $K_REPRO_EMITIDA);
                $result = $db->build_results($sql);
                
                if ($result[0]['CANTI']>0){
                    $this->alert("El Deudor ya tiene una Reprogramación Codigo:".$resultado[0]['COD_REPROGRAMACION']." en estado Emitida.");
                    $this->_redraw();
                } else{
                    /* se ocupa cuando es WS de mineduc
                     demo => "fscu.demo.exec.cl"
                     oficial => reprogramar.creditosolidario.cl*/
                    //$serverURL = "http://fscu.demo.exec.cl/ws/getDeudor.php";
                    //$serverURL = "http://fscu.curie.exec.cl/ws/getDeudor.php"; // otro ambiente
                    $serverURL = "http://reprogramar.creditosolidario.cl/ws/getDeudor.php";
                    
                    $serverScript = 'getDeudor.php';
                    $metodoALlamar = 'getDeudor';
                    
                    $rut_universidad = str_replace('.', '', base::get_parametro($K_PARAM_RUT_UNI));
                    $array = explode('-', $rut_universidad);
                    $rut_universidad = $array[0];
                    $div_universidad = $array[1];
                    $clave_ws_mineduc = base::get_parametro($K_PARAM_MINEDUC);
                    
                    $ws_client = new ws_cliente($serverURL);
                    $param = array('Universidad'=>$rut_universidad, 'clave'=>$clave_ws_mineduc, 'rutDeudor'=>$rut_deudor, 'idPagare'=>$nro_folio);
                    
                    if($ws_client->WS_save($param, $metodoALlamar, $nro_folio)){
                        /*registra bitacora de las creadas*/
                        $cod_reprogramacion = $ws_client->get_cod_reprogramacion();
                        $sp = 'spu_log_reprogramacion';
                        $param ="$cod_reprogramacion
							,$this->cod_usuario
							,'REPROGRAMACION'
							,'cod_reprogramacion'
							,'Creacion Reprogramacion Via Sistema Helen'";
                        $error = '';
                        if (!$db->EXECUTE_SP($sp, $param)){
                            $error = 'Error: Bitacora Crea Reprogramacion 2012.';
                        }
                        
                        $this->retrieve();
                        $this->make_filtros();
                        $this->_redraw();
                        $this->alert("Carga Reprogramacion.$error");
                    }else{
                        $error =  $ws_client->GET_ERROR();
                        $this->retrieve();
                        $this->make_filtros();
                        $this->_redraw();
                        $this->alert("$error.");
                    }
                    
                }
            }
        }
    }
    
    function redraw(&$temp) {
        parent::redraw($temp);
        $this->habilita_boton($temp, 'create', true);
    }
    
    function procesa_event() {
        if(isset($_POST['b_create_x']))
            $this->crear_desde_WS($_POST['wo_hidden']);
            else
                parent::procesa_event();
    }
}

/////////////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wo_reprogramacion.php";

if (file_exists($file_name)){
    require_once($file_name);
} else {
    class wo_reprogramacion extends wo_reprogramacion_base {
        function __construct() {
            parent::__construct();
        }
    }
}
?>