<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_familia_deuda = $_REQUEST["cod_familia_deuda"];
$fecha_ingreso = $_REQUEST["fecha_ingreso"];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

if ($fecha_ingreso == 'null'){
	if (K_TIPO_BD=="oci"){
		$sql = "select f_mon_get_valor_moneda(COD_MONEDA, sysdate) VALOR_MONEDA
				from TIPO_DEUDA
				where COD_FAMILIA_DEUDA = $cod_familia_deuda";
	}
	else if (K_TIPO_BD=="mssql"){
		$sql = "select dbo.f_mon_get_valor_moneda(COD_MONEDA, getdate()) VALOR_MONEDA
				from TIPO_DEUDA
				where COD_FAMILIA_DEUDA = $cod_familia_deuda";		
	}	
}
else{
	$fecha_ingreso = base::str2date($fecha_ingreso, '23:59:59');
	
	if (K_TIPO_BD=="oci"){
		$sql = "select f_mon_get_valor_moneda(COD_MONEDA, $fecha_ingreso) VALOR_MONEDA
				from TIPO_DEUDA
				where COD_FAMILIA_DEUDA = $cod_familia_deuda";		
	}
	else if (K_TIPO_BD=="mssql"){
		$sql = "select dbo.f_mon_get_valor_moneda(COD_MONEDA, $fecha_ingreso) VALOR_MONEDA
				from TIPO_DEUDA
				where COD_FAMILIA_DEUDA = $cod_familia_deuda";		
	}	
}

$result = $db->build_results($sql);
print urlencode($result[0]['VALOR_MONEDA']); //todas las deudas de una familia x tendr�n la misma moneda
?>