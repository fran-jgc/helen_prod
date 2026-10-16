<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('familia_deuda')) {
	$wo_familia_deuda = new wo_familia_deuda();
	$wo_familia_deuda->retrieve();
}
else {
    $wo = unserialize(session::get('wo_familia_deuda'));
	$wo->procesa_event();
	
}

?>