<?php
require_once(
	dirname(__FILE__).
	"/../../../../commonlib_seven/trunk/php/auto_load.php"
);

$cod_plan_pago = isset($_GET['cod_plan_pago'])
	? (int)$_GET['cod_plan_pago']
	: 0;

if ($cod_plan_pago <= 0) {
	print '0|0';
	exit;
}

$db = new database(
	K_TIPO_BD,
	K_SERVER,
	K_BD,
	K_USER,
	K_PASS
);

if (K_TIPO_BD == 'oci') {
	$sql = "SELECT
				NRO_CUOTA,
				F_PP_SALDO_PLAN_PAGO(
					COD_PLAN_PAGO,
					FECHA_VENCTO
				) SALDO_CUOTA
			FROM PLAN_PAGO
			WHERE COD_PLAN_PAGO = $cod_plan_pago";
}
else {
	$sql = "SELECT
				NRO_CUOTA,
				dbo.f_pp_saldo_plan_pago(
					COD_PLAN_PAGO,
					FECHA_VENCTO
				) SALDO_CUOTA
			FROM PLAN_PAGO
			WHERE COD_PLAN_PAGO = $cod_plan_pago";
}

$resultado = $db->build_results($sql);

if (count($resultado) == 0) {
	print '0|0';
	exit;
}

print $resultado[0]['NRO_CUOTA']
	.'|'.$resultado[0]['SALDO_CUOTA'];
?>