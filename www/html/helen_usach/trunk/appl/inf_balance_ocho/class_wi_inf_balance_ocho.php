<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");

class wi_inf_balance_ocho extends w_param_informe_helen {
	function __construct($cod_item_menu) {
		$xml = session::get('K_ROOT_DIR').'appl/inf_balance_ocho/inf_balance_ocho.xml';
		
		$sql_informe = sql_inf_balance_ocho::get_sql('INPUT');
		
		parent::__construct('inf_balance_ocho', $cod_item_menu, 'Balance ocho columnas', $xml, $sql_informe, 'spi_balance_ocho');
		
		$sql = sql_inf_balance_ocho::get_sql('FECHA');
		
		$this->dws['dw_param'] = new datawindow($sql);
		
		$this->dws['dw_param']->add_control(new edit_date('FECHA'));
		$sql_cod_fam = sql_inf_balance_ocho::get_sql('DD_COD_FAMILIA_DEUDA');
		if(K_CLIENTE == 'UCM'){
			/*para UCM1 solo pueden pedir famiula 1 y 2*/
			$sql_cod_fam = "select	 COD_FAMILIA_DEUDA
									,NOM_FAMILIA_DEUDA 
							from	FAMILIA_DEUDA
							where COD_FAMILIA_DEUDA <> 3
							order by 	COD_FAMILIA_DEUDA";
		}else if(K_CLIENTE == 'UCM2'){
			/*para UCM2 solo familia 3*/
			$sql_cod_fam = "select	 COD_FAMILIA_DEUDA
									,NOM_FAMILIA_DEUDA 
							from	FAMILIA_DEUDA
							where COD_FAMILIA_DEUDA = 3
							order by 	COD_FAMILIA_DEUDA";
		}
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_FAMILIA_DEUDA', $sql_cod_fam));
		$this->dws['dw_param']->add_control(new edit_check_box('APROBADO', 'S', 'N', 'Aprobado'));
		$this->dws['dw_param']->add_control(new edit_check_box('PENDIENTE', 'S', 'N', 'Pendiente'));

		// mandatorys		
		$this->dws['dw_param']->set_mandatory('FECHA', 'Fecha');	
		$this->dws['dw_param']->set_mandatory('COD_FAMILIA_DEUDA', 'Familia Deuda');	
	}
	function make_filtro() {
		$fecha = $this->dws['dw_param']->get_item(0, 'FECHA'); 
		$cod_familia_deuda = $this->dws['dw_param']->get_item(0, 'COD_FAMILIA_DEUDA'); 
		$aprobado = $this->dws['dw_param']->get_item(0, 'APROBADO'); 
		$pendiente = $this->dws['dw_param']->get_item(0, 'PENDIENTE'); 
		
		// Filtro
		$this->filtro = "Fecha = $fecha;\nEstado = ";
		if ($aprobado=='S')
			$this->filtro .= "Contabilizado, ";  		
		if ($pendiente=='S')
			$this->filtro .= "Aprobado;\n";
	
		$nom_familia = $this->dws['dw_param']->controls['COD_FAMILIA_DEUDA']->get_label_from_value($cod_familia_deuda);
		$this->filtro .= "Familia Deuda = $nom_familia";

		$fecha = $this->str2date($fecha, '23:59:59');
		// Arma los paramaetros para el SP
		$this->param = "$fecha, $cod_familia_deuda, '$aprobado', '$pendiente'";		
	}
	function genera_pdf($labels = array(), $con_logo = true,$orientation='L',$unit='pt',$format='letter') {
		parent::genera_pdf($labels, $con_logo,$orientation, $unit,$format);
	}
}
?>