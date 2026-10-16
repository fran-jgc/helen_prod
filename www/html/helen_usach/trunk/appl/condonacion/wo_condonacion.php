<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('condonacion')) {
	$wo_condonacion = new wo_condonacion();
	$wo_condonacion->retrieve();
}
else {
    $wo = unserialize(session::get('wo_condonacion'));
	$wo->procesa_event();
	
}
?>