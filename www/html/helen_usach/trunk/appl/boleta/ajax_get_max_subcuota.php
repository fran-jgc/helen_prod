<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$kl_cod_max_subcuota = 18;

$sql = sql_boleta::get_sql('AJAX_MAX_SUBCUOTA',$kl_cod_max_subcuota);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql);
$max_subcuota = '';
$max_subcuota = $result[0]['MAX_SUBCUOTA'];
print $max_subcuota;
?>