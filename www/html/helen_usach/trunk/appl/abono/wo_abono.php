<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('abono')) {
	$wo_abono = new wo_abono();
	$wo_abono->retrieve();
}
else {
	$wo = unserialize(session::get('wo_abono'));
	$wo->procesa_event();
	
}
?>