<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_proceso::f_viene_del_menu('comprobante_correccion_monetaria')) {
	$w = new wp_comprobante_correccion_monetaria();
}
else {
    $w = unserialize(session::get('wp_comprobante_correccion_monetaria'));
	$w->procesa_event();	
}
?>