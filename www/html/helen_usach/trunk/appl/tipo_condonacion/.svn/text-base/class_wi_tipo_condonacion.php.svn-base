<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/*
Clase : WI_TIPO_CONDONACION
*/
class wi_tipo_condonacion extends w_input {
    function __construct($cod_item_menu) {
        parent::__construct('tipo_condonacion', $cod_item_menu);

		//////////////// CLASS_SQL = INPUT ///////////////
		$sql = sql_tipo_condonacion::get_sql('INPUT');
		
		$this->dws['dw_tipo_condonacion'] = new datawindow($sql);

		// asigna los formatos			
		$this->dws['dw_tipo_condonacion']->add_control($control = new edit_text_upper('NOM_TIPO_CONDONACION', 80, 100));
		$control->set_onChange("valida_nom_tipo_condonacion();");			
		
		// asigna los mandatorys		
		$this->dws['dw_tipo_condonacion']->set_mandatory('NOM_TIPO_CONDONACION', 'Nombre Tipo Condonación');
	}
	function new_record() {	
		$this->dws['dw_tipo_condonacion']->insert_row();
	}
	function load_record() {
		$COD_TIPO_CONDONACION = $this->get_item_wo($this->current_record, 'COD_TIPO_CONDONACION');
		$this->dws['dw_tipo_condonacion']->retrieve($COD_TIPO_CONDONACION);
	}
	function get_key() {
		return $this->dws['dw_tipo_condonacion']->get_item(0, 'COD_TIPO_CONDONACION');
	}
	
	function save_record($db) {
		$COD_TIPO_CONDONACION = $this->get_key();
		$NOM_TIPO_CONDONACION = $this->dws['dw_tipo_condonacion']->get_item(0, 'NOM_TIPO_CONDONACION');		

		$COD_TIPO_CONDONACION = ($COD_TIPO_CONDONACION=='') ? "null" : $COD_TIPO_CONDONACION;		
    
		$sp = 'spu_tipo_condonacion';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    
	    $param	= "'$operacion', $COD_TIPO_CONDONACION, '$NOM_TIPO_CONDONACION'"; 
	    
	    if ($db->EXECUTE_SP($sp, $param)){
	    	if ($this->is_new_record()) {				
        		$COD_TIPO_CONDONACION = $db->GET_IDENTITY("tipo_condonacion");
        		$this->dws['dw_tipo_condonacion']->set_item(0, 'COD_TIPO_CONDONACION', $COD_TIPO_CONDONACION);
			}
			return true;
		}
		return false;				
	}
}
?>