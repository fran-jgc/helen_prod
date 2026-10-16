<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$mes 					= $_REQUEST['mes'];
$ano 					= $_REQUEST['ano'];
$cta_cte				= $_REQUEST['cta_cte'];
$cod_tipo_ingreso 		= 1;
$cod_tipo_egreso 		= 2;

$sql = sql_conciliacion::get_sql('AJAX_SET_VALORES',$mes,$ano,$cod_tipo_ingreso, $cta_cte,$cod_tipo_egreso);
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql);
$respuesta =  $result[0]['INGRESO_MES']."|";
$respuesta =  $respuesta.$result[0]['EGRESO_MES']."|";
$respuesta =  $respuesta.$result[0]['CONCILIACION_ANTERIOR'];
			
print $respuesta;
?>