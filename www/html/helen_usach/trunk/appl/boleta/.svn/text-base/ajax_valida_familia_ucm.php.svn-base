<?php
require_once(dirname(__FILE__)."/../../appl.ini");
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_tipo_deuda		= $_REQUEST['cod_tipo_deuda'];

$sql_familia = sql_boleta::get_sql('AJAX_VALIDA_FAMILIA',$cod_tipo_deuda);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql_familia);

$familia_deuda	= $result[0]['COD_FAMILIA_DEUDA'];
print $familia_deuda;
?>