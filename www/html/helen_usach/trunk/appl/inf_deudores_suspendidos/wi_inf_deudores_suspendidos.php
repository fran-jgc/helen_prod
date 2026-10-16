<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (!session::is_set('wi_inf_deudores_suspendidos')) {
	$wi = new wi_inf_deudores_suspendidos($_REQUEST['cod_item_menu']);
	$wi->_load_record();
}
else {
    $wi = unserialize(session::get('wi_inf_deudores_suspendidos'));
	$wi->procesa_event();
}
?>