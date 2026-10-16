<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('carga_otra_ues')) {

	$wo_carga_otra_ues= new wo_carga_otra_ues();
	$wo_carga_otra_ues->retrieve();
}
else {
    $wo = unserialize(session::get('wo_carga_otra_ues'));
	$wo->procesa_event();
	
}
?>