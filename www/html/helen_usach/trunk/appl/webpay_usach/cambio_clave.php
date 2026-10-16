<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
include("funciones.php");

class w_cambio_clave {
	var $dws = array();
	var $cod_deudor;
	
	function w_cambio_clave() {
		valida_inicio();
		$this->cod_deudor = session::get('cod_deudor');
		
		$t = new Template_appl("cambio_clave.html");
		$sql = "select null ACTUAL_CLAVE
						,null NUEVA_CLAVE
						,null CONFIRMA_CLAVE
			from dual";
		$this->dws['dw_cambio_clave'] = new datawindow($sql);
		$this->dws['dw_cambio_clave']->add_control(new edit_password('ACTUAL_CLAVE', 20, 20));
		$this->dws['dw_cambio_clave']->add_control(new edit_password('NUEVA_CLAVE', 20, 20));
		$this->dws['dw_cambio_clave']->add_control(new edit_password('CONFIRMA_CLAVE', 20, 20));
		
		$this->dws['dw_cambio_clave']->insert_row();
		$this->dws['dw_cambio_clave']->habilitar($t, true);
		
		session::set('w_cambio_clave', serialize($this));
		
		print $t->toString();
	}
	function cambio_clave(){
		$this->dws['dw_cambio_clave']->get_values_from_POST();
		$nueva_clave = $this->dws['dw_cambio_clave']->get_item(0, 'NUEVA_CLAVE');
		
		$sp = "spu_deudor";
		$param = "'CHANGE_PASSWORD'
					,$this->cod_deudor
					,null	/* @ve_rut */
					,null	/* @ve_dig_verif */
					,null	/* @ve_nombre */
					,null	/* @ve_apellido_paterno */
					,null	/* @ve_apellido_materno */
					,null	/* @ve_sexo */
					,null	/* @ve_cod_estado_civil */
					,null	/* @ve_fecha_nacim */
					,null	/* @ve_fecha_defuncion */
					,'$nueva_clave'
					,null";
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$db->BEGIN_TRANSACTION();
		if ($db->EXECUTE_SP($sp, $param)) {
			$db->COMMIT_TRANSACTION();
			header ('Location:'."error.php?error=5");
			return true;
		}
		$db->ROLLBACK_TRANSACTION();
		header ('Location:'."error.php?error=3");
		return false;
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