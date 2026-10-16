<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_proceso::f_viene_del_menu('genera_comprobante_ingreso')) {
	$w = new wp_genera_comprobante_ingreso();
}
else {
    $w = unserialize(session::get('wp_genera_comprobante_ingreso'));
	$w->procesa_event();
}
?>