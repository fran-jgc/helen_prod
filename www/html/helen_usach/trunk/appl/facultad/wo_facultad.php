<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('facultad')) {
	$wo_facultad = new wo_facultad();
	$wo_facultad->retrieve();
}
else {
    $wo = unserialize(session::get('wo_facultad'));
	$wo->procesa_event();
	
}
?>