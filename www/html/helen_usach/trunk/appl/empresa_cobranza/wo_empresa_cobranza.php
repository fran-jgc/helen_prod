<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");


if (w_output::f_viene_del_menu('empresa_cobranza')) {
	$wo_empresa_cobranza = new wo_empresa_cobranza();
	$wo_empresa_cobranza->retrieve();
}
else {
    $wo = unserialize(session::get('wo_empresa_cobranza'));
	$wo->procesa_event();
	
}
?>