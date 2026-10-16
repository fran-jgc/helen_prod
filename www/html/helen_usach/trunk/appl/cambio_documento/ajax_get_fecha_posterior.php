<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$fecha = urldecode($_REQUEST['fecha']);

$sql 		= sql_cambio_documento::get_sql('AJAX_FECHA_POSTERIOR',$fecha);
$db 		= new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result 	= $db->build_results($sql);
$FECHA_POSTERIOR = $result[0]['FECHA_POSTERIOR'];
print $FECHA_POSTERIOR; //urlencode(json_encode($result));
?>