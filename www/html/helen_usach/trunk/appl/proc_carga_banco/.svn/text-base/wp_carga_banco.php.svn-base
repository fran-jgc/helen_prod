<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
if (w_proceso::f_viene_del_menu('carga_banco')) {
	$w = new wp_carga_banco();
}
else {
    $w = unserialize(session::get('wp_carga_banco'));
	$w->procesa_event();	
}
?>