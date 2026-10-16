<?php
///////////////////////
// php llamado con ajax en el javascript function f_get_info_sub_boleta()
////////////////////////
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_sub_boleta = $_REQUEST['cod_sub_boleta'];

$sql = "SELECT 	SB.MONTO_CAPITAL
		     	,SB.MONTO_INTERES
     			,SB.MONTO_MULTA
     			,SB.MONTO_CAPITAL + SB.MONTO_INTERES + SB.MONTO_MULTA TOTAL_MONTO
     			,case B.CANT
					when 1 then B.MONTO_PESOS
					else round((SB.MONTO_CAPITAL + SB.MONTO_INTERES + SB.MONTO_MULTA) * dbo.f_mon_get_valor_moneda(T.COD_MONEDA, getdate()), 0) 
				 end TOTAL_MONTO_PESO
     			,case B.CANT
					when 1 then B.MONTO_PESOS
					else round((SB.MONTO_CAPITAL + SB.MONTO_INTERES + SB.MONTO_MULTA) * dbo.f_mon_get_valor_moneda(T.COD_MONEDA, getdate()), 0) 
				 end TOTAL_MONTO_PESO_H
				 
     			,B.DSCTO_CAPITAL
     			,B.DSCTO_INTERES
     			,B.DSCTO_MULTA
     			,B.DSCTO_CAPITAL + B.DSCTO_INTERES + B.DSCTO_MULTA TOTAL_DSCTO
		     	,B.DSCTO_PESOS TOTAL_DSCTO_PESO
		     	,(SB.MONTO_CAPITAL + SB.MONTO_INTERES + SB.MONTO_MULTA) + (B.DSCTO_CAPITAL + B.DSCTO_INTERES + B.DSCTO_MULTA) TOTAL
				,case B.CANT
					when 1 then B.MONTO_PESOS + B.DSCTO_PESOS
					else round((SB.MONTO_CAPITAL + SB.MONTO_INTERES + SB.MONTO_MULTA) * dbo.f_mon_get_valor_moneda(T.COD_MONEDA, getdate()), 0) + round((B.DSCTO_CAPITAL + B.DSCTO_INTERES + B.DSCTO_MULTA) * dbo.f_mon_get_valor_moneda(T.COD_MONEDA, getdate()), 0) 
					end TOTAL_PESO
				,case B.CANT
					when 1 then B.MONTO_PESOS + B.DSCTO_PESOS
					else round((SB.MONTO_CAPITAL + SB.MONTO_INTERES + SB.MONTO_MULTA) * dbo.f_mon_get_valor_moneda(T.COD_MONEDA, getdate()), 0) + round((B.DSCTO_CAPITAL + B.DSCTO_INTERES + B.DSCTO_MULTA) * dbo.f_mon_get_valor_moneda(T.COD_MONEDA, getdate()), 0) 
				end TOTAL_PESO_H
		  FROM 	SUB_BOLETA SB
		     	,BOLETA B
		     	,DEUDA D
		     	,TIPO_DEUDA T
		 WHERE 	SB.COD_BOLETA = B.COD_BOLETA
		   		AND COD_SUB_BOLETA = $cod_sub_boleta
		   		AND D.COD_DEUDA = B.COD_DEUDA
		   		AND T.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA";

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql);
$row_count = $db->count_rows();
$respuesta = '';

if ($row_count == 0){
	$respuesta = "X|";
}
else{
	$respuesta =  $result[0]['MONTO_CAPITAL']."|";
	$respuesta =  $respuesta.$result[0]['MONTO_INTERES']."|";
	$respuesta =  $respuesta.$result[0]['MONTO_MULTA']."|";
	$respuesta =  $respuesta.$result[0]['TOTAL_MONTO']."|";
	$respuesta =  $respuesta.$result[0]['TOTAL_MONTO_PESO']."|";
	$respuesta =  $respuesta.$result[0]['DSCTO_CAPITAL']."|";
	$respuesta =  $respuesta.$result[0]['DSCTO_INTERES']."|";
	$respuesta =  $respuesta.$result[0]['DSCTO_MULTA']."|";
	$respuesta =  $respuesta.$result[0]['TOTAL_DSCTO']."|";
	$respuesta =  $respuesta.$result[0]['TOTAL_DSCTO_PESO']."|";
	$respuesta =  $respuesta.$result[0]['TOTAL']."|";
	$respuesta =  $respuesta.$result[0]['TOTAL_PESO']."|";
}

print urlencode($respuesta);

?>