<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
$temp = new Template_appl('request_crear_desde.htm');	
$entrable = true;

$sql = "select	'' RUT_DEUDOR
					,null  NRO_FOLIO
			from dual";


$dw_reprogramacion = new datawindow($sql);

$dw_reprogramacion->add_control($control = new edit_text('RUT_DEUDOR',20,20));
$dw_reprogramacion->add_control($control = new edit_text('NRO_FOLIO',20,20));
//$control->set_onChange("f_get_info_seleccion();");

$dw_reprogramacion->insert_row();

$dw_reprogramacion->habilitar($temp, $entrable);
print $temp->toString();
?>