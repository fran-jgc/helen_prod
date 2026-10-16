<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

$cod_deuda = $_REQUEST['cod_deuda'];
$cod_deudor = session::get('COD_DEUDOR');

$sql_deuda = "SELECT CASE Count(*)
						when 0 then 'N' 
						else 'S' 
					 end EXISTE_CUOTA_ACTUAL 
				FROM plan_pago
				WHERE cod_deuda = $cod_deuda
				AND ano_cobro = To_Number(To_Char(SYSDATE, 'yyyy'))";

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql_deuda);

$existe_cuota_actual = $result[0]['EXISTE_CUOTA_ACTUAL'];

$cod_pp_99 = 0;
if($existe_cuota_actual == 'N'){
	//obtiene la cuota 99
	$sql_deuda = "SELECT	COD_PLAN_PAGO 
				  FROM plan_pago
				  WHERE cod_deuda = $cod_deuda
				  AND NRO_CUOTA = 99";
	$result_futura = $db->build_results($sql_deuda);
	
	$cod_pp_99 = $result_futura[0]['COD_PLAN_PAGO'];
}

	$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	if ($cod_pp_99 == ''){
		$sql = "SELECT	 pp.COD_PLAN_PAGO
				,pp.NRO_CUOTA
				,pp.ANO_COBRO
				,to_char(pp.FECHA_VENCTO, 'dd/mm/yyyy') FECHA_VENCTO
				,f_pp_saldo_plan_pago(pp.COD_PLAN_PAGO, sysdate) SALDO
				,round(f_pp_saldo_plan_pago(pp.COD_PLAN_PAGO, sysdate) * f_mon_get_valor_moneda(td.COD_MONEDA, sysdate),0) SALDO_PESOS
				,'S' ES_RADIO_BUTTON
		FROM	plan_pago pp, deuda de, tipo_deuda td
		WHERE	pp.COD_DEUDA = $cod_deuda
		AND		pp.COD_DEUDA = de.COD_DEUDA
		AND		de.COD_TIPO_DEUDA = td.COD_TIPO_DEUDA
		AND		pp.COD_ESTADO_PLAN_PAGO NOT IN (3, 4)	-- PAGADA O MOROSA
		UNION 
		  SELECT  pp.COD_PLAN_PAGO
			 ,pp.NRO_CUOTA
			 ,pp.ANO_COBRO
			 ,to_char(pp.FECHA_VENCTO, 'dd/mm/yyyy') FECHA_VENCTO
			 ,f_pp_saldo_plan_pago(pp.COD_PLAN_PAGO, sysdate) SALDO
			 ,round(f_pp_saldo_plan_pago(pp.COD_PLAN_PAGO, sysdate) * f_mon_get_valor_moneda(td.COD_MONEDA, sysdate),0) SALDO_PESOS
			 ,'S' ES_RADIO_BUTTON
		    FROM plan_pago pp, deuda de, tipo_deuda td
  		   WHERE pp.COD_DEUDA = $cod_deuda
 		     AND pp.COD_DEUDA = de.COD_DEUDA
		     AND de.COD_TIPO_DEUDA = td.COD_TIPO_DEUDA
		     AND pp.COD_ESTADO_PLAN_PAGO= 4 -- MOROSA 2012
		     AND pp.ANO_COBRO='2012'
		ORDER BY 2 ASC";
	}
	else{
		$sql = "SELECT	 pp.COD_PLAN_PAGO
				,pp.NRO_CUOTA
				,pp.ANO_COBRO
				,to_char(pp.FECHA_VENCTO, 'dd/mm/yyyy') FECHA_VENCTO
				,f_pp_saldo_plan_pago(pp.COD_PLAN_PAGO, sysdate) SALDO
				,round(f_pp_saldo_plan_pago(pp.COD_PLAN_PAGO, sysdate) * f_mon_get_valor_moneda(td.COD_MONEDA, sysdate),0) SALDO_PESOS
				,'S' ES_RADIO_BUTTON
		FROM	plan_pago pp, deuda de, tipo_deuda td
		WHERE	pp.COD_DEUDA = $cod_deuda
		AND		pp.COD_PLAN_PAGO <> $cod_pp_99
		AND		pp.COD_DEUDA = de.COD_DEUDA
		AND		de.COD_TIPO_DEUDA = td.COD_TIPO_DEUDA
		AND		pp.COD_ESTADO_PLAN_PAGO NOT IN (3, 4)	-- PAGADA O MOROSA
		ORDER BY pp.NRO_CUOTA ASC";		
	}

$result = $db->build_results($sql);
$row_count = $db->count_rows();
if ($row_count == 0){
	print urlencode('NO_EXISTE_PLAN_PAGO');
}else{
	$result 	= $db->build_results($sql);
	print urlencode(json_encode($result));
}
?>
