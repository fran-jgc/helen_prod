<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");


if (w_output::f_viene_del_menu('origen_pago')) {
	$wo_origen_pago = new wo_origen_pago();
	$wo_origen_pago->retrieve();
}
else {
    $wo = unserialize(session::get('wo_origen_pago'));
	$wo->procesa_event();
	
}
?>