<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('descuento')) {
	$wo_descuento = new wo_descuento();
	$wo_descuento->retrieve();
}
else {
    $wo = unserialize(session::get('wo_descuento'));
	$wo->procesa_event();
	
}
?>