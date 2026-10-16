<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_plan_pago = $_REQUEST['cod_plan_pago'];


$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = "SELECT COD_ESTADO_PLAN_PAGO
				,ANO_COBRO
				,TD.COD_FAMILIA_DEUDA
		FROM PLAN_PAGO PP, DEUDA DE, TIPO_DEUDA TD
		WHERE COD_PLAN_PAGO =$cod_plan_pago
	      AND PP.COD_DEUDA =  DE.COD_DEUDA
	      AND DE.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA";
$result = $db->build_results($sql);
$resultado = '';
if (count($result)!=0)
	$resultado = $result[0]['COD_ESTADO_PLAN_PAGO'].'|'.$result[0]['ANO_COBRO'].'|'.$result[0]['COD_FAMILIA_DEUDA'];

print $resultado;
?>