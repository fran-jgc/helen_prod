<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('condonacion_cs')) {
	$wo_condonacion_cs = new wo_condonacion_cs();
	$wo_condonacion_cs->retrieve();
}
else {
    $wo = unserialize(session::get('wo_condonacion_cs'));
	$wo->procesa_event();
	
}
?>