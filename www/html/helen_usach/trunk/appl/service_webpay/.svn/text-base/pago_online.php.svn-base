<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../web_service/class_ws_helen.php");
require_once ("../../appl.ini");
require_once ("funciones.php");

class w_pago_online{
	function w_pago_online() {
		$cod_plan_pago	= $_REQUEST['CB_COD_PLAN_PAGO_H_0'];
		$monto_abono_pesos	= $_REQUEST['CB_MONTO_PESOS_BOLETA_H_0'];
		$cod_tipo_descuento = "NULL";
		
		
		$ws_helen = new wakeUp_helen();
		$result = $ws_helen->pago_online_1($cod_plan_pago);
		
		$cod_deuda = $result[0]['COD_DEUDA'];
		$nro_cuota = $result[0]['NRO_CUOTA'];
		$ano_cobro = $result[0]['ANO_COBRO'];
		
		$result_c = $ws_helen->pago_online_2($cod_deuda, $cod_plan_pago, $cod_tipo_descuento, $monto_abono_pesos); 
		
			if (count($result_c) != 0) {
				$tipo_pago	= 'PAGO_ABONO';
				$cod_deudor	= session::get('COD_DEUDOR');
				$cod_sub_boletas	= 'NULL';
				$monto_capital	= $result_c[0]['D_MONTO_CAPITAL'];
				$monto_interes	= $result_c[0]['D_MONTO_INTERES'];
				$monto_multa	= $result_c[0]['D_MONTO_MULTA'];
				$monto_moneda	= $monto_capital+$monto_interes+$monto_multa;
				$monto_pesos	= $result_c[0]['D_MONTO_PESOS'];
				$cod_tipo_descuento = 'NULL';
				$dscto_utm		= 0;
				$dscto_pesos	= 0;
			}else{
				$tipo_pago	= '';
				$cod_deudor	= session::get('COD_DEUDOR');
				$cod_sub_boletas	= 'NULL';
				$monto_capital	= 0;
				$monto_interes	= 0;
				$monto_multa	= 0;
				$monto_moneda	= 0;
				$monto_pesos	= 0;
				$cod_tipo_descuento = 'NULL';
				$dscto_utm		= 0;
				$dscto_pesos	= 0;				
			}
		//formatos de montos
		$monto_pesos_display = number_format($monto_pesos, 0, ',', '.');
		$monto_capital_aux	= number_format($monto_capital, 4, ',', '.');
		$monto_interes_aux	= number_format($monto_interes, 4, ',', '.');
		$monto_multa_aux	= number_format($monto_multa, 4, ',', '.');
		$monto_moneda_aux	= number_format($monto_moneda, 4, ',', '.');
		$monto_pesos_aux	= number_format($monto_pesos, 0, ',', '.');
		
		//$TOTAL_PAGO = $_REQUEST['TOTAL_PESOS_H_0'];
		//$TOTAL_PAGO = number_format($TOTAL_PAGO, 0, ',', '.');
		//$TOTAL_PAGO_H = $_REQUEST['TOTAL_PESOS_H_0'];

		$t = new Template_appl("pago_online.html"); /*pago_online.htm*/
		$cod_deudor = session::get('COD_DEUDOR');

		
		$result = $ws_helen->pago_online_4($cod_deudor);
		
		
		$t->setVar("TBK_NOMBRE", utf8_encode($result[0]['TBK_NOMBRE']));
		$t->setVar("TBK_APELLIDO", utf8_encode($result[0]['TBK_APELLIDO']));
		$t->setVar("TBK_DIRECCION", utf8_encode($result[0]['TBK_DIRECCION']));
		$t->setVar("TBK_FONO", utf8_encode($result[0]['TBK_FONO']));
		$t->setVar("TBK_EMAIL", utf8_encode($result[0]['TBK_EMAIL']));
		$t->setVar("TBK_MONTO", $monto_pesos);
		$t->setVar("TBK_MONTO_DISPLAY", $monto_pesos_display);
		$t->setVar("TBK_CIUDAD", utf8_encode($result[0]['TBK_CIUDAD']));
		$t->setVar("TBK_PAIS", utf8_encode($result[0]['TBK_PAIS']));
		$t->setVar("COD_DEUDA", $cod_deuda);
		$t->setVar("NRO_CUOTA", $nro_cuota);
		$t->setVar("ANO_COBRO", $ano_cobro);
		$t->setVar("TIPO_PAGO_H", $tipo_pago);
		$t->setVar("COD_USUARIO_H", $cod_deudor);
		$t->setVar("COD_PLAN_PAGO_H", $cod_plan_pago);
		$t->setVar("MONTO_PESOS_H", $monto_pesos);
		$t->setVar("MONTO_CAPITAL", $monto_capital_aux);
		$t->setVar("MONTO_INTERES", $monto_interes_aux);
		$t->setVar("MONTO_MULTA", $monto_multa_aux);
		$t->setVar("MONTO_MONEDA", $monto_moneda_aux);
		$t->setVar("MONTO_PESOS", $monto_pesos_aux);
		
		print $t->toString();
	}
}		
$w = new w_pago_online();
?>
