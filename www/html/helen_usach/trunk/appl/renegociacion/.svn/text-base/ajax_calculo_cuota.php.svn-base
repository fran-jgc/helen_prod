<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$monto_renegociar = $_REQUEST['monto_renegociar'];
$cant_cuota = $_REQUEST['cant_cuota'];
$fecha_reneg = $_REQUEST['fecha_reneg'];
$ano_inicio = $_REQUEST['ano_inicio'];

$b = new base();
$fecha_inicio = $b->str2date('01/01/'.$ano_inicio);
$fecha_reneg = $b->str2date($fecha_reneg);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = "select dbo.f_reneg_monto_actualizado($monto_renegociar, $fecha_reneg, $fecha_inicio) MONTO_ACT
				,dbo.f_reneg_calculo_cuota($monto_renegociar, $cant_cuota, $fecha_reneg, $fecha_inicio) MONTO_CUOTA";
$result = $db->build_results($sql);

print urlencode(json_encode($result));
?>