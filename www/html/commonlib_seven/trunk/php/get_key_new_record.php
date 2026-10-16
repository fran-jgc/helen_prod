<?php
require_once(dirname(__FILE__)."/auto_load.php");

$tabla = strtoupper($_REQUEST['tabla']);

if (unserialize(session::is_set('COD_'.$tabla))) {
	$cod = unserialize(session::get('COD_'.$tabla));
	unserialize(session::un_set('COD_'.$tabla));
}
else
	$cod = '';
print $cod;
?>