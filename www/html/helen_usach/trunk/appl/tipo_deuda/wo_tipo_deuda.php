<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('tipo_deuda')) {
	$wo_tipo_deuda = new wo_tipo_deuda();
	$wo_tipo_deuda->retrieve();
}
else {
    $wo = unserialize(session::get('wo_tipo_deuda'));
	$wo->procesa_event();
	
}
?>