<?php
///////////////////////////////////////////
// php llamado con ajax en el javascript //
///////////////////////////////////////////
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");


$rut_deudor 		= $_REQUEST['rut_deudor'];
$ano_declaracion	= $_REQUEST['ano_declaracion'];

$K_PAIS_CHILE = 'K_CHILE';
$K_DECL_SOLICITADA = 1;
$K_DECL_APROBADA = 2;
$K_PARAM_CARTERA = 2;
$K_PARAM_CARTERA_ORSAN = 26;

if ($rut_deudor =='') {
	print urlencode('VACIO');
	return;
}
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

$sql =sql_declaracion::get_sql('AJAX_CUANTO', $rut_deudor);
$result = $db->build_results($sql);
if ($result[0]['CUANTO']== 0){
	print urlencode('NO');
	return;
}

$sql =sql_declaracion::get_sql('AJAX_DEUDA_EXIGIBLE', $rut_deudor);
$result = $db->build_results($sql);
if ($result[0]['CUANTO1']== 0){
	print urlencode('NO_POSEE_DEUDA_EXIGIBLE');
	return;
}

$sql =sql_declaracion::get_sql('AJAX_DEUDA_TIPO_CS_CSR', $rut_deudor);
$result = $db->build_results($sql);
if ($result[0]['CUANTO']== 0){
	print urlencode('NO_POSEE_DEUDA_CS_CSR');
	return;
}

if ($ano_declaracion !=''){
	$sql = sql_declaracion::get_sql('AJAX_DECLARACION_DEUDOR', $rut_deudor, $K_DECL_SOLICITADA ,$K_DECL_APROBADA, $ano_declaracion);
	$result = $db->build_results($sql);
	if ($result[0]['CUANTO'] > 0){
		print urlencode('CONTIENE_DECLARACION');
		return;
	}	
}

$cartera_defecto = base::get_parametro($K_PARAM_CARTERA);
$sql = sql_declaracion::get_sql('DEUDA_CARTERA_DIFERENTE_USACH', $cartera_defecto,$rut_deudor, $K_PARAM_CARTERA_ORSAN);
$result = $db->build_results($sql);

if($result[0]['CANT'] > 0) {
print urlencode('CARTERA_DIFERENTE');
return;
}


$sql = sql_declaracion::get_sql('AJAX_CUANTO_1', $rut_deudor, $K_PAIS_CHILE);
	$result = $db->build_results($sql);

	print urlencode($result[0]['COD_DEUDOR']."|".
	$result[0]['DIG_VERIF']."|".
	$result[0]['NOMBRE']."|".
	$result[0]['DIRECCION_PARTICULAR']."|".
	$result[0]['FECHA_DIRECCION_PARTICULAR']."|".
	$result[0]['COD_COMUNA_PARTICULAR']."|".
	$result[0]['COD_CIUDAD_PARTICULAR']."|".
	$result[0]['COD_PAIS_PARTICULAR']."|".
	$result[0]['EMAIL_PARTICULAR']."|".
	$result[0]['TELEFONO_PARTICULAR']."|".
	$result[0]['CELULAR_PARTICULAR']."|".
	$result[0]['DIRECCION_COMERCIAL']."|".
	$result[0]['FECHA_DIRECCION_COMERCIAL']."|".
	$result[0]['COD_COMUNA_COMERCIAL']."|".
	$result[0]['COD_CIUDAD_COMERCIAL']."|".
	$result[0]['COD_PAIS_COMERCIAL']."|".
	$result[0]['EMAIL_COMERCIAL']."|".
	$result[0]['TELEFONO_COMERCIAL']."|".
	$result[0]['CELULAR_COMERCIAL']."|".
	$result[0]['COD_REGION_PARTICULAR']."|".
	$result[0]['COD_REGION_COMERCIAL']
	);

?>