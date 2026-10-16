<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");

/*
Clase : WI_ORIGEN_PAGO
*/
class wi_origen_pago extends wi_origen_pago_base {
	function __construct($cod_item_menu){
	    parent::__construct($cod_item_menu);
	}
	
	function new_record(){
		$this->dws['dw_origen_pago']->insert_row();
		$this->dws['dw_origen_pago']->set_entrable('COD_ORIGEN_PAGO', false);
		$this->b_modify_visible	 = true;
		$this->b_save_visible = true;
		$this->b_no_save_visible = true;
	}
	
	function load_record() {
		$cod_origen_pago = $this->get_item_wo($this->current_record, 'COD_ORIGEN_PAGO');
		$this->dws['dw_origen_pago']->retrieve($cod_origen_pago);
		$this->dws['dw_origen_pago']->set_entrable('COD_ORIGEN_PAGO', false);
		
		if($cod_origen_pago > 99){
			$this->b_save_visible = true;
			$this->b_no_save_visible = true;
			$this->b_modify_visible = true;	
		}else{
			$this->b_save_visible = false;
			$this->b_no_save_visible = false;
			$this->b_modify_visible = false;
		}
	}
}
?>