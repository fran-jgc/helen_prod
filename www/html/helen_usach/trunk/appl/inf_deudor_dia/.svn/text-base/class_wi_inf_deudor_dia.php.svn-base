<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

ini_set('memory_limit', '480M');
ini_set('max_execution_time', 900); //900 seconds = 15 minutes

class wi_inf_deudor_dia extends w_param_informe_helen {
    function __construct($cod_item_menu) {
		$xml = session::get('K_ROOT_DIR').'appl/inf_deudor_dia/inf_deudor_dia.xml';
		$sql_informe = sql_inf_deudor_dia::get_sql('INPUT');
		parent::__construct('inf_deudor_dia', $cod_item_menu, 'Deudores al día', $xml, $sql_informe, 'spi_deudor_dia');
		$sql = sql_inf_deudor_dia::get_sql('TIPO_DEUDA');
		$this->dws['dw_param'] = new datawindow($sql);
		$sql = sql_inf_deudor_dia::get_sql('DD_COD_TIPO_DEUDA');
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_TIPO_DEUDA', $sql));

		// mandatorys		
		$this->dws['dw_param']->set_mandatory('COD_TIPO_DEUDA', 'Tipo Deuda');	
	}
	function make_filtro() {
	 
		$cod_tipo_deuda = $this->dws['dw_param']->get_item(0, 'COD_TIPO_DEUDA');
				
		// Filtro
		$nom_tipo_deuda = $this->dws['dw_param']->controls['COD_TIPO_DEUDA']->get_label_from_value($cod_tipo_deuda);
		$this->filtro = "Tipo Deuda = $nom_tipo_deuda\n";
		
		// Arma los parametros para el SP
		$this->param = "'$cod_tipo_deuda'";	
	}	
}
?>