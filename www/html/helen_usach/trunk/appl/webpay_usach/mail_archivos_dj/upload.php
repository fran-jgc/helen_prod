<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
require_once (dirname(__FILE__)."/../../../appl.ini");
session::set('K_ROOT_DIR', K_ROOT_DIR);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$tipo_archivo = $_REQUEST['tp'];
$cod_dj = $_REQUEST['cod_dj'];
$cardinalidad = $_REQUEST['cardinalidad'];

//se considera la primera Fecha para no crear nuevas carpetas al volver a cargar los archivos
$sql  = "select to_char(FECHA_SUBIDA, 'dd-mm-yyyy') FECHA_SUBIDA
			   ,to_char(FECHA_SUBIDA,'YYYY') ANIO
      		   ,to_number(to_char(FECHA_SUBIDA,'MM')) MES  
		 from DECLARACION_ARCHIVO
		 where cod_declaracion = $cod_dj
		 AND ROWNUM = 1
		 ORDER BY cod_declaracion_archivo asc";
$result = $db->build_results($sql);
$fecha_actual = $result[0]['FECHA_SUBIDA'];
$ano_actual = $result[0]['ANIO'];
$mes_actual = $result[0]['MES'];

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
$folder_bd = "https://sishelen.usach.cl/helen_usach/trunk/appl/webpay_usach/declaracionjurada/".$ano_actual."/".$mes_palabra."/".$fecha_actual."/".$cod_dj."/";

if (!file_exists($folder))
    mkdir($folder, 0777 , true);	// recursive = true
    
$storeFolder = $folder;   //2

if (!empty($_FILES)) {
    $i = 0; // inicia en version archivo 2
    
    foreach($_FILES['file']['tmp_name'] as $key => $tmp_name){ 
        $tempFile = $_FILES['file']['tmp_name'][$key];          //3
        
        //$targetPath = dirname( __FILE__ ) . $ds. $storeFolder . $ds;  //4 original
        $targetPath = $storeFolder;
        $nom_archivo_original = $_FILES['file']['name'][$key];
        
        for($j=0 ; $j < strlen($nom_archivo_original) ; $j++){
            $char = substr($nom_archivo_original, $j, 1);
            if($char == '.')
                $pos = $j;
        }
        $nom_archivo_truncado = substr($nom_archivo_original, 0, 4); //nombre archivo sin extension truncado
        $extension   = substr($nom_archivo_original, $pos, strlen($nom_archivo_original)); //la extension
        
        $v= 2;
        do {
        	if($cardinalidad > 1){
    	        $nom_archivo = $ini.'_'.$cod_dj.'_'.$nom_archivo_truncado.'_v'.$v;
            }else{
                $nom_archivo = $ini.'_'.$cod_dj.'_v'.$v;
            }
            $existe = file_exists ($targetPath.$nom_archivo.$extension);
            if ($existe)
                $v++;        
        } while ($existe);
        
        //agrega extension
        $nom_archivo .= $extension;
        
        //$targetFile =  $targetPath.$_FILES['file']['name'];  //5 original
        $targetFile =  $targetPath.$nom_archivo;
        $targetFileBd = $folder_bd.$nom_archivo;
        $tamano_archivo = $_FILES['file']['size'][$key];
        $tmp_name = $tempFile;
        $error = $_FILES['file']['error'][$key];
        $name_original = $_FILES['file']['name'][$key];
        $type = $_FILES['file']['type'][$key];
        
        $ds = new datos_server();
        
        $ip						= $ds->getRealIP();
        $navegador				= $ds->browser();
        $sistema_operativo		= $ds->so();
        
        if(move_uploaded_file($tempFile,$targetFile)){ //6
            /*Guardado en BD*/
            $sp = "spu_declaracion_archivo_portal";
            $param = "'INSERT',
                        $cod_dj,
                        $tipo_archivo,
                        '$targetFileBd',
                        '$nom_archivo',
                        $tamano_archivo,
                        '$tmp_name',
                        '$name_original',
                        '$type',
                        '$error',
                        '$ip',
                        '$sistema_operativo',
                        '$navegador'";
            
            $db->EXECUTE_SP($sp,$param);
            $i++;
        }
    }
    
}
?>
