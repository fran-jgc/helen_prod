<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_proceso::f_viene_del_menu('cartera_conocida')) {
	$w = new wp_cartera_conocida();
}
else {
    $w = unserialize(session::get('wp_cartera_conocida'));
	$w->procesa_event();	
}
?>