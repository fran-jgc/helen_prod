<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('plan_pago')) {
	$wo_plan_pago = new wo_plan_pago();
	$wo_plan_pago->retrieve();
}
else {
	$wo = unserialize(session::get('wo_plan_pago'));
	$wo->procesa_event();
	
}
?>