<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('rendicion_ley')) {
	$wo_rendicion_ley = new wo_rendicion_ley();
	$wo_rendicion_ley->retrieve();
}
else {
    $wo = unserialize(session::get('wo_rendicion_ley'));
	$wo->procesa_event();
	
}
?>