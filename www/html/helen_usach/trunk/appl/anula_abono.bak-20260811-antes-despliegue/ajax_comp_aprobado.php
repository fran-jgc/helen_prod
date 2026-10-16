<?php
///////////////////////
// php llamado con ajax en el javascript 
////////////////////////
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$K_COMP_APROBADO = 2;
$cod_abono = $_REQUEST['cod_abono'];
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql =sql_anula_abono::get_sql('COMP_APROBADO',$cod_abono);
$result = $db->build_results($sql);
if ($result[0]['COD_COMPROBANTE']='')
	$resp = 1;
else if ($result[0]['COD_ESTADO_COMPROBANTE']=$K_COMP_APROBADO)
	$resp = 1;
else
	$resp = 0;
print $resp;
?>