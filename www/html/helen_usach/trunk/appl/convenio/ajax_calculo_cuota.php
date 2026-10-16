<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

$monto_convenio = $_REQUEST['monto_convenio'];
$cant_cuota = $_REQUEST['cant_cuota'];
$fecha_reneg = $_REQUEST['fecha_reneg'];
$fecha_inicio = $_REQUEST['fecha_inicio'];

$b = new base();
$fecha_inicio = $b->str2date($fecha_inicio);
$fecha_reneg = $b->str2date($fecha_reneg);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = sql_convenio::get_sql('AJAX_CALCULO_CUOTA',$monto_convenio,$fecha_reneg,$fecha_inicio,$cant_cuota);
$result = $db->build_results($sql);

print urlencode(json_encode($result));
?>