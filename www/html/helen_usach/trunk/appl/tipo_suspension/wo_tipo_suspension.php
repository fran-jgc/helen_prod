<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (w_output::f_viene_del_menu('tipo_suspension')) {
	$wo_tipo_suspension = new wo_tipo_suspension();
	$wo_tipo_suspension->retrieve();
}
else {
    $wo = unserialize(session::get('wo_tipo_suspension'));
	$wo->procesa_event();

}
?>