<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../web_service/class_ws_helen.php");
include("funciones.php");

class w_cambio_clave {
	var $dws = array();
	var $cod_deudor;
	
	function w_cambio_clave() {
		valida_inicio();
		$this->cod_deudor = session::get('cod_deudor');
		
		$t = new Template_appl("cambio_clave.html");
		
		serialize(session::set('w_cambio_clave', $this));
		
		print $t->toString();
	}
	function cambio_clave(){
	
		$nueva_clave = $_POST['NUEVA_CLAVE_0'];
		
		$ws_helen = new wakeUp_helen();
		$result = $ws_helen->cambio_clave($this->cod_deudor,$nueva_clave);
		
		if($result['COD_RESPUESTA']==100){
			header ('Location:'."error.php?error=5");
			return true;
		}
		else{
			header ('Location:'."error.php?error=3");
			return false;
		}
		/*
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$db->BEGIN_TRANSACTION();
		if ($db->EXECUTE_SP($sp, $param)) {
			$db->COMMIT_TRANSACTION();
			header ('Location:'."error.php?error=5");
			return true;
		}
		$db->ROLLBACK_TRANSACTION();
		header ('Location:'."error.php?error=3");
		return false;*/
	}
}

if (isset($_POST['b_cambiar'])){
    $w = unserialize(session::get('w_cambio_clave'));
	$w->cambio_clave();
}
else{
	new w_cambio_clave();
}
?>