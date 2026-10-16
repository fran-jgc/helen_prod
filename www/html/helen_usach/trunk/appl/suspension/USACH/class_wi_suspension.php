<?php
////////////////////////////////////////
/////////////// USACH //////////////////
////////////////////////////////////////

class wi_suspension extends wi_suspension_base {
    
	const K_PARAM_CARTERA = 2;
	
	function __construct($cod_item_menu) {
	    parent::__construct($cod_item_menu); 
	}
	function new_record(){
		parent::new_record();
		$this->dws['dw_suspension']->set_item(0, 'VISIBLE_CAMPO', 'none');
	}
	function load_record(){
		parent::load_record();
		$cod_suspension = $this->get_item_wo($this->current_record, 'COD_SUSPENSION');
		
		$cartera_defecto = base::get_parametro(self::K_PARAM_CARTERA);
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql ="SELECT 	COUNT(*) CUANTO 
				FROM 	SUSPENSION S, DEUDA DA
				WHERE 	S.COD_DEUDA = DA.COD_DEUDA 
				AND 	COD_SUSPENSION = $cod_suspension
				AND 	COD_CARTERA <> $cartera_defecto";
		$result = $db->build_results($sql);
		if ($result[0]['CUANTO']!= 0)
			$this->dws['dw_suspension']->set_item(0, 'VISIBLE_CAMPO', '');
		else
			$this->dws['dw_suspension']->set_item(0, 'VISIBLE_CAMPO', 'none');
	}
}	
?>