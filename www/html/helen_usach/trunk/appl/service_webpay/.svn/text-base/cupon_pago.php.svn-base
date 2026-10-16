<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../web_service/class_ws_helen.php");
require_once ("../../appl.ini");
include("funciones.php");

class w_cupon_pago extends w_base {
	var $dws = array();
	var $cod_deudor;
	
	function w_cupon_pago() {
		valida_inicio();
		$this->cod_deudor = session::get('cod_deudor');
		$cod_deudor = $this->cod_deudor;
		
		$t = new Template_appl("cupon_pago.html");

		$ws_helen = new wakeUp_helen();
		
		$result = $ws_helen->cupon_pago_1($cod_deudor);
		
		$t->setVar("RUT", $result[0]['RUT']);
		$t->setVar("NOMBRE", utf8_encode($result[0]['NOMBRE']));
		$t->setVar("DIG_VERIF", $result[0]['DIG_VERIF']);
		$result_cuotas = $ws_helen->cupon_pago_cuotas($cod_deudor);
		
		$START_BLOCK = '';
		
		for($i = 0; $i < count($result_cuotas); $i++){
			
			$saldo_pesos        = $result_cuotas[$i]['SALDO_PESOS'];
			$cod_plan_pago      = $result_cuotas[$i]['COD_PLAN_PAGO'];
			$cod_familia_deuda  = $result_cuotas[$i]['COD_FAMILIA_DEUDA'];
			$num_cuota          = $result_cuotas[$i]['NRO_CUOTA'];
			$cod_tipo_deuda     = $result_cuotas[$i]['COD_TIPO_DEUDA'];
			$ano_inicio_cobro   = $result_cuotas[$i]['ANO_INICIO_COBRO'];
			/////nuevas validaciones
			$cod_estado_deuda   = $result_cuotas[$i]['COD_ESTADO_DEUDA'];
			$cod_cartera        = $result_cuotas[$i]['COD_CARTERA'];
			$cod_estado_planpago= $result_cuotas[$i]['COD_ESTADO_PLAN_PAGO'];
			
			if ($saldo_pesos <= 0 or $cod_cartera != 6 or $cod_estado_deuda == 4){ 
				//$cod_tipo_deuda = $this->dws['dw_plan_pago']->get_item($i, 'COD_TIPO_DEUDA');
				if ($cod_tipo_deuda == 'CRPI' OR $cod_tipo_deuda == 'CR12' OR $cod_tipo_deuda == 'CRI')
					$imprimir = ' ';
				else
					$imprimir = '';
			}else {
				if ($cod_familia_deuda == 1){ //1 DEUDA = FONDO SOLIDARIO
					//valida  CS no cruzados con año inicio de cobro >= 2006 y los CSR no cruzados con año inicio cobro >=2004
					//$cod_tipo_deuda = $this->dws['dw_plan_pago']->get_item($i, 'COD_TIPO_DEUDA');
					//$ano_inicio_cobro = $this->dws['dw_plan_pago']->get_item($i, 'ANO_INICIO_COBRO');
					
					if ($cod_tipo_deuda == 'CU'){
						$imprimir = '';
					}
					else{
						$result_boleta = $ws_helen->cupon_pago_boleta($cod_plan_pago);

						$cod_boleta = $result_boleta[0]['COD_BOLETA'];
						
						if ($cod_boleta==''){
							$imprimir = 'Plan Pago Sin Boleta';
						}else{
							if ($cod_tipo_deuda == 'CS' || $cod_tipo_deuda == 'CSR' || $cod_tipo_deuda == 'CSN'|| $cod_tipo_deuda == 'CR12'|| $cod_tipo_deuda == 'CRPI'|| $cod_tipo_deuda == 'CRI'){
								if ($cod_tipo_deuda == 'CS' and $ano_inicio_cobro >= 2006){
									$imprimir = '<a href="#" onclick="window.open(\'imprime_cupon.php?cod_boleta='.$cod_boleta.'\');"><img src="imagen/printer.png" /></a>';
								}else if ($cod_tipo_deuda == 'CSR' and $ano_inicio_cobro >= 2004){
									$imprimir = '<a href="#" onclick="window.open(\'imprime_cupon.php?cod_boleta='.$cod_boleta.'\');"><img src="imagen/printer.png" /></a>';
								}else if ($cod_tipo_deuda == 'CSN' and $ano_inicio_cobro >= 2006){
									$imprimir = '<a href="#" onclick="window.open(\'imprime_cupon.php?cod_boleta='.$cod_boleta.'\');"><img src="imagen/printer.png" /></a>';
								}else if ($cod_tipo_deuda == 'CR12' ){
									$imprimir = '<a href="#" onclick="window.open(\'imprime_cupon.php?cod_boleta='.$cod_boleta.'\');"><img src="imagen/printer.png" /></a>';
								}else if ($cod_tipo_deuda == 'CRI' ){
									$imprimir = '<a href="#" onclick="window.open(\'imprime_cupon.php?cod_boleta='.$cod_boleta.'\');"><img src="imagen/printer.png" /></a>';
								}else{
									$imprimir = '';
								}
							}

							/*elseif ($cod_tipo_deuda == 'CRI' ){
									$imprimir = '<a href="#" onclick="window.open(\'imprime_cupon.php?cod_boleta='.$cod_boleta.'\');"><img src="images/printer.png" /></a>';
								}
							
							else if ($cod_tipo_deuda == 'CRPI' || $cod_tipo_deuda == 'CR12'){
								$imprimir = '<a href="#" onclick="window.open(\'imprime_cupon.php?cod_boleta='.$cod_boleta.'\');"><img src="images/printer.png" /></a>';
							}else{
								$imprimir = '';
							}*/
							
						}
					}
					if ($num_cuota == '99' or $cod_estado_planpago == 4) { 
					    $imprimir = '&nbsp;';}
				}else{
					$imprimir = '';					
				}
			}
			
			$START_BLOCK.='<tr valign="middle" bgcolor="#FFFFFF" align="center">
						<td>
							<div class="deuda_resultado">'.$result_cuotas[$i]['COD_DEUDA'].'</div>
						</td>
						<td>
							<div class="deuda_resultado">'.$result_cuotas[$i]['COD_TIPO_DEUDA'].'</div>
						</td>
						<td>
							<div class="deuda_resultado">'.$result_cuotas[$i]['NRO_CUOTA'].'</div>
						</td>
						<td>
							<div class="deuda_resultado">'.$result_cuotas[$i]['ANO_COBRO'].'</div>
						</td>
						<td>
							<div class="deuda_resultado">'.$result_cuotas[$i]['FECHA_VENCTO'].'</div>
						</td>
						<td>
							<div class="deuda_resultado" align="right">'.$result_cuotas[$i]['SALDO'].'</div>
						</td>
						<td>
							<div class="deuda_resultado" align="right">'.number_format($result_cuotas[$i]['SALDO_PESOS'], 0, ',', '.').'</div>
						</td>
						<td>
							<div class="deuda_resultado">'.$result_cuotas[$i]['NOM_ESTADO_PLAN_PAGO'].'</div>
						</td>
						<td>
							<div class="deuda_resultado" align="center">'.$imprimir.'</div>
						</td>
					</tr>';
			
		}
		
		$t->setVar("START_BLOCK_CUOTA",$START_BLOCK);
		
		print $t->toString();
	}
}
$w = new w_cupon_pago();
?>