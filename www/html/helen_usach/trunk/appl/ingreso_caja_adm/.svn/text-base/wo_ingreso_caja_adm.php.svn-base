<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('ingreso_caja_adm')) {
	$wo = new wo_ingreso_caja_adm();
	$wo->retrieve();
}
else {
    $wo = unserialize(session::get('wo_ingreso_caja_adm'));
	$wo->procesa_event();
}
?>