<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$w = unserialize(session::get('wi_deudor'));
$cod_deudor = $w->dws['dw_deudor']->get_item(0, 'COD_DEUDOR');
session::set("CREADA_DESDE_DEUDOR", $cod_deudor);
header ('Location:'."../../../../commonlib_seven/trunk/php/link_wi.php?modulo_origen=deudor&modulo_destino=simulacion&cod_modulo_destino=-3&cod_item_menu=1502&current_tab_page=4");
?>