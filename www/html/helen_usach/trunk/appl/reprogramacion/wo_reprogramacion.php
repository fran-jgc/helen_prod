<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('reprogramacion')) {
	$wo_reprogramacion = new wo_reprogramacion();
	$wo_reprogramacion->retrieve();
}
else {
    $wo = unserialize(session::get('wo_reprogramacion'));
	$wo->procesa_event();
}
?>