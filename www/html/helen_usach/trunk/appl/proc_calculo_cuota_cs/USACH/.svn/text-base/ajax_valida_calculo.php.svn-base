<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

$K_ESTADO_DECLARACION  = 1;

$sql = "SELECT  COUNT(*) CANTIDAD
		FROM DECLARACION D, DEUDOR DR, DEUDA DA
		WHERE   D.COD_DEUDOR = DR.COD_DEUDOR
			AND     D.COD_DEUDOR = DA.COD_DEUDOR
			AND     D.ANO+1 = TO_NUMBER(TO_CHAR(SYSDATE, 'YYYY'))
			AND     DA.COD_TIPO_DEUDA = 'CS'
			AND     D.COD_ESTADO_DECLARACION =".$K_ESTADO_DECLARACION;
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql);
$row_count = $db->count_rows();
$cantidad = $result[0]['CANTIDAD'];

if ($cantidad > 0){
	$respuesta = 1;
}
else{
	$respuesta = 0;
}
print $respuesta;	
?>			