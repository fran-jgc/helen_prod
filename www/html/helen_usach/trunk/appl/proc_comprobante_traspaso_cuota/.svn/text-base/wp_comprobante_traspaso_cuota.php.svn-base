<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_proceso::f_viene_del_menu('comprobante_traspaso_cuota')) {
	$w = new wp_comprobante_traspaso_cuota();
}
else {
    $w = unserialize(session::get('wp_comprobante_traspaso_cuota'));
	$w->procesa_event();	
}
?>