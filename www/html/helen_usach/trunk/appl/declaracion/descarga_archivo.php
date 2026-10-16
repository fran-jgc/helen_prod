<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$cod = $_GET["cod_doc"];
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

$sql = "select RUTA_ARCHIVO, NOM_ARCHIVO, ORIGEN 
		from DECLARACION_ARCHIVO 
		where COD_DECLARACION_ARCHIVO = $cod";	
$result = $db->build_results($sql);
$nom_archivo = $result[0]['NOM_ARCHIVO'];
$archivo = $result[0]['RUTA_ARCHIVO'];
$archivo = str_replace("var/www/html/","",$result[0]['RUTA_ARCHIVO']);
$origen = $result[0]['ORIGEN'];


header('Location: '.$archivo);
?>