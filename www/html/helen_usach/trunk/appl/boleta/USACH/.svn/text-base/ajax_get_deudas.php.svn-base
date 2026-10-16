<?php
////////////////////// USACH ///////////////////////////////
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
$cod_deuda		= $_REQUEST['cod_deuda'];

$sql_deuda = sql_boleta::get_sql('AJAX_DEUDAS',$cod_deuda);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$result = $db->build_results($sql_deuda);

$estado_deuda 	= $result[0]['COD_ESTADO_DEUDA'];
$cod_tipo_deuda = $result[0]['COD_TIPO_DEUDA'];

$result = $db->build_results(sql_boleta::get_sql('AJAX_SALDO_DEUDA',$cod_deuda));
$saldo_deuda = $result[0]['SALDO_DEUDA'];

$result = $db->build_results(sql_boleta::get_sql('AJAX_SALDO_CAPITAL',$saldo_deuda,$cod_deuda)); 
$capital = $result[0]['SALDO_CAPITAL'];

$interes =  $saldo_deuda - $capital;

$multa = 0; 
$total = $capital+$interes+$multa;

$result = $db->build_results(sql_boleta::get_sql('AJAX_VALOR_MONEDA',$cod_tipo_deuda)); 
$valor_moneda = $result[0]['VALOR_MONEDA'];

$wi = unserialize(session::get('wi_boleta'));
$sql_original = $wi->dws['dw_detalle_plan_pago']->get_sql();

if($estado_deuda == 1) {	
	$sql = sql_boleta::get_sql('AJAX_DEUDAS_1',$capital,$saldo_deuda,$multa,$total,$valor_moneda,$cod_deuda);
}
else{
	$sql = "SELECT  	PP.COD_PLAN_PAGO								COD_PLAN_PAGO
						,PP.COD_PLAN_PAGO								COD_PLAN_PAGO_H
						,PP.ANO_COBRO 									ANO_COBRO
						,PP.ANO_COBRO 									ANO_COBRO_H
        				,PP.NRO_CUOTA									NRO_CUOTA
        				,TO_CHAR(PP.FECHA_VENCTO,'dd/mm/yyyy') 			FECHA_VENCTO
        				,TO_CHAR(PP.FECHA_VENCTO,'dd/mm/yyyy') 			FECHA_VENCTO_H
        				,F_PP_SALDO_CAPITAL(PP.COD_PLAN_PAGO) 			SALDO_CAPITAL
        				,F_PP_SALDO_INTERES_NORMAL(PP.COD_PLAN_PAGO) 	SALDO_INTERES
        				,0 SALDO_MULTA 
        				,f_pp_saldo_capital(cod_plan_pago) + f_pp_saldo_interes_normal(cod_plan_pago) SALDO_MONEDA         
        				,F_MON_GET_VALOR_MONEDA(M.COD_MONEDA) * (f_pp_saldo_capital(cod_plan_pago) + f_pp_saldo_interes_normal(cod_plan_pago)) SALDO_PESOS 
        				,PP.COD_ESTADO_PLAN_PAGO COD_ESTADO_PLAN_PAGO_H
        				,EPP.NOM_ESTADO_PLAN_PAGO
				FROM    PLAN_PAGO PP
						, DEUDA D
						, MONEDA M
						, TIPO_DEUDA TD
						, ESTADO_PLAN_PAGO EPP
				WHERE   D.COD_DEUDA = $cod_deuda
						AND F_PP_SALDO_PLAN_PAGO (PP.COD_PLAN_PAGO) > 0
						AND D.COD_DEUDA = PP.COD_DEUDA
						AND TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA
						AND TD.COD_MONEDA = M.COD_MONEDA
						AND PP.COD_ESTADO_PLAN_PAGO = EPP.COD_ESTADO_PLAN_PAGO
						AND PP.COD_EMPRESA_COBRANZA IS NULL
				ORDER BY ANO_COBRO ASC";//sql_boleta::get_sql('AJAX_DEUDAS_2',$cod_deuda);
}

$wi->dws['dw_detalle_plan_pago']->set_sql($sql);
$wi->dws['dw_detalle_plan_pago']->make_tabla_htm($wi->nom_template);
$wi->dws['dw_detalle_plan_pago']->set_sql($sql_original);
?>