<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

ini_set('memory_limit', '480M');
ini_set('max_execution_time', 900); //900 seconds = 15 minutes


class wi_inf_deudores_con_excedentes extends w_param_informe_helen{
    function __construct($cod_item_menu) {
		$xml = session::get('K_ROOT_DIR').'appl/inf_deudores_con_excedentes/inf_deudores_con_excedentes.xml';
		$sql_informe = sql_inf_deudores_con_excedentes::get_sql('INPUT');
		parent::__construct('inf_deudores_con_excedentes', $cod_item_menu, 'Deudores con Excedentes', $xml, $sql_informe, 'spi_deudores_con_excedentes');

		$fecha = getdate();
  		$ano   = $fecha["year"];
  		$sql = sql_inf_deudores_con_excedentes::get_sql('TIPO_DEUDA', $ano);
		
		$this->dws['dw_param'] = new datawindow($sql);
		
		$this->dws['dw_param']->add_control(new edit_date('FECHA_INICIO'));
		$this->dws['dw_param']->add_control(new edit_date('FECHA_TERMINO'));
		
		$sql =  sql_inf_deudores_con_excedentes::get_sql('DD_COD_ESTADO_EXCEDENTE');
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_ESTADO_EXCEDENTE', $sql));	
	
		// mandatorys		
		$this->dws['dw_param']->set_mandatory('FECHA_INICIO', 'Fecha Inicio');	
		$this->dws['dw_param']->set_mandatory('FECHA_TERMINO', 'Fecha Termino');	
		$this->dws['dw_param']->set_mandatory('COD_ESTADO_EXCEDENTE', 'Estado');
	}
	function make_filtro() {
		
		$fecha_inicio 	= $this->dws['dw_param']->get_item(0, 'FECHA_INICIO'); 
		$fecha_termino 	= $this->dws['dw_param']->get_item(0, 'FECHA_TERMINO'); 
		$estado 		= $this->dws['dw_param']->get_item(0, 'COD_ESTADO_EXCEDENTE'); 
		
		// Filtro
		$nom_estado = $this->dws['dw_param']->controls['COD_ESTADO_EXCEDENTE']->get_label_from_value($estado);
		$this->filtro = "Fecha Inicio = $fecha_inicio\n Fecha Término = $fecha_termino\nEstado = ";
		$this->filtro .= "$nom_estado";
		$fecha_inicio = $this->str2date($fecha_inicio);	
		$fecha_termino = $this->str2date($fecha_termino);				
		// Arma los paramaetros para el SP		
		$this->param = "$fecha_inicio,$fecha_termino,$estado";	
	}
	
	function genera_pdf($labels = array(), $con_logo = true,$orientation='P',$unit='pt',$format='letter') {
		$labels['str_filtro'] = $this->filtro;

		$sql_informe = $this->sql_informe;
		$orientation = 'L';
		$rpt = new reporte_helen($sql_informe, $this->xml, $labels, $this->nom_informe.'.pdf', $con_logo, true, $this->sp, $this->param,$orientation,$unit,$format);
	}
}
?>
