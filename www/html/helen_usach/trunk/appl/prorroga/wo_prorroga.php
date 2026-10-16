<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('prorroga')) {
	$wo_prorroga = new wo_prorroga();
	$wo_prorroga->retrieve();
}
else {
    $wo = unserialize(session::get('wo_prorroga'));
	$wo->procesa_event();
	
}
?>