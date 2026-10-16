<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('repro_rcs')) {
	$wo_repro_rcs = new wo_repro_rcs();
	$wo_repro_rcs->retrieve();
}
else {
    $wo = unserialize(session::get('wo_repro_rcs'));
	$wo->procesa_event();
}
?>