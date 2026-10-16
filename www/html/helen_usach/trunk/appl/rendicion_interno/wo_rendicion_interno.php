<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('rendicion_interno')) {
	$wo_rendicion = new wo_rendicion_interno();
	$wo_rendicion->retrieve();
}
else {
    $wo = unserialize(session::get('wo_rendicion_interno'));
	$wo->procesa_event();
	
}
?>