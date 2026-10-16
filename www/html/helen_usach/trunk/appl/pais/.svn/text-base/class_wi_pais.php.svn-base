<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../appl.ini");

/*
Clase : WI_PAIS
*/
class wi_pais_base extends w_input {
	function __construct($cod_item_menu){
	    parent::__construct('pais', $cod_item_menu);
		$this->valida_llave = true;
		
		//////////////// CLASS_SQL = INPUT ///////////////
		$sql = sql_pais::get_sql('INPUT');
		$this->dws['dw_pais'] = new datawindow($sql);
		
		// asigna los formatos
		$this->dws['dw_pais']->add_control($control = new edit_text_upper('NOM_PAIS', 80, 100));
		$control->set_onChange("valida_nom_pais();");
		
		// asigna los mandatorys
		$this->dws['dw_pais']->set_mandatory('COD_PAIS', 'Código del País');
		$this->dws['dw_pais']->set_mandatory('NOM_PAIS', 'Nombre del País');
	}
	
	function new_record() {
		$this->dws['dw_pais']->add_control($control = new edit_num('COD_PAIS',4,4));
		$control->set_onChange("valida_cod_pais();");
		$this->dws['dw_pais']->insert_row();
	}
	
	function load_record() {
		$cod_pais = $this->get_item_wo($this->current_record, 'COD_PAIS');
		$this->dws['dw_pais']->retrieve($cod_pais);
	}
	
	function get_key() {
		return $this->dws['dw_pais']->get_item(0, 'COD_PAIS');
	}
	
	function save_record($db) {
		
		$COD_PAIS = $this->get_key();
		$NOM_PAIS = $this->dws['dw_pais']->get_item(0, 'NOM_PAIS');
		$COD_PAIS = ($COD_PAIS=='') ? "null" : $COD_PAIS;
		$sp = 'spu_pais';
		
		if ($this->is_new_record())
		{
			$operacion = 'INSERT';
		}	
		else{
			$operacion = 'UPDATE';
		}
		
		$param	= "'$operacion', $COD_PAIS, '$NOM_PAIS'";
		
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$cod_pais = $this->dws['dw_pais']->get_item(0, 'COD_PAIS');
				$this->dws['dw_pais']->set_item(0, 'COD_PAIS', $cod_pais);
			}
			return true;
		}return false;
	}
}
// Se separa para UCT
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wi_pais.php";
if (file_exists($file_name)){ 
	require_once($file_name);
}
else {
	class wi_pais extends wi_pais_base {
	    function __construct($cod_item_menu) {
	        parent::__construct($cod_item_menu);
		}
	}
}
?>