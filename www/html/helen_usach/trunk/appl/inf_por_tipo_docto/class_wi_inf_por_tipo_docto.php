<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class wi_inf_por_tipo_docto extends w_param_informe_helen {
    function __construct($cod_item_menu) {
		$xml = session::get('K_ROOT_DIR').'appl/inf_por_tipo_docto/inf_por_tipo_docto.xml';		
		$sql_informe = sql_inf_por_tipo_docto::get_sql('INPUT');
		parent::__construct('inf_por_tipo_docto', $cod_item_menu, 'Tipo Documento', $xml, $sql_informe, 'spi_tipo_documento_pago');
		
		$sql = sql_inf_por_tipo_docto::get_sql('FECHA');
		$this->dws['dw_param'] = new datawindow($sql);
		
		$this->dws['dw_param']->add_control(new edit_date('FECHA_INICIO'));
		$this->dws['dw_param']->add_control(new edit_date('FECHA_TERMINO'));
		$sql = sql_inf_por_tipo_docto::get_sql('DD_COD_ESTADO_DOCUMENTO_PAGO');
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_ESTADO_DOCUMENTO_PAGO', $sql));
		
		$sql = sql_inf_por_tipo_docto::get_sql('DD_COD_TIPO_DOCUMENTO_PAGO');
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_TIPO_DOCUMENTO_PAGO', $sql));
		
		// mandatorys		
		$this->dws['dw_param']->set_mandatory('FECHA_INICIO', 'Fecha Inicio');	
		$this->dws['dw_param']->set_mandatory('FECHA_TERMINO', 'Fecha Termino');	
		$this->dws['dw_param']->set_mandatory('COD_ESTADO_DOCUMENTO_PAGO', 'Estado Documento');
		$this->dws['dw_param']->set_mandatory('COD_TIPO_DOCUMENTO_PAGO', 'Tipo Documento');	
	}
	function make_filtro() {
		$fecha_inicio 				= $this->dws['dw_param']->get_item(0, 'FECHA_INICIO'); 
		$fecha_termino 				= $this->dws['dw_param']->get_item(0, 'FECHA_TERMINO'); 
		$cod_estado_documento_pago 	= $this->dws['dw_param']->get_item(0, 'COD_ESTADO_DOCUMENTO_PAGO'); 
		$cod_tipo_documento_pago 	= $this->dws['dw_param']->get_item(0, 'COD_TIPO_DOCUMENTO_PAGO'); 
		
		// Filtro
		$this->filtro = "Fecha Inicio = $fecha_inicio;\n Fecha Término = $fecha_termino;\n";
	
		$nom_estado_documento_pago = $this->dws['dw_param']->controls['COD_ESTADO_DOCUMENTO_PAGO']->get_label_from_value($cod_estado_documento_pago);
		$this->filtro .= "Estado Documento = $nom_estado_documento_pago;\n";
		
		$nom_tipo_documento_pago = $this->dws['dw_param']->controls['COD_TIPO_DOCUMENTO_PAGO']->get_label_from_value($cod_tipo_documento_pago);
		$this->filtro .= "Tipo Documento = $nom_tipo_documento_pago;";

		$fecha_inicio 	= $this->str2date($fecha_inicio);
		$fecha_termino 	= $this->str2date($fecha_termino);
		
		// Arma los paramaetros para el SP
		$this->param = "$fecha_inicio, $fecha_termino, $cod_estado_documento_pago, $cod_tipo_documento_pago";		
	}
}
?>