<?php
///////////////////////
// PHP llamado con AJAX desde JavaScript.
///////////////////////

require_once(
	dirname(__FILE__).
	"/../../../../commonlib_seven/trunk/php/auto_load.php"
);

$estados_permitidos = array(1, 2, 3);

$cod_abono = isset($_REQUEST['cod_abono'])
	? (int)$_REQUEST['cod_abono']
	: 0;

$resp = 0;

if ($cod_abono > 0) {
	$db = new database(
		K_TIPO_BD,
		K_SERVER,
		K_BD,
		K_USER,
		K_PASS
	);

	$sql = sql_anula_abono::get_sql(
		'COMP_APROBADO',
		$cod_abono
	);

	$result = $db->build_results($sql);

	/*
	 * Se permite continuar cuando:
	 * - El abono no tiene un comprobante asociado.
	 * - El comprobante está en estado 1, 2 o 3.
	 */
	if (count($result) > 0) {
		$cod_comprobante =
			$result[0]['COD_COMPROBANTE'];

		$cod_estado =
			(int)$result[0]['COD_ESTADO_COMPROBANTE'];

		if (
			$cod_comprobante == ''
			|| in_array(
				$cod_estado,
				$estados_permitidos,
				true
			)
		) {
			$resp = 1;
		}
	}
}

print $resp;
?>