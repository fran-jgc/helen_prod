<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/*
Clase : WI_TIPO_SUSPENSION
*/
class wi_tipo_suspension extends w_input {
	function __construct($cod_item_menu) {
	    parent::__construct('tipo_suspension', $cod_item_menu);
		
		//////////////// CLASS_SQL = INPUT ///////////////
		$sql = sql_tipo_suspension::get_sql('INPUT');
		
		$this->dws['dw_tipo_suspension'] = new datawindow($sql);

		// asigna los formatos			
		$this->dws['dw_tipo_suspension']->add_control($control = new edit_text_upper('NOM_TIPO_SUSPENSION', 80, 100));	
		$control->set_onChange("valida_nom_tipo_suspension();");		
		$this->dws['dw_tipo_suspension']->add_control(new edit_num('PERIODO_MAXIMO', 10, 10));
		
		// asigna los mandatorys		
		$this->dws['dw_tipo_suspension']->set_mandatory('NOM_TIPO_SUSPENSION', 'Nombre Tipo Suspención');
		$this->dws['dw_tipo_suspension']->set_mandatory('PERIODO_MAXIMO', 'Período máximo');
	}
	function new_record() {	
		$this->dws['dw_tipo_suspension']->insert_row();
	}
	function load_record() {
		$COD_TIPO_SUSPENSION = $this->get_item_wo($this->current_record, 'COD_TIPO_SUSPENSION');
		$this->dws['dw_tipo_suspension']->retrieve($COD_TIPO_SUSPENSION);
	}
	function get_key() {
		return $this->dws['dw_tipo_suspension']->get_item(0, 'COD_TIPO_SUSPENSION');
	}
	
	function save_record($db) {
		$COD_TIPO_SUSPENSION = $this->get_key();
		$NOM_TIPO_SUSPENSION = $this->dws['dw_tipo_suspension']->get_item(0, 'NOM_TIPO_SUSPENSION');		
		$PERIODO_MAXIMO = $this->dws['dw_tipo_suspension']->get_item(0, 'PERIODO_MAXIMO');

		$COD_TIPO_SUSPENSION = ($COD_TIPO_SUSPENSION=='') ? "null" : $COD_TIPO_SUSPENSION;		
        	
		$sp = 'spu_tipo_suspension';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    
	    $param	= "'$operacion', $COD_TIPO_SUSPENSION, '$NOM_TIPO_SUSPENSION',$PERIODO_MAXIMO";   
	    if ($db->EXECUTE_SP($sp, $param)){
	    	if ($this->is_new_record()) {				
        		$COD_TIPO_SUSPENSION = $db->GET_IDENTITY("tipo_suspension");
        		$this->dws['dw_tipo_suspension']->set_item(0, 'COD_TIPO_SUSPENSION', $COD_TIPO_SUSPENSION);
			}
			return true;
		}
		return false;				
	}
}
?>