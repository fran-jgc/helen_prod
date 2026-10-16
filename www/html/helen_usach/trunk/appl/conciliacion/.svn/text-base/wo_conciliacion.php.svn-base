<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('conciliacion')) {
	$wo_conciliacion = new wo_conciliacion();
	$wo_conciliacion->retrieve();
}
else {
    $wo = unserialize(session::get('wo_conciliacion'));
	$wo->procesa_event();
	
}
?>