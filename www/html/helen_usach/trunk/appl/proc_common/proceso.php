<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$modulo = $_REQUEST['modulo'];
$cod_item_menu = $_REQUEST['cod_item_menu'];
session::un_set('wp_'.$modulo);
session::un_set($modulo);

$ROOT = session::get('K_ROOT_URL');
$ROOT_DIR = session::get('K_ROOT_DIR');
if (file_exists($ROOT_DIR.'appl/proc_'.$modulo)) {
	header ('Location:'.$ROOT.'appl/proc_'.$modulo.'/wp_'.$modulo.'.php?cod_item_menu='.$cod_item_menu);
	return;
}
?>