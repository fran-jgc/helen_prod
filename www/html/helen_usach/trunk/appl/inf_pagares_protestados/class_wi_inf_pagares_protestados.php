<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");

class wi_inf_pagares_protestados extends w_param_informe_helen {
    function __construct($cod_item_menu) {
		$xml = session::get('K_ROOT_DIR').'appl/inf_pagares_protestados/inf_pagares_protestados.xml';
		
		$sql_informe = sql_inf_pagares_protestados::get_sql('INPUT_INF');
		
		parent::__construct('inf_pagares_protestados', $cod_item_menu, 'Pagaré Protestado.pdf', $xml, $sql_informe, 'spi_pagares_protestados');
		
		$fecha = getdate();
		$ano   = $fecha["year"];

		$sql = sql_inf_pagares_protestados::get_sql('INPUT_FECHA_BUSQUEDA', $ano);
		
		$this->dws['dw_param'] = new datawindow($sql);
		
		$this->dws['dw_param']->add_control(new edit_date('FECHA_INICIO'));
		$this->dws['dw_param']->add_control(new edit_date('FECHA_TERMINO'));
		
		// mandatorys		
		$this->dws['dw_param']->set_mandatory('FECHA_INICIO', 'Fecha Inicio');
		$this->dws['dw_param']->set_mandatory('FECHA_TERMINO', 'Fecha Termino');
	}
	function make_filtro() {
	 
		$fecha_inicio	   = $this->dws['dw_param']->get_item(0, 'FECHA_INICIO');
		$fecha_termino	   = $this->dws['dw_param']->get_item(0, 'FECHA_TERMINO');

		// Filtro
		$this->filtro = "Fecha Inicio = $fecha_inicio; Fecha Término = $fecha_termino\n";
		
		$fecha_inicio = $this->str2date($fecha_inicio);
		$fecha_termino = $this->str2date($fecha_termino, '23:59:59');
		$this->param = "$fecha_inicio, $fecha_termino";
	}
	function genera_pdf($labels = array(), $con_logo = true,$orientation='L',$unit='pt',$format='letter') {
		//	echo $this->sql_informe.' '.$this->param;
		parent::genera_pdf($labels, $con_logo,$orientation,$unit,$format);
	}
}
?>