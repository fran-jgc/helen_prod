<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");

//////////////////// USACH /////////////////
class wo_ingreso extends wo_ingreso_base {
	
    function __construct() {
        parent::__construct();
	}
	function cierre_caja() {
		// en wo_hidden viene fecha_desde y fecha_hasta
		$fechas = explode("|", $_POST['wo_hidden']);
		
		$fecha_desde = base::str2date($fechas[0]);
		$fecha_hasta = base::str2date($fechas[1], '23:59:59');

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sp = "sp_ingreso_junta_pre";
		$param = "$fecha_desde, $fecha_hasta";
		$db->EXECUTE_SP($sp, $param);
		
		$this->retrieve();
		$this->make_filtros();
		$this->_redraw();
		$this->alert("Cierre realizado.");
	}
}
?>