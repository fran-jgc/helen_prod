<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('tipo_bitacora')) {
	$wo_tipo_bitacora = new wo_tipo_bitacora();
	$wo_tipo_bitacora->retrieve();
}
else {
    $wo = unserialize(session::get('wo_tipo_bitacora'));
	$wo->procesa_event();

}
?>