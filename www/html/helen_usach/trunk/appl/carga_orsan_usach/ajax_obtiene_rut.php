<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_boleta	= $_REQUEST['cod_boleta'];
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

$sql = "select COUNT(*)CANT
		from DEUDOR DE,DEUDA D,BOLETA BO
		where DE.COD_DEUDOR=D.COD_DEUDOR
		and BO.COD_DEUDA=D.COD_DEUDA
		and BO.COD_BOLETA=$cod_boleta";

$result = $db->build_results($sql);
$count=$db->count_rows();

if($count<>0){

$sql = "select DE.RUT
		from DEUDOR DE,DEUDA D,BOLETA BO,SUB_BOLETA SB
		where DE.COD_DEUDOR=D.COD_DEUDOR
		AND BO.COD_BOLETA = SB.COD_BOLETA
		and BO.COD_DEUDA=D.COD_DEUDA
		and BO.COD_BOLETA=$cod_boleta";

$result = $db->build_results($sql);
}else 
$result=0;

print urlencode(json_encode($result));	
?>