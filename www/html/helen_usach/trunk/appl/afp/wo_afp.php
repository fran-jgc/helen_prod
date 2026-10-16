<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");


if (w_output::f_viene_del_menu('afp')) {
	$wo_afp = new wo_afp();
	$wo_afp->retrieve();
}
else {
    $wo = unserialize(session::get('wo_afp'));
	$wo->procesa_event();
	
}
?>