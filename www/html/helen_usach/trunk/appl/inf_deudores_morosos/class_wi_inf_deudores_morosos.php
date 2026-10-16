<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

ini_set('memory_limit', '480M');
ini_set('max_execution_time', 900); //900 seconds = 15 minutes
if (K_TIPO_BD=="mssql"){
	ini_set('mssql.connect_timeout','300');
	ini_set('mssql.timeout','300');
}

class wi_inf_deudores_morosos extends w_param_informe_helen {
    function __construct($cod_item_menu) {
		$xml = session::get('K_ROOT_DIR').'appl/inf_deudores_morosos/inf_deudores_morosos.xml';
		$sql_informe = sql_inf_deudores_morosos::get_sql('INPUT');
		parent::__construct('inf_deudores_morosos', $cod_item_menu, 'Deudores Morosos.pdf', $xml, $sql_informe, 'spi_deudores_morosos');
		
		$sql = sql_inf_deudores_morosos::get_sql('FECHA');
		
		$this->dws['dw_param'] = new datawindow($sql);
	
		$sql = sql_inf_deudores_morosos::get_sql('DD_COD_TIPO_DEUDA');
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_TIPO_DEUDA', $sql));
		$this->dws['dw_param']->add_control(new edit_date('FECHA'));
		
		// mandatorys		
		$this->dws['dw_param']->set_mandatory('COD_TIPO_DEUDA', 'Tipo Deuda');
		$this->dws['dw_param']->set_mandatory('FECHA', 'Fecha');				
	}
	function make_filtro() {
	 	
		$cod_tipo_deuda = $this->dws['dw_param']->get_item(0, 'COD_TIPO_DEUDA');
		$fecha		    = $this->dws['dw_param']->get_item(0, 'FECHA'); 
				
		// Filtro
		$nom_tipo_deuda = $this->dws['dw_param']->controls['COD_TIPO_DEUDA']->get_label_from_value($cod_tipo_deuda);
		$this->filtro = "Tipo Deuda = $nom_tipo_deuda\n";
		$this->filtro .= "Fecha = $fecha\n";
			
		$fecha = $this->str2date($fecha, '23:59:59');		
		// Arma los paramaetros para el SP
		$this->param = "'$cod_tipo_deuda',$fecha";
	}
}
?>