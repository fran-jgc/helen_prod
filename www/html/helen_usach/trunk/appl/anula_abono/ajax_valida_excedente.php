<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$K_CONCEPTO_CUENTA_EXCEDENTE = 19;
$cod_abono = isset($_REQUEST['cod_abono'])
	? (int)$_REQUEST['cod_abono']
	: 0;

$msg = '';

if ($cod_abono <= 0) {
	print 'El código de abono no es válido.';
	exit;
}

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

$sql = sql_anula_abono::get_sql('COMP_APROBADO', $cod_abono);
$result = $db->build_results($sql);

/*
 * La cuenta contable de excedente solamente es necesaria cuando
 * el abono posee un comprobante asociado.
 */
if (
	count($result) > 0
	&& $result[0]['COD_COMPROBANTE'] != ''
) {
	$sql = "SELECT f_comp_get_cuenta_tipo_deuda(
						d.cod_tipo_deuda,
						$K_CONCEPTO_CUENTA_EXCEDENTE
					) AS COD_CUENTA_CONTABLE
			FROM ABONO a
			JOIN DEUDA d
			  ON d.cod_deuda = a.cod_deuda
			WHERE a.cod_abono = $cod_abono";

	$result_cta = $db->build_results($sql);

	if (
		count($result_cta) == 0
		|| $result_cta[0]['COD_CUENTA_CONTABLE'] == ''
	) {
		$msg = 'No existe una cuenta contable configurada para excedente en el tipo de deuda del abono.';
	}
}

print $msg;
?>