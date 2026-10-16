<?php
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_deuda = $_REQUEST['cod_deuda'];
$K_DEUDA_MOROSA = 4;
$sql_deuda  = "";

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

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

?>