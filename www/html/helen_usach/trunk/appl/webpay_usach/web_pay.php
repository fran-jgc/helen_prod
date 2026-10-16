<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
include("funciones.php");

class w_web_pay_pago{
	function __construct() {
		valida_inicio();
		
		$t = new Template_appl("web_pay.html");

		$cod_deudor = session::get('cod_deudor');

		valida_ultimo_pago_segic($cod_deudor);
		
		$cod_estado_deuda = 2; //ESTADO_COBRANZA_NORMAL

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT	 D.RUT
				    ,D.DIG_VERIF
				    ,D.NOMBRE || ' ' || D.APELLIDO_PATERNO || ' ' || D.APELLIDO_MATERNO NOMBRE
				    ,DIP.DIRECCION  DIRECCION
				    ,DIP.TELEFONO FONO_PARTICULAR
				    ,DIP.EMAIL EMAIL_PARTICULAR
				    --,'' ABONO_SUB_BOLETA
				    ,null DESCTO_SUB_BOLETA
					,null TOTAL_SUB_BOLETA
				    ,'' CB_COD_PLAN_PAGO_H
				    ,'' CB_MONTO_PESOS_BOLETA_H
			FROM 	DEUDOR D left outer join DIRECCION DIP 
					 on DIP.COD_DIRECCION = f_get_direccion_deudor(D.COD_DEUDOR,'P') 
			WHERE 	D.COD_DEUDOR= $cod_deudor";
					
		$dw_datos_deudor = new datawindow($sql);
		$dw_datos_deudor->add_control(new static_num('RUT'));
		// $dw_datos_deudor->add_control(new edit_num('ABONO_SUB_BOLETA', 10, 10));
		$dw_datos_deudor->add_control(new static_text('DESCTO_SUB_BOLETA'));
		$dw_datos_deudor->add_control(new static_text('TOTAL_SUB_BOLETA'));
		$dw_datos_deudor->add_control(new edit_text_hidden('CB_COD_PLAN_PAGO_H', 10, 10));
		$dw_datos_deudor->add_control(new edit_text_hidden('CB_MONTO_PESOS_BOLETA_H', 10, 10));
				
		$dw_datos_deudor->retrieve();
		$dw_datos_deudor->habilitar($t, true);
		
		$sql_deudas = "SELECT	 COD_DEUDA LINK_DEUDA
					,COD_DEUDA
					,NOM_ESTADO_DEUDA
					,D.COD_TIPO_DEUDA
					,SIMBOLO
					,To_Number(To_Char(FECHA_INICIO_COBRO, 'yyyy')) ANO_INICIO_COBRO
					,To_Char(FECHA_INICIO_COBRO, 'dd/mm/yyyy') FECHA_INICIO_COBRO
					,f_deuda_saldo_total(COD_DEUDA, sysdate)   SALDO
					,ROUND (ROUND(f_deuda_saldo_total(COD_DEUDA, sysdate),4)* f_mon_get_valor_moneda(td.cod_moneda, sysdate),0) SALDO_PESOS
					,TD.COD_FAMILIA_DEUDA
					,D.COD_ESTADO_DEUDA
					,D.COD_CARTERA
				FROM   	DEUDA D
					,ESTADO_DEUDA ED
					,MONEDA M
					,TIPO_DEUDA TD
				WHERE	D.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA
				  AND	M.COD_MONEDA = TD.COD_MONEDA
				  AND	TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA
				  --AND D.COD_ESTADO_DEUDA NOT IN (1)  
				  AND D.COD_DEUDA NOT IN (SELECT COD_DEUDA 
										FROM REPRO_RCS_DEUDA 
										WHERE COD_REPRO_RCS IN (SELECT MAX (COD_REPRO_RCS) 
															  FROM REPRO_RCS 
															  WHERE COD_ESTADO_REPRO_RCS<>3
															  AND COD_DEUDOR =$cod_deudor))
				  AND	D.COD_DEUDOR = ".$cod_deudor;

		$dw_deudas = new datawindow($sql_deudas, 'deuda');
		$dw_deudas->add_control(new static_num('SALDO', 4));
		$dw_deudas->add_control(new static_num('SALDO_PESOS'));
		$dw_deudas->retrieve();
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$START_BLOCK_CUOTAS = '';
		for($i = 0; $i < $dw_deudas->row_count(); $i++){
			$cod_deuda        = $dw_deudas->get_item($i, 'COD_DEUDA');			
			/////nuevas validaciones
			$saldo_pesos      = $dw_deudas->get_item($i, 'SALDO_PESOS');
			$cod_estado_deuda = $dw_deudas->get_item($i, 'COD_ESTADO_DEUDA');
			$cod_cartera      = $dw_deudas->get_item($i, 'COD_CARTERA');
			$NOM_ESTADO_DEUDA      = $dw_deudas->get_item($i, 'NOM_ESTADO_DEUDA');
			$FECHA_INICIO_COBRO  = $dw_deudas->get_item($i, 'FECHA_INICIO_COBRO');
			$SALDO  = $dw_deudas->get_item($i, 'SALDO');

			// NUEVA VALIDACIÓN PRINCIPAL: Solo estados 1 y 2 permiten pago
			if ($cod_estado_deuda == 1 || $cod_estado_deuda == 2) {
				// La deuda es pagable (estado 1 o 2)
				// Ahora validamos las condiciones específicas para permitir el enlace
				
				//valida  que la deuda no sea cruzada
				$sql = "SELECT	count(*) CANTIDAD
						FROM	CRUZADO
						where	COD_DEUDA = $cod_deuda";
				
				$result = $db->build_results($sql);
				$cant_cruzado = $result[0]['CANTIDAD'];
				$cod_tipo_deuda = $dw_deudas->get_item($i, 'COD_TIPO_DEUDA');
				
				if($cant_cruzado > 0){
					// Deuda cruzada
					if ($cod_tipo_deuda == 'CRPI' || $cod_tipo_deuda == 'CR12' || $cod_tipo_deuda == 'CRI' ){
						$link_deuda = '<a href="#" onclick="display_plan_pago('.$cod_deuda.');">'.$cod_deuda.'</a>';	
					}else{
						$link_deuda = $cod_deuda;
					}
				}else{
					// Deuda no cruzada - aplicar todas las validaciones existentes
					$cod_familia_deuda = $dw_deudas->get_item($i, 'COD_FAMILIA_DEUDA');
					
					if($cod_familia_deuda == 1){ //1=>fondo solidario
						//valida  CS no cruzados con año inicio de cobro >= 2006 y los CSR no cruzados con año inicio cobro >=2004
						$cod_tipo_deuda = $dw_deudas->get_item($i, 'COD_TIPO_DEUDA');
						$ano_inicio_cobro = $dw_deudas->get_item($i, 'ANO_INICIO_COBRO');
						
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
							}else if ($cod_tipo_deuda == 'CRPI' || $cod_tipo_deuda == 'CR12' || $cod_tipo_deuda == 'CRI' || $cod_tipo_deuda == 'CAM' || $cod_tipo_deuda == 'CAME'){
								$link_deuda = '<a href="#" onclick="display_plan_pago('.$cod_deuda.');">'.$cod_deuda.'</a>';	
							}else{
								$link_deuda = $cod_deuda;
							}					
						}else{
							$link_deuda = $cod_deuda;
						}					
					}else if($cod_familia_deuda == 2 and $cod_cartera == 6 ){
						if ($cod_tipo_deuda == 'CAM' || $cod_tipo_deuda == 'CAME'|| $cod_tipo_deuda == 'CD'){  
							$link_deuda = '<a href="#" onclick="display_plan_pago('.$cod_deuda.');">'.$cod_deuda.'</a>';
						}else{
							$link_deuda = $cod_deuda;
						}
					}else{
						// Para otras familias, permite pago por defecto
						$link_deuda = '<a href="#" onclick="display_plan_pago('.$cod_deuda.');">'.$cod_deuda.'</a>';
					}
				}
			} else {
				// Cualquier estado distinto a 1 o 2: NO permite pago
				$link_deuda = $cod_deuda;
			}
			/////nuevas validaciones

			$dw_deudas->set_item($i, 'LINK_DEUDA', $link_deuda);
			
			$START_BLOCK_CUOTAS.= '
						<tr>
							<th scope="row">
								<div>
									<div class="deuda_resultado">'.$link_deuda.'</div>
								</div>
							</th>
									    
							<th scope="row">
								<div class="deuda_resultado">'.$cod_tipo_deuda.'</div>
							</th>
								    
							<th scope="row">
								<div class="deuda_resultado">'.$NOM_ESTADO_DEUDA.'</div>
							</th>
								    
							<th scope="row">
								<div class="deuda_resultado">'.$FECHA_INICIO_COBRO.'</div>
							</th>
							<!--
							<th scope="row">
								<div class="deuda_resultado">'.$SALDO.'</div>
							</th>
								    
							<th scope="row">
								<div class="deuda_resultado" align="right">'.number_format($saldo_pesos, 0, ',', '.').'</div>
							</th> -->
						</tr>';
		}
		
		$dw_deudas->habilitar($t, false);

        $t->setVar("START_BLOCK_CUOTAS",$START_BLOCK_CUOTAS);

		print $t->toString();
	}
}

$w = new w_web_pay_pago();
?>