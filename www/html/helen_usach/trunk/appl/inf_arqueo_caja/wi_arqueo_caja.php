<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (!session::is_set('wi_arqueo_caja.html')) {
	$wi = new wi_inf_balance_ocho($_REQUEST['cod_item_menu']);
	$wi->_load_record();
}
else {
	$wi = session::get('wi_arqueo_caja.html');
	$wi->procesa_event();
}
?>