<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('renegociacion')) {
	$wo_renegociacion = new wo_renegociacion();
	$wo_renegociacion->retrieve();
}
else {
    $wo = unserialize(session::get('wo_renegociacion'));
	$wo->procesa_event();
	
}
?>