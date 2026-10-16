<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('dep_plazo_fijo')) {
	$wo_dep_plazo_fijo = new wo_dep_plazo_fijo();
	$wo_dep_plazo_fijo->retrieve();
}
else {
    $wo = unserialize(session::get('wo_dep_plazo_fijo'));
	$wo->procesa_event();
	
}
?>