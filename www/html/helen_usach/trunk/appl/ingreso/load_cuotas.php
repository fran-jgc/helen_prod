<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

class load_cuotas extends w_base{
    
	static function get_cuotas($rut, $fecha_ingreso) {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		
		if ($fecha_ingreso == 'null'){
			$fecha_ingreso = base::str2date($db->current_date(), '23:59:59');			
		}
		else{
			$fecha_ingreso = base::str2date($fecha_ingreso, '23:59:59');		
		}

		if(session::get('K_CLIENTE') == 'SURCOB_UACH'){
			$sql = "select count(*) CANT
					from DEUDOR
					where RUT = $rut
					and dbo.f_visible_emp_cobranza(1, 'DEUDOR', COD_DEUDOR) = 'S'";
		}
		else
			$sql = "select count(*) CANT
			from DEUDOR
			where RUT = $rut";
		
		$result = $db->build_results($sql);
		
		if ($result[0]['CANT']==0) {
			return 'DEUDOR_INEXISTENTE';
		}
		
		//valida cartera diferente a la universidad
		$K_PARAM_CARTERA = 2;
		
		$sql_param = "SELECT VALOR FROM PARAMETRO WHERE COD_PARAMETRO = 2";//$K_PARAM_CARTERA;
		$result_param = $db->build_results($sql_param);
		$cartera_defecto = $result_param[0]['VALOR'];
		
		if (K_TIPO_BD=="oci")
			$sql = "SELECT 'N' SELECCION
			      ,NULL COD_ITEM_INGRESO
			      ,NULL COD_INGRESO
			      ,DA.COD_DEUDA 
			      ,DA.COD_TIPO_DEUDA
			      ,M.SIMBOLO
			      ,PP.COD_PLAN_PAGO
			      ,NVL(PP.ANO_COBRO, 9999) ANO_COBRO
			      ,PP.NRO_CUOTA 
			      ,TO_CHAR (PP.FECHA_VENCTO, 'DD/MM/YYYY') FECHA_VENCTO
			      ,f_mon_get_valor_moneda(T.COD_MONEDA, $fecha_ingreso) VALOR_MONEDA
			      ,NULL SALDO_CUOTA
			      ,NULL SALDO_MULTA
			      ,NULL SALDO_TOTAL
			      ,NULL SALDO_TOTAL_PESOS
			      ,0 MONTO_PAGO       
			      ,0 MONTO_PAGO_PESOS   
		          ,Decode(Sign(Nvl(fecha_vencto, $fecha_ingreso+1) - $fecha_ingreso), 1, 'N', 'S') ES_VENCIDA
		          ,D.COD_DEUDOR
		          ,RUT
		          ,DIG_VERIF
		          ,NOMBRE||' '||APELLIDO_PATERNO||' '||APELLIDO_MATERNO NOMBRE
		          ,T.COD_FAMILIA_DEUDA COD_FAMILIA_DEUDA_H
				  ,decode(T.COD_FAMILIA_DEUDA, 1, '', 'none') DISPLAY_TR
			      ,f_ingreso_pp_en_CR (PP.COD_PLAN_PAGO) EN_CR			
		        FROM DEUDOR D, DEUDA DA left outer join PLAN_PAGO PP on DA.COD_DEUDA = PP.COD_DEUDA 
			    , TIPO_DEUDA T, MONEDA M
			WHERE D.RUT = $rut
			  AND DA.COD_DEUDOR = D.COD_DEUDOR
			  AND DA.COD_ESTADO_DEUDA IN (1,2)
			  AND T.COD_TIPO_DEUDA = DA.COD_TIPO_DEUDA
			  AND M.COD_MONEDA = T.COD_MONEDA
			  AND DA.COD_CARTERA  = $cartera_defecto
			order by T.COD_FAMILIA_DEUDA ASC, DA.COD_TIPO_DEUDA, DA.COD_DEUDA ASC, PP.ANO_COBRO ASC, PP.FECHA_VENCTO ASC";
				
		else if (K_TIPO_BD=="mssql"){
			if(session::get('K_CLIENTE') == 'SURCOB_UACH'){
				$sql = "SELECT 'N' SELECCION
				      ,NULL COD_ITEM_INGRESO
				      ,NULL COD_INGRESO
				      ,DA.COD_DEUDA 
				      ,DA.COD_TIPO_DEUDA
				      ,M.SIMBOLO
				      ,PP.COD_PLAN_PAGO
				      ,(select COUNT(*) from PLAN_PAGO 
						where cod_deuda = DA.COD_DEUDA
						and COD_ESTADO_PLAN_PAGO = 4
						and NRO_CUOTA <> 99) CUOTAS_MOROSAS
				      ,isnull(PP.ANO_COBRO, 9999) ANO_COBRO
				      ,PP.NRO_CUOTA 
				      ,convert(varchar(10), PP.FECHA_VENCTO, 103) FECHA_VENCTO
				      ,dbo.f_mon_get_valor_moneda(T.COD_MONEDA, $fecha_ingreso) VALOR_MONEDA
				      ,NULL SALDO_CUOTA
				      ,NULL SALDO_MULTA
				      ,NULL SALDO_TOTAL
				      ,NULL SALDO_TOTAL_PESOS
				      ,0 MONTO_PAGO       
				      ,0 MONTO_PAGO_PESOS   
			          ,case sign(datediff(day,$fecha_ingreso,isnull(fecha_vencto, $fecha_ingreso+1)))
						when 1 then 'N'
						else 'S'
					  end ES_VENCIDA
			          ,D.COD_DEUDOR
			          ,RUT
			          ,DIG_VERIF
			          ,NOMBRE' 'APELLIDO_PATERNO' 'APELLIDO_MATERNO NOMBRE
			          ,T.COD_FAMILIA_DEUDA COD_FAMILIA_DEUDA_H
					  ,case T.COD_FAMILIA_DEUDA
						when 1 then '1'
						else 'none'
					  end DISPLAY_TR
					  ,dbo.f_ingreso_pp_en_CR (PP.COD_PLAN_PAGO) EN_CR 			
			        FROM DEUDOR D, DEUDA DA left outer join PLAN_PAGO PP on DA.COD_DEUDA = PP.COD_DEUDA 
				    , TIPO_DEUDA T, MONEDA M
				WHERE D.RUT = $rut
				  AND DA.COD_DEUDOR = D.COD_DEUDOR
				  AND DA.COD_ESTADO_DEUDA IN (1,2)
				  AND T.COD_TIPO_DEUDA = DA.COD_TIPO_DEUDA
				  AND M.COD_MONEDA = T.COD_MONEDA
				  AND dbo.f_visible_emp_cobranza(1, 'DEUDA', DA.COD_DEUDA) = 'S'
				  AND DA.COD_CARTERA  = $cartera_defecto
				order by T.COD_FAMILIA_DEUDA ASC, DA.COD_TIPO_DEUDA DESC, DA.COD_DEUDA ASC, PP.ANO_COBRO ASC, PP.FECHA_VENCTO ASC";
			}
			else if(K_CLIENTE == 'UTEM'){
				$sql = "SELECT 'N' SELECCION
				      ,NULL COD_ITEM_INGRESO
				      ,NULL COD_INGRESO
				      ,DA.COD_DEUDA 
				      ,DA.COD_TIPO_DEUDA
				      ,M.SIMBOLO
				      ,PP.COD_PLAN_PAGO
				      ,(select COUNT(*) from PLAN_PAGO 
						where cod_deuda = DA.COD_DEUDA
						and COD_ESTADO_PLAN_PAGO = 4
						and NRO_CUOTA <> 99) CUOTAS_MOROSAS
				      ,isnull(PP.ANO_COBRO, 9999) ANO_COBRO
				      ,PP.NRO_CUOTA 
				      ,convert(varchar(10), PP.FECHA_VENCTO, 103) FECHA_VENCTO
				      ,dbo.f_mon_get_valor_moneda(T.COD_MONEDA, $fecha_ingreso) VALOR_MONEDA
				      ,NULL SALDO_CUOTA
				      ,NULL SALDO_MULTA
				      ,NULL SALDO_TOTAL
				      ,NULL SALDO_TOTAL_PESOS
				      ,0 MONTO_PAGO       
				      ,0 MONTO_PAGO_PESOS   
			          ,case sign(datediff(day,$fecha_ingreso,isnull(fecha_vencto, $fecha_ingreso+1)))
						when 1 then 'N'
						else 'S'
					  end ES_VENCIDA
			          ,D.COD_DEUDOR
			          ,RUT
			          ,DIG_VERIF
			          ,NOMBRE' 'APELLIDO_PATERNO' 'APELLIDO_MATERNO NOMBRE
			          ,T.COD_FAMILIA_DEUDA COD_FAMILIA_DEUDA_H
					  ,case T.COD_FAMILIA_DEUDA
						when 1 then '1'
						else 'none'
					  end DISPLAY_TR
					  ,dbo.f_ingreso_pp_en_CR (PP.COD_PLAN_PAGO) EN_CR 			
			        FROM DEUDOR D, DEUDA DA left outer join PLAN_PAGO PP on DA.COD_DEUDA = PP.COD_DEUDA 
				    , TIPO_DEUDA T, MONEDA M
				WHERE D.RUT = $rut
				  AND DA.COD_DEUDOR = D.COD_DEUDOR
				  AND DA.COD_ESTADO_DEUDA IN (1,2)
				  AND T.COD_TIPO_DEUDA = DA.COD_TIPO_DEUDA
				  AND M.COD_MONEDA = T.COD_MONEDA
				AND DA.COD_CARTERA  = $cartera_defecto
				AND COD_PLAN_PAGO NOT IN (SELECT COD_PLAN_PAGO 
											FROM PLAN_PAGO_RENEG PPR, RENEG R 
											WHERE PPR.COD_RENEG = R.COD_RENEG
											AND R.COD_ESTADO_RENEG =2)
				order by T.COD_FAMILIA_DEUDA ASC, DA.COD_TIPO_DEUDA DESC, DA.COD_DEUDA ASC, PP.ANO_COBRO ASC, PP.FECHA_VENCTO ASC";
			}
			else{
				
				// CAMPO CUOTAS_MOROSAS aplica solo para UTEM, esto tambien se realizo en BOLETA,
				// El requerimiento es: el sistema no debe permitir cancelar cuotas futura (9999) si tiene pendiente  de pago cuotas en estado morosas 
				$sql = "SELECT 'N' SELECCION
				      ,NULL COD_ITEM_INGRESO
				      ,NULL COD_INGRESO
				      ,DA.COD_DEUDA 
				      ,DA.COD_TIPO_DEUDA
				      ,M.SIMBOLO
				      ,PP.COD_PLAN_PAGO
				      ,(select COUNT(*) from PLAN_PAGO 
						where cod_deuda = DA.COD_DEUDA
						and COD_ESTADO_PLAN_PAGO = 4
						and NRO_CUOTA <> 99) CUOTAS_MOROSAS
				      ,isnull(PP.ANO_COBRO, 9999) ANO_COBRO
				      ,PP.NRO_CUOTA 
				      ,convert(varchar(10), PP.FECHA_VENCTO, 103) FECHA_VENCTO
				      ,dbo.f_mon_get_valor_moneda(T.COD_MONEDA, $fecha_ingreso) VALOR_MONEDA
				      ,NULL SALDO_CUOTA
				      ,NULL SALDO_MULTA
				      ,NULL SALDO_TOTAL
				      ,NULL SALDO_TOTAL_PESOS
				      ,0 MONTO_PAGO       
				      ,0 MONTO_PAGO_PESOS   
			          ,case sign(datediff(day,$fecha_ingreso,isnull(fecha_vencto, $fecha_ingreso+1)))
						when 1 then 'N'
						else 'S'
					  end ES_VENCIDA
			          ,D.COD_DEUDOR
			          ,RUT
			          ,DIG_VERIF
			          ,NOMBRE' 'APELLIDO_PATERNO' 'APELLIDO_MATERNO NOMBRE
			          ,T.COD_FAMILIA_DEUDA COD_FAMILIA_DEUDA_H
					  ,case T.COD_FAMILIA_DEUDA
						when 1 then '1'
						else 'none'
					  end DISPLAY_TR
					  ,dbo.f_ingreso_pp_en_CR (PP.COD_PLAN_PAGO) EN_CR 	
					FROM DEUDOR D, DEUDA DA left outer join PLAN_PAGO PP on DA.COD_DEUDA = PP.COD_DEUDA 
				    , TIPO_DEUDA T, MONEDA M
				WHERE D.RUT = $rut
				  AND DA.COD_DEUDOR = D.COD_DEUDOR
				  AND DA.COD_ESTADO_DEUDA IN (1,2)
				  AND T.COD_TIPO_DEUDA = DA.COD_TIPO_DEUDA
				  AND M.COD_MONEDA = T.COD_MONEDA
				AND DA.COD_CARTERA  = $cartera_defecto
				order by T.COD_FAMILIA_DEUDA ASC, DA.COD_TIPO_DEUDA DESC, DA.COD_DEUDA ASC, PP.ANO_COBRO ASC, PP.FECHA_VENCTO ASC";
			}
		}	
		$result = $db->build_results($sql);
		if (count($result)==0) {
			return 'DEUDOR_SIN_DEUDAS';
		}
		for($i=0; $i<count($result); $i++) {
			$cod_plan_pago = $result[$i]['COD_PLAN_PAGO']; 
			if ($result[$i]['ES_VENCIDA']=='S') {
				if (K_TIPO_BD=="oci")
					$sql = "select f_pp_saldo_mora_caja($cod_plan_pago, $fecha_ingreso) SALDO_CUOTA
								,f_pp_saldo_multa_caja($cod_plan_pago, $fecha_ingreso) MULTA
							from DUAL";
				else if (K_TIPO_BD=="mssql")
					$sql = "select dbo.f_pp_saldo_mora_caja($cod_plan_pago, $fecha_ingreso) SALDO_CUOTA
								,dbo.f_pp_saldo_multa_caja($cod_plan_pago, $fecha_ingreso) MULTA";
							
				$res_saldo = $db->build_results($sql);
				$result[$i]['SALDO_CUOTA'] = $res_saldo[0]['SALDO_CUOTA']; 
				$result[$i]['SALDO_MULTA'] = $res_saldo[0]['MULTA']; 
			}
			else {
				if ($cod_plan_pago <> '') {
					if (K_TIPO_BD=="oci")
						$sql = "select f_pp_saldo_capital_caja($cod_plan_pago, $fecha_ingreso) + f_pp_saldo_interes_normal_caja($cod_plan_pago, $fecha_ingreso) SALDO_CUOTA
								from DUAL";
					else if (K_TIPO_BD=="mssql")
						$sql = "select dbo.f_pp_saldo_capital_caja($cod_plan_pago, $fecha_ingreso) + dbo.f_pp_saldo_interes_normal_caja($cod_plan_pago, $fecha_ingreso) SALDO_CUOTA";
				}
				else {
					$cod_deuda = $result[$i]['COD_DEUDA']; 
					if (K_TIPO_BD=="oci")
						$sql = "select f_deuda_saldo_total($cod_deuda, sysdate) SALDO_CUOTA
							from DUAL";
					else if (K_TIPO_BD=="mssql")
						$sql = "select dbo.f_deuda_saldo_total($cod_deuda, getdate()) SALDO_CUOTA";
							
				}
				$res_saldo = $db->build_results($sql);
				$result[$i]['SALDO_CUOTA'] = $res_saldo[0]['SALDO_CUOTA']; 
				$result[$i]['SALDO_MULTA'] = 0; 		
			}
			$result[$i]['SALDO_TOTAL'] = $result[$i]['SALDO_CUOTA'] + $result[$i]['SALDO_MULTA']; 
			$result[$i]['SALDO_TOTAL_PESOS'] = round($result[$i]['SALDO_TOTAL'] * $result[$i]['VALOR_MONEDA'], 0);
			$result[$i]['NOMBRE'] = urlencode($result[$i]['NOMBRE']);  
		}
		return $result;
	}
}