<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/**************
Clase : W_LOGIN
**************/
class w_login extends w_base {
    
    function __construct(){
        base::__construct();
        $this->work_directory = getcwd();
        session::un_set("COD_DEUDOR");
    }
/*	function w_login() {
		// No llama al parent, sino q llama al abuelo
		parent::base();
		$this->work_directory = getcwd();
		session::un_set("COD_USUARIO");
	}*/
	function login($login, $password) {
		$t = new Template_appl($this->root_dir."html/login.htm");
		$t->setVar('W_LOGIN', $login);
		$t->setVar('W_PASSOWRD', $password);
		$t->setVar("W_FECHA_ACTUAL", $this->current_date());
		print $t->toString();

		session::un_set("COD_USUARIO");
		print '<script type="text/javascript">
						var username = document.getElementById("login"); 
						username.focus();
					 </script>';
	}
	function indicadores($tipo,$limite, $fin,$fuente){
	
		$domain = strstr($fuente, $limite);
		$domain = $domain;
	
		switch ($tipo) {
		    case "fecha":
		        $domain=split('\n',$domain);
		       return strip_tags(str_replace("al ","",str_replace($fin,"",$domain[0])));
		        break;
		    case "uf":
		        $domain=strip_tags($domain);
		        $domain=split('\n',$domain);
		        return str_replace(array("\r\n", "\n", "\r", "\t","&","UF"," ","$"),"",$domain[0]);
		        break;
		     case "utm":
		        $domain=strip_tags($domain);
		        $domain=split('\n',$domain);
		        return str_replace(array("\r\n", "\n", "\r", "\t","&","UTM"," ","$"),"",$domain[0]);
		        break;
		         case "dolar":
		        $domain=strip_tags($domain);
		        $domain=split('\n',$domain);
		        return str_replace(array("\r\n", "\n", "\r", "\t","&","lar Observado"," ","$"),"",$domain[0]);
		        break;
		    case 2:
		        echo "i equals 2";
		        break;
		}
	}
	
	function validate_login() {
		$login = base::parsearParametros(strtoupper($_POST['login']));
		$password = $_POST['password'];
		$passwd = $this->f_encriptar($password);
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		if(K_TIPO_BD == "oci"){
			$sql = "select 	PASSWORD,
	           				AUTORIZA_INGRESO,
	           				COD_USUARIO,
	           				NOM_USUARIO,
	           				COD_PERFIL
					from 	USUARIO
	    			where 	LOGIN = '$login'
	    			  and   NVL(K_CLIENTE, '".K_CLIENTE."') = '".K_CLIENTE."'";
		}else if(K_TIPO_BD == "mssql"){
			$sql = "select 	PASSWORD,
	           				AUTORIZA_INGRESO,
	           				COD_USUARIO,
	           				NOM_USUARIO,
	           				COD_PERFIL
					from 	USUARIO
	    			where 	LOGIN = '$login'
	    			  and   isnull(K_CLIENTE, '".K_CLIENTE."') = '".K_CLIENTE."'";
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
						
		//VALIDAR si valor moneda de la fecha actual esta ingresado
		
		if(K_TIPO_BD == "oci"){
			$sql = "select f_valida_moneda() RESULTADO from dual";			
		}else if(K_TIPO_BD == "mssql"){
			$sql = "select dbo.f_valida_moneda() RESULTADO";
		}
		$result = $db->build_results($sql);
		$resultado = $result[0]['RESULTADO'];
		
		if ($resultado != '0') {
			/*
			//Idea EP + FS. C�digo  que trae el valor de la UTM del mes actual en caso de no estar registrada,
			//		        ingresa el valor moneda para todo el mes actual desde la pagina de banco estado.
			
			//Pagina => http://www.bancoestado.cl/bancoestado/indiceseconomicos/indicadores.asp
					
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$fuente  = file_get_contents('http://www.bancoestado.cl/bancoestado/indiceseconomicos/indicadores.asp');
			$utm = str_replace('.','',str_replace(',00','',$this->indicadores('utm','UTM</a></font></td>', "z",$fuente)));
			$fecha = $this->indicadores('fecha','al ', ")</fo",$fuente);
			$array = explode('/',$fecha);
			
			$mes = $array[1];
			$ano = $array[2];
			$valor_mensual = 'S';
			 
			for ($i=1; $i <= 31; $i++) {
				if ($valor_mensual=='S'){
					$valor = $utm;				
				} 	
				$sp = 'spu_valor_moneda';			
				$param = "3,".$i.",".$mes.",".$ano.",".$valor;
				if (!$db->EXECUTE_SP($sp,$param))
					return false;
			}*/
		
			$this->presentacion($resultado);
		}else
			$this->presentacion();
	}
}
$w = new w_login();
if (!isset($_POST['b_ingresar']))
	$w->login('','');
else
	$w->validate_login();
?>
