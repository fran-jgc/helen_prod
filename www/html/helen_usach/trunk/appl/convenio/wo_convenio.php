<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('convenio')) {
	$wo_convenio = new wo_convenio();
	$wo_convenio->retrieve();
}
else {
    $wo = unserialize(session::get('wo_convenio'));
	$wo->procesa_event();
	
}
?>