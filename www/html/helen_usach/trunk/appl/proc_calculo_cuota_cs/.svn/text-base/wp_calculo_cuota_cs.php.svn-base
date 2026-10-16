<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_proceso::f_viene_del_menu('calculo_cuota_cs')) {
	$w = new wp_calculo_cuota_cs();
}
else {
    $w = unserialize(session::get('wp_calculo_cuota_cs'));
	$w->procesa_event();
}
?>