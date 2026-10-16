<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('cartola_banco')) {
	$wo_cartola_banco = new wo_cartola_banco();
	$wo_cartola_banco->retrieve();
}
else {
    $wo = unserialize(session::get('wo_cartola_banco'));
	$wo->procesa_event();
	
}
?>