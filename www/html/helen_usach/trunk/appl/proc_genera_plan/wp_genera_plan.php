<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_proceso::f_viene_del_menu('genera_plan')) {
	$w = new wp_genera_plan();
}
else {
    $w = unserialize(session::get('wp_genera_plan'));
	$w->procesa_event();	
}
?>