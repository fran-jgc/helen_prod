<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
include("../../appl.ini");

if (file_exists(K_CLIENTE.'/dlg_cta_corriente.htm')) 
	$temp = new Template_appl(K_CLIENTE.'/dlg_cta_corriente.htm');	
else 
	$temp = new Template_appl('dlg_cta_corriente.htm');	

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$entrable = true;

$sql = "SELECT '9' COD_CUENTA_CORRIENTE";

$dw_datos = new datawindow($sql);

$sql = "SELECT COD_CUENTA_CORRIENTE
			   ,NOM_CUENTA_CORRIENTE
		FROM CUENTA_CORRIENTE
		ORDER BY COD_CUENTA_CORRIENTE";
$dw_datos->add_control($control = new drop_down_dw('COD_CUENTA_CORRIENTE', $sql, 150));

$dw_datos->insert_row();

$dw_datos->habilitar($temp, $entrable);

$menu = unserialize(session::get('menu_appl'));
$menu->draw($temp);	
print $temp->toString();
?>