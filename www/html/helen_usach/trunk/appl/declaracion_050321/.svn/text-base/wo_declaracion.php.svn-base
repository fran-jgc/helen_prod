<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('declaracion')) {
	$wo_declaracion = new wo_declaracion();
	$wo_declaracion->retrieve();
}
else {
    $wo = unserialize(session::get('wo_declaracion'));
	$wo->procesa_event();
}
?>