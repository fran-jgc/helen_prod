<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_proceso::f_viene_del_menu('comprobante_genera_interes_ano')) {
	$w = new wp_comprobante_genera_interes_ano();
}
else {
    $w = unserialize(session::get('wp_comprobante_genera_interes_ano'));
	$w->procesa_event();	
}
?>