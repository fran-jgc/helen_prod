<?php
////////////////////////////////////////
/////////////// USACH //////////////////
////////////////////////////////////////

class wi_novacion extends wi_novacion_base {
    
	const K_PARAM_CARTERA = 2;
	
	function __construct($cod_item_menu) {
	    parent::__construct($cod_item_menu);
		
		$this->dws['dw_novacion']->add_control($control = new edit_rut('RUT', 10, 10));
		$control->set_onChange("get_datos_deudor();");
	}
	function new_record(){
		parent::new_record();
		$this->dws['dw_novacion']->set_item(0, 'VISIBLE_CAMPO', 'none');
	}
	function load_record(){
		parent::load_record();
		$cod_novacion = $this->get_item_wo($this->current_record, 'COD_NOVACION');
		
		$cartera_defecto = base::get_parametro(self::K_PARAM_CARTERA);
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql ="SELECT 	count(*) CUANTO 
				FROM 	novacion n, deuda d
				WHERE 	n.cod_deuda_antigua = d.cod_deuda
				AND 	n.cod_novacion = $cod_novacion
				AND 	d.cod_cartera <> $cartera_defecto";
		$result = $db->build_results($sql);
		if ($result[0]['CUANTO']!= 0)
			$this->dws['dw_novacion']->set_item(0, 'VISIBLE_CAMPO', '');
		else
			$this->dws['dw_novacion']->set_item(0, 'VISIBLE_CAMPO', 'none');
	}
}
?>