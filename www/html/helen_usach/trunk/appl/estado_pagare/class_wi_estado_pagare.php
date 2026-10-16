<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

/*
Clase : WI_ESTADO_PAGARE
*/
class wi_estado_pagare extends w_input {
	function __construct($cod_item_menu) {
	    parent::__construct('estado_pagare', $cod_item_menu);
		$this->b_delete_visible = false;
		$this->b_save_visible = true;
		$this->b_no_save_visible = true;
		$this->b_modify_visible = true;
		
		$sql = sql_estado_pagare::get_sql('INPUT');
		
		$this->dws['dw_estado_pagare'] = new datawindow($sql);

		// asigna los formatos		
		$this->dws['dw_estado_pagare']->add_control($control = new edit_text_upper('NOM_ESTADO_PAGARE', 80, 100));		
		$control->set_onChange("valida_nom_estado();");
		// asigna los mandatorys
		$this->dws['dw_estado_pagare']->set_mandatory('NOM_ESTADO_PAGARE', 'Estado Pagaré');
	}
	function new_record() {
		$this->dws['dw_estado_pagare']->insert_row();
	}
	function load_record() {
		$cod_estado_pagare = $this->get_item_wo($this->current_record, 'COD_ESTADO_PAGARE');
		$this->dws['dw_estado_pagare']->retrieve($cod_estado_pagare);
		$this->b_delete_visible = true;
		$this->b_save_visible = true;
		$this->b_no_save_visible = true;
		$this->b_modify_visible = true;
	}
	function get_key() {
		return $this->dws['dw_estado_pagare']->get_item(0, 'COD_ESTADO_PAGARE');
	}
	
	function save_record($db) {
		$COD_ESTADO_PAGARE = $this->get_key();
		$NOM_ESTADO_PAGARE = $this->dws['dw_estado_pagare']->get_item(0, 'NOM_ESTADO_PAGARE');

		
		$COD_ESTADO_PAGARE = ($COD_ESTADO_PAGARE=='') ? "null" : $COD_ESTADO_PAGARE;		
    
		$sp = 'spu_estado_pagare';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    		    
	    $param	= "'$operacion', $COD_ESTADO_PAGARE, '$NOM_ESTADO_PAGARE'";
	    	
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$COD_ESTADO_PAGARE = $db->GET_IDENTITY("estado_pagare");
				$this->dws['dw_estado_pagare']->set_item(0, 'COD_ESTADO_PAGARE', $COD_ESTADO_PAGARE);
			}
			return true;
		}
		return false;		
				
	}
	
}

?>