<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('ingreso')) {
	$wo_ingreso = new wo_ingreso();
	$wo_ingreso->retrieve();
}
else {
    $wo = unserialize(session::get('wo_ingreso'));
	$wo->procesa_event();
}
?>