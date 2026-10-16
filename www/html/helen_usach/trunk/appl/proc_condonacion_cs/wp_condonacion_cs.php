<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_proceso::f_viene_del_menu('condonacion_cs')) {
	$w = new wp_condonacion_cs();
}
else {
    $w = unserialize(session::get('wp_condonacion_cs'));
	$w->procesa_event();	
}
?>