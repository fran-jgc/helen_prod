<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$nom_login = $_REQUEST['nom_login'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = "SELECT	LOGIN
							FROM	USUARIO
							WHERE	LOGIN = '$nom_login'";
$result = $db->build_results($sql);
$row_count = count($result);

if ($row_count > 0){
    print urlencode(1);
}
?>