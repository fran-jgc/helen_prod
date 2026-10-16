<?php
//cambio
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");


if (w_input::f_viene_del_output('estado_pagare')) {
	$wi = new wi_estado_pagare($_REQUEST['cod_item_menu']);
	$rec_no = $_REQUEST['rec_no'];
	$wi->goto_record($rec_no);
}
else {
    $wi = unserialize(session::get('wi_estado_pagare'));
	$wi->procesa_event();
}
?>