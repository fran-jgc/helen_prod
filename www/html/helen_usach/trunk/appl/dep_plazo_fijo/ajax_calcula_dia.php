<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$fecha_emision = $_REQUEST['ve_fecha_emision'];
$fecha_vencto = $_REQUEST['ve_fecha_vencto'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = "select TO_DATE('$fecha_vencto','DD/MM/YYYY') - TO_DATE('$fecha_emision','DD/MM/YYYY') DIAS from dual";
$result = $db->build_results($sql);
$resultado = '';
$resultado= $resultado.$result[0]['DIAS'];
print $resultado;

?>