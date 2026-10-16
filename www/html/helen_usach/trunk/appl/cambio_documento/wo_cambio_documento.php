<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('cambio_documento')) {
	$wo_cambio_documento = new wo_cambio_documento();
	$wo_cambio_documento->retrieve();
}
else {
    $wo = unserialize(session::get('wo_cambio_documento'));
	$wo->procesa_event();
	
}
?>