<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('boleta')) {
	$wo_boleta = new wo_boleta();
	$wo_boleta->retrieve();
}
else {
    $wo = unserialize(session::get('wo_boleta'));
	$wo->procesa_event();
	
}
?>