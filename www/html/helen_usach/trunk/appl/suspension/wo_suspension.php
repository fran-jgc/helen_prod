<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('suspension')) {
	$wo_suspension = new wo_suspension();
	$wo_suspension->retrieve();
}
else {
    $wo = unserialize(session::get('wo_suspension'));
	$wo->procesa_event();
	
}

?>