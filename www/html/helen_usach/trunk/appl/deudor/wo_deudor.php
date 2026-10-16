<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('deudor')) {
	$wo_deudor = new wo_deudor();
	$wo_deudor->retrieve();
}
else {
	$wo = unserialize(session::get('wo_deudor'));
	$wo->procesa_event();
}
?>