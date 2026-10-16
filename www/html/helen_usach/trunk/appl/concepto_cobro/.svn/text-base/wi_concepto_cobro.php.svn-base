<?php
//cambio
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");


if (w_input::f_viene_del_output('concepto_cobro')) {
	$wi = new wi_concepto_cobro($_REQUEST['cod_item_menu']);
	$rec_no = $_REQUEST['rec_no'];
	$wi->goto_record($rec_no);
}
else {
    $wi = unserialize(session::get('wi_concepto_cobro'));
	$wi->procesa_event();
}
?>