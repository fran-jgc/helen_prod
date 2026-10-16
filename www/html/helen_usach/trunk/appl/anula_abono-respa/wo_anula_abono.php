<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('anula_abono')) {
	$wo_anula_abono = new wo_anula_abono();
	$wo_anula_abono->retrieve();
}
else {
    $wo = unserialize(session::get('wo_anula_abono'));
	$wo->procesa_event();

}
?>