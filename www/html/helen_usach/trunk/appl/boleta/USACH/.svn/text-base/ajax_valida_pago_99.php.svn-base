<?php
////////////////////// USACH ///////////////////////////////
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
$cod_plan_pago		= $_REQUEST['cod_plan_pago'];

$sql_deuda = "SELECT CASE Count(*)
						when 0 then 'N' 
						else 'S' 
					 end EXISTE_CUOTA_ACTUAL 
				FROM plan_pago
				WHERE cod_deuda IN (SELECT cod_deuda FROM plan_pago
									WHERE cod_plan_pago = $cod_plan_pago)
				AND ano_cobro = To_Number(To_Char(SYSDATE, 'yyyy'))";

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql_deuda);

print $result[0]['EXISTE_CUOTA_ACTUAL'];	
?>