<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$K_UTM = 3;
$ano = $_REQUEST['ano'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

$sql =sql_declaracion::get_sql('AJAX_VALOR_UTM', $K_UTM, $ano);
$result = $db->build_results($sql);
print urlencode(json_encode($result));
?>