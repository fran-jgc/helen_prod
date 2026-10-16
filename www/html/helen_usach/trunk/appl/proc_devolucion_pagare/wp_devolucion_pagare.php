<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_proceso::f_viene_del_menu('devolucion_pagare')) {
	$w = new wp_devolucion_pagare();
}
else {
    $w = unserialize(session::get('wp_devolucion_pagare'));
	$w->procesa_event();	
}
?>