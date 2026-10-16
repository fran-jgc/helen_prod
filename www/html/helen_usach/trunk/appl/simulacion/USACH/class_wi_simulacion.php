<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
////////////////////////////////////////
/////////////// USACH //////////////////
////////////////////////////////////////
class wi_simulacion extends wi_simulacion_base {
    
    function __construct ($cod_item_menu){
        parent::__construct($cod_item_menu);
	}
	
	function load_record() {
		parent::load_record();
		$validez_sumulacion = $this->dws['dw_ingreso']->get_item(0, 'VALIDEZ_SIMULACION');
		$fecha_simulacion = $this->dws['dw_ingreso']->get_item(0, 'FECHA_INGRESO');
		$separado_fecha = explode('/',$fecha_simulacion);
		$fecha_impresa = $validez_sumulacion + $separado_fecha[0].'/'.$separado_fecha[1].'/'.$separado_fecha[2];
		$this->dws['dw_ingreso']->set_item(0, 'VALIDEZ_SIMULACION', $fecha_impresa);
	}
	
	function new_record() {
		parent::new_record();
		//validez de la SIMULACI�N
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT To_Char(To_Date(f_get_ultimo_dia_habil(sysdate)),'dd/mm/yyyy') VAL_SIMULACION FROM dual";
		$result = $db->build_results($sql);
		$this->dws['dw_ingreso']->set_item(0, 'VALIDEZ_SIMULACION',$result[0]['VAL_SIMULACION']);
	}
}
?>