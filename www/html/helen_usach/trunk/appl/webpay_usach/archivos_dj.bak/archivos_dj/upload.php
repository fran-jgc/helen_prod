<?php
ini_set('display_errors', 'On');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');

$tipo_archivo = $_REQUEST['tp'];
$cod_dj = $_REQUEST['cod_dj'];
$cardinalidad = $_REQUEST['cardinalidad'];
$modo_debug = $_REQUEST['debug'];
$cod_pers = $_REQUEST['cod_pers'];
$exito = false;

if($modo_debug == 'S'){
    $logFile = fopen("../declaracionjurada/log.txt", 'a') or die("Error creando archivo");
    $fecha = date("d/m/Y H:i:s");
    $linea = $cod_dj.'|'.$cod_pers.'|'.'INICIO_UPLOAD|'.$tipo_archivo.'|NULL|NULL|'.$fecha.'|SIN OBS';
    fwrite($logFile, $linea."\n") or die("Error escribiendo en el archivo");
}

require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
require_once (dirname(__FILE__)."/../../../appl.ini");
session::set('K_ROOT_DIR', K_ROOT_DIR);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$ano_actual = $db->current_year();
$mes_actual = $db->current_month();
$fecha_actual = $db->current_date();
$fecha_actual = str_replace('/','-',$fecha_actual); 

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
    mkdir($folder, 0777 , true);	// recursive = true
}
$storeFolder = $folder;   //2

if (!empty($_FILES)) {
    $i = 0;
    
    $tempFile = $_FILES['file']['tmp_name'];        //3
        
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
        $nombre_con_ruta = $targetPath.$ini.'_'.$cod_dj.'_'.$nom_archivo_truncado.'_'.$i.$extension;
        clearstatcache();//limpia la cach que deja el file_exists
        if (file_exists($nombre_con_ruta)){
            $encontrado = false;
            while (!$encontrado) {
                $i++;
                $nombre_con_ruta = $targetPath.$ini.'_'.$cod_dj.'_'.$nom_archivo_truncado.'_'.$i.$extension;
                clearstatcache();//limpia la cach que deja el file_exists
                if (!file_exists($nombre_con_ruta)){
                    $nom_archivo = $ini.'_'.$cod_dj.'_'.$nom_archivo_truncado.'_'.$i.$extension;
                    $encontrado = true;
                }
            }
        }else{
            $nom_archivo = $ini.'_'.$cod_dj.'_'.$nom_archivo_truncado.'_'.$i.$extension;
        }
    }else{
        $nom_archivo = $ini.'_'.$cod_dj.$extension;
    }
    
    //$targetFile =  $targetPath.$_FILES['file']['name'];  //5 original
    $targetFile =  $targetPath.$nom_archivo;
   
    $tamano_archivo = $_FILES['file']['size'];
    $tmp_name = $tempFile;
    $error = $_FILES['file']['error'];
    $name_original = $_FILES['file']['name'];
    $type = $_FILES['file']['type'];
    
    if($modo_debug == 'S'){
        $fecha = date("d/m/Y H:i:s");
        $linea = $cod_dj.'|'.$cod_pers.'|'.'DURANTE_SUBIDA|'.$tipo_archivo.'|'.utf8_decode($nom_archivo_original).'|'.$tamano_archivo.'|'.$fecha.'|SIN OBS';
        fwrite($logFile, $linea."\n") or die("Error escribiendo en el archivo");
    }
    
    if(move_uploaded_file($tempFile,$targetFile)){
        if($modo_debug == 'S'){
            $fecha = date("d/m/Y H:i:s");
            $linea = $cod_dj.'|'.$cod_pers.'|'.'DURANTE_SUBIDA|'.$tipo_archivo.'|'.utf8_decode($nom_archivo_original).'|'.$tamano_archivo.'|'.$fecha.'|Se guardo el archivo  en el servidor';
            fwrite($logFile, $linea."\n") or die("Error escribiendo en el archivo");
        }
        $exito = true;
    }else{
        if($modo_debug == 'S'){
            $fecha = date("d/m/Y H:i:s");
            $linea = $cod_dj.'|'.$cod_pers.'|'.'DURANTE_SUBIDA|'.$tipo_archivo.'|'.utf8_decode($nom_archivo_original).'|'.$tamano_archivo.'|'.$fecha.'|No se guardo el archivo  en el servidor';
            fwrite($logFile, $linea."\n") or die("Error escribiendo en el archivo");
        }
    }
    
    $ds = new datos_server();
    
    $ip						= $ds->getRealIP();
    $navegador				= $ds->browser();
    $sistema_operativo		= $ds->so();
    
    
    /*Guardado en BD*/

    if($exito){
        $sp = "spu_declaracion_archivo_portal";
        $param = "'INSERT',
                    $cod_dj,
                    $tipo_archivo,
                    '$targetFile',
                    '$nom_archivo',
                    $tamano_archivo,
                    '$tmp_name',
                    '$name_original',
                    '$type',
                    '$error',
                    '$ip',
                    '$sistema_operativo',
                    '$navegador'";
        
        if($db->EXECUTE_SP($sp,$param)){
            if($modo_debug == 'S'){
                $fecha = date("d/m/Y H:i:s");
                $linea = $cod_dj.'|'.$cod_pers.'|'.'DESPUES_SUBIDA_BD|'.$tipo_archivo.'|'.utf8_decode($nom_archivo_original).'|'.$tamano_archivo.'|'.$fecha.'|guardado correcto en bd';
                fwrite($logFile, $linea."\n") or die("Error escribiendo en el archivo");
            }
        }else{
            
            $fecha = date("d/m/Y H:i:s");
            $linea = $cod_dj.'|'.$cod_pers.'|'.'DESPUES_SUBIDA_BD|'.$tipo_archivo.'|'.utf8_decode($nom_archivo_original).'|'.$tamano_archivo.'|'.$fecha.'|guardado incorrecto en bd';
            fwrite($logFile, $linea."\n") or die("Error escribiendo en el archivo");
        }
    }else if($modo_debug == 'S'){
        $fecha = date("d/m/Y H:i:s");
        $linea = $cod_dj.'|'.$cod_pers.'|'.'DESPUES_SUBIDA_BD|'.$tipo_archivo.'|'.utf8_decode($nom_archivo_original).'|'.$tamano_archivo.'|'.$fecha.'|no se inserta en bd porque la subida fallo';
        fwrite($logFile, $linea."\n") or die("Error escribiendo en el archivo");
    }
}else{
    if($modo_debug == 'S'){
        $fecha = date("d/m/Y H:i:s");
        $linea = $cod_dj.'|'.$cod_pers.'|'.'ERROR_SUBIDA_FILE_VACIO|'.$tipo_archivo.'|NULL|NULL|'.$fecha.'|$_FILE llego vacio';
        fwrite($logFile, $linea."\n") or die("Error escribiendo en el archivo");
    }
}
if($modo_debug == 'S'){
    fclose($logFile);
}
if($exito)echo json_encode('existoso');
else echo json_encode('fracaso');
?>    
