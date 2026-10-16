<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once ("../../appl.ini");
require_once ("funciones.php");

class w_pago_online{
	function __construct() {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

		$plan_pagos	= $_REQUEST['CB_COD_PLAN_PAGO_H_0'];
		$cod_deuda	= $_REQUEST['CB_COD_DEUDA_H_0'];
		$montos	= $_REQUEST['CB_MONTO_PESOS_BOLETA_H_0'];
		$cod_tipo_descuento = "NULL";
		
		$plan_pagos = substr($plan_pagos, 0,-1);
		$montos = substr($montos,0, -1);

		$array_plan_pagos = explode("|", $plan_pagos);
		$array_montos = explode("|", $montos);

		$tr='';
		$TABLE_PARCIALIDAD_ITEM='';
		$monto_pesos_display=0;
		$plan_pago_boleta='';
		$monto_pesos_boleta='';
		$nro_cuota_boleta='';
		$monto_total=0;
		for ($i=0; $i < count($array_plan_pagos); $i++) { 
			$cod_plan_pago = $array_plan_pagos[$i];
			$monto_abono_pesos = $array_montos[$i];
			//deudas en estado SVP
			if($cod_plan_pago == "NULL"){
				$sql = "SELECT COD_DEUDA
							,99 NRO_CUOTA
							,9999 ANO_COBRO
							,COD_TIPO_DEUDA
							,COD_ESTADO_DEUDA
						FROM DEUDA
						WHERE COD_DEUDA = $cod_deuda
											";
			}
			else{
				$sql = "SELECT	 PP.COD_DEUDA
								,NRO_CUOTA
								,ANO_COBRO
								,DE.COD_TIPO_DEUDA
								,DE.COD_ESTADO_DEUDA
						FROM PLAN_PAGO PP, DEUDA DE
						WHERE COD_PLAN_PAGO = $cod_plan_pago
						AND PP.COD_DEUDA = DE.COD_DEUDA";
			}
			$result = $db->build_results($sql);
			$cod_deuda = $result[0]['COD_DEUDA'];
			$nro_cuota = $result[0]['NRO_CUOTA'];
			$ano_cobro = $result[0]['ANO_COBRO'];
			$cod_tipo_deuda = $result[0]['COD_TIPO_DEUDA'];
			$cod_estado_deuda = $result[0]['COD_ESTADO_DEUDA'];

			$sp = 'sp_boleta_calculo_web';
			$param	= "$cod_deuda, $cod_plan_pago, $cod_tipo_descuento, $monto_abono_pesos"; 

			$db->EXECUTE_SP($sp, $param);

			$sql = "SELECT	 D_MONTO_CAPITAL	
						,D_MONTO_INTERES
						,D_MONTO_MULTA
						,D_MONTO_PESOS
						,D_DSCTO_CAPITAL
						,D_DSCTO_INTERES
						,D_DSCTO_MULTA
						,D_DSCTO_PESOS
				FROM	TEMP_TOTAL_BOLETA";
			$db_c = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$result_c = $db_c->build_results($sql);
			$db->ROLLBACK_TRANSACTION();

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
					
					//NO EXIGIBLE
					if($cod_estado_deuda == 1){
						$dscto_utm		= $result_c[0]['D_DSCTO_CAPITAL'];
						$dscto_pesos	= $result_c[0]['D_DSCTO_PESOS'];
					}else if($cod_estado_deuda == 2 && $nro_cuota == 99){
						$dscto_utm		= $result_c[0]['D_DSCTO_CAPITAL'];
						$dscto_pesos	= $result_c[0]['D_DSCTO_PESOS'];
					}
					else{
						$dscto_utm		= 0;
						$dscto_pesos	= 0;
					}
					
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
				
			$monto_total_pesos = $monto_pesos - $dscto_pesos;
			$monto_total = $monto_total + $monto_total_pesos;	

			//formatos de montos
			$monto_pesos_display = number_format($monto_total_pesos, 0, ',', '.');
			$monto_capital_aux	= number_format($monto_capital, 4, ',', '.');
			$monto_interes_aux	= number_format($monto_interes, 4, ',', '.');
			$monto_multa_aux	= number_format($monto_multa, 4, ',', '.');
			$monto_moneda_aux	= number_format($monto_moneda, 4, ',', '.');
			$monto_pesos_aux	= number_format($monto_total_pesos, 0, ',', '.');
			
			$dscto_utm_aux		= number_format($dscto_utm, 4, ',', '.');
			$dscto_pesos_aux	= number_format($dscto_pesos, 0, ',', '.');

			if($cod_estado_deuda == 1){
				$monto_total_pesos = $monto_abono_pesos;
			}
			else if($cod_estado_deuda == 2 && $nro_cuota == 99){
				$monto_total_pesos = $monto_abono_pesos;
			}

			$tr='<tr id="TABLE_PARCIALIDAD_ITEM_'.$i.'" class="claro" height="23px">
					<td class="text-center" ><label id="">'.$nro_cuota.'</label><input type="hidden" id="NRO_CUOTA_H_'.$i.'" value="'.$nro_cuota.'"><input type="hidden" id="COD_PLAN_PAGO_H_'.$i.'" value="'.$cod_plan_pago.'"></td>
					<td class="text-center" ><label id="">'.$ano_cobro.'</label></td>
					<td class="text-center" ><label id="">'.$monto_capital_aux.'</label></td>
					<td class="text-center" ><label id="">'.$monto_interes_aux.'</label></td>
					<td class="text-center" ><label id="">'.$monto_multa_aux.'</label></td>
					<td class="text-center" ><label id="">'.$monto_moneda_aux.'</label></td>
					<td class="text-center" ><label id="">'.$dscto_utm_aux.'</label></td>
					<td class="text-center" ><label id="">'.$dscto_pesos_aux.'</label></td>
					<td class="text-center" ><label id="">'.$monto_pesos_aux.'</label><input type="hidden" id="MONTO_PESOS_H_'.$i.'" value="'.$monto_total_pesos.'"></td>
				</tr>';
				
			$TABLE_PARCIALIDAD_ITEM=$TABLE_PARCIALIDAD_ITEM.$tr;	
			$plan_pago_boleta=$plan_pago_boleta.'-'.$cod_plan_pago;
			$monto_pesos_boleta=$monto_pesos_boleta.'-'.$monto_pesos;
			$nro_cuota_boleta=$nro_cuota_boleta.'-'.$nro_cuota;
		}
		$monto_total = number_format($monto_total, 0, ',', '.');

		$t = new Template_appl("pago_online.html"); /*pago_online.htm*/
		$cod_deudor = session::get('COD_DEUDOR');
		$t->setVar("TABLE_PARCIALIDAD_ITEM", $TABLE_PARCIALIDAD_ITEM);
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

		$sql_tbk = "SELECT	 D.NOMBRE TBK_NOMBRE 
						    ,D.APELLIDO_PATERNO || ' ' || D.APELLIDO_MATERNO TBK_APELLIDO
						    ,DIP.DIRECCION TBK_DIRECCION
						    ,DIP.TELEFONO TBK_FONO
						    ,DIP.EMAIL TBK_EMAIL
							,'$monto_total_pesos' TBK_MONTO
							,'$monto_total' TBK_MONTO_DISPLAY
							,CI.NOM_CIUDAD TBK_CIUDAD
							,PS.NOM_PAIS TBK_PAIS
							,'$cod_deuda' COD_DEUDA
							,'$tipo_pago'	TIPO_PAGO_H
							,'$cod_deudor'		COD_USUARIO_H
							,'$plan_pagos'	COD_PLAN_PAGO_H
							,'$cod_deuda'		COD_DEUDA_H
							,'$monto_total_pesos'		MONTO_PESOS_H
							,'$cod_tipo_deuda'		COD_TIPO_DEUDA_H
					FROM 	DEUDOR D left outer	join DIRECCION DIP on DIP.COD_DIRECCION = f_get_direccion_deudor(D.COD_DEUDOR,'P')
									 left outer join COMUNA CO on DIP.COD_COMUNA = CO.COD_COMUNA
									 left outer join CIUDAD CI on DIP.COD_CIUDAD = CI.COD_CIUDAD
									 left outer join PAIS PS on DIP.COD_PAIS = PS.COD_PAIS
					WHERE 	D.COD_DEUDOR = $cod_deudor";
					
		$dw_transbank = new datawindow($sql_tbk);
		
		$dw_transbank->retrieve();
		$dw_transbank->habilitar($t, false);

		print $t->toString();
	}
}		
$w = new w_pago_online();
?>
