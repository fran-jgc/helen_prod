<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

//ini_set('display_errors', 'On');
include("funciones.php");
include("mail/class_email.php");

$cod_usuario = session::get("COD_USUARIO");
$nom_usuario = session::get("NOM_USUARIO");

$cod_declaracion = $_REQUEST['cod_declaracion'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

$sql = "SELECT COD_DEUDOR FROM declaracion WHERE cod_declaracion = $cod_declaracion";
$result = $db->build_results($sql);
$cod_deudor = $result[0]['COD_DEUDOR'];

$sql_mail = "SELECT MAIL FROM USUARIO WHERE COD_USUARIO =$cod_usuario";
$result_mail = $db->build_results($sql_mail);
$mail_usuario = $result_mail[0]['MAIL'];


$sql_cd = "SELECT f_get_direccion_deudor($cod_deudor,'P')DIR FROM DUAL";
$result_direccion = $db->build_results($sql_cd);
$cod_direccion = $result_direccion[0]['DIR'];


$sql_dir = "SELECT EMAIL FROM direccion WHERE COD_DIRECCION = $cod_direccion";
$result_mail = $db->build_results($sql_dir);

$dir_email = $result_mail[0]['EMAIL'];
$asunto 			 = utf8_decode("Declaración Jurada, con documentos observados");
$address             = $dir_email;

$sql = "SELECT 	NOMBRE ||' '|| APELLIDO_PATERNO ||' '|| APELLIDO_MATERNO as NOMBRE
			FROM 	DEUDOR
			WHERE 	COD_DEUDOR = $cod_deudor";
$result = $db->build_results($sql);
$nombre=$result[0]['NOMBRE'];


$sql= "SELECT TA.NOM_TIPO_ARCHIVO
		     ,OBS_REVISADO
	   FROM DECLARACION_ARCHIVO DA, TIPO_ARCHIVO TA
	   where DA.COD_TIPO_ARCHIVO = TA.COD_TIPO_ARCHIVO
	   and DA.COD_DECLARACION = $cod_declaracion
	   and DA.COD_REVISADO_ARCHIVO = 3
	   order by DA.COD_TIPO_ARCHIVO, COD_DECLARACION_ARCHIVO";

$result = $db->build_results($sql);	

$obs_archivos= "";
for($i=0; $i < count($result); $i++){
	$obs_archivos.= "<tr><td>-".utf8_decode($result[$i]['NOM_TIPO_ARCHIVO'])."</td><td>".utf8_decode($result[$i]['OBS_REVISADO'])."</td><tr>";
}
	    	   
$enviado = 0;
if(!empty($address)){
    $mail = new Email();
    
    $temp = new Template('mail_dj_confirmada.htm');
    $temp->setVar("NOMBRE", "$nombre");
    $temp->setVar("OBS_ARCHIVOS", "$obs_archivos");
    
    $cod_dj_encrypt = base64url_encode(encriptar_url($cod_declaracion, 'ENCRIPTACION_DJ'));
    $temp->setVar("LINK", "https://sishelen.usach.cl/helen_usach/trunk/appl/webpay_usach/mail_archivos_dj/archivos_dj.php?cdj=".$cod_dj_encrypt);
    
    $pdf = false;
    
    $body = $temp->toString();
    if($mail->dj_mail($address,$body,$asunto,$pdf,$mail_usuario,$nom_usuario)){
        $enviado = 1;
        $sp = "spu_declaracion_archivo_dj";
        $param = "'UPDATE_ENVIO'
          		    ,$cod_declaracion
          		    ,null
                    ,4
                    ,null
                    ,null
                    ,null 
                    ,null
                    ,null
                    ,null
                    ,null
                    ,null
                    ,null
                    ,null
                    ,null
                    ,null
                    ,null"; 
        
        if ($db->EXECUTE_SP($sp, $param)){
        	$enviado = 1;
        }
    }
}
print urlencode($enviado);
?>    
