<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/USACH/class_print_condonacion.php");

/*
 Clase : WI_CONDONACION
 */
class dw_detalle_deuda_base extends datawindow{
    
    const K_ESTADO_CONDONACION_APROBADA        = 2;
    
    function __construct() {
        $sql = sql_condonacion::get_sql('INPUT_DETALLE_DEUDA');
        
        parent::__construct($sql, 'DETALLE_DEUDA');
        $this->add_control(new static_num('NO_EXIGIBLE'));
        $this->add_control(new static_num('VENCIDA'));
        $this->add_control(new static_num('VIGENTE'));
        $this->add_control(new static_num('FUTURA'));
        $this->add_control(new static_num('TOTAL'));
                
        $this->add_control(new edit_text('COD_DEUDA_CONDONACION_H', 10, 10, 'hidden'));
        $this->add_control(new edit_text('COD_CONDONACION_H', 10, 10, 'hidden'));
        $this->add_control(new edit_text('NO_EXIGIBLE_H', 10, 10, 'hidden'));
        $this->add_control(new edit_text('VENCIDA_H', 10, 10, 'hidden'));
        $this->add_control(new edit_text('VIGENTE_H', 10, 10, 'hidden'));
        $this->add_control(new edit_text('FUTURA_H', 10, 10, 'hidden'));
    }

    function update($db,$cod_condonacion, $operacion,$cod_usuario_confirma,$cod_estado_condonacion) {
        $sp = 'sp_condonacion_deuda';

        for ($i = 0; $i < $this->row_count(); $i++) {
            $cod_deuda_condonacion    = $this->get_item($i, 'COD_DEUDA_CONDONACION_H');
            $cod_deuda                 = $this->get_item($i, 'COD_DEUDA');
            $no_exigible             = $this->get_item($i, 'NO_EXIGIBLE_H');
            $vencida                = $this->get_item($i, 'VENCIDA_H');
            $vigente                 = $this->get_item($i, 'VIGENTE_H');
            $futura                 = $this->get_item($i, 'FUTURA_H');
            $cod_comprobante        = $this->get_item($i, 'COD_COMPROBANTE');
                
            $cod_deuda_condonacion        = ($cod_deuda_condonacion=='') ? "null" : $cod_deuda_condonacion;
    
            if ($cod_estado_condonacion == self::K_ESTADO_CONDONACION_APROBADA){
                $confirma ='S';
            }
            else{
                $confirma ='N';
            }
            
            if(K_TIPO_BD == "oci"){
                $cod_deuda_condonacion    = ($cod_deuda_condonacion=='') ? "null" : $cod_deuda_condonacion;
            }
            else if(K_TIPO_BD == "mssql"){
                $cod_deuda_condonacion    = ($cod_deuda_condonacion==' ') ? "null" : $cod_deuda_condonacion;
            }
            $param = "    '$operacion'
            ,$cod_deuda_condonacion
            ,$cod_condonacion
            ,$cod_deuda
            ,$no_exigible
            ,$vencida
            ,$vigente
            ,$futura
            ,'$confirma'
            ,$cod_usuario_confirma";

            if (!$db->EXECUTE_SP($sp, $param))
            return false;
        }
        return true;
    }
}

class wi_condonacion_base extends w_input {
    const K_ESTADO_CONDONACION_SOLICITADA    = 1;
    const K_ESTADO_CONDONACION_APROBADA        = 2;
    const K_ESTADO_CONDONACION_RECHAZADA    = 3;
    const K_TIPO_CONDONACION_UNO            = 1;
    const K_TIPO_CONDONACION_DEFUNCION        = 2;

    function __construct($cod_item_menu) {
        parent::__construct('condonacion', $cod_item_menu);
        $this->b_delete_visible  = false;
        

        $sql = sql_condonacion::get_sql('INPUT');


        $this->dws['dw_condonacion'] = new datawindow($sql);
        $this->dws['dw_detalle_deuda'] = new dw_detalle_deuda($sql);

        //CONTROLES
        $this->dws['dw_condonacion']->add_control(new edit_text('RUT', 10, 10, 'hidden'));
        $this->dws['dw_condonacion']->add_control($control = new edit_num('RUT', 10, 10));
        $control->set_onChange("f_get_name_user();f_get_deudas()");


        $this->dws['dw_condonacion']->add_control(new edit_text('COD_DEUDOR', 10, 10, 'hidden'));
        $this->dws['dw_condonacion']->add_control(new edit_text('COD_USUARIO', 10, 10, 'hidden'));


        $this->dws['dw_condonacion']->add_control(new static_text('DIG_VERIF'));
        $this->dws['dw_condonacion']->add_control(new static_text('NOMBRE'));
        $this->dws['dw_condonacion']->add_control(new edit_date('FECHA_SOLICITUD_TRAMITE', 10,10));
        
        //DETALLE_CONDONACION
        
        $sql_1 = sql_condonacion::get_sql('DD_TIPO_CONDONACION');
        
        $this->dws['dw_condonacion']->add_control($control = new drop_down_dw('COD_TIPO_CONDONACION', $sql_1, 140));
        $control->set_onFocus("forzar_focus();");

        $sql_estado = sql_condonacion::get_sql('DD_ESTADO_CONDONACION');
        $this->dws['dw_condonacion']->add_control(new drop_down_dw('COD_ESTADO_CONDONACION', $sql_estado, 100));
        $this->dws['dw_condonacion']->add_control(new static_text('FECHA_CONFIRMA_NO_ING'));
        $this->dws['dw_condonacion']->add_control(new edit_text('COD_USUARIO_CONFIRMA',10,10, 'hidden'));
        $this->dws['dw_condonacion']->add_control(new static_text('NOM_USUARIO_CONFIRMA'));

        $this->dws['dw_condonacion']->add_control(new edit_text_multiline('OBS', 80, 7));

        $this->dws['dw_condonacion']->add_control(new edit_text('COD_USUARIO_CONFIRMA',10,10, 'hidden'));
        $this->dws['dw_condonacion']->add_control(new static_text('NOM_USUARIO_CONFIRMA'));

        //mandatory
        $this->dws['dw_condonacion']->set_mandatory('RUT', 'Rut');
        $this->dws['dw_condonacion']->set_mandatory('COD_TIPO_CONDONACION', 'Tipo Condonación');
        $this->dws['dw_condonacion']->set_mandatory('COD_ESTADO_CONDONACION', 'Estado Condonación');
    }

    function new_record() {
        $this->b_print_visible = FALSE;
        $this->dws['dw_condonacion']->insert_row();
        $this->dws['dw_detalle_deuda']->insert_row();
        $this->dws['dw_condonacion']->set_item(0, 'COD_USUARIO', $this->cod_usuario);
        $this->dws['dw_condonacion']->set_item(0, 'NOM_USUARIO', $this->nom_usuario);
        $this->dws['dw_condonacion']->set_item(0, 'FECHA_REGISTRO', $this->current_date());

        $this->dws['dw_condonacion']->set_item(0, 'COD_ESTADO_CONDONACION', self::K_ESTADO_CONDONACION_SOLICITADA);
        $this->dws['dw_condonacion']->set_entrable('COD_ESTADO_CONDONACION', false);
        $this->dws['dw_condonacion']->set_item(0, 'FECHA_SOLICITUD_TRAMITE', $this->current_date());
        
    }

    function load_record() {
        $K_PARAM_CARTERA = 2;
        
        $cod_condonacion    = $this->get_item_wo($this->current_record, 'COD_CONDONACION');
        $this->dws['dw_condonacion']->set_entrable('RUT', false);
        $this->dws['dw_condonacion']->retrieve($cod_condonacion);
        $this->dws['dw_detalle_deuda']->retrieve($cod_condonacion);
        $rut    = $this->dws['dw_condonacion']->get_item(0, 'RUT');
        
        $cod_estado_condonacion = $this->dws['dw_condonacion']->get_item(0, 'COD_ESTADO_CONDONACION');
        $nombre_usuario = $this->nom_usuario;
        if ($cod_estado_condonacion == self::K_ESTADO_CONDONACION_SOLICITADA){
            
            $this->b_print_visible  = false;
            $this->dws['dw_condonacion']->set_entrable('COD_ESTADO_CONDONACION', true);
            $fecha_confirma = $this->current_date();
            
            $sql = sql_condonacion::get_sql('DD_ESTADO_CONDONACION_1');

            unset($this->dws['dw_condonacion']->controls['COD_ESTADO_CONDONACION']);
            $this->dws['dw_condonacion']->add_control($control = new drop_down_dw('COD_ESTADO_CONDONACION',$sql,125));
            $control->set_onChange("f_usuario_confirma('$nombre_usuario','$fecha_confirma');");
        }
        else if ($cod_estado_condonacion==self::K_ESTADO_CONDONACION_APROBADA){
            $this->b_print_visible  = true;
            $sql_1 = sql_condonacion::get_sql('DD_ESTADO_CONDONACION_2');
            
            $this->dws['dw_condonacion']->add_control($control = new drop_down_dw('COD_ESTADO_CONDONACION', $sql_1, 90));

            $this->b_modify_visible         = false;
            $this->b_save_visible         = false;
            $this->b_no_save_visible    = false;
        }
        else if ($cod_estado_condonacion==self::K_ESTADO_CONDONACION_RECHAZADA){
            $this->b_modify_visible         = false;
            $this->b_save_visible         = false;
            $this->b_no_save_visible    = false;
            $this->b_print_visible         = false;
        }
    }

    function get_key() {
        return $this->dws['dw_condonacion']->get_item(0, 'COD_CONDONACION');
    }
    function save_record($db) {
        /*
         *
         * FALTA REVISAR SI ESTAN PASADOS  LOS PARAMETRO
         ve_operacion               IN       VARCHAR2
         , ve_cod_condonacion         IN       NUMBER
         , ve_cod_usuario             IN       NUMBER
         , ve_cod_tipo_condonacion    IN       NUMBER
         , ve_cod_deudor              IN       NUMBER
         , ve_obs                     IN       VARCHAR2
         , ve_cod_estado_condonacion  IN       NUMBER
         , ve_cod_usuario_confirma
         */
        $cod_condonacion        = $this->dws['dw_condonacion']->get_item(0, 'COD_CONDONACION');
        $rut                    = $this->dws['dw_condonacion']->get_item(0, 'RUT');
        $cod_tipo_condonacion    = $this->dws['dw_condonacion']->get_item(0, 'COD_TIPO_CONDONACION');
        $fecha_registro            = $this->dws['dw_condonacion']->get_item(0, 'FECHA_REGISTRO');
        $cod_usuario            = $this->dws['dw_condonacion']->get_item(0, 'COD_USUARIO');
        $cod_deudor                = $this->dws['dw_condonacion']->get_item(0, 'COD_DEUDOR');
        $obs                    = $this->dws['dw_condonacion']->get_item(0, 'OBS');
        $cod_estado_condonacion    = $this->dws['dw_condonacion']->get_item(0, 'COD_ESTADO_CONDONACION');
        $cod_usuario_confirma   = $this->cod_usuario;
		$fecha_solicitud_tramite  = $this->dws['dw_condonacion']->get_item(0, 'FECHA_SOLICITUD_TRAMITE');
		
        $cod_condonacion        = ($cod_condonacion=='') ? "null" : $cod_condonacion;
        $cod_usuario_confirma    = ($cod_usuario_confirma=='') ? "null" : $cod_usuario_confirma;
        $obs                    = ($obs=='') ? "null" : "'$obs'";
        $fecha_solicitud_tramite = ($fecha_solicitud_tramite=='') ? "null" : "'$fecha_solicitud_tramite'";


        $sp = 'spu_condonacion';
        if ($this->is_new_record()){
            $operacion = 'INSERT';
        }
        else {
            $operacion = 'UPDATE';
        }


        $param = "'$operacion',
        $cod_condonacion,
        $cod_usuario,
        $cod_tipo_condonacion,
        $cod_deudor,
        $obs,
        $cod_estado_condonacion,
        $cod_usuario_confirma,
        null,
		null,
        $fecha_solicitud_tramite";

        if ($db->EXECUTE_SP($sp, $param)) {
            if ($this->is_new_record()) {
                $cod_condonacion = $db->GET_IDENTITY("condonacion");
                $this->dws['dw_condonacion']->set_item(0, 'COD_CONDONACION', $cod_condonacion);
            }

            for ($i=0; $i<$this->dws['dw_detalle_deuda']->row_count(); $i++){
            }

            if (!$this->dws['dw_detalle_deuda']->update($db,$cod_condonacion,$operacion,$cod_usuario_confirma,$cod_estado_condonacion)) return false;

            return true;
        }
        return false;
    }
    
    function print_record() {
        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
        $K_NOM_ADMIN=19;
        $operacion = 'PRINT_RECORD';
        $cod_condonacion = $this->get_key();
        $fecha_registro = $db->current_date();
        $cod_usuario = session::get("COD_USUARIO");
        $tipo_doc = "CONDONACION";
        $nom_receptor_doc = strtoupper(trim($_POST['wi_hidden']));
        
        $sp = 'spu_condonacion';
        $param    =    "'$operacion'
                    ,$cod_condonacion
                    ,$cod_usuario
                    ,null
                    ,null
                    ,'null'
                    ,null
                    ,null
                    ,'$tipo_doc'
                    ,'$nom_receptor_doc'";     
        
        if ($db->EXECUTE_SP($sp, $param)) {
            $cod_condonacion = $this->get_key();
             $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
            $sql= sql_condonacion::get_sql('PRINT_RECORD',$cod_condonacion,$K_NOM_ADMIN, $nom_receptor_doc);
            // reporte
            $labels = array();
            $file_name = $this->find_file('condonacion', 'condonacion.xml');                    
            $rpt = new print_condonacion($sql, $file_name, $labels, "condonacion".$cod_condonacion, 1);                                        
        }
        $this->_load_record();
    }
    function create_ingreso() {
        $cod_condonacion = $this->dws['dw_condonacion']->get_item(0, 'COD_CONDONACION');
        session::set("wi_condonacion.create_ingreso", $cod_condonacion);
        $url = $this->root_url.'../../commonlib_seven/trunk/php/link_wi.php?modulo_origen=condonacion&modulo_destino=condonacion&cod_modulo_destino='.K_NEW_RECORD.'&cod_item_menu=1045';
        header ('Location: '.$url);
    }
    function procesa_event() {
        if(isset($_POST['b_create_ingreso_x']))  {
            $this->create_ingreso();
        }
        else
            parent::procesa_event();
    }
}
/////////////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wi_condonacion.php";
if (file_exists($file_name))
    require_once($file_name);
else {
    class wi_condonacion extends wi_condonacion_base {
        function __construct($cod_item_menu) {
            parent::__construct($cod_item_menu);
        }
    }
    class dw_detalle_deuda extends dw_detalle_deuda_base {
        function __construct() {
            parent::__construct();
        }
    }
}
?>