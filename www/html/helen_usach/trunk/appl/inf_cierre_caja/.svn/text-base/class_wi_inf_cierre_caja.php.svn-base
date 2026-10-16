<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");

class wi_inf_cierre_caja extends w_param_informe_helen {
	function wi_inf_cierre_caja($cod_item_menu) {
		$xml = session::get('K_ROOT_DIR').'appl/inf_cierre_caja/inf_cierre_caja.xml';
		$sql_informe = sql_inf_cierre_caja::get_sql('INPUT');
		parent::w_param_informe('inf_cierre_caja', $cod_item_menu, 'Cierre Caja', $xml, $sql_informe, 'spi_cierre_caja');
		
		$sql = sql_inf_cierre_caja::get_sql('ORIGEN');
		
		$this->dws['dw_param'] = new datawindow($sql);
		
		$sql = sql_inf_cierre_caja::get_sql('DD_COD_ORIGEN_PAGO');
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_ORIGEN_PAGO', $sql));
		$this->dws['dw_param']->add_control(new edit_date('FECHA_INICIAL'));
		$this->dws['dw_param']->add_control(new edit_date('FECHA_FINAL'));

		// mandatorys		
		$this->dws['dw_param']->set_mandatory('COD_ORIGEN_PAGO', 'Tipo Pago');	
		$this->dws['dw_param']->set_mandatory('ANO_INICIAL', 'Fecha Inicial');
		$this->dws['dw_param']->set_mandatory('ANO_FINAL', 'Fecha Final');	
	}
	function make_filtro() {
	 
		$cod_origen_pago = $this->dws['dw_param']->get_item(0, 'COD_ORIGEN_PAGO');
		$ano_inicial    = $this->dws['dw_param']->get_item(0, 'FECHA_INICIAL');
		$ano_final      = $this->dws['dw_param']->get_item(0, 'FECHA_FINAL');
				
		// Filtro
		$nom_origen_pago = $this->dws['dw_param']->controls['COD_ORIGEN_PAGO']->get_label_from_value($cod_origen_pago);
		$this->filtro = "Tipo Origen Pago = $nom_origen_pago\n";
		$this->filtro .= "Fecha Inicial = $ano_inicial; Fecha Final = $ano_final\n";
		
		$ano_inicial = $this->str2date($ano_inicial);	
		$ano_final = $this->str2date($ano_final);		
		// Arma los parametros para el SP
		$this->param = " '$cod_origen_pago',$ano_inicial,$ano_final";			
	}	
}
?>