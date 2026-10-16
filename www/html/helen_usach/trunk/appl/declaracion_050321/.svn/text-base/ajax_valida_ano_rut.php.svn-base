<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$rut = $_REQUEST['rut'];
$ano = $_REQUEST['ano'];

$K_DECL_SOLICITADA = 1;
$K_DECL_APROBADA = 2;

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql =sql_declaracion::get_sql('AJAX_DECLARACION_DEUDOR', $rut, $K_DECL_SOLICITADA, $K_DECL_APROBADA, $ano);
$result = $db->build_results($sql);

if($result[0]['CUANTO'] > 0)
	print urlencode(json_encode("TIENE"));
else
	print urlencode(json_encode("NO_TIENE"));
?>