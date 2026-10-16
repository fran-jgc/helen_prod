<?php
////////////////////////////////////////
/////////////// USACH //////////////////
////////////////////////////////////////

class wi_prorroga extends wi_prorroga_base {
    
	const K_PARAM_CARTERA = 2;
	
	function __construct($cod_item_menu) {
	    parent::__construct($cod_item_menu); 
	}
	function new_record(){
		parent::new_record();
		$this->dws['dw_prorroga']->set_item(0, 'VISIBLE_CAMPO', 'none');
	}
	function load_record(){
		parent::load_record();
		$COD_PRORROGA	= $this->get_item_wo($this->current_record, 'COD_PRORROGA');
		
		$cartera_defecto = base::get_parametro(self::K_PARAM_CARTERA);
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql ="SELECT 	COUNT(*) CUANTO 
				FROM 	PRORROGA P, DEUDA DA
				WHERE 	COD_PRORROGA = $COD_PRORROGA
				AND   	P.COD_DEUDA = DA.COD_DEUDA
				AND   	COD_CARTERA <> $cartera_defecto";
		$result = $db->build_results($sql);
		if ($result[0]['CUANTO']!= 0)
			$this->dws['dw_prorroga']->set_item(0, 'VISIBLE_CAMPO', '');
		else
			$this->dws['dw_prorroga']->set_item(0, 'VISIBLE_CAMPO', 'none');
	}
}	
?>