<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

if (isset($_REQUEST['cod_item_menu'])) {
	$w = new wi_parametro();
} else {
    $w = unserialize(session::get('wi_parametro'));
	$w->procesa_event();
}
?>