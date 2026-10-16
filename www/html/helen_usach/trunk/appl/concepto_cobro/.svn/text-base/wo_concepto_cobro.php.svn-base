<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");


if (w_output::f_viene_del_menu('concepto_cobro')) {
	$wo_concepto_cobro = new wo_concepto_cobro();
	$wo_concepto_cobro->retrieve();
}
else {
    $wo = unserialize(session::get('wo_concepto_cobro'));
	$wo->procesa_event();
	
}
?>