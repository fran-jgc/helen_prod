<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('deuda')) {
	$wo_deuda = new wo_deuda();
	$wo_deuda->retrieve();
}
else {
	$wo = unserialize(session::get('wo_deuda'));
	$wo->procesa_event();
	
}
?>