<?php
ini_set('display_errors', 'Off');
set_time_limit(60);
header('Content-Type: text/plain; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');

$tipo_archivo = $_REQUEST['tp'];
$cod_dj = $_REQUEST['cod_dj'];
$cardinalidad = $_REQUEST['cardinalidad'];
$modo_debug = 'N';
$cod_pers = $_REQUEST['cod_pers'];
$cod_carga_intento = isset($_REQUEST['cod_carga_intento']) ? (int)$_REQUEST['cod_carga_intento'] : 0;
$exito = false;
$detalle_error = 'UPLOAD_NOT_COMPLETED';

if($modo_debug == 'S'){
    $logFile = fopen("../declaracionjurada/log.txt", 'a') or die("Error creando archivo");
    $fecha = date("d/m/Y H:i:s");
    $linea = $cod_dj.'|'.$cod_pers.'|'.'INICIO_UPLOAD|'.$tipo_archivo.'|NULL|NULL|'.$fecha.'|SIN OBS';
    fwrite($logFile, $linea."\n") or die("Error escribiendo en el archivo");
}

require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
require_once (dirname(__FILE__)."/../../../appl.ini");
session::set('K_ROOT_DIR', K_ROOT_DIR);

function escape_sql_literal($value){
    return str_replace("'", "''", $value);
}

function trace_upload($codDj, $tipoArchivo, $mensaje){
    $linea = date('Y-m-d H:i:s').'|DJ='.$codDj.'|TP='.$tipoArchivo.'|'.$mensaje."\n";
    @file_put_contents(sys_get_temp_dir().DIRECTORY_SEPARATOR.'webpay_usach_upload_trace.log', $linea, FILE_APPEND);
}

trace_upload($cod_dj, $tipo_archivo, 'INICIO_UPLOAD');

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$cod_dj = (int)$cod_dj;
$cod_pers = (int)$cod_pers;
$tipo_archivo = (int)$tipo_archivo;

$sql_intento = "SELECT I.COD_CARGA_INTENTO, I.NUMERO_INTENTO
                  FROM DECLARACION_CARGA_INTENTO I
                  JOIN DECLARACION D ON D.COD_DECLARACION = I.COD_DECLARACION
                 WHERE I.COD_CARGA_INTENTO = $cod_carga_intento
                   AND I.COD_DECLARACION = $cod_dj
                   AND D.COD_DEUDOR = $cod_pers
                   AND I.NUMERO_INTENTO BETWEEN 1 AND 2
                   AND I.ESTADO = 'EN_CARGA'";
$intento_valido = $db->build_results($sql_intento);
if($cod_carga_intento <= 0 || count($intento_valido) == 0){
    http_response_code(409);
    echo 'INTENTO_NO_DISPONIBLE';
    exit;
}
$numero_intento = (int)$intento_valido[0]['NUMERO_INTENTO'];
$ano_actual = date('Y');
$mes_actual = intval(date('n'));
$fecha_actual = date('d-m-Y');

switch ($mes_actual) {
    case 1:
        $mes_palabra = "ENERO";
        break;
    case 2:
        $mes_palabra = "FEBRERO";
        break;
    case 3:
        $mes_palabra = "MARZO";
        break;
    case 4:
        $mes_palabra = "ABRIL";
        break;
    case 5:
        $mes_palabra = "MAYO";
        break;
    case 6:
        $mes_palabra = "JUNIO";
        break;
    case 7:
        $mes_palabra = "JULIO";
        break;
    case 8:
        $mes_palabra = "AGOSTO";
        break;
    case 9:
        $mes_palabra = "SEPTIEMBRE";
        break;
    case 10:
        $mes_palabra = "OCTUBRE";
        break;
    case 11:
        $mes_palabra = "NOVIEMBRE";
        break;
    case 12:
        $mes_palabra = "DICIEMBRE";
        break;
}

$tipo_archivo = $_REQUEST['tp'];
$cod_dj = $_REQUEST['cod_dj'];
$cardinalidad = $_REQUEST['cardinalidad'];

switch ($tipo_archivo) {
    case 1:
    $ini = 'dj';
    break;
    case 2:
    $ini = 'f22';
    break;
    case 3:
    $ini = 'ar';
    break;
    case 4:
    $ini = 'ccp';
    break;
    case 5:
    $ini = 'cr';
    break;
    case 6:
    $ini = 'cn';
    break;
    case 7:
    $ini = 'cm';
    break;
    case 8:
    $ini = 'ot';
    break;
    case 9:
    $ini = 'lq';
    break;
}


$cod_param=37;

$ruta_param=$db->get_parametro($cod_param);

$RUTA = K_ROOT_DIR.'appl/webpay_usach/'.$ruta_param;//'/ariadna/www13/cgi-bin/afc/declaracionjurada';

$folder = $RUTA."/".$ano_actual."/".$mes_palabra."/".$fecha_actual."/".$cod_dj."/";

clearstatcache();//limpia la cache que deja el file_exists
if (!file_exists($folder)){
    clearstatcache();
    if(!mkdir($folder, 0777 , true)){	// recursive = true
        $detalle_error = 'MKDIR_FAILED';
        trace_upload($cod_dj, $tipo_archivo, $detalle_error.'|'.$folder);
        echo $detalle_error;
        exit;
    }
}
trace_upload($cod_dj, $tipo_archivo, 'RUTA_OK|'.$folder);
$storeFolder = $folder;   //2

if (!empty($_FILES)) {
    $i = 0;
    
    $tempFile = $_FILES['file']['tmp_name'];        //3
    trace_upload($cod_dj, $tipo_archivo, 'ARCHIVO_RECIBIDO|'.$_FILES['file']['name'].'|TMP='.$tempFile.'|SIZE='.$_FILES['file']['size'].'|ERR='.$_FILES['file']['error']);
        
    //$targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;  //4 original
    $targetPath = $storeFolder;
    $nom_archivo_original = $_FILES['file']['name'];
    
    if($nom_archivo_original == 'qwertyuiopasdfghjklnzxcvbnm.jpg'){
        echo 'TEST_FILE';
        return false;
    }
    
    for($j=0 ; $j < strlen($nom_archivo_original) ; $j++){
        $char = substr($nom_archivo_original, $j, 1);
        if($char == '.')
            $pos = $j;
    }
    $nom_archivo_truncado = substr($nom_archivo_original, 0, 4); //nombre archivo sin extension truncado
    $extension   = substr($nom_archivo_original, $pos, strlen($nom_archivo_original)); //la extension
    
    if($cardinalidad > 1){
        $i = 0;
        $nombre_con_ruta = $targetPath.$ini.'_'.$cod_dj.'_i'.$numero_intento.'_'.$nom_archivo_truncado.'_'.$i.$extension;
        clearstatcache();//limpia la cach que deja el file_exists
        if (file_exists($nombre_con_ruta)){
            $encontrado = false;
            while (!$encontrado) {
                $i++;
                $nombre_con_ruta = $targetPath.$ini.'_'.$cod_dj.'_i'.$numero_intento.'_'.$nom_archivo_truncado.'_'.$i.$extension;
                clearstatcache();//limpia la cach que deja el file_exists
                if (!file_exists($nombre_con_ruta)){
                    $nom_archivo = $ini.'_'.$cod_dj.'_i'.$numero_intento.'_'.$nom_archivo_truncado.'_'.$i.$extension;
                    $encontrado = true;
                }
            }
        }else{
            $nom_archivo = $ini.'_'.$cod_dj.'_i'.$numero_intento.'_'.$nom_archivo_truncado.'_'.$i.$extension;
        }
    }else{
        $nom_archivo = $ini.'_'.$cod_dj.'_i'.$numero_intento.$extension;
    }
    
    //$targetFile =  $targetPath.$_FILES['file']['name'];  //5 original
    $targetFile =  $targetPath.$nom_archivo;
   
    $tamano_archivo = $_FILES['file']['size'];
    $tmp_name = $tempFile;
    $error = $_FILES['file']['error'];
    $name_original = $_FILES['file']['name'];
    $type = $_FILES['file']['type'];
    $detalle_error = 'MOVE_FAILED';
    
    if($modo_debug == 'S'){
        $fecha = date("d/m/Y H:i:s");
        $linea = $cod_dj.'|'.$cod_pers.'|'.'DURANTE_SUBIDA|'.$tipo_archivo.'|'.utf8_decode($nom_archivo_original).'|'.$tamano_archivo.'|'.$fecha.'|SIN OBS';
        fwrite($logFile, $linea."\n") or die("Error escribiendo en el archivo");
    }
    
    trace_upload($cod_dj, $tipo_archivo, 'ANTES_MOVE|TARGET='.$targetFile);
    if(move_uploaded_file($tempFile,$targetFile)){
        if($modo_debug == 'S'){
            $fecha = date("d/m/Y H:i:s");
            $linea = $cod_dj.'|'.$cod_pers.'|'.'DURANTE_SUBIDA|'.$tipo_archivo.'|'.utf8_decode($nom_archivo_original).'|'.$tamano_archivo.'|'.$fecha.'|Se guardo el archivo  en el servidor';
            fwrite($logFile, $linea."\n") or die("Error escribiendo en el archivo");
        }
        $exito = true;
        $detalle_error = '';
        trace_upload($cod_dj, $tipo_archivo, 'MOVE_OK|'.$targetFile);
    }else{
        trace_upload($cod_dj, $tipo_archivo, 'MOVE_FAILED|TARGET='.$targetFile);
        if($modo_debug == 'S'){
            $fecha = date("d/m/Y H:i:s");
            $linea = $cod_dj.'|'.$cod_pers.'|'.'DURANTE_SUBIDA|'.$tipo_archivo.'|'.utf8_decode($nom_archivo_original).'|'.$tamano_archivo.'|'.$fecha.'|No se guardo el archivo  en el servidor';
            fwrite($logFile, $linea."\n") or die("Error escribiendo en el archivo");
        }
    }
    
    if($exito){
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $navegador = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $sistema_operativo = PHP_OS;
        $error_int = intval($error);

        $targetFileSql = escape_sql_literal($targetFile);
        $nomArchivoSql = escape_sql_literal($nom_archivo);
        $tmpNameSql = escape_sql_literal($tmp_name);
        $nameOriginalSql = escape_sql_literal($name_original);
        $typeSql = escape_sql_literal($type);
        $errorSql = escape_sql_literal((string)$error);
        $ipSql = escape_sql_literal($ip);
        $sistemaSql = escape_sql_literal($sistema_operativo);
        $navegadorSql = escape_sql_literal($navegador);

        $columnasExtra = ', COD_CARGA_INTENTO';
        $valoresExtra = ', '.$cod_carga_intento;
        if($error_int !== 0){
            $obsRevisado = escape_sql_literal('Archivo '.$name_original.' no subio correctamente');
            $columnasExtra .= ", COD_REVISADO_ARCHIVO, OBS_REVISADO";
            $valoresExtra .= ", 3, '".$obsRevisado."'";
        }

        $sql_insert = "BEGIN
            INSERT INTO DECLARACION_ARCHIVO (
                COD_DECLARACION_ARCHIVO,
                COD_DECLARACION,
                COD_TIPO_ARCHIVO,
                RUTA_ARCHIVO,
                NOM_ARCHIVO,
                TAMANO,
                F_ERROR,
                F_NAME,
                F_TYPE,
                F_TMP_NAME,
                NAVEGADOR,
                IP,
                OS,
                FECHA_SUBIDA".$columnasExtra."
            ) VALUES (
                declaracion_archivo_sq.NEXTVAL,
                $cod_dj,
                $tipo_archivo,
                '".$targetFileSql."',
                '".$nomArchivoSql."',
                $tamano_archivo,
                '".$errorSql."',
                '".$nameOriginalSql."',
                '".$typeSql."',
                '".$tmpNameSql."',
                '".$navegadorSql."',
                '".$ipSql."',
                '".$sistemaSql."',
                SYSDATE".$valoresExtra."
            );
            COMMIT;
        END;";

        trace_upload($cod_dj, $tipo_archivo, 'ANTES_INSERT_DB');
        if(!$db->query($sql_insert)){
            $exito = false;
            $detalle_error = 'DB_INSERT_FAILED';
            trace_upload($cod_dj, $tipo_archivo, 'DB_INSERT_FAILED');
        }else{
            trace_upload($cod_dj, $tipo_archivo, 'DB_INSERT_OK');
        }
    }
}else{
    $detalle_error = 'NO_FILE_RECEIVED';
    trace_upload($cod_dj, $tipo_archivo, 'NO_FILE_RECEIVED');
    if($modo_debug == 'S'){
        $fecha = date("d/m/Y H:i:s");
        $linea = $cod_dj.'|'.$cod_pers.'|'.'ERROR_SUBIDA_FILE_VACIO|'.$tipo_archivo.'|NULL|NULL|'.$fecha.'|$_FILE llego vacio';
        fwrite($logFile, $linea."\n") or die("Error escribiendo en el archivo");
    }
}
if($modo_debug == 'S'){
    fclose($logFile);
}
if($exito){
    trace_upload($cod_dj, $tipo_archivo, 'FIN_UPLOAD|existoso');
    echo 'existoso';
}
else{
    trace_upload($cod_dj, $tipo_archivo, 'FIN_UPLOAD|'.$detalle_error);
    echo $detalle_error;
}
?>    
