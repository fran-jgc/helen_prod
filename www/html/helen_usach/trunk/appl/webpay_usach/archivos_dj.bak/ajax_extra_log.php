<?php

$obs = $_REQUEST['obs'];
$cod_dj = $_REQUEST['cod_dj'];
$cod_pers = $_REQUEST['cod_pers'];
$peso = $_REQUEST['peso'];
$nom_archivo = utf8_decode($_REQUEST['nom_archivo']);
$tipo_archivo = $_REQUEST['tipo_archivo'];

$logFile = fopen("../declaracionjurada/log.txt", 'a') or die("Error creando archivo");

$fecha = date("d/m/Y H:i:s");
$linea = $cod_dj.'|'.$cod_pers.'|'.'SUBIR_DZ_RESPUESTA|'.$tipo_archivo.'|'.$nom_archivo.'|'.$peso.'|'.$fecha.'|'.$obs;
fwrite($logFile, $linea."\n") or die("Error escribiendo en el archivo");

fclose($logFile);
?>    