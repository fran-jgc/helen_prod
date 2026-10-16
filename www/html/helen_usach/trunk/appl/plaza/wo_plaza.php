<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('plaza')) {
	$wo_plaza = new wo_plaza();
	$wo_plaza->retrieve();
}
else {
    $wo = unserialize(session::get('wo_plaza'));
	$wo->procesa_event();
	
}
?>