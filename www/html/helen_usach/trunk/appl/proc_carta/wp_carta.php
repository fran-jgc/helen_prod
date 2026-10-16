<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_proceso::f_viene_del_menu('carta')) {
	$w = new wp_carta();
}
else {
    $w = unserialize(session::get('wp_carta'));
	$w->procesa_event();	
}
?>