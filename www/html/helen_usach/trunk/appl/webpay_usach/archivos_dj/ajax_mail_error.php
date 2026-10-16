<?php
ini_set('display_errors', 'Off');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");

include("../w_mail_class/class_email.php");

$COD_DEUDOR = $_REQUEST['cod_pers'];


$dbc = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

$sql = "select RUT,
DIG_VERIF,
NOMBRE || ' ' || APELLIDO_PATERNO || ' ' || APELLIDO_MATERNO NOMBRE
from DEUDOR where COD_DEUDOR = $COD_DEUDOR";
$resultado = $dbc->build_results($sql);

$rut = number_format($resultado[0]['RUT'], 0, '.', '.');
$dig_verif=$resultado[0]['DIG_VERIF'];
$rut_completo = $rut.'-'.$dig_verif;

$nombre = $resultado[0]['NOMBRE'];

$asunto 			 = "Error en la Declaracion Jurada";
$address             = 'francisco.rojas.o@usach.cl';
//$address             = 'ibrito@integrasystem.cl';

$mail = new Email();

$temp = new Template('mail_dj_error.htm');
$temp->setVar("NOMBRE", "$nombre");
$temp->setVar("RUT", "$rut_completo");
$pdf = false;

$body = $temp->toString();
if($mail->dj_mail($address,$body,$asunto,$pdf)){
    echo 'CORRECTO';
    return true;
}

?>    
