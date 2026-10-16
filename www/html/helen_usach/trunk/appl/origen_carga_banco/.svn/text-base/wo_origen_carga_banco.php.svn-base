<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('origen_carga_banco')) {
	$wo_origen_carga_banco = new wo_origen_carga_banco();
	$wo_origen_carga_banco->retrieve();
}
else {
    $wo = unserialize(session::get('wo_origen_carga_banco'));
	$wo->procesa_event();
	
}
?>