<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_proceso::f_viene_del_menu('obtiene_mora')) {
	$w = new wp_obtiene_mora();
}
else {
    $w = unserialize(session::get('wp_obtiene_mora'));
	$w->procesa_event();
}
?>