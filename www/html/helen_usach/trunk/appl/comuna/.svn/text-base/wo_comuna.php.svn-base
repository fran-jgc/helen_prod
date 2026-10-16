<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('comuna')) {
	$wo_comuna = new wo_comuna();
	$wo_comuna->retrieve();
}
else {
    $wo = unserialize(session::get('wo_comuna'));
	$wo->procesa_event();
	
}
?>