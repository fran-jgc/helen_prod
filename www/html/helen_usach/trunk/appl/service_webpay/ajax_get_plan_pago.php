<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../web_service/class_ws_helen.php");

$cod_deuda = $_REQUEST['cod_deuda'];
$cod_deudor = session::get('COD_DEUDOR');

$ws_helen = new wakeUp_helen();
		
$result = $ws_helen->ajax_plan_pago_1($cod_deuda);

$existe_cuota_actual = $result[0]['EXISTE_CUOTA_ACTUAL'];

$cod_pp_99 = 0;
if($existe_cuota_actual == 'N'){
	//obtiene la cuota 99
	$result_futura = $ws_helen->ajax_plan_pago_2($cod_deuda);
	
	$cod_pp_99 = $result_futura[0]['COD_PLAN_PAGO'];
}

if ($cod_pp_99 == ''){
	$result = $ws_helen->ajax_plan_pago_3($cod_deuda);
}
else{
	$result = $ws_helen->ajax_plan_pago_4($cod_deuda,$cod_pp_99);
}

$row_count = count($result);
if ($row_count == 0){
	print urlencode('NO_EXISTE_PLAN_PAGO');
}else{
	print urlencode(json_encode($result));
}
?>