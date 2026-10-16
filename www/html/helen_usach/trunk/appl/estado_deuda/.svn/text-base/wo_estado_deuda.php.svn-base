<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");


if (w_output::f_viene_del_menu('estado_deuda')) {
	$wo_estado_deuda = new wo_estado_deuda();
	$wo_estado_deuda->retrieve();
}
else {
    $wo = unserialize(session::get('wo_estado_deuda'));
	$wo->procesa_event();
}
?>