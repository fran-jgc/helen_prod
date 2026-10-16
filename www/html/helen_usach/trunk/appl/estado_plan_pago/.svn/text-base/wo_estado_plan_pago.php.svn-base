<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");


if (w_output::f_viene_del_menu('estado_plan_pago')) {
	$wo_estado_plan_pago = new wo_estado_plan_pago();
	$wo_estado_plan_pago->retrieve();
}
else {
    $wo = unserialize(session::get('wo_estado_plan_pago'));
	$wo->procesa_event();
}
?>