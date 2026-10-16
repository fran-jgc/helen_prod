<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../web_service/class_ws_helen.php");
include("funciones.php");

class w_web_pay_pago{
	function w_web_pay_pago() {
		valida_inicio();
		
		$t = new Template_appl("web_pay.html");

		$cod_deudor = session::get('COD_DEUDOR');
		$cod_estado_deuda = 2; //ESTADO_COBRANZA_NORMAL

		$ws_helen = new wakeUp_helen();
		
		$result = $ws_helen->datos_web_pay($cod_deudor);
		
		$t->setVar("RUT", $result[0]['RUT']);
		$t->setVar("DIG_VERIF", $result[0]['DIG_VERIF']);
		$t->setVar("NOMBRE", utf8_encode($result[0]['NOMBRE']));
		$t->setVar("DIRECCION", utf8_encode($result[0]['DIRECCION']));
		$t->setVar("FONO_PARTICULAR", utf8_encode($result[0]['FONO_PARTICULAR']));
		$t->setVar("EMAIL_PARTICULAR", utf8_encode($result[0]['EMAIL_PARTICULAR']));
		$t->setVar("ABONO_SUB_BOLETA", $result[0]['ABONO_SUB_BOLETA']);
		$t->setVar("CB_COD_PLAN_PAGO_H", $result[0]['CB_COD_PLAN_PAGO_H']);
		$t->setVar("CB_MONTO_PESOS_BOLETA_H", $result[0]['CB_MONTO_PESOS_BOLETA_H']);
		
		$result = $ws_helen->listado_deudas($cod_deudor);
		
		$START_BLOCK_CUOTAS = '';
		
		for($i = 0; $i < count($result); $i++){
			$cod_deuda        = $result[$i]['COD_DEUDA'];
			/////nuevas validaciones
			$saldo_pesos      = $result[$i]['SALDO_PESOS'];
			$cod_estado_deuda = $result[$i]['COD_ESTADO_DEUDA'];
			$cod_cartera      = $result[$i]['COD_CARTERA'];
			
			$result_cruzado = $ws_helen->cant_cruzado($cod_deuda);

			$cant_cruzado = $result_cruzado[0]['CANTIDAD'];

			if($cant_cruzado > 0){
				$cod_tipo_deuda = $result[$i]['COD_TIPO_DEUDA'];				
				if ($cod_tipo_deuda == 'CRPI' || $cod_tipo_deuda == 'CR12' || $cod_tipo_deuda == 'CRI' ){
					$link_deuda = '<a href="#" onclick="display_plan_pago('.$cod_deuda.');">'.$cod_deuda.'</a>';	
				}else{
					$link_deuda = $cod_deuda;
				}
			}else{
				if ($cod_estado_deuda == 1){ //DEUDA NO EXIGIBLE (COD_ESTADO_DEUDA = 1)
					$link_deuda = '<a href="#" onclick="display_plan_pago('.$cod_deuda.');">'.$cod_deuda.'</a>';
				}else if ($cod_estado_deuda == 4){ //DEUDA CANCELADA (COD_ESTADO_DEUDA = 4)
					$link_deuda = $cod_deuda;
				}else{
					$cod_familia_deuda = $result[$i]['COD_FAMILIA_DEUDA'];
					if($cod_familia_deuda == 1){ //1=>fondo solidario
						//valida  CS no cruzados con año inicio de cobro >= 2006 y los CSR no cruzados con año inicio cobro >=2004
						$cod_tipo_deuda = $result[$i]['COD_TIPO_DEUDA'];
						$ano_inicio_cobro = $result[$i]['ANO_INICIO_COBRO'];
						
						if ($cod_cartera == 6 and $saldo_pesos > 0) {
							// solo deudas en cartera USACH (6)
							if ($cod_tipo_deuda == 'CS' || $cod_tipo_deuda == 'CSR' || $cod_tipo_deuda == 'CSN'){
								if ($cod_tipo_deuda == 'CS' and $ano_inicio_cobro >= 2006){
									$link_deuda = '<a href="#" onclick="display_plan_pago('.$cod_deuda.');">'.$cod_deuda.'</a>';
								}else if ($cod_tipo_deuda == 'CSR' and $ano_inicio_cobro >= 2004){
									$link_deuda = '<a href="#" onclick="display_plan_pago('.$cod_deuda.');">'.$cod_deuda.'</a>';
								}else if ($cod_tipo_deuda == 'CSN' and $ano_inicio_cobro >= 2006){
									$link_deuda = '<a href="#" onclick="display_plan_pago('.$cod_deuda.');">'.$cod_deuda.'</a>';
								}else{
									$link_deuda = $cod_deuda;
								}
							}else if ($cod_tipo_deuda == 'CRPI' || $cod_tipo_deuda == 'CR12' || $cod_tipo_deuda == 'CRI'){
								$link_deuda = '<a href="#" onclick="display_plan_pago('.$cod_deuda.');">'.$cod_deuda.'</a>';	
								
								
							}else{
								$link_deuda = $cod_deuda;
							}					
						}else{
							$link_deuda = $cod_deuda;
						}					
					}else{
						$link_deuda = $cod_deuda;
					}					
				}
			}
			
			$START_BLOCK_CUOTAS.= '
						<tr>
							<th scope="row">
								<div>
									<div class="deuda_resultado">'.$link_deuda.'</div>
								</div>
							</th>
	
							<th scope="row">
								<div class="deuda_resultado">'.$result[$i]['COD_TIPO_DEUDA'].'</div>
							</th>
	
							<th scope="row">
								<div class="deuda_resultado">'.$result[$i]['NOM_ESTADO_DEUDA'].'</div>
							</th>
	
							<th scope="row">
								<div class="deuda_resultado">'.$result[$i]['FECHA_INICIO_COBRO'].'</div>
							</th>
	
							<th scope="row">
								<div class="deuda_resultado">'.$result[$i]['SALDO'].'</div>
							</th>
	
							<th scope="row">
								<div class="deuda_resultado" align="right">'.number_format($result[$i]['SALDO_PESOS'], 0, ',', '.').'</div>
							</th>
						</tr>';
		}
		
		$t->setVar("START_BLOCK_CUOTAS",$START_BLOCK_CUOTAS);

		print $t->toString();
	}
}

$w = new w_web_pay_pago();
?>
