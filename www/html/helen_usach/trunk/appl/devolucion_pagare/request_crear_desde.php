<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
$temp = new Template_appl('request_crear_desde.htm');	
$entrable = true;

$sql = "select	'' RUT_DEUDOR
			from dual";


$dw_add_dev = new datawindow($sql);

$dw_add_dev->add_control($control = new edit_text('RUT_DEUDOR',20,20));
//$control->set_onChange("f_get_info_seleccion();");

$dw_add_dev->insert_row();

$dw_add_dev->habilitar($temp, $entrable);
print $temp->toString();



?>