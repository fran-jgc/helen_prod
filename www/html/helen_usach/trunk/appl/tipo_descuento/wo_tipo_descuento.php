<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");


if (w_output::f_viene_del_menu('tipo_descuento')) {
	$wo_tipo_descuento = new wo_tipo_descuento();
	$wo_tipo_descuento->retrieve();
}
else {
    $wo = unserialize(session::get('wo_tipo_descuento'));
	$wo->procesa_event();
	
}
?>