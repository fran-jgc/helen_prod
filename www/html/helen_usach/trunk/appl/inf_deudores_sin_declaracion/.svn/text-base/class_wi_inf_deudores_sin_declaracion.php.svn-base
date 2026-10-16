<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_param_informe_helen.php");
require_once(dirname(__FILE__)."/../common_appl/class_reporte_helen.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

ini_set('memory_limit', '480M');
ini_set('max_execution_time', 900); //900 seconds = 15 minutes

class wi_inf_deudores_sin_declaracion_base extends w_param_informe_helen {
    function __construct($cod_item_menu) {
		$xml = session::get('K_ROOT_DIR').'appl/inf_deudores_sin_declaracion/inf_deudores_sin_declaracion.xml';
		$sql_informe = sql_inf_deudores_sin_declaracion::get_sql('INPUT');
		parent::__construct('inf_deudores_sin_declaracion', $cod_item_menu, 'Deudores sin Declaración', $xml, $sql_informe, 'spi_deudores_sin_declaracion');
	 
		
		$sql = sql_inf_deudores_sin_declaracion::get_sql('FECHA');
		$this->dws['dw_param'] = new datawindow($sql);
		$this->dws['dw_param']->add_control(new edit_num('ANO', 4, 4));
		
		$sql2 = sql_inf_deudores_sin_declaracion::get_sql('INPUT_DD_FAMILIA_DEUDA');
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_FAMILIA_DEUDA', $sql2));

		// mandatorys
		$this->dws['dw_param']->set_mandatory('ANO', 'Año');
		$this->dws['dw_param']->set_mandatory('COD_FAMILIA_DEUDA', 'Familia Deuda');
	}
		
	function make_filtro() {
	 	$ano = $this->dws['dw_param']->get_item(0, 'ANO');
	 	$cod_familia_deuda 	= $this->dws['dw_param']->get_item(0, 'COD_FAMILIA_DEUDA');
		
		// Filtro
		$this->filtro = "Año = $ano\n";
		
		
		// Arma los parametros para el SP
		$this->param = " $ano,$cod_familia_deuda";			
	}
	
	
	function genera_pdf($labels = array(), $con_logo = true,$orientation='L',$unit='pt',$format='letter') {
		parent::genera_pdf($labels, $con_logo,$orientation, $unit,$format);
	}	
}

/////////////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wi_inf_deudores_sin_declaracion.php";
if (file_exists($file_name))
    require_once($file_name);
else {
    class wi_inf_deudores_sin_declaracion extends wi_inf_deudores_sin_declaracion_base {
        function __construct($cod_item_menu) {
            parent::__construct($cod_item_menu);
        }
    }
} 
?>