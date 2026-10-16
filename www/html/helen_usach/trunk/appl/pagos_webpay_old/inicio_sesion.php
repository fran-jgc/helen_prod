<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once ("../../appl.ini");
ini_set('display_errors', 'Off');
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
	
	function __construct() {
		// No llama al parent, sino q llama al abuelo
	    base::__construct();
		$this->work_directory = getcwd();
		session::un_set("COD_USUARIO");
	}
	function login($login, $password) {
		$this->crea_dws();
		$login = ($login=='') ? 'null' : "'$login'"; 
		$password = ($password=='') ? 'null' : "'$password'"; 
		$this->dws['dw_param']->retrieve($login, $password);

		$t = new Template_appl($this->root_dir."appl/pagos_webpay/inicio_sesion.html");// cambiar
		$this->dws['dw_param']->habilitar($t, true);
		print $t->toString();

		session::un_set("COD_USUARIO");
		print '<script type="text/javascript">
						var username = document.getElementById("login"); 
						username.focus();
					 </script>';
	}
	function crea_dws() {
		$sql = "select {KEY1} login
						,{KEY2} password
						,to_char(sysdate, 'dd/mm/yyyy') FECHA_ACTUAL from dual";
		$this->dws['dw_param'] = new datawindow($sql);
		$this->dws['dw_param']->add_control(new edit_num('login', 20, 20));
		$this->dws['dw_param']->add_control(new edit_password('password', 20, 20));		
	}
	function validate_login() {
		$login = base::parsearParametros(strtoupper($_POST['login']));
		$password = $_POST['password'];
		$passwd = $this->f_encriptar($password);
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		if(K_TIPO_BD == "oci"){
			$sql = "select 	PASSWORD,
	           				COD_USUARIO,
	           				NOM_USUARIO,
	           				COD_PERFIL
					from 	USUARIO
	    			where 	LOGIN = '$login'
	    			 ";
		}else if(K_TIPO_BD == "mssql"){
			$sql = "select 	PASSWORD,
	           				COD_USUARIO,
	           				NOM_USUARIO,
	           				COD_PERFIL
					from 	USUARIO
	    			where 	LOGIN = '$login'
	    			  ";
		}
		$result = $db->build_results($sql);
		if (count($result)==0) {
			$this->login(str_replace("''", "'", $login), $password);
			$this->message('Usuario inexistente.');
			return;
		}
		$passwd_bd = $result[0]['PASSWORD'];
		$autorizado_ingreso = $result[0]['AUTORIZA_INGRESO'];
		$cod_usuario = $result[0]['COD_USUARIO'];
		$nom_usuario = $result[0]['NOM_USUARIO'];
		$cod_perfil = $result[0]['COD_PERFIL'];
		if ($autorizado_ingreso=='N') {
			$this->login(str_replace("''", "'", $login), $password);
			$this->message('Usuario no autorizado a ingresar.');
			return;
		}
		elseif ($passwd <> $passwd_bd) {
			$this->login(str_replace("''", "'", $login), $password);
			$this->message('Password incorrecto');
			print '<script type="text/javascript">
							var passwd = document.getElementById("password"); 
							passwd.focus();
						 </script>';
			return;
		}
		session::set("COD_USUARIO", $cod_usuario);
		session::set("NOM_USUARIO", $nom_usuario);
		session::set("COD_PERFIL", $cod_perfil);
		base::spd_lock_table_user();
		
		$db->BEGIN_TRANSACTION();
		$db->EXECUTE_SP("sp_log_cambio", "'LOGIN', '', $cod_usuario, 'L'");
		$db->COMMIT_TRANSACTION();
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

		// Ingreso OK
		

		header ('Location:'."consulta_deuda.php");
		

		
	}
}
$w = new w_login();
if (!isset($_POST['b_ingresar']))
	$w->login('','');
else
	$w->validate_login();
?>
