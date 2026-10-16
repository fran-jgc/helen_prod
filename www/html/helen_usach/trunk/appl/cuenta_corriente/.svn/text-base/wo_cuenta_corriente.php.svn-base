<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('cuenta_corriente')) {
	$wo_cuenta_corriente = new wo_cuenta_corriente();
	$wo_cuenta_corriente->retrieve();
}
else {
    $wo = unserialize(session::get('wo_cuenta_corriente'));
	$wo->procesa_event();
	
}
?>