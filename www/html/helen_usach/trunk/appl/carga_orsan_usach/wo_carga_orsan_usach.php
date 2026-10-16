<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('carga_orsan_usach')) {

	$wo_carga_banco= new wo_carga_orsan_usach();
	$wo_carga_banco->retrieve();
}
else {
    $wo = unserialize(session::get('wo_carga_orsan_usach'));
	$wo->procesa_event();
	
}
?>