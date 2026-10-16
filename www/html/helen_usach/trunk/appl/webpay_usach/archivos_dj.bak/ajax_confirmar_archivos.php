<?php
ini_set('display_errors', 'Off');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_declaracion = $_REQUEST['cod_dj'];
$json_log = $_REQUEST['json_log'];
$logs = json_decode($json_log);

$dbc = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = "SELECT COD_TIPO_ARCHIVO,
        F_NAME
        from declaracion_archivo 
        WHERE COD_DECLARACION = $cod_declaracion
        AND TRIM(f_error) = '0'";

$result = $dbc->build_results($sql);
$respuesta = '';
$no_diferencias = true;
$arregloPrincipal = array();
foreach($logs as $log){
    $tipoArchivo = $log->tipoArchivo;
    $nomArchivo = $log->nomArchivo;
    $encontrado = false;
    
    for($i = 0; $i < count($result);$i++){
        $tipoArchivo_db = $result[$i]['COD_TIPO_ARCHIVO'];
        $nomArchivo_db = $result[$i]['F_NAME'];
        
        
        if($tipoArchivo == $tipoArchivo_db && $nomArchivo == $nomArchivo_db){
            $encontrado = true;
        }
    }
    if(!$encontrado){
        $archivoFaltante = array(
            'NOMBRE' => $nomArchivo,
            'TIPO' => $tipoArchivo
        );
        array_push($arregloPrincipal, $archivoFaltante);
        $no_diferencias = false;
    }
}


if($no_diferencias){
    $respuesta = 'CORRECTO';
    echo trim($respuesta);
}else{
    echo json_encode($arregloPrincipal);
}

?>    