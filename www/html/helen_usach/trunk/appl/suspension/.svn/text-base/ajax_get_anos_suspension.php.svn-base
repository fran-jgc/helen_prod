<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
$rut		  	= $_REQUEST['rut'];
$cod_tipo_susp	= $_REQUEST['cod_tipo_suspension'];
$respuesta		= '';

$db		= new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql	= sql_suspension::get_sql('AJAX_ANOS_SUSPENSION', $rut, $cod_tipo_susp);
$result 	= $db->build_results($sql);
$respuesta 	= $result[0]['ANOS_SUSPENSION'];
print urlencode($respuesta);
?>