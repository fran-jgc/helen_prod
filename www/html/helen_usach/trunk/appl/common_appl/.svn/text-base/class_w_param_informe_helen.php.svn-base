<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class w_param_informe_helen extends w_param_informe {
	function __construct($nom_tabla, $cod_item_menu, $nom_informe, $xml, $sql_informe, $sp='') {
	    parent::__construct($nom_tabla, $cod_item_menu, $nom_informe, $xml, $sql_informe, $sp);
	}
	function genera_pdf($labels = array(), $con_logo = true,$orientation='P',$unit='pt',$format='letter') {
		$labels['str_filtro'] = $this->filtro;

		$sql_informe = $this->sql_informe;
		$rpt = new reporte_helen($sql_informe, $this->xml, $labels, $this->nom_informe, $con_logo, true, $this->sp, $this->param,$orientation,$unit,$format);
	}
}
?>