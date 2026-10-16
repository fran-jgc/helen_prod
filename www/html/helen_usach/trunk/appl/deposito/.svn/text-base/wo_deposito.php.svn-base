<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('deposito')) {
	$wo_deposito = new wo_deposito();
	$wo_deposito->retrieve();
}
else {
    $wo = unserialize(session::get('wo_deposito'));
	$wo->procesa_event();
	
}
?>
