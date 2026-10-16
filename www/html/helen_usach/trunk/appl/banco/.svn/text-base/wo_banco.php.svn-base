<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");


if (w_output::f_viene_del_menu('banco')) {
	$wo_banco = new wo_banco();
	$wo_banco->retrieve();
}
else {
    $wo = unserialize(session::get('wo_banco'));
	$wo->procesa_event();
	
}
?>