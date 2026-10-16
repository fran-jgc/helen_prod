<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");

class wi_inf_recaudacion_entidad extends w_param_informe_helen {
    function __construct($cod_item_menu) {
		$xml = session::get('K_ROOT_DIR').'appl/inf_recaudacion_entidad/inf_recaudacion_entidad.xml';
		$sql_informe = sql_inf_recaudacion_entidad::get_sql('INPUT_RECAUDACION');
		parent::__construct('inf_recaudacion_entidad', $cod_item_menu, 'Recaudación por Entidad.pdf', $xml, $sql_informe, 'spi_recaudacion_entidad');
		
		$fecha = getdate();
  		$ano   = $fecha["year"];
  		
		$sql = sql_inf_recaudacion_entidad::get_sql('INPUT_FECHA',$ano);
		
		$this->dws['dw_param'] = new datawindow($sql);
		
		$this->dws['dw_param']->add_control(new edit_date('FECHA_INICIO', 10, 10));
		$this->dws['dw_param']->add_control(new edit_date('FECHA_TERMINO', 10, 10));
		
		$sql = sql_inf_recaudacion_entidad::get_sql('INPUT_DD_PAGO');
		
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_ORIGEN_PAGO', $sql));
		
		$sql = sql_inf_recaudacion_entidad::get_sql('TIPO_DEUDA');
		
		$sql = sql_inf_recaudacion_entidad::get_sql('INPUT_DD_DEUDA');
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_TIPO_DEUDA', $sql));
		
		// mandatorys		
		$this->dws['dw_param']->set_mandatory('FECHA_INICIO', 'Fecha Inicio');
		$this->dws['dw_param']->set_mandatory('FECHA_TERMINO', 'Fecha Termino');
		$this->dws['dw_param']->set_mandatory('COD_TIPO_DEUDA', 'Tipo Deuda');
		$this->dws['dw_param']->set_mandatory('COD_ORIGEN_PAGO', 'Origen Pago');
	}
	function make_filtro() {
	 
		$ano_inicial     = $this->dws['dw_param']->get_item(0, 'FECHA_INICIO');
		$ano_final       = $this->dws['dw_param']->get_item(0, 'FECHA_TERMINO');
		$cod_tipo_deuda  = $this->dws['dw_param']->get_item(0, 'COD_TIPO_DEUDA');		
		$cod_origen_pago = $this->dws['dw_param']->get_item(0, 'COD_ORIGEN_PAGO');
		
		// Filtro
		$nom_tipo_deuda  = $this->dws['dw_param']->controls['COD_TIPO_DEUDA']->get_label_from_value($cod_tipo_deuda);
		$nom_origen_pago = $this->dws['dw_param']->controls['COD_ORIGEN_PAGO']->get_label_from_value($cod_origen_pago);
		
		$this->filtro = "año Inicial = $ano_inicial; año Final = $ano_final\n";
		$this->filtro .= "Tipo Deuda = $nom_tipo_deuda\n";
		$this->filtro .= "Origen Pago = $nom_origen_pago\n";
		
		$ano_inicial=$this->str2date($ano_inicial);
		$ano_final=$this->str2date($ano_final);
		
		// Arma los parametros para el SP
		$this->param = "$ano_inicial, $ano_final, $cod_origen_pago, '$cod_tipo_deuda'";			
	}	
}
?>