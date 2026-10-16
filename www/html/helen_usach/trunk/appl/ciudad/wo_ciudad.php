<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('ciudad')) {
	$wo_ciudad = new wo_ciudad();
	$wo_ciudad->retrieve();
}
else {
    $wo = unserialize(session::get('wo_ciudad'));
	$wo->procesa_event();	
}
?>