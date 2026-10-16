<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$rut = $_REQUEST['rut'];
$ano = $_REQUEST['ano'];
$cod_deuda = $_REQUEST['cod_deuda'];

$K_VIGENTE = 2;
$K_CS   = 'CS';
$K_CSR  = 'CSR';
$K_CSN  = 'CSN';
$K_CR12 = 'CR12';
$K_CRI  = 'CRI';
$K_CAME = 'CAME';

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

$sql = sql_suspension::get_sql(
	'AJAX_INFO_DEUDOR',
	$rut,
	$cod_deuda,
	$K_VIGENTE,
	$K_CS,
	$K_CSR,
	$K_CSN,
	$K_CR12,
	$K_CRI,
	$K_CAME
);

$result = $db->build_results($sql);
$row_count = $db->count_rows();

if ($row_count == 0) {
	print urlencode('DEUDA_NO_CS_CSR');
	return;
}

$sql = "SELECT COD_SUSPENSION, NOM_ESTADO_SUSPENSION
		FROM SUSPENSION SU, ESTADO_SUSPENSION ES
		WHERE SU.COD_DEUDA = $cod_deuda
		AND SU.COD_ESTADO_SUSPENSION = ES.COD_ESTADO_SUSPENSION
		AND SU.ANO_SUSPENSION = $ano";

$result_suspension = $db->build_results($sql);
$row_suspension_deuda = $db->count_rows();

if ($row_suspension_deuda == 1) {
	$resultado  = "SUSPENSION_DEUDA_RECHAZADA|";
	$resultado .= $result[0]['DIG_VERIF']."|";
	$resultado .= $result[0]['NOMBRE']."|";
	$resultado .= $result[0]['COD_TIPO_DEUDA']."|";
	$resultado .= $result[0]['COD_DEUDA']."|";
	$resultado .= $result[0]['COD_DEUDOR']."|";
	$resultado .= $result_suspension[0]['COD_SUSPENSION']."|";
	$resultado .= $result_suspension[0]['NOM_ESTADO_SUSPENSION'];

	print urlencode($resultado);
	return;
}

$resultado  = "SUSPENSION_DEUDA_NUEVA|";
$resultado .= $result[0]['DIG_VERIF']."|";
$resultado .= $result[0]['NOMBRE']."|";
$resultado .= $result[0]['COD_TIPO_DEUDA']."|";
$resultado .= $result[0]['COD_DEUDA']."|";
$resultado .= $result[0]['COD_DEUDOR'];

print urlencode($resultado);
?>
