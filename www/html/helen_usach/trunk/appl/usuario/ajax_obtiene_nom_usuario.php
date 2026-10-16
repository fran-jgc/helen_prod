<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$nom_usuario = $_REQUEST['nom_usuario'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = "SELECT	NOM_USUARIO
							FROM	USUARIO
							WHERE	NOM_USUARIO = '$nom_usuario'";
$result = $db->build_results($sql);
$row_count = count($result);

if ($row_count > 0){
    print urlencode(1);
}
?>