<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once ("../../appl.ini");
include("funciones.php");

class w_detalle_deuda extends w_base {
	function w_detalle_deuda() {
		$t = new Template_appl("detalle_deuda.html");
		$key = 'helen';
		$txt_encripter = $_REQUEST['id'];
		$cod_deuda = urldecode(dencriptar_url($txt_encripter, $key));

		$sql_detalle_deuda = "SELECT  RUT
									,DIG_VERIF
							        ,NOMBRE||' '||APELLIDO_PATERNO||' '||APELLIDO_MATERNO NOMBRE
							        ,COD_DEUDA
							        ,TD.COD_TIPO_DEUDA
							        ,SIMBOLO
							FROM    DEUDA D
							        ,DEUDOR DR
							        ,MONEDA M
							        ,TIPO_DEUDA TD
							WHERE   COD_DEUDA = ".$cod_deuda."
							        AND DR.COD_DEUDOR = d.COD_DEUDOR
							        AND M.COD_MONEDA = TD.COD_MONEDA
							        AND TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA";

		$dw_datos_deuda = new datawindow($sql_detalle_deuda);
		$dw_datos_deuda->add_control(new static_num('RUT'));
		$dw_datos_deuda->retrieve();
		$dw_datos_deuda->habilitar($t, false);

		$sql_plan_pago = "SELECT  NRO_CUOTA
								,ANO_COBRO
								,to_char(FECHA_VENCTO,'dd/mm/yyyy') FECHA_VENCTO
								,f_pp_saldo_plan_pago(COD_PLAN_PAGO, sysdate) SALDO
								,round(f_pp_saldo_plan_pago(COD_PLAN_PAGO, sysdate) * f_mon_get_valor_moneda(cod_moneda, sysdate),0) SALDO_PESOS
								,NOM_ESTADO_PLAN_PAGO
								,'' IMPRIMIR
								,PP.COD_PLAN_PAGO
								,TD.COD_FAMILIA_DEUDA
								,D.COD_TIPO_DEUDA
						FROM    PLAN_PAGO PP
				        		,DEUDA    D
				      			,TIPO_DEUDA TD
				      			,ESTADO_PLAN_PAGO EPP
						WHERE   PP.COD_DEUDA = ".$cod_deuda."
				        		AND PP.COD_DEUDA = D.COD_DEUDA
				        		AND D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
				        		AND EPP.COD_ESTADO_PLAN_PAGO = PP.COD_ESTADO_PLAN_PAGO
				        order by NRO_CUOTA";

		$dw_plan_pago = new datawindow($sql_plan_pago, 'detalle_cuotas');
		$dw_plan_pago->add_control(new static_num('SALDO', 4));
		$dw_plan_pago->add_control(new static_num('SALDO_PESOS'));
		$dw_plan_pago->retrieve();
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		for($i = 0; $i < $dw_plan_pago->row_count(); $i++){
			$saldo_pesos = $dw_plan_pago->get_item($i, 'SALDO_PESOS');
			$cod_plan_pago = $dw_plan_pago->get_item($i, 'COD_PLAN_PAGO');
			$cod_familia_deuda = $dw_plan_pago->get_item($i, 'COD_FAMILIA_DEUDA');
			$cod_tipo_deuda = $dw_plan_pago->get_item($i, 'COD_TIPO_DEUDA');
			
			if ($saldo_pesos <= 0) 
				$imprimir = '';
			else {
				if ($cod_familia_deuda == 1){ //COD_FAMILIDA DEUDA = FONDO SOLIDARIO 
					if ($cod_tipo_deuda == 'CU'){
						$imprimir = '';
					}else{
						$sql = "select max(B.COD_BOLETA) COD_BOLETA
								from  BOLETA B
								where B.COD_PLAN_PAGO = $cod_plan_pago
								  and B.CARTERA_CONOCIDA = 'S'
								  AND To_Char(B.FECHA_BOLETA,'yyyy') = To_Char(SYSDATE,'yyyy')";
						$result = $db->build_results($sql);
						$cod_boleta = $result[0]['COD_BOLETA'];
						if ($cod_boleta=='')
							$imprimir = '';
						else
							$imprimir = '<a href="#" onclick="window.open(\'imprime_cupon.php?cod_boleta='.$cod_boleta.'\');"><img src="images/printer.png" /></a>';
					}
				}else {
					$imprimir = '';
				}
			}
			$dw_plan_pago->set_item($i, 'IMPRIMIR', $imprimir);
		}
		
		$dw_plan_pago->habilitar($t, false);

		print $t->toString();
	}
}
$w = new w_detalle_deuda();
?>