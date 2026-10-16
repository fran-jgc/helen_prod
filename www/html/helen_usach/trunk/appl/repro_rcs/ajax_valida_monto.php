<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

$rut	 	= $_REQUEST['rut'];
$valor 		= $_REQUEST['monto'];
//rescata el cod_deudor
$sql_deudor = sql_repro_rcs::get_sql('AJAX_CARGA_DEUDOR',$rut);
$result_deudor = $db->build_results($sql_deudor);

$sql_deudas = sql_repro_rcs::get_sql('AJAX_CARGA_DEUDA',$result_deudor[0]['COD_DEUDOR']);
$result = $db->build_results($sql_deudas);


$mensaje	= "";

for($i=0;$i<count($result);$i++)
{
	$valor = $result[$i]['SALDO_CAPITAL'];

	if($saldo_capital < $valor){
		$mensaje = 'S';
	}
	else{
		$mensaje = 'N';
		$i = count($result);
	}
}

print urlencode(json_encode($mensaje));

?>