<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../web_service/class_ws_helen.php");
require_once ("../../appl.ini");

session::set('K_ROOT_URL', K_ROOT_URL);
session::set('K_ROOT_DIR', K_ROOT_DIR);
session::set('K_APPL', K_APPL);

// La commonlib usa estos datos
session::set("COD_USUARIO","2");
session::set("NOM_USUARIO","USUARIO WEB");


/**************
 Clase : W_LOGIN
 **************/
class w_login extends w_base {
	var $dws = array();
	
	function __construct(){
		base::__construct();
		$this->work_directory = getcwd();
		session::un_set("COD_DEUDOR");
	}
	
	function login($login, $password) {
		//$this->crea_dws(); se desabilita y se crean los controles en el html
		$login = ($login=='') ? 'null' : "'$login'"; 
		$password = ($password=='') ? 'null' : "'$password'"; 
		//$this->dws['dw_param']->retrieve($login, $password); se desabilita y se crean los controles en el html

		$t = new Template_appl($this->root_dir."appl/service_webpay/inicio_sesion.html");// cambiar
		//$this->dws['dw_param']->habilitar($t, true); se desabilita y se crean los controles en el html
		
		$ws_helen = new wakeUp_helen();
		
		$result = $ws_helen->datos_inicio('1');
		
		$t->setVar("FECHA_ACTUAL", $result[0]['FECHA_ACTUAL']);
		$t->setVar("VALOR_UTM", number_format($result[0]['MONTO_VALOR_MONEDA'], 0, ',', '.'));
		
		print $t->toString();

		session::un_set("COD_DEUDOR");
		print '<script type="text/javascript">
						var username = document.getElementById("RUT_0"); 
						username.focus();
					 </script>';
	}
	function crea_dws() {
		$sql = "select {KEY1} RUT
						,{KEY2} CLAVE
						,to_char(sysdate, 'dd/mm/yyyy') FECHA_ACTUAL from dual";
		$this->dws['dw_param'] = new datawindow($sql);
		$this->dws['dw_param']->add_control(new edit_num('RUT', 20, 20));
		$this->dws['dw_param']->add_control(new edit_password('CLAVE', 20, 20));		
	}
	function validate_login() {
		//$this->crea_dws();
		//$this->dws['dw_param']->retrieve('null', 'null');
		//$this->dws['dw_param']->entrable = true;
		//$this->dws['dw_param']->get_values_from_POST();
		
		//$login = $this->dws['dw_param']->get_item(0, 'RUT');
		//$password = $this->dws['dw_param']->get_item(0, 'CLAVE');
		
		$login = $_POST["rut"];
		$password = $_POST["pwd"];
		
		$ws_helen = new wakeUp_helen();
		
		$result = $ws_helen->valida_rut($login,'1');
		
		if (!is_numeric($login)){
			$this->login($login, $password);
			$this->message('Ingrese un rut válido, debe ser sin puntos ni guión.');
			return;
		}

		if ($result['COD_RESPUESTA'] == 101){
			$this->login($login, $password);
			$this->message('Usuario inexistente.');
			return;
		}
		
		$passwd_bd	= $result['PASS'];
		$cod_deudor = $result['COD_DEUDOR'];
		//session::set('cod_deudor', $cod_deudor);
		$nom_deudor	= $result['NOM_DEUDOR'];
		
		if ($password <> $passwd_bd){
			$this->login($login, $password);
			$this->message('Password incorrecto');
			print '<script type="text/javascript">
							var passwd = document.getElementById("password"); 
							passwd.focus();
						 </script>';
			return;
		}

		// Ingreso OK
		session::set("cod_deudor", $cod_deudor);
		session::set("COD_DEUDOR", $cod_deudor);
		session::set("NOM_DEUDOR", $nom_deudor);

		header ('Location:'."consulta_deuda.php");
		function procesa_event() {
	
		}
	}
}
$w = new w_login();

if (!isset($_POST['b_ingresar'])){
	$w->login('','');
}
else{
	$w->validate_login();
}
?>