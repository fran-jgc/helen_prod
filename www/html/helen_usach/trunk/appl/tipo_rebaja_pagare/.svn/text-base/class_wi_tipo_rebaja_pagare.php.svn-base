<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/*
Clase : WI_TIPO_REBAJA_PAGARE
*/
class wi_tipo_rebaja_pagare extends w_input {
    function __construct($cod_item_menu) {
        parent::__construct('tipo_rebaja_pagare', $cod_item_menu);
		
		$sql = sql_tipo_rebaja_pagare::get_sql('INPUT');
		
		$this->dws['dw_tipo_rebaja_pagare'] = new datawindow($sql);

		// asigna los formatos			
		$this->dws['dw_tipo_rebaja_pagare']->add_control($control = new edit_text_upper('NOM_TIPO_REBAJA_PAGARE', 80, 100));
		$control->set_onChange("valida_nom_tipo_rebaja_pagare();");			
		
		// asigna los mandatorys		
		$this->dws['dw_tipo_rebaja_pagare']->set_mandatory('NOM_TIPO_REBAJA_PAGARE', 'Nombre del Tipo Rebaja Pagaré');
	}
	function new_record() {	
		$this->dws['dw_tipo_rebaja_pagare']->insert_row();
	}
	function load_record() {
		$COD_TIPO_REBAJA_PAGARE = $this->get_item_wo($this->current_record, 'COD_TIPO_REBAJA_PAGARE');
		$this->dws['dw_tipo_rebaja_pagare']->retrieve($COD_TIPO_REBAJA_PAGARE);
	}
	function get_key() {
		return $this->dws['dw_tipo_rebaja_pagare']->get_item(0, 'COD_TIPO_REBAJA_PAGARE');
	}
	
	function save_record($db) {
		$COD_TIPO_REBAJA_PAGARE = $this->get_key();
		$NOM_TIPO_REBAJA_PAGARE = $this->dws['dw_tipo_rebaja_pagare']->get_item(0, 'NOM_TIPO_REBAJA_PAGARE');		

		$COD_TIPO_REBAJA_PAGARE = ($COD_TIPO_REBAJA_PAGARE=='') ? "null" : $COD_TIPO_REBAJA_PAGARE;		
    
		$sp = 'spu_tipo_rebaja_pagare';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    
	    $param	= "'$operacion', $COD_TIPO_REBAJA_PAGARE, '$NOM_TIPO_REBAJA_PAGARE'"; 
		
	    if ($db->EXECUTE_SP($sp, $param)){
	    	if ($this->is_new_record()) {				
        		$COD_TIPO_REBAJA_PAGARE = $db->GET_IDENTITY("tipo_rebaja_pagare");
        		$this->dws['dw_tipo_rebaja_pagare']->set_item(0, 'COD_TIPO_REBAJA_PAGARE', $COD_TIPO_REBAJA_PAGARE);
			}
			return true;
		}
		return false;				
	}
}
?>