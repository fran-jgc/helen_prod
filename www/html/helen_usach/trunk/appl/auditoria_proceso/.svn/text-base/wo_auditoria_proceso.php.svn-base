<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('auditoria_proceso')) {
	$wo_auditoria_proceso = new wo_auditoria_proceso();
	$wo_auditoria_proceso->retrieve();
}
else {
    $wo = unserialize(session::get('wo_auditoria_proceso'));
	$wo->procesa_event();
	
}
?>