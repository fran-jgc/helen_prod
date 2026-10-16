<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");


if (w_output::f_viene_del_menu('carga_consolidado_project')) {
	$wo = new wo_carga_consolidado_project();
	$wo->retrieve();
}
else {
    $wo = unserialize(session::get('wo_carga_consolidado_project'));
	$wo->procesa_event();	
}
?>