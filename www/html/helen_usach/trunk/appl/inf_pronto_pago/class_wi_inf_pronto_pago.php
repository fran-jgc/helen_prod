<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");

class wi_inf_pronto_pago extends w_param_informe_helen {
    function __construct($cod_item_menu) {
		$xml = session::get('K_ROOT_DIR').'appl/inf_pronto_pago/inf_pronto_pago.xml';
		
		$sql_informe = sql_inf_pronto_pago::get_sql('INPUT');		
			
		$fecha = getdate();
		$ano   = $fecha["year"];
		
		parent::__construct('inf_pronto_pago', $cod_item_menu, 'Pronto Pago.pdf', $xml, $sql_informe, 'spi_pronto_pago');
		
		$sql = sql_inf_pronto_pago::get_sql('INPUT_FECHA', $ano);
		
		$this->dws['dw_param'] = new datawindow($sql);
		
		$sql = sql_inf_pronto_pago::get_sql('INPUT_DD_DEUDA');
		
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_TIPO_DEUDA', $sql));
		$this->dws['dw_param']->add_control(new edit_date('FECHA_INICIAL', 10, 10));
		$this->dws['dw_param']->add_control(new edit_date('FECHA_FINAL', 10, 10));

		// mandatorys		
		$this->dws['dw_param']->set_mandatory('COD_TIPO_DEUDA', 'Tipo Deuda');	
		$this->dws['dw_param']->set_mandatory('FECHA_INICIAL', 'Fecha Inicial');
		$this->dws['dw_param']->set_mandatory('FECHA_FINAL', 'Fecha Final');	
	}
	function make_filtro() {
	 
		$cod_tipo_deuda = $this->dws['dw_param']->get_item(0, 'COD_TIPO_DEUDA');
		$fecha_inicial    = $this->dws['dw_param']->get_item(0, 'FECHA_INICIAL');
		$fecha_final      = $this->dws['dw_param']->get_item(0, 'FECHA_FINAL');
				
		// Filtro
		$nom_tipo_deuda = $this->dws['dw_param']->controls['COD_TIPO_DEUDA']->get_label_from_value($cod_tipo_deuda);
		$this->filtro = "Tipo Deuda = $nom_tipo_deuda\n";
		$this->filtro .= "Fecha Inicial = $fecha_inicial; Fecha final = $fecha_final\n";
		
		// Arma los parametros para el SP
		$fecha_inicial = $this->str2date($fecha_inicial);
		$fecha_final = $this->str2date($fecha_final, '23:59:59');
		$this->param = "$fecha_inicial, $fecha_final,'$cod_tipo_deuda'";
		//echo $this->param;
	}	
	function genera_pdf($labels = array(), $con_logo = true,$orientation='L',$unit='pt',$format='letter') {
		//	echo $this->sql_informe.' '.$this->param;
		parent::genera_pdf($labels, $con_logo,$orientation,$unit,$format);
	}
}
?>