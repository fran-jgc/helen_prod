<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_proceso::f_viene_del_menu('cuponera')) {
	$w = new wp_cuponera();
}
else {
    $w = unserialize(session::get('wp_cuponera'));
	$w->procesa_event();	
}
?>