<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_proceso::f_viene_del_menu('calculo_cuota_csr')) {
	$w = new wp_calculo_cuota_csr();
}
else {
    $w = unserialize(session::get('wp_calculo_cuota_csr'));
	$w->procesa_event();
}
?>