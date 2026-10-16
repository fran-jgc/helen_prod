<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/".K_CLIENTE."/class_print_novacion.php");

class wi_novacion_base extends w_input{
	const K_NO_EXIGIBLE 		= 1;
	const K_COMP_VENCIDA 		= 2;
	const K_COMP_VIGENTE 		= 3;
	const K_COMP_FUTURA         = 4;

	const K_ESTADO_NOV_SOLICITADA 	= 1;
	const K_ESTADO_NOV_APROBADA 	= 2;
	const K_ESTADO_NOV_RECHAZADA 	= 3;

	function __construct($cod_item_menu){
	    parent::__construct('novacion', $cod_item_menu);
		$this->b_delete_visible  = false;
		$this->b_print_visible  = true;
		$sql = sql_novacion::get_sql('INPUT');

		$this->dws['dw_novacion'] = new datawindow($sql);

		$this->dws['dw_novacion']->add_control($control = new edit_rut('RUT', 10, 10));
		$control->set_onChange("get_datos_deudor();");

		//tipos de deuda asociados al deudor de la suspensión que tengan cuota del año(usar año de la suspensión) y CS o CSR
		$this->dws['dw_novacion']->add_control(new static_text('DIG_VERIF'));
		$this->dws['dw_novacion']->add_control(new static_text('NOMBRE'));
		$this->dws['dw_novacion']->add_control(new static_text('COD_DEUDA'));
		$this->dws['dw_novacion']->add_control(new edit_text('COD_DEUDA_H', 10, 10, 'hidden'));
		$this->dws['dw_novacion']->add_control(new static_text('COD_TIPO_DEUDA'));
		$this->dws['dw_novacion']->add_control(new static_text('SIMBOLO'));
		$this->dws['dw_novacion']->add_control(new static_text('NOM_CARTERA'));
		$this->dws['dw_novacion']->add_control(new static_text('NOM_ESTADO_DEUDA'));
		$this->dws['dw_novacion']->add_control(new static_num('CANT_CUOTA'));
		$this->dws['dw_novacion']->add_control(new static_text('FECHA_INICIO_COBRO'));
		$this->dws['dw_novacion']->add_control(new static_num('MONTO_INICIO_COBRO',4));
		$this->dws['dw_novacion']->add_control(new static_num('CAT',4));

		$this->dws['dw_novacion']->add_control(new static_num('MONTO_SVP'));
		$this->dws['dw_novacion']->add_control(new edit_text('MONTO_SVP_H',10,10,'hidden'));
		$this->dws['dw_novacion']->add_control(new static_num('MONTO_MORA'));
		$this->dws['dw_novacion']->add_control(new edit_text('MONTO_MORA_H',10,10,'hidden'));
		$this->dws['dw_novacion']->add_control(new static_num('MONTO_CP'));
		$this->dws['dw_novacion']->add_control(new edit_text('MONTO_CP_H',10,10,'hidden'));

		$this->dws['dw_novacion']->add_control(new static_num('MONTO_LP'));
		$this->dws['dw_novacion']->add_control(new edit_text('MONTO_LP_H',10,10,'hidden'));

		$this->dws['dw_novacion']->add_control(new static_num('TOTAL_PESOS'));

		$sql = sql_novacion::get_sql('INPUT_DD');

		$this->dws['dw_novacion']->add_control($control = new drop_down_dw('COD_ESTADO_NOVACION',$sql,125));
		$nombre_usuario = $this->nom_usuario;
		$fecha_confirma = $this->current_date();
		$control->set_onChange("f_usuario_confirma('$nombre_usuario','$fecha_confirma');");
		$this->dws['dw_novacion']->add_control(new edit_text_multiline('OBS',80,5));

		$this->dws['dw_novacion']->add_control(new static_num('COD_USUARIO_CONFIRMA', 0));
		$this->dws['dw_novacion']->add_control(new static_text('NOM_USUARIO_CONFIRMA', 0));
		$this->dws['dw_novacion']->add_control(new static_text('FECHA_CONFIRMA', 0));

		$sql = sql_novacion::get_sql('NRO_ANOS_DD');
		$this->dws['dw_novacion']->add_control($control = new drop_down_dw('NRO_ANOS',$sql,125));

		// asigna los mandatorys
		$this->dws['dw_novacion']->set_mandatory('RUT', 'Rut');
		$this->dws['dw_novacion']->set_mandatory('COD_ESTADO_NOVACION', 'Estado');
		$this->dws['dw_novacion']->set_mandatory('NRO_ANOS', 'años Novación');
	}

	function new_record(){
		$this->dws['dw_novacion']->insert_row();
		$this->dws['dw_novacion']->set_item(0, 'COD_USUARIO', $this->cod_usuario);
		$this->dws['dw_novacion']->set_item(0, 'NOM_USUARIO', $this->nom_usuario);
		$this->dws['dw_novacion']->set_item(0, 'FECHA_NOVACION', $this->current_date());
		$this->dws['dw_novacion']->set_item(0, 'COD_ESTADO_NOVACION', self::K_ESTADO_NOV_SOLICITADA);
		$this->dws['dw_novacion']->set_entrable('COD_ESTADO_NOVACION', false);
	}
	function load_record(){
		$cod_novacion = $this->get_item_wo($this->current_record, 'COD_NOVACION');
		$this->dws['dw_novacion']->retrieve($cod_novacion);
		$this->dws['dw_novacion']->set_entrable('RUT', false);
		$this->b_no_save_visible = true;
		$this->b_save_visible 	 = true;
		$this->b_modify_visible	 = true;

		$nombre_usuario = $this->nom_usuario;
		$fecha_confirma = $this->current_date();

		$this->dws['dw_novacion']->set_entrable('COD_ESTADO_NOVACION', true);
		$COD_ESTADO_NOVACION = $this->dws['dw_novacion']->get_item(0, 'COD_ESTADO_NOVACION');
		//estado aprobada solo permite modificar el estado_suspension
		if ($COD_ESTADO_NOVACION == self::K_ESTADO_NOV_SOLICITADA){
			$this->dws['dw_novacion']->set_entrable('COD_ESTADO_NOVACION', true);
		}
		else if ($COD_ESTADO_NOVACION == self::K_ESTADO_NOV_APROBADA || $COD_ESTADO_NOVACION == self::K_ESTADO_NOV_RECHAZADA ) {

			$this->b_no_save_visible = false;
			$this->b_save_visible 	 = false;
			$this->b_modify_visible	 = false;
		}
		if($COD_ESTADO_NOVACION == self::K_ESTADO_NOV_APROBADA){
			$this->b_print_visible	 = true;
		}
		else{
			$this->b_print_visible	 = false;
		}
	}
	function get_key(){
		return $this->dws['dw_novacion']->get_item(0, 'COD_NOVACION');
	}

	function save_record($db){
		//$db->debug = 1;
		$COD_NOVACION     		= $this->dws['dw_novacion']->get_item(0, 'COD_NOVACION');
		$COD_USUARIO           	= $this->dws['dw_novacion']->get_item(0, 'COD_USUARIO');
		$COD_DEUDA_ANTIGUA      = $this->dws['dw_novacion']->get_item(0, 'COD_DEUDA_H');
		$MONTO_SVP              = $this->dws['dw_novacion']->get_item(0, 'MONTO_SVP_H');
		$MONTO_MORA             = $this->dws['dw_novacion']->get_item(0, 'MONTO_MORA_H');
		$MONTO_CP               = $this->dws['dw_novacion']->get_item(0, 'MONTO_CP_H');
		$MONTO_LP               = $this->dws['dw_novacion']->get_item(0, 'MONTO_LP_H');
		$OBS                    = $this->dws['dw_novacion']->get_item(0, 'OBS');
		$COD_ESTADO_NOVACION    = $this->dws['dw_novacion']->get_item(0, 'COD_ESTADO_NOVACION');
		$NRO_ANOS    			= $this->dws['dw_novacion']->get_item(0, 'NRO_ANOS');

		if ($COD_ESTADO_NOVACION == self::K_ESTADO_NOV_APROBADA)
		$COD_USUARIO_CONFIRMA = $this->cod_usuario;
		else
		$COD_USUARIO_CONFIRMA = "null";

		$COD_NOVACION = ($COD_NOVACION == '') ? "null" : $COD_NOVACION;

		$sp = 'spu_novacion';
		if ($this->is_new_record())
		$operacion = 'INSERT';
		else
		$operacion = 'UPDATE';

		$param	= "'$operacion'
				 , $COD_NOVACION 
				 , $COD_USUARIO                        
				 , $COD_DEUDA_ANTIGUA       
				 , $MONTO_SVP               
				 , $MONTO_MORA              
				 , $MONTO_CP                
				 , $MONTO_LP                
				 , '$OBS'                     
				 , $COD_ESTADO_NOVACION     
				 , $COD_USUARIO_CONFIRMA
				 , $NRO_ANOS";
				 
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$COD_NOVACION = $db->GET_IDENTITY("novacion");
				$this->dws['dw_novacion']->set_item(0, 'COD_NOVACION', $COD_NOVACION);
			}
			return true;
		}else{
				return false;
		}
	}
	
	function print_record() {
		$cod_novacion = $this->get_key();
		// reporte
		$labels = array();
		$file_name = $this->find_file('novacion', 'novacion.xml');
		$rpt = new print_novacion($cod_novacion, $file_name, $labels, "novacion".$cod_novacion.'.pdf', 0);
		$this->_load_record();
	}	
	function create_ingreso() {
		$cod_novacion = $this->dws['dw_novacion']->get_item(0, 'COD_NOVACION');
		session::set("wi_novacion.create_ingreso", $cod_novacion);
		$url = $this->root_url.'../../commonlib_seven/trunk/php/link_wi.php?modulo_origen=novacion&modulo_destino=novacion&cod_modulo_destino='.K_NEW_RECORD.'&cod_item_menu=1055';
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
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wi_novacion.php";
if (file_exists($file_name)) 
	require_once($file_name);
else {
	class wi_novacion extends wi_novacion_base {
	    function __construct($cod_item_menu) {
	        parent::__construct($cod_item_menu); 
		}
	}
}
?>