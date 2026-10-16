<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");


if (w_output::f_viene_del_menu('carrera')) {
	$wo_carrera = new wo_carrera();
	$wo_carrera->retrieve();
}
else {
    $wo = unserialize(session::get('wo_carrera'));
	$wo->procesa_event();
	
}
?>