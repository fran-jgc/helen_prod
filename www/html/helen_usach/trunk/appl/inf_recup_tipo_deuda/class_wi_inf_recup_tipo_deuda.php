<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");

class wi_inf_recup_tipo_deuda extends w_param_informe_helen {
	function __construct($cod_item_menu) {
		$xml = session::get('K_ROOT_DIR').'appl/inf_recup_tipo_deuda/inf_recup_tipo_deuda.xml';
		
		$sql_informe = sql_inf_recup_tipo_deuda::get_sql('INPUT_INF');
		
		parent::__construct('inf_recup_tipo_deuda', $cod_item_menu, 'Recuperación por Tipo Deuda.pdf', $xml, $sql_informe, 'spi_recup_tipo_deuda');
		
		$fecha = getdate();
		$ano   = $fecha["year"];
		
		$sql = sql_inf_recup_tipo_deuda::get_sql('INPUT_FAMILIA_DEUDA', $ano);
		
		$this->dws['dw_param'] = new datawindow($sql);
		
		$this->dws['dw_param']->add_control(new edit_date('FECHA_INICIO'));
		$this->dws['dw_param']->add_control(new edit_date('FECHA_TERMINO'));
		
		$sql = sql_inf_recup_tipo_deuda::get_sql('INPUT_DD_FAMILIA_DEUDA');
		
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_FAMILIA_DEUDA', $sql));

		// mandatorys		
		$this->dws['dw_param']->set_mandatory('COD_FAMILIA_DEUDA', 'Familia Deuda');	
		$this->dws['dw_param']->set_mandatory('ANO', 'año');	
	}
	function make_filtro() {
	 
		$fecha_inicio	   = $this->dws['dw_param']->get_item(0, 'FECHA_INICIO');
		$fecha_termino	   = $this->dws['dw_param']->get_item(0, 'FECHA_TERMINO');
		$cod_familia_deuda = $this->dws['dw_param']->get_item(0, 'COD_FAMILIA_DEUDA');
				
		// Filtro
		$this->filtro = "Fecha Inicio = $fecha_inicio; Fecha Término = $fecha_termino\n";
		$nom_familia = $this->dws['dw_param']->controls['COD_FAMILIA_DEUDA']->get_label_from_value($cod_familia_deuda);
		$this->filtro .= "Familia Deuda = $nom_familia\n";
		
		$fecha_inicio = $this->str2date($fecha_inicio);
		$fecha_termino = $this->str2date($fecha_termino, '23:59:59');
		$this->param = "$fecha_inicio, $fecha_termino,$cod_familia_deuda";
	}
	
	function genera_pdf($labels = array(), $con_logo = true,$orientation='L',$unit='pt',$format='letter') {
		//	echo $this->sql_informe.' '.$this->param;
		parent::genera_pdf($labels, $con_logo,$orientation,$unit,$format);
	}
}
?>