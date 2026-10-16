<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_tipo_deuda	= $_REQUEST['cod_tipo_deuda'];

$sql = sql_boleta::get_sql('AJAX_VALOR_MONEDA', $cod_tipo_deuda);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql);

$valor_moneda = '';
$valor_moneda = $result[0]['VALOR_MONEDA'];
print urlencode($valor_moneda);
?>