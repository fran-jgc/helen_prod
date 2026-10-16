<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$rut = $_REQUEST['rut'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = "exec spdw_ingreso_selecciona_pago ".$rut;
$result = $db->build_results($sql);

if (count($result)==1)
	print 'PAGO_NORMAL';
else
	print 'SELECCIONA_PAGO'; 
?>