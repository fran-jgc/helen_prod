<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/*
Clase : WI_REGION
*/
class wi_region_base extends w_input {
	function __construct($cod_item_menu){
	    parent::__construct('region', $cod_item_menu);
		$this->valida_llave = true;		// valida que la PK sea unica

		//////////////// CLASS_SQL = INPUT ///////////////
		$sql = sql_region::get_sql('INPUT');
							
		//////////////// CLASS_SQL = INPUT_DD_PAIS ///////////////
		$sql_pais = sql_region::get_sql('INPUT_DD_PAIS');							
						
		$this->dws['dw_region'] = new datawindow($sql);

		// asigna los formatos
		$this->dws['dw_region']->add_control($control = new edit_text_upper('NOM_REGION', 80, 80));
		$control->set_onChange("valida_nom_region();");
		$this->dws['dw_region']->add_control(new drop_down_dw('COD_PAIS',$sql_pais,180));
				
		// asigna los mandatorys
		$this->dws['dw_region']->set_mandatory('COD_REGION', 'Código');
		$this->dws['dw_region']->set_mandatory('NOM_REGION', 'Región');
		$this->dws['dw_region']->set_mandatory('COD_PAIS', 'País');		
	}
	function new_record() {
		$this->dws['dw_region']->add_control($control = new edit_num('COD_REGION',3,3));
		$control->set_onChange("valida_cod_region();");
		$this->dws['dw_region']->insert_row();
		$this->dws['dw_region']->set_item(0, 'COD_PAIS', 56);	// por defecto CHILE
	}
	function load_record() {
		$cod_region = $this->get_item_wo($this->current_record, 'COD_REGION');
		$this->dws['dw_region']->retrieve($cod_region);
	}
	function get_key() {
		return $this->dws['dw_region']->get_item(0, 'COD_REGION');
	}
	
	function save_record($db) {
		$COD_REGION = $this->get_key();
		$COD_PAIS = $this->dws['dw_region']->get_item(0, 'COD_PAIS');		
		$NOM_REGION = $this->dws['dw_region']->get_item(0, 'NOM_REGION');		
		$COD_REGION = ($COD_REGION=='') ? "null" : $COD_REGION;		
    	$sp = 'spu_region';
		
	    if ($this->is_new_record()){
	    	$operacion = 'INSERT';
	    }else{
	    	$operacion = 'UPDATE';
	    }
	    $param	= "'$operacion', $COD_REGION,$COD_PAIS, '$NOM_REGION'"; 
	    
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$COD_REGION = $this->dws['dw_region']->get_item(0, 'COD_REGION');
				$this->dws['dw_region']->set_item(0, 'COD_REGION', $COD_REGION);				
			}
			return true;
		}
		return false;					
	}
}
// Se separa para UCT
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wi_region.php";
if (file_exists($file_name)){ 
	require_once($file_name);
}
else {
	class wi_region extends wi_region_base {
	    function __construct($cod_item_menu) {
	        parent::__construct($cod_item_menu);
		}
	}
}
?>