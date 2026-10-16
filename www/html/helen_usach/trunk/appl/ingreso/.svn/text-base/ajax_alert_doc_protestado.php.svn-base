<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$rut = $_REQUEST['rut'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql =sql_ingreso::get_sql('ALERT_DOC_PROTESTADO', $rut);
$result = $db->build_results($sql);
$resultado = '';
$resultado= $resultado.$result[0]['CANT'];
print $resultado;

?>