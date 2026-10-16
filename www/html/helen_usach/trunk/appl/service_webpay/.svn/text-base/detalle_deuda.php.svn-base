<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../web_service/class_ws_helen.php");
require_once ("../../appl.ini");
include("funciones.php");

class w_detalle_deuda extends w_base {
	function w_detalle_deuda() {
		$t = new Template_appl("detalle_deuda.html");
		$key = 'helen';
		$txt_encripter = $_REQUEST['id'];
		$cod_deuda = urldecode(dencriptar_url($txt_encripter, $key));

		$ws_helen = new wakeUp_helen();
		
		$result = $ws_helen->detalle_deuda($cod_deuda);
		
		$t->setVar("RUT", $result[0]['RUT']);
		$t->setVar("DIG_VERIF", $result[0]['DIG_VERIF']);
		$t->setVar("NOMBRE", $result[0]['NOMBRE']);
		$t->setVar("COD_DEUDA", $result[0]['COD_DEUDA']);
		$t->setVar("COD_TIPO_DEUDA", $result[0]['COD_TIPO_DEUDA']);
		$t->setVar("SIMBOLO", $result[0]['SIMBOLO']);
		
		$result = $ws_helen->obtiene_plan_pago($cod_deuda);
		
		$START_BLOCK_PLAN_PAGO = '';
		for($i = 0; $i < count($result); $i++){

			$saldo_pesos = $result[$i]['SALDO_PESOS'];
			$cod_plan_pago = $result[$i]['COD_PLAN_PAGO'];
			$cod_familia_deuda = $result[$i]['COD_FAMILIA_DEUDA'];
			$cod_tipo_deuda = $result[$i]['COD_TIPO_DEUDA'];

			/////nuevas validaciones
			$cod_estado_deuda = $result[$i]['COD_ESTADO_DEUDA'];
			$cod_cartera = $result[$i]['COD_CARTERA'];
			$num_cuota = $result[$i]['NRO_CUOTA'];
			$cod_estado_planpago= $result[$i]['COD_ESTADO_PLAN_PAGO'];
			$ano_cobro= $result[$i]['ANO_COBRO'];
			$fecha_vencto= $result[$i]['FECHA_VENCTO'];
			$saldo= $result[$i]['SALDO'];
			$estado_plan_pago= $result[$i]['NOM_ESTADO_PLAN_PAGO'];
			
			if ($saldo_pesos <= 0 or $cod_cartera != 6 or $cod_estado_deuda == 4) 
				$imprimir = '';
			else {
				if ($cod_familia_deuda == 1){ //COD_FAMILIDA DEUDA = FONDO SOLIDARIO 
					if ($cod_tipo_deuda == 'CU'){
						$imprimir = '';
					}else if ($cod_tipo_deuda == 'CRPI' OR $cod_tipo_deuda == 'CR12'){
						
						$result = $ws_helen->obtiene_boleta($cod_plan_pago);
						
						$cod_boleta = $result[0]['COD_BOLETA'];
						
						if ($cod_boleta=='')
							$imprimir = 'Plan Pago Sin Boleta';
						
						else
							$imprimir = '<a href="#" onclick="window.open(\'imprime_cupon.php?cod_boleta='.$cod_boleta.'\');"><img src="imagen/printer.png" /></a>';
							if ($cod_tipo_deuda == 'CR12' OR $num_cuota == '99') {  
							    $imprimir = '&nbsp;';}
					}else{
						$result = $ws_helen->obtiene_ult_boleta($cod_plan_pago);	
					
						$cod_boleta = $result[0]['COD_BOLETA'];
						if ($cod_boleta=='')
							$imprimir = 'Plan Pago Sin Boleta';
						else
							$imprimir = '<a href="#" onclick="window.open(\'imprime_cupon.php?cod_boleta='.$cod_boleta.'\');"><img src="imagen/printer.png" /></a>';
					}
					if ($num_cuota == '99' or $cod_estado_planpago == 4) { 
					    $imprimir = '&nbsp;';}
				}else {
					$imprimir = '';
				}
			}
			
			$START_BLOCK_PLAN_PAGO.=' 
			<tr>
				<td>
					<div class="deuda_resultado" align="center">'.$num_cuota.'</div>
				</td>
				<td>
					<div class="deuda_resultado" align="center">'.$ano_cobro.'</div>
				</td>
				<td>
					<div class="deuda_resultado" align="center">'.$fecha_vencto.'</div>
				</td>
				<td>
					<div class="deuda_resultado" align="center">'.$saldo.'</div>
				</td>
				<td>
					<div class="deuda_resultado" align="center">'.number_format($saldo_pesos, 0, ',', '.').'</div>
				</td>
				<td>
					<div class="deuda_resultado" align="center">'.$estado_plan_pago.'</div>
				</td>
				<td>
					<div class="deuda_resultado" align="center">'.$imprimir.'</div>
				</td>
			</tr>							
			';
			
		}
		
		$t->setVar("START_BLOCK_PLAN_PAGO",$START_BLOCK_PLAN_PAGO);
				
		print $t->toString();
	}
}
$w = new w_detalle_deuda();
?>