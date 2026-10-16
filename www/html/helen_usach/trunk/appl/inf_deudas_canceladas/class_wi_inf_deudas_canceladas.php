<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");

class wi_inf_deudas_canceladas extends w_param_informe_helen {
	function __construct($cod_item_menu) {
		$xml = session::get('K_ROOT_DIR').'appl/inf_deudas_canceladas/inf_deudas_canceladas.xml';
		$sql_informe = sql_inf_deudas_canceladas::get_sql('INPUT');
		parent::__construct('inf_deudas_canceladas', $cod_item_menu, 'Deudas Canceladas', $xml, $sql_informe, 'spi_deudas_canceladas');

		$fecha = getdate();
  		$ano   = $fecha["year"];

		$sql = sql_inf_deudas_canceladas::get_sql('TIPO_DEUDA_1', $ano);
		
		$this->dws['dw_param'] = new datawindow($sql);

		$sql = sql_inf_deudas_canceladas::get_sql('DD_COD_TIPO_DEUDA');
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_TIPO_DEUDA', $sql));
		$this->dws['dw_param']->add_control(new edit_date('FECHA_INICIO'));
		$this->dws['dw_param']->add_control(new edit_date('FECHA_TERMINO'));

		// mandatorys
		$this->dws['dw_param']->set_mandatory('COD_TIPO_DEUDA', 'Tipo Deuda');
		$this->dws['dw_param']->set_mandatory('FECHA_INICIO', 'Fecha Inicial');
		$this->dws['dw_param']->set_mandatory('FECHA_TERMINO', 'Fecha Final');

	}
	function make_filtro() {

		$cod_tipo_deuda = $this->dws['dw_param']->get_item(0, 'COD_TIPO_DEUDA');
		$fecha_inicial    = $this->dws['dw_param']->get_item(0, 'FECHA_INICIO');
		$fecha_final      = $this->dws['dw_param']->get_item(0, 'FECHA_TERMINO');

		// Filtro
		$nom_tipo_deuda = $this->dws['dw_param']->controls['COD_TIPO_DEUDA']->get_label_from_value($cod_tipo_deuda);
		$this->filtro = "Tipo Deuda = $nom_tipo_deuda\n";
		$this->filtro .= "Fecha Inicial = $fecha_inicial; Fecha final = $fecha_final\n";
		$fecha_inicial = $this->str2date($fecha_inicial);	
		$fecha_final = $this->str2date($fecha_final);	
		// Arma los paramaetros para el SP
		$this->param = "'$cod_tipo_deuda', $fecha_inicial, $fecha_final";
	}
}
?>