<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");


if (w_output::f_viene_del_menu('tipo_documento_pago')) {
	$wo_tipo_documento_pago = new wo_tipo_documento_pago();
	$wo_tipo_documento_pago->retrieve();
}
else {
    $wo = unserialize(session::get('wo_tipo_documento_pago'));
	$wo->procesa_event();
	
}
?>