<?php
///////////////////////
// mensaje de "debe seleccionar deuda�." no aplica al grabar. 
// Al momento de condonar una deuda CS y esta se encuentra morosa no debe permitir 
// hacerlo por el ministerio de la ley.
////////////////////////

require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_deuda = $_REQUEST['cod_deuda'];
$K_DEUDA_MOROSA = 4;
$sql_deuda  = "";

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
//si la deuda esta tiene planes de pago 12 ó 15
$sql_deuda  =	"SELECT Count(*)  CANT_PP
				 FROM PLAN_PAGO
				 WHERE COD_DEUDA =  $cod_deuda
				 AND ano_cobro = To_Number(To_Char(SYSDATE, 'yyyy'))
				 AND (nro_cuota = 12 OR nro_cuota = 15)
				 AND nro_cuota <> 99";
$result = $db->build_results($sql_deuda);
$row_count = $result[0]['CANT_PP'];

If ($row_count == 0){
	
	print urlencode('CUOTA_12_15');
	return;
}else{
	//si la deuda tiene planes de pago morosos
	$sql_deuda  =	"SELECT Count(*) CANT_PP_MOROSA
					 FROM  PLAN_PAGO
					 WHERE COD_DEUDA =  $cod_deuda
					 AND nro_cuota <> 99
					 AND COD_ESTADO_PLAN_PAGO = $K_DEUDA_MOROSA";
	$result = $db->build_results($sql_deuda);
	$row_count = $result[0]['CANT_PP_MOROSA'];
	If ($row_count == 0){
	
		print urlencode('true');
		return;
	}else{
		print urlencode('DEUDA_MOROSA');
		return;
	}
}
?>