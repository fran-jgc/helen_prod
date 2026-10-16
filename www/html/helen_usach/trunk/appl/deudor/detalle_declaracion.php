<?php
///////////////////////
// php llamado con ajax en el javascript function load_detalle_declaracion()
////////////////////////
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_declaracion = $_REQUEST['cod_declaracion'];

$sql = sql_deudor::get_sql('DETALLE_DECLARACION', $cod_declaracion);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql);
$row_count = $db->count_rows();
If ($row_count == 0) {
	$respuesta  = 0;
}
else{
	$respuesta='';
	for ($i = 0; $i <= 11; $i++) {
		for ($j = 0; $j <= 3; $j++) {
			$respuesta = $respuesta.$result[$i][$j]."|";
		}
	}
}
$sql = sql_deudor::get_sql('DETALLE_DECLARACION2', $cod_declaracion);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql);
$row_count = $db->count_rows();
If ($row_count == 0) {
	$respuesta;
}
else{
	for ($i = 0; $i <= 3; $i++) {
		$respuesta = $respuesta.$result[0][$i]."|";
	}
	
}
print urlencode($respuesta);
?>