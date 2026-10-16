<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$respuesta = $db->current_date();
print urlencode($respuesta);
?>