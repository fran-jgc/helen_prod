<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/*
Clase : WI_TIPO_BITACORA
*/
class wi_tipo_bitacora extends w_input {
	function __construct($cod_item_menu) {
	    parent::__construct('tipo_bitacora', $cod_item_menu);

		$sql = sql_tipo_bitacora::get_sql("INPUT");
		
		$this->dws['dw_tipo_bitacora'] = new datawindow($sql);

		// asigna los formatos			
		$this->dws['dw_tipo_bitacora']->add_control($control = new edit_text_upper('NOM_TIPO_BITACORA', 80, 100));
		$control->set_onChange("valida_nom_tipo_bitacora();");
		$this->dws['dw_tipo_bitacora']->add_control(new edit_check_box('PERMITE_MANUAL','S', 'N'));
		
		// asigna los mandatorys		
		$this->dws['dw_tipo_bitacora']->set_mandatory('NOM_TIPO_BITACORA', 'Nombre Tipo Bitácora');
	}
	function new_record() {	
		$row = $this->dws['dw_tipo_bitacora']->insert_row();
		$this->dws['dw_tipo_bitacora']->set_item($row, 'PERMITE_MANUAL', 'S');
	}
	function load_record() {
		$COD_TIPO_BITACORA = $this->get_item_wo($this->current_record, 'COD_TIPO_BITACORA');
		/*
		 * SC 05/08/2010
		 * No se pueden modificar ni eliminar los tipo bitacora que sean los bï¿½sicos
		 * */
		if (1 < $COD_TIPO_BITACORA and $COD_TIPO_BITACORA < 17
			or 17 < $COD_TIPO_BITACORA and $COD_TIPO_BITACORA < 25){
			$this->b_modify_visible	 	= false;
			$this->b_save_visible	 	= false;
			$this->b_no_save_visible	= false;
			$this->b_delete_visible		= false;
		}
		$this->dws['dw_tipo_bitacora']->retrieve($COD_TIPO_BITACORA);
	}
	function get_key() {
		return $this->dws['dw_tipo_bitacora']->get_item(0, 'COD_TIPO_BITACORA');
	}
	
	function save_record($db) {
		$COD_TIPO_BITACORA = $this->get_key();
		$NOM_TIPO_BITACORA = $this->dws['dw_tipo_bitacora']->get_item(0, 'NOM_TIPO_BITACORA');
		$PERMITE_MANUAL = $this->dws['dw_tipo_bitacora']->get_item(0, 'PERMITE_MANUAL');
		

		$COD_TIPO_BITACORA = ($COD_TIPO_BITACORA=='') ? "null" : $COD_TIPO_BITACORA;		
        	
		$sp = 'spu_tipo_bitacora';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    
	    $param	= "'$operacion', $COD_TIPO_BITACORA, '$NOM_TIPO_BITACORA', '$PERMITE_MANUAL'"; 
	    
	    if ($db->EXECUTE_SP($sp, $param)){
	    	if ($this->is_new_record()) {				
        		$COD_TIPO_BITACORA = $db->GET_IDENTITY("tipo_bitacora");
        		$this->dws['dw_tipo_bitacora']->set_item(0, 'COD_TIPO_BITACORA', $COD_TIPO_BITACORA);
			}
			return true;
		}
		return false;				
	}
}
?>