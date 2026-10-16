<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$resultado = "";
$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

$sql = "select COUNT(*) CANT from comprobante c
		where (select SUM(a.monto_debe) from ITEM_COMPROBANTE a where a.COD_COMPROBANTE = c.COD_COMPROBANTE)
		<> (select SUM(b.monto_haber) from ITEM_COMPROBANTE b where b.COD_COMPROBANTE = c.COD_COMPROBANTE)";

$result = $db->build_results($sql);
if ($result[0]['CANT']== 0)
	print (0);
else{
	$sql = "select COD_COMPROBANTE from comprobante c
			where (select SUM(a.monto_debe) from ITEM_COMPROBANTE a where a.COD_COMPROBANTE = c.COD_COMPROBANTE)
			<> (select SUM(b.monto_haber) from ITEM_COMPROBANTE b where b.COD_COMPROBANTE = c.COD_COMPROBANTE)";
	
	$result = $db->build_results($sql);	
	$row_count = $db->count_rows();	
	for($i=0;$i<$row_count;$i++){
		$resultado .= $result[$i]['COD_COMPROBANTE'].",";	
	}
	$resultado = substr ($resultado, 0, strlen($resultado) - 1);
	print($resultado);
}
?>