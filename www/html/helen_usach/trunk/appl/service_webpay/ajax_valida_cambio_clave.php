<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$actual_clave = $_REQUEST['actual_clave'];
$nueva_clave = $_REQUEST['nueva_clave'];
$confirma_clave = $_REQUEST['confirma_clave'];

$cod_deudor = session::get('cod_deudor');
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = "select PASSWORD
		from DEUDOR
		where COD_DEUDOR = $cod_deudor";
$result = $db->build_results($sql);
if ($result[0]['PASSWORD']!=$actual_clave) {
	print urlencode('PASSWORD_INVALIDA');
	return;
}
else if ($nueva_clave=='') {
	print urlencode('PASSWORD_VACIA');
	return;	
}
else if ($nueva_clave != $confirma_clave) {
	print urlencode('CONFIRMA_ERRONEA');
	return;	
}
else
	print urlencode('OK');
?>