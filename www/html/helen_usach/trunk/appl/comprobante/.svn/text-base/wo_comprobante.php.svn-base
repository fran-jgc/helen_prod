<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('comprobante')) {
	$wo_comprobante = new wo_comprobante();
	$wo_comprobante->retrieve();
}
else {
    $wo = unserialize(session::get('wo_comprobante'));
	$wo->procesa_event();
	
}
?>