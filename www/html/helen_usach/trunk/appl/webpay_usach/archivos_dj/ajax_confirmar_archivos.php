<?php
ini_set('display_errors', 'Off');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_declaracion = $_REQUEST['cod_dj'];
$json_log = $_REQUEST['json_log'];
$cod_carga_intento = isset($_REQUEST['cod_carga_intento']) ? (int)$_REQUEST['cod_carga_intento'] : 0;
$logs = json_decode($json_log);

$dbc = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = "SELECT COD_TIPO_ARCHIVO,
        F_NAME,
        TAMANO
        from declaracion_archivo 
        WHERE COD_DECLARACION = $cod_declaracion
        AND COD_CARGA_INTENTO = $cod_carga_intento
        AND TRIM(f_error) = '0'";

$result = $dbc->build_results($sql);
$respuesta = '';
$no_diferencias = true;
$arregloPrincipal = array();
foreach($logs as $log){
    $tipoArchivo = $log->tipoArchivo;
    $nomArchivo = $log->nomArchivo;
    $pesoArchivo = isset($log->peso) ? $log->peso : null;
    $encontrado = false;
    
    for($i = 0; $i < count($result);$i++){
        if(isset($result[$i]['USADO']) && $result[$i]['USADO']){
            continue;
        }
        $tipoArchivo_db = $result[$i]['COD_TIPO_ARCHIVO'];
        $nomArchivo_db = trim($result[$i]['F_NAME']);
        $tamanoArchivo_db = $result[$i]['TAMANO'];
        
        $mismoNombre = trim($nomArchivo) == $nomArchivo_db;
        $mismoTamano = ($pesoArchivo !== null && $pesoArchivo == $tamanoArchivo_db);

        if($tipoArchivo == $tipoArchivo_db && ($mismoNombre || $mismoTamano)){
            $encontrado = true;
            $result[$i]['USADO'] = true;
            break;
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
    $sql = "BEGIN
                UPDATE DECLARACION_CARGA_INTENTO
                   SET ESTADO = 'EN_REVISION',
                       FECHA_ENVIO = SYSDATE
                 WHERE COD_CARGA_INTENTO = $cod_carga_intento
                   AND COD_DECLARACION = $cod_declaracion
                   AND ESTADO = 'EN_CARGA';
                COMMIT;
            END;";
    if(!$dbc->query($sql)){
        echo 'ERROR_CONFIRMAR_INTENTO';
        exit;
    }
    $respuesta = 'CORRECTO';
    echo trim($respuesta);
}else{
    echo json_encode($arregloPrincipal);
}

?>    
