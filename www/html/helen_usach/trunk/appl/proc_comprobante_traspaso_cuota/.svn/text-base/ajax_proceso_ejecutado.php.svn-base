<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$proceso_ctc_traspaso_cuota = 6;
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
if (K_TIPO_BD=="oci"){
	$sql = "SELECT	TO_CHAR(FECHA_EJECUCION_PROCESO,'dd/mm/yyyy') FECHA_EJECUCION_PROCESO
			FROM	EJECUCION_PROCESO
			WHERE	COD_PROCESO = $proceso_ctc_traspaso_cuota
			AND		to_char(FECHA_EJECUCION_PROCESO, 'YYYY') = to_char(sysdate, 'YYYY')";
	
}else if(K_TIPO_BD=="mssql"){
	$sql = "SELECT	CONVERT(VARCHAR(10),FECHA_EJECUCION_PROCESO, 103) FECHA_EJECUCION_PROCESO
			FROM	EJECUCION_PROCESO
			WHERE	COD_PROCESO = $proceso_ctc_traspaso_cuota
			AND		year(FECHA_EJECUCION_PROCESO) = year(getdate())";
}
		
$result = $db->build_results($sql);
$count = count($result);
if($count == 0)
	print urlencode($count."|''");
else
	print urlencode($count."|".$result[0]['FECHA_EJECUCION_PROCESO']);
?>