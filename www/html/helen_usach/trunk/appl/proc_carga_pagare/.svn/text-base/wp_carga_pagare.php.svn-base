<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_proceso::f_viene_del_menu('carga_pagare')) {
	$w = new wp_carga_pagare();
}
else {
    $w = unserialize(session::get('wp_carga_pagare'));
	$w->procesa_event();	
}
?>