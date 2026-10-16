<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('region')) {
	$wo_region = new wo_region();
	$wo_region->retrieve();
}
else {
    $wo = unserialize(session::get('wo_region'));
	$wo->procesa_event();
	
}
?>