<?php

$json_log = $_REQUEST['json_log'];
$cod_dj = $_REQUEST['cod_dj'];
$cod_pers = $_REQUEST['cod_pers'];

$logs = json_decode($json_log);

$logFile = fopen("../declaracionjurada/log.txt", 'a') or die("Error creando archivo");


foreach($logs as $log){
    $fecha = date("d/m/Y H:i:s");
    $tipoArchivo = $log->tipoArchivo;
    $nomArchivo = utf8_decode($log->nomArchivo);
    $peso = $log->peso;
    $linea = $cod_dj.'|'.$cod_pers.'|'.'ANTES_SUBIDA|'.$tipoArchivo.'|'.$nomArchivo.'|'.$peso.'|'.$fecha.'|SIN OBS';
    fwrite($logFile, $linea."\n") or die("Error escribiendo en el archivo");
}
fclose($logFile);
?>    