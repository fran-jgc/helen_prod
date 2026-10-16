<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../appl.ini");

class wo_ingreso_base extends w_output {
    const K_INGRESO_EMITIDO = 1;
    const K_ESTADO_SIMULACION		= 1000;
    
    function __construct() {
        $K_USU_ROBERTO_SOLAR = 4;
        
        $cod_usuario = session::get("COD_USUARIO");
        
        if(($cod_usuario == $K_USU_ROBERTO_SOLAR)&&(K_CLIENTE =='UACH'))
            $sql = sql_ingreso::get_sql('OUTPUT_R_SOLAR', $cod_usuario);
            else
                $sql = sql_ingreso::get_sql('OUTPUT', $cod_usuario);
                
                parent::__construct('ingreso', $sql, $_REQUEST['cod_item_menu']);
                // formato
                $this->dw->add_control(new static_num('RUT'));
                
                //header
                $COD_INGRESO = new header_num('COD_INGRESO', 'COD_INGRESO', 'N° Ingreso');
                $FECHA_INGRESO = new header_date('FECHA_INGRESO', 'FECHA_INGRESO', 'Fecha');
                
                $this->add_header($COD_INGRESO);
                $this->add_header($FECHA_INGRESO);
                
                $sql = "select COD_FAMILIA_DEUDA, NOM_CORTO_FAMILIA_DEUDA
				from FAMILIA_DEUDA
				ORDER BY COD_FAMILIA_DEUDA";
                $NOM_CORTO_FAMILIA_DEUDA = new header_drop_down('NOM_CORTO_FAMILIA_DEUDA', 'FD.COD_FAMILIA_DEUDA', 'Familia Deuda',$sql);
                $this->add_header($NOM_CORTO_FAMILIA_DEUDA);
                $NOM_CORTO_FAMILIA_DEUDA->field_bd_order = 'NOM_CORTO_FAMILIA_DEUDA';
                
                $RUT = new header_rut('RUT', 'DE', 'Rut');
                $this->add_header($RUT);
                $NOMBRE = new header_text('NOMBRE', 'NOMBRE', 'Nombre');
                $this->add_header($NOMBRE);
                $APELLIDO_PATERNO = new header_text('APELLIDO_PATERNO', 'APELLIDO_PATERNO', 'Paterno');
                $this->add_header($APELLIDO_PATERNO);
                $APELLIDO_MATERNO = new header_text('APELLIDO_MATERNO', 'APELLIDO_MATERNO', 'Materno');
                $this->add_header($APELLIDO_MATERNO);
                
                $sql = "select COD_ESTADO_INGRESO, NOM_ESTADO_INGRESO
			from ESTADO_INGRESO
			WHERE COD_ESTADO_INGRESO <> ".self::K_ESTADO_SIMULACION." order by COD_ESTADO_INGRESO";
                $NOM_ESTADO_INGRESO = new header_drop_down('NOM_ESTADO_INGRESO', 'I.COD_ESTADO_INGRESO', 'Estado', $sql);
                $this->add_header($NOM_ESTADO_INGRESO );
                
                $K_CLIENTE = new header_text('K_CLIENTE', 'I.K_CLIENTE', 'Emisor');
                $this->add_header($K_CLIENTE);
                
                
                // Filtro inicial
                $NOM_ESTADO_INGRESO->valor_filtro = self::K_INGRESO_EMITIDO;
                $this->make_filtros();
                
    }
    function habilita_boton(&$temp, $boton, $habilita) {
        if ($boton=='create' && $habilita)
            $temp->setVar("WO_ADD_DESDE_BOLETA", '<input name="b_'.$boton.'" id="b_'.$boton.'" src="../../../../commonlib_seven/trunk/images/b_'.$boton.'.jpg" type="image" '.
                'onMouseDown="MM_swapImage(\'b_'.$boton.'\',\'\',\'../../../../commonlib_seven/trunk/images/b_'.$boton.'_click.jpg\',1)" '.
                'onMouseUp="MM_swapImgRestore()" onMouseOut="MM_swapImgRestore()" '.
                'onMouseOver="MM_swapImage(\'b_'.$boton.'\',\'\',\'../../../../commonlib_seven/trunk/images/b_'.$boton.'_over.jpg\',1)" '.
                'onClick="return request_crear_desde();" />');
            else
                parent::habilita_boton($temp, $boton, $habilita);
    }
    function redraw(&$temp) {
        parent::redraw($temp);
        $this->habilita_boton($temp, 'create', true);
        $this->habilita_boton($temp, 'cierre_caja', true);
    }
    function crear_desde($seleccion) {
        $seleccion = explode("|", $seleccion);
        if ($seleccion[0]=="BOLETA") {
            $cod_boleta = $seleccion[1];
            session::set('CREADA_DESDE_BOLETA', $cod_boleta);
            $this->add();
        }
        else if ($seleccion[0]=="SIMULACION") {
            //$this->alert("FALTA EL CREAR DESDE SIMULACION.");
            
            $cod_simulacion = $seleccion[1];
            session::set('CREADA_DESDE_SIMULACION', $cod_simulacion);
            $this->add();
        }
    }
    function cierre_caja() {
        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
        $sp = "sp_ingreso_junta_pre";
        
        $db->EXECUTE_SP($sp, $param);
        
        $this->retrieve();
        $this->make_filtros();
        $this->_redraw();
        $this->alert("Cierre realizado.");
    }
    function procesa_event() {
        if(isset($_POST['b_create_x']))
            $this->crear_desde($_POST['wo_hidden']);
            else if(isset($_POST['b_cierre_caja_x']))
                $this->cierre_caja();
                else
                    parent::procesa_event();
    }
}

// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wo_ingreso.php";
if (file_exists($file_name))
    require_once($file_name);
    else {
        class wo_ingreso extends wo_ingreso_base {
            function __construct() {
                parent::__construct();
            }
        }
    }
    ?>