<?php
ini_set('display_errors', 'Off');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");

include("../w_mail_class/class_email.php");

$cod_deudor = $_REQUEST['cod_pers'];
$cod_declaracion = $_REQUEST['cod_dj'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

$sql_cd = "SELECT f_get_direccion_deudor($cod_deudor,'P')DIR from dual";
$result_direccion = $db->build_results($sql_cd);
$cod_direccion = $result_direccion[0]['DIR'];


$sql_dir = "SELECT EMAIL FROM direccion WHERE COD_DIRECCION = $cod_direccion";
$result_mail = $db->build_results($sql_dir);

$dir_email = $result_mail[0]['EMAIL'];
$asunto 			 = "Declaracion Jurada, documentos ingresados";
$address             = $dir_email;//'ibrito@integrasystem.cl';//

$sql = "SELECT 	NOMBRE ||' '|| APELLIDO_PATERNO ||' '|| APELLIDO_MATERNO NOMBRE
			FROM 	DEUDOR
			WHERE 	COD_DEUDOR = $cod_deudor";
$result = $db->build_results($sql);
$nombre= $result[0]['NOMBRE'];

if(!empty($address)){
    $mail = new Email();
    
    $temp = new Template('mail_dj_confirmada.htm');
    $temp->setVar("NOMBRE", "$nombre");
    $pdf = false;
    
    $body = $temp->toString();
    $mail->dj_mail($address,$body,$asunto,$pdf);
}
echo 'CORRECTO';

?>    
