<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class wi_inf_planilla_caja extends w_param_informe_helen {
	function wi_inf_planilla_caja($cod_item_menu) {
		$xml = session::get('K_ROOT_DIR').'appl/inf_planilla_caja/inf_planilla_caja.xml';
		$sql_informe = sql_inf_planilla_caja::get_sql('SQL_REPORTE');
		parent::w_param_informe('inf_planilla_caja', $cod_item_menu, 'Informe planilla diaria de caja', $xml, $sql_informe, 'spi_planilla_caja');
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
  		
		$sql = sql_inf_planilla_caja::get_sql('INPUT_PARAM');
		$this->dws['dw_param'] = new datawindow($sql);
		
		$this->dws['dw_param']->add_control(new edit_date('FECHA_INICIO'));
		$this->dws['dw_param']->add_control(new edit_date('FECHA_TERMINO'));
	}
	function make_filtro() {
		$fecha_inicio 	= $this->dws['dw_param']->get_item(0, 'FECHA_INICIO');
		$fecha_termino 	= $this->dws['dw_param']->get_item(0, 'FECHA_TERMINO');

		// Filtro
		$nom_usuario = $this->nom_usuario;
		$this->filtro  = "Desde:  $fecha_inicio\r\r";
		$this->filtro .= "Hasta:  $fecha_termino\r\r\r\r\r\r";
		$this->filtro .= "Cajero:  $nom_usuario\n";
		
		// Arma los parametros para el SP
		$cod_usuario = $this->cod_usuario;
		$fecha_inicio = $this->str2date($fecha_inicio);
		$fecha_termino = $this->str2date($fecha_termino, '23:59:59');
		$this->param = "$cod_usuario, $fecha_inicio, $fecha_termino";
	}
}
?>