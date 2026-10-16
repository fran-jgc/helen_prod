<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('carga_tgr')) {

	$wo_carga_tgr= new wo_carga_tgr();
	$wo_carga_tgr->retrieve();
}
else {
    $wo = unserialize(session::get('wo_carga_tgr'));
	$wo->procesa_event();
	
}
?>