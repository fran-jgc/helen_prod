<?php
////////////////////// USACH ///////////////////////////////
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
$cod_deuda		= $_REQUEST['cod_deuda'];

$sql_deuda = "SELECT CASE Count(*)
						when 0 then 'N' 
						else 'S' 
					 end EXISTE_CUOTA_ACTUAL 
				FROM plan_pago
				WHERE cod_deuda = $cod_deuda
				AND ano_cobro = To_Number(To_Char(SYSDATE, 'yyyy'))";

$sql_exigible = "SELECT COD_ESTADO_DEUDA
				FROM DEUDA
				WHERE COD_DEUDA = $cod_deuda";

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql_exigible);

if($result[0]['COD_ESTADO_DEUDA'] == 2){//cobranza_normal
	$result = $db->build_results($sql_deuda);
	$existe = $result[0]['EXISTE_CUOTA_ACTUAL'];
}else{
	$existe = 'S';	
}

print $existe;	
?>