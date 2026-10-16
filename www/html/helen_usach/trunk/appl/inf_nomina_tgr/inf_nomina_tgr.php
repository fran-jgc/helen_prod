<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
$temp = new Template_appl('wi_inf_nomina_tgr.htm');	
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = sql_inf_nomina_tgr::get_sql('INPUT_MONTO_MINIMO');

$dw_datos = new datawindow($sql);

$entrable = false;

$temp->setVar("COD_USUARIO", session::get("COD_USUARIO"));

$dw_datos->habilitar($temp, $entrable);
$menu = unserialize(session::get('menu_appl'));
$menu->draw($temp,$db);
print $temp->toString();
?>