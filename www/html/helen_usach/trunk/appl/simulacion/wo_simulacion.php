<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('simulacion')) {
	$wo_simulacion = new wo_simulacion();
	$wo_simulacion->retrieve();
}
else {
    $wo = unserialize(session::get('wo_simulacion'));
	$wo->procesa_event();
}
?>