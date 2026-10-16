<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_tipo_susp	= $_REQUEST['tipo_suspension'];
$respuesta		= '';

$db		= new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql	= sql_suspension::get_sql('AJAX_MAX_SUSP_X_TIPO', $cod_tipo_susp);
$result 	= $db->build_results($sql);
$respuesta 	= $result[0]['PERIODO_MAXIMO'];
print urlencode($respuesta);
?>