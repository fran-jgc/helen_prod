<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");

class wi_inf_nomina_comercial extends w_param_informe_helen {
    function __construct($cod_item_menu) {
		$xml = session::get('K_ROOT_DIR').'appl/inf_nomina_comercial/inf_nomina_comercial.xml';
		$sql_informe = sql_inf_nomina_comercial::get_sql('INPUT');
		parent::__construct('inf_nomina_comercial', $cod_item_menu, 'Informe CTY', $xml, $sql_informe, 'spi_informe_cty');
		
		$sql = sql_inf_nomina_comercial::get_sql('INPUT');
		
		$this->dws['nomina_comercial'] = new datawindow($sql);
		
		$sql = sql_inf_nomina_comercial::get_sql('DD_COD_TIPO_DEUDA');
		$this->dws['nomina_comercial']->add_control(new drop_down_dw('COD_TIPO_DEUDA', $sql));
		$this->dws['nomina_comercial']->add_control(new edit_num('ANIO', 4, 4));
		
		// mandatorys		
		$this->dws['nomina_comercial']->set_mandatory('COD_TIPO_DEUDA', 'Tipo Deuda');	
		$this->dws['nomina_comercial']->set_mandatory('ANIO', 'Año');	
		
	}
	function make_filtro() {
	 
		$cod_cartera = $this->dws['dw_param']->get_item(0, 'COD_CARTERA');
		$ano   		 = $this->dws['dw_param']->get_item(0, 'ANO');
				
		// Filtro
		$nom_cartera = $this->dws['dw_param']->controls['COD_CARTERA']->get_label_from_value($cod_cartera);
		$this->filtro = "Cartera = $nom_cartera\n";
		$this->filtro .= "Año = $ano\n";
		
		// Arma los parametros para el SP
		$this->param = "$cod_cartera, $ano";
		}
}
?>