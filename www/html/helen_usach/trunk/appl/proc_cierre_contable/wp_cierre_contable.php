<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_proceso::f_viene_del_menu('cierre_contable')) {
	$w = new wp_cierre_contable();
}
else {
    $w = unserialize(session::get('wp_cierre_contable'));
	$w->procesa_event();	
}
?>