<?php
//cambio
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");


if (w_input::f_viene_del_output('estado_deuda')) {
	$wi = new wi_estado_deuda($_REQUEST['cod_item_menu']);
	$rec_no = $_REQUEST['rec_no'];
	$wi->goto_record($rec_no);
}
else {
	$wi = unserialize(session::get('wi_estado_deuda'));
	$wi->procesa_event();
}
?>