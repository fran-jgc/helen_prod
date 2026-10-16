<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_familia = $_REQUEST["cod_familia"];

$resultado = "";
$K_DESCTO_TODO = 21;

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

if ($cod_familia == 1){
	$sql = "SELECT COD_TIPO_DESCUENTO
				  ,NOM_TIPO_DESCUENTO
			      ,PORC_DESCUENTO
			FROM TIPO_DESCUENTO
			WHERE COD_TIPO_DESCUENTO <> $K_DESCTO_TODO
			ORDER BY COD_TIPO_DESCUENTO";
}
else
{
	$sql = "SELECT COD_TIPO_DESCUENTO
				  ,NOM_TIPO_DESCUENTO
			      ,PORC_DESCUENTO
			FROM TIPO_DESCUENTO
			WHERE COD_TIPO_DESCUENTO = $K_DESCTO_TODO
			ORDER BY COD_TIPO_DESCUENTO";
	
	
}

$result = $db->build_results($sql);
print urlencode(json_encode($result));
?>