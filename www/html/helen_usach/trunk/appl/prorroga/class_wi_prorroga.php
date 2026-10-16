<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/".K_CLIENTE."/class_print_prorroga.php");

class wi_prorroga_base extends w_input{
    
    const K_ESTADO_PRORROGA_SOLICITADA = 1;
    const K_ESTADO_PRORROGA_APROBADA   = 2;
    const K_ESTADO_PRORROGA_RECHAZADA  = 3;
    
    function __construct($cod_item_menu){
        
        parent::__construct('prorroga', $cod_item_menu);
        $this->b_delete_visible  = false;
        
        ///////////////// CLASS_SQL = INPUT ///////////////
        $sql = sql_prorroga::get_sql('INPUT');
        
        $this->dws['dw_prorroga'] = new datawindow($sql);
        
        //CONTROLES
        $this->dws['dw_prorroga']->add_control(new edit_text('RUT_H', 10, 10, 'hidden'));
        $this->dws['dw_prorroga']->add_control($control = new edit_num('RUT', 10, 10));
        $control->set_onChange(" f_get_name_user();");
        
        $this->dws['dw_prorroga']->add_control(new static_text('DIG_VERIF'));
        $this->dws['dw_prorroga']->add_control(new static_text('NOMBRE'));
        $this->dws['dw_prorroga']->add_control(new static_text('COD_TIPO_DEUDA'));
        $this->dws['dw_prorroga']->add_control(new static_link('COD_DEUDA', '../../../../commonlib_seven/trunk/php/link_wi.php?modulo_origen=prorroga&modulo_destino=deuda&cod_modulo_destino=[COD_DEUDA]&cod_item_menu=1010'));
        $this->dws['dw_prorroga']->add_control(new edit_text('COD_DEUDA_H',10,10,'hidden'));
        
        $this->dws['dw_prorroga']->add_control($control = new edit_date('FECHA_INICIO_COBRO', 10, 10));
        $fecha = getdate();
        $ano = $fecha["year"];
        $control->set_onChange("f_valida_fecha(this,".$ano.");");
        
        $this->dws['dw_prorroga']->add_control(new edit_text('COD_ESTADO_PRORROGA',10,10,'hidden'));
        $this->dws['dw_prorroga']->add_control(new static_num('COD_USUARIO_CONFIRMA'));
        $this->dws['dw_prorroga']->add_control(new static_text('NOM_USUARIO_CONFIRMA'));
        $this->dws['dw_prorroga']->add_control(new static_text('FECHA_CONFIRMA'));
        $this->dws['dw_prorroga']->add_control(new edit_text_multiline('OBS', 80, 6));
        $this->dws['dw_prorroga']->add_control(new edit_date('FECHA_SOLICITUD_TRAMITE', 10, 10));
        $this->dws['dw_prorroga']->add_control(new static_num('MONTO_DEUDA',4,20));
        
        $this->set_first_focus('RUT');
        
        //mandatorys
        $this->dws['dw_prorroga']->set_mandatory('RUT', 'Rut');
        $this->dws['dw_prorroga']->set_mandatory('COD_ESTADO_PRORROGA', 'Estado Prórroga');
        $this->dws['dw_prorroga']->set_mandatory('FECHA_INICIO_COBRO', 'Fecha Inicio Cobro');
    }
    function new_record(){
        $this->b_print_visible = FALSE;
        $this->dws['dw_prorroga']->insert_row();
        $this->dws['dw_prorroga']->set_item(0, 'NOM_USUARIO', $this->nom_usuario);
        $this->dws['dw_prorroga']->set_item(0, 'FECHA_PRORROGA', $this->current_date());
        $this->dws['dw_prorroga']->set_item(0, 'COD_ESTADO_PRORROGA', self::K_ESTADO_PRORROGA_SOLICITADA);
        $this->dws['dw_prorroga']->set_item(0, 'NOM_ESTADO_PRORROGA', 'Solicitada');
        $fecha = $this->current_date();
        $fecha_arr = explode('/', $fecha);
        $ano = $fecha_arr[2];
        $ano = $ano + 1;
        $this->dws['dw_prorroga']->set_item(0, 'FECHA_INICIO_COBRO', "01/01/$ano");
        $this->dws['dw_prorroga']->set_item(0, 'FECHA_SOLICITUD_TRAMITE', $this->current_date());
        
        // Solo en new record es ingresable el cod_tipo_deuda
        // Se usa como sql "{KEY1}" para que no se ejecute el select en el copnstructor
        $this->dws['dw_prorroga']->add_control($control = new drop_down_dw('COD_TIPO_DEUDA', "{KEY1}"));
        $control->set_onChange("change_tipo_deuda(this);");
    }
    function load_record(){
        $COD_PRORROGA	= $this->get_item_wo($this->current_record, 'COD_PRORROGA');
        $this->dws['dw_prorroga']->retrieve($COD_PRORROGA);
        $this->dws['dw_prorroga']->set_entrable('RUT', false);
        $rut = $this->dws['dw_prorroga']->get_item(0, 'RUT');
        
        $nombre_usuario = $this->nom_usuario;
        $fecha_confirma = $this->current_date();
        
        $this->b_no_save_visible = true;
        $this->b_save_visible 	 = true;
        $this->b_modify_visible	 = true;
        
        $COD_ESTADO_PRORROGA = $this->dws['dw_prorroga']->get_item(0, 'COD_ESTADO_PRORROGA');
        
        if ($COD_ESTADO_PRORROGA == self::K_ESTADO_PRORROGA_SOLICITADA){
            $this->b_print_visible	 = false;
            $fecha_confirma = $this->current_date();
            
            ///////////////// CLASS_SQL = OUTPUT_DD_COD_ESTADO ///////////////
            $sql = sql_prorroga::get_sql('OUTPUT_DD_COD_ESTADO');
            unset($this->dws['dw_prorroga']->controls['COD_ESTADO_PRORROGA']);
            $this->dws['dw_prorroga']->add_control($control = new drop_down_dw('COD_ESTADO_PRORROGA',$sql,125));
            $control->set_onChange("f_usuario_confirma('$nombre_usuario','$fecha_confirma');");
            
            $this->dws['dw_prorroga']->add_control(new edit_text('NOM_ESTADO_PRORROGA',10,10, 'hidden'));
            unset($this->dws['dw_prorroga']->controls['FECHA_INICIO_COBRO']);
            $this->dws['dw_prorroga']->add_control($control = new static_text('FECHA_INICIO_COBRO'));
        }
        else if ($COD_ESTADO_PRORROGA == self::K_ESTADO_PRORROGA_APROBADA || $COD_ESTADO_PRORROGA == self::K_ESTADO_PRORROGA_RECHAZADA){
            $this->b_no_save_visible = false;
            $this->b_save_visible 	 = false;
            $this->b_modify_visible	 = false;
            if ($COD_ESTADO_PRORROGA == self::K_ESTADO_PRORROGA_APROBADA)
                $this->b_print_visible	 = true;
                else
                    $this->b_print_visible	 = false;
        }
        
    }
    function get_key()	{
        return $this->dws['dw_prorroga']->get_item(0, 'COD_PRORROGA');
    }
    function save_record($db)	{
        
        $COD_PRORROGA			= $this->dws['dw_prorroga']->get_item(0, 'COD_PRORROGA');
        $COD_USUARIO			= $this->cod_usuario;
        $COD_DEUDA				= $this->dws['dw_prorroga']->get_item(0, 'COD_DEUDA_H');
        $OBS					= $this->dws['dw_prorroga']->get_item(0, 'OBS');
        $COD_ESTADO_PRORROGA	= $this->dws['dw_prorroga']->get_item(0, 'COD_ESTADO_PRORROGA');
        $FECHA_INICIO_COBRO		= $this->dws['dw_prorroga']->get_item(0, 'FECHA_INICIO_COBRO');
        $FECHA_SOLICITUD_TRAMITE = $this->dws['dw_prorroga']->get_item(0, 'FECHA_SOLICITUD_TRAMITE');
        
        $FECHA_INICIO_COBRO		= ($FECHA_INICIO_COBRO=='') ? "null" : $FECHA_INICIO_COBRO;
        $COD_PRORROGA			= ($COD_PRORROGA=='') ? "null" : $COD_PRORROGA;
        $FECHA_SOLICITUD_TRAMITE = ($FECHA_SOLICITUD_TRAMITE=='') ? "null" : $FECHA_SOLICITUD_TRAMITE;
        
        if ($COD_ESTADO_PRORROGA == self::K_ESTADO_PRORROGA_APROBADA){
            $COD_USUARIO_CONFIRMA = $this->cod_usuario;
        }
        else{
            $COD_USUARIO_CONFIRMA = "null";
        }
        
        if ($FECHA_INICIO_COBRO != "NULL"){
            if(K_TIPO_BD=="oci")
                $FECHA_INICIO_COBRO = "TO_DATE('".$FECHA_INICIO_COBRO."','dd/mm/yyyy')";
                else if (K_TIPO_BD=="mssql")
                    $FECHA_INICIO_COBRO = $this->str2date($FECHA_INICIO_COBRO);
        }
        if($FECHA_SOLICITUD_TRAMITE != "NULL"){
            $FECHA_SOLICITUD_TRAMITE = "TO_DATE('".$FECHA_SOLICITUD_TRAMITE."','dd/mm/yyyy')";
        }
        
        $sp = 'spu_prorroga';
        if ($this->is_new_record()){
            $operacion = 'INSERT';
        }
        else{
            $operacion = 'UPDATE';
        }
        $param = " '$operacion'
				   ,$COD_PRORROGA
				   ,$COD_USUARIO
				   ,$COD_DEUDA
				   ,'$OBS'
				   ,$COD_ESTADO_PRORROGA
				   ,$COD_USUARIO_CONFIRMA
				   ,$FECHA_INICIO_COBRO
				   ,$FECHA_SOLICITUD_TRAMITE";
        echo $param;
        if ($db->EXECUTE_SP($sp, $param)){
            if ($this->is_new_record()) {
                $COD_PRORROGA = $db->GET_IDENTITY("prorroga");
                $this->dws['dw_prorroga']->set_item(0, 'COD_PRORROGA', $COD_PRORROGA);
            }
            
            // Si se esta en newrecord, entonces se borra el control de dropdown
            if ($this->is_new_record())
                $this->dws['dw_prorroga']->add_control(new static_text('COD_TIPO_DEUDA'));
                
                return true;
        }
        return false;
    }
    function print_record() {
        $K_NOM_ADMIN=19;
        $cod_prorroga = $this->get_key();
        
        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
        $sql= sql_prorroga::get_sql('PRINT_RECORD',$cod_prorroga,$K_NOM_ADMIN);
        // reporte
        $labels = array();
        $file_name = $this->find_file('prorroga', 'prorroga.xml');
        $rpt = new print_prorroga($sql, $file_name, $labels, "prorroga".$cod_prorroga.'.pdf',1);
        $this->_load_record();
    }
    function create_ingreso() {
        $cod_condonacion = $this->dws['dw_prorroga']->get_item(0, 'COD_prorroga');
        session::set("wi_prorroga.create_ingreso", $cod_prorroga);
        $url = $this->root_url.'../../commonlib_seven/trunk/php/link_wi.php?modulo_origen=prorroga&modulo_destino=prorroga&cod_modulo_destino='.K_NEW_RECORD.'&cod_item_menu=1055';
        header ('Location: '.$url);
    }
    function procesa_event() {
        if(isset($_POST['b_create_ingreso_x']))  {
            $this->create_ingreso();
        }
        else{
            parent::procesa_event();
        }
    }
}
/////////////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wi_prorroga.php";
if (file_exists($file_name))
    require_once($file_name);
    else {
        class wi_prorroga extends wi_prorroga_base {
            function __construct($cod_item_menu) {
                parent::__construct($cod_item_menu);
            }
        }
    }
    ?>