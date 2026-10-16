<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('novacion')) {
	$wo_novacion = new wo_novacion();
	$wo_novacion->retrieve();
}
else {
    $wo = unserialize(session::get('wo_novacion'));
	$wo->procesa_event();
	
}
?>