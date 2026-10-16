<<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class wo_carga_consolidado_project extends w_output{
    function __construct() {
        $this->b_add_visible = false;
        $sql = sql_carga_consolidado_project::get_sql('OUTPUT');
        
        parent::__construct('carga_consolidado_project', $sql, $_REQUEST['cod_item_menu']);
        $this->dw->entrable = true;
        
        //formato
        $this->dw->add_control(new static_num('RUT'));
        
        // headers
        $COD_CARGA_CONSOLIDADO_PROJECT = new header_num('COD_CARGA_CONSOLIDADO_PROJECT', 'COD_CARGA_CONSOLIDADO_PROJECT', 'Código');
        $RUT = new header_rut('RUT', 'CCP', 'Rut');
        $NOMBRE = new header_text('NOMBRE', 'NOMBRE', 'Nombre');
        $APELLIDO_PATERNO = new header_text('APELLIDO_PATERNO', 'APELLIDO_PATERNO', 'Paterno');
        $APELLIDO_MATERNO = new header_text('APELLIDO_MATERNO', 'APELLIDO_MATERNO', 'Materno');
        $sql_universidad = sql_carga_consolidado_project::get_sql('DD_UNIVERSIDAD');
        $NOM_UNIVERSIDAD = new header_drop_down('NOM_UNIVERSIDAD', 'COD_UNIVERSIDAD', 'Universidad', $sql_universidad);
        
        $this->add_header($COD_CARGA_CONSOLIDADO_PROJECT);
        $this->add_header($RUT);
        $this->add_header($NOMBRE);
        $this->add_header($APELLIDO_PATERNO);
        $this->add_header($APELLIDO_MATERNO);
        $this->add_header($NOM_UNIVERSIDAD);
    }
    function set_count_output() {
        // Se cargan todos los datos
        parent::set_count_output();
    }
    function habilita_boton(&$temp, $boton, $habilita) {
        if ($boton=='save' && $habilita)
            $temp->setVar("WO_".strtoupper($boton), '<input name="b_'.$boton.'" id="b_'.$boton.'" src="../../../../commonlib_seven/trunk/images/b_'.$boton.'.jpg" type="image" '.
                'onMouseDown="MM_swapImage(\'b_'.$boton.'\',\'\',\'../../../../commonlib_seven/trunk/images/b_'.$boton.'_click.jpg\',1)" '.
                'onMouseUp="MM_swapImgRestore()" onMouseOut="MM_swapImgRestore()" '.
                'onMouseOver="MM_swapImage(\'b_'.$boton.'\',\'\',\'../../../../commonlib_seven/trunk/images/b_'.$boton.'_over.jpg\',1)" '.
                'onClick="return validate_save();" />');
            if ($boton=='load' && $habilita)
                $temp->setVar("WO_".strtoupper($boton), '<input name="b_'.$boton.'" id="b_'.$boton.'" src="../../../../commonlib_seven/trunk/images/b_'.$boton.'.jpg" type="image" '.
                    'onMouseDown="MM_swapImage(\'b_'.$boton.'\',\'\',\'../../../../commonlib_seven/trunk/images/b_'.$boton.'_click.jpg\',1)" '.
                    'onMouseUp="MM_swapImgRestore()" onMouseOut="MM_swapImgRestore()" '.
                    'onMouseOver="MM_swapImage(\'b_'.$boton.'\',\'\',\'../../../../commonlib_seven/trunk/images/b_'.$boton.'_over.jpg\',1)" '.
                    'onClick="return validate_load();" />');
                else
                    parent::habilita_boton($temp, $boton, $habilita);
    }
    function redraw(&$temp) {
        parent::redraw($temp);
        $this->habilita_boton($temp, 'load', true);
        $this->habilita_boton($temp, 'save', true);
    }
    function save() {
        $this->dw->get_values_from_POST();
        $sp = 'sp_consolidado_project';
        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
        $db->BEGIN_TRANSACTION();
        $error 			= false;
        $cod_usuario	= $this->cod_usuario;
        $param = 		"$cod_usuario";
        
        if (!$db->EXECUTE_SP($sp, $param)) {
            $error = true;
            $db->ROLLBACK_TRANSACTION();
            $this->alert('No se pudo grabar el registro.\n\n'.$db->make_msg_error_bd());
            //break;
        }
        if (!$error)
            $db->COMMIT_TRANSACTION();
            $this->retrieve();
    }
    function load() {
        $columnas = array('STRING' 		=> array('TIPO' => 'TEXT', 'FORMATO' => '',			'NULL' => 'N'));
        
        $archivo = new archivo_datos('FILE_NAME', "/tmp/", $columnas);
        $u = $archivo->upLoadFile();
        
        $sp = 'spu_carga_consolidado_project';
        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
        
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
                $string = $record['STRING'];
                $string = str_replace("'","''",$string);
                $param = "'INSERT'
						, '$string'";
                
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
    function procesa_event() {
        if(isset($_POST['b_load_x'])) {
            $this->load();
        }
        elseif(isset($_POST['b_save_x'])) {
            $this->save();
        }
        else
            parent::procesa_event();
    }
}
?>