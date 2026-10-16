<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../appl.ini");

/*
 Clase : WI_CIUDAD
 */
 
class wi_ciudad_base extends w_input {
	function __construct($cod_item_menu) {
	    parent::__construct('ciudad', $cod_item_menu);
		
		$this->valida_llave = true;		// valida que la PK sea unica

		$sql_ciudad = sql_ciudad::get_sql('INPUT');
		$sql_pais 	= sql_ciudad::get_sql('DD_COD_PAIS');
		$sql_region	= sql_ciudad::get_sql('DD_COD_REGION');

		$this->dws['dw_ciudad'] = new datawindow($sql_ciudad);

		// asigna los formatos
		$this->dws['dw_ciudad']->add_control($control = new drop_down_dw('COD_PAIS',$sql_pais, 280));
		$control->set_drop_down_dependiente('ciudad', 'COD_REGION');
		$sql_ciudad_region = sql_ciudad::get_sql('DD_COD_CIUDAD_REGION');

		$this->dws['dw_ciudad']->add_control(new drop_down_dw('COD_REGION',$sql_ciudad_region, 280));
		$this->dws['dw_ciudad']->add_control($control = new edit_text_upper('NOM_CIUDAD', 80, 100));
		$control->set_onChange("valida_nom_ciudad();");

		// asigna los mandatorys
		$this->dws['dw_ciudad']->set_mandatory('COD_CIUDAD', 'Código');
		$this->dws['dw_ciudad']->set_mandatory('COD_REGION', 'Nombre de la Región');
		$this->dws['dw_ciudad']->set_mandatory('COD_PAIS', 'Nombre del País');
		$this->dws['dw_ciudad']->set_mandatory('NOM_CIUDAD', 'Nombre de la Ciudad');
	}
	
	function new_record() {
		$this->dws['dw_ciudad']->add_control($control = new edit_num('COD_CIUDAD',5,3));
		$control->set_onChange("valida_cod_ciudad();");
		$this->dws['dw_ciudad']->insert_row();
	}
	function load_record() {
		$cod_ciudad = $this->get_item_wo($this->current_record, 'COD_CIUDAD');
		$this->dws['dw_ciudad']->retrieve($cod_ciudad);
		$COD_PAIS = $this->dws['dw_ciudad']->get_item(0, 'COD_PAIS');
		$this->dws['dw_ciudad']->controls['COD_REGION']->retrieve($COD_PAIS);
	}
	function get_key() {
		return $this->dws['dw_ciudad']->get_item(0, 'COD_CIUDAD');
	}
	function save_record($db) {
		$COD_CIUDAD = $this->get_key();
		$COD_REGION = $this->dws['dw_ciudad']->get_item(0, 'COD_REGION');
		$COD_PAIS = $this->dws['dw_ciudad']->get_item(0, 'COD_PAIS');
		$NOM_CIUDAD = $this->dws['dw_ciudad']->get_item(0, 'NOM_CIUDAD');

		$COD_CIUDAD = ($COD_CIUDAD=='') ? "null" : $COD_CIUDAD;

		$sp = 'spu_ciudad';
		if ($this->is_new_record())
		$operacion = 'INSERT';
		else
		$operacion = 'UPDATE';
			
		$param	= "'$operacion', $COD_CIUDAD, $COD_REGION, $COD_PAIS, '$NOM_CIUDAD'";
			
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$cod_ciudad = $this->dws['dw_ciudad']->get_item(0, 'COD_CIUDAD');
				$this->dws['dw_ciudad']->set_item(0, 'COD_CIUDAD', $cod_ciudad);
			}
			return true;
		}
		return false;

	}
}
// Se separa para UCT
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wi_ciudad.php";
if(file_exists($file_name)){ 
	require_once($file_name);
}
else {
	class wi_ciudad extends wi_ciudad_base {
	    function __construct($cod_item_menu) {
	        parent::__construct($cod_item_menu);
		}
	}
}
?>