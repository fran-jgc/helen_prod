<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_repro_rcs_base extends sql_tabla 
{
	static function get_sql($selector_sql) 
	{
		switch ($selector_sql) 
		{
			/////////////////  INPUT ///////////////
			case 'INPUT':
				if(K_TIPO_BD=="oci")
				{
					$sql = "select R.COD_REPRO_RCS COD_REPRO
									,R.COD_REPRO_RCS COD_REPRO_RCS
									,TO_CHAR(R.FECHA_REPRO_RCS, 'dd/mm/yyyy') FECHA_REPRO_RCS
									,R.COD_USUARIO
									,U.NOM_USUARIO
									,D.RUT
									,D.DIG_VERIF
									,D.NOMBRE|| ' '|| D.APELLIDO_PATERNO|| ' ' || D.APELLIDO_MATERNO NOM_DEUDOR
									,R.COD_ESTADO_REPRO_RCS COD_ESTADO_REPRO
									,R.COD_ESTADO_REPRO_RCS COD_ESTADO_REPRO_RCS
									,f_last_mod('NOM_USUARIO', 'REPRO_RCS', 'COD_ESTADO_REPRO_RCS', R.COD_REPRO_RCS) NOM_USUARIO_ESTADO
											,f_last_mod('FECHA_CAMBIO', 'REPRO_RCS', 'COD_ESTADO_REPRO_RCS', R.COD_REPRO_RCS) FECHA_ESTADO
									,R.PORC_PAGO_INICIAL
									,R.MONTO_PAGO_INICIAL
									,ROUND(R.MONTO_PAGO_INICIAL * f_mon_get_valor_moneda(3, R.FECHA_REPRO_RCS),0) MONTO_PAGO_INICIAL_P
									,f_get_ingreso('COD_INGRESO',R.COD_REPRO_RCS)		COD_INGRESO  --COD ABONO
									,f_get_ingreso('FECHA_INGRESO',R.COD_REPRO_RCS)		FECHA_INGRESO --FECHA ABONO
									,f_get_ingreso('MONTO_PAGO_PESOS',R.COD_REPRO_RCS)	MONTO_INGRESO_P --MONTO ABONO
									,f_get_ingreso('ORIGEN_PAGO',R.COD_REPRO_RCS)	    ORIGEN_PAGO -- ABONO ORIGEN_PAGO
									,(select COD_ESTADO_PLAN_PAGO from PLAN_PAGO where NRO_CUOTA = 0
									  and cod_deuda = (SELECT COD_DEUDA_RCS FROM REPRO_RCS WHERE COD_REPRO_RCS = R.COD_REPRO_RCS)) COD_ESTADO_PP_H
									,R.PORC_DSCTO_MULTA
									,R.MONTO_DSCTO_MULTA
									,ROUND(R.MONTO_DSCTO_MULTA * f_mon_get_valor_moneda(3, R.FECHA_REPRO_RCS),0) MONTO_DSCTO_MULTA_P
									,R.MONTO_A_REPROGRAMAR
									,ROUND(R.MONTO_A_REPROGRAMAR * f_mon_get_valor_moneda(3, R.FECHA_REPRO_RCS),0) MONTO_A_REPROGRAMAR_P
									,R.CANT_CUOTA
									,R.MONTO_CUOTA
									,ROUND(R.MONTO_CUOTA *f_mon_get_valor_moneda(3, R.FECHA_REPRO_RCS),0) MONTO_CUOTA_P
									,R.OBS
									,D.COD_DEUDOR
									,R.PORC_DSCTO_MULTA PORC_DSCTO_MULTA_H
									,R.MONTO_DSCTO_MULTA MONTO_DSCTO_MULTA_H
									,R.MONTO_A_REPROGRAMAR MONTO_A_REPROGRAMAR_H
									,R.MONTO_CUOTA MONTO_CUOTA_H
									,ROUND(R.MONTO_PAGO_INICIAL * f_mon_get_valor_moneda(3, R.FECHA_REPRO_RCS),0) MONTO_PAGO_INICIAL_P_H
									,D.RUT RUT_H
									,R.COD_DEUDA_RCS
									,R.ORIGEN
									,R.RECIBIDO_DOCTO
									,TO_CHAR(R.FECHA_RECIBIDO_DOCTO, 'dd/mm/yyyy') FECHA_RECIBIDO_DOCTO
									,(SELECT UU.NOM_USUARIO FROM USUARIO UU WHERE UU.COD_USUARIO = R.USUARIO_RECIBIDO_DOCTO) USUARIO_RECIBIDO_DOCTO
									,R.NRO_REPRO_RCS
							from REPRO_RCS R, USUARIO U,DEUDOR D
							where R.COD_REPRO_RCS = {KEY1}
								and U.COD_USUARIO = R.COD_USUARIO
								and D.COD_DEUDOR = R.COD_DEUDOR";
				}
				else if (K_TIPO_BD=="mssql")
				{
					$sql = "select R.COD_REPRO_RCS COD_REPRO
									,R.COD_REPRO_RCS COD_REPRO_RCS
									,CONVERT(varchar, R.FECHA_REPRO_RCS, 103) FECHA_REPRO_RCS
									,R.COD_USUARIO
									,U.NOM_USUARIO
									,D.RUT
									,D.DIG_VERIF
									,D.NOMBRE+ ' ' + D.APELLIDO_PATERNO+ ' ' + D.APELLIDO_MATERNO NOM_DEUDOR
									,R.COD_ESTADO_REPRO_RCS COD_ESTADO_REPRO
									,R.COD_ESTADO_REPRO_RCS COD_ESTADO_REPRO_RCS
									,dbo.f_last_mod('NOM_USUARIO', 'REPRO_RCS', 'COD_ESTADO_REPRO_RCS', R.COD_REPRO_RCS) NOM_USUARIO_ESTADO
							        ,dbo.f_last_mod('FECHA_CAMBIO', 'REPRO_RCS', 'COD_ESTADO_REPRO_RCS', R.COD_REPRO_RCS) FECHA_ESTADO
									,R.PORC_PAGO_INICIAL
									,R.MONTO_PAGO_INICIAL
									,ROUND(R.MONTO_PAGO_INICIAL * dbo.f_mon_get_valor_moneda(3, R.FECHA_REPRO_RCS),0) MONTO_PAGO_INICIAL_P
									,dbo.f_get_ingreso('COD_INGRESO',R.COD_REPRO_RCS)			COD_INGRESO --COD ABONO
									,dbo.f_get_ingreso('FECHA_INGRESO',R.COD_REPRO_RCS)		FECHA_INGRESO --FECHA ABONO
									,dbo.f_get_ingreso('MONTO_PAGO_PESOS',R.COD_REPRO_RCS)	MONTO_INGRESO_P --MONTO ABONO
									,dbo.f_get_ingreso('ORIGEN_PAGO',R.COD_REPRO_RCS)	    ORIGEN_PAGO -- ABONO ORIGEN_PAGO
									,(select COD_ESTADO_PLAN_PAGO from PLAN_PAGO where NRO_CUOTA = 0
									  and cod_deuda = (SELECT COD_DEUDA_RCS FROM REPRO_RCS WHERE COD_REPRO_RCS = R.COD_REPRO_RCS)) COD_ESTADO_PP_H
									,R.PORC_DSCTO_MULTA
									,R.MONTO_DSCTO_MULTA
									,ROUND(R.MONTO_DSCTO_MULTA * dbo.f_mon_get_valor_moneda(3, R.FECHA_REPRO_RCS),0) MONTO_DSCTO_MULTA_P
									,R.MONTO_A_REPROGRAMAR
									,ROUND(R.MONTO_A_REPROGRAMAR * dbo.f_mon_get_valor_moneda(3, R.FECHA_REPRO_RCS),0) MONTO_A_REPROGRAMAR_P
									,R.CANT_CUOTA
									,R.MONTO_CUOTA
									,ROUND(R.MONTO_CUOTA * dbo.f_mon_get_valor_moneda(3, R.FECHA_REPRO_RCS),0) MONTO_CUOTA_P
									,R.OBS
									,D.COD_DEUDOR
									,R.PORC_DSCTO_MULTA PORC_DSCTO_MULTA_H
									,R.MONTO_DSCTO_MULTA MONTO_DSCTO_MULTA_H
									,R.MONTO_A_REPROGRAMAR MONTO_A_REPROGRAMAR_H
									,R.MONTO_CUOTA MONTO_CUOTA_H
									,ROUND(R.MONTO_PAGO_INICIAL * dbo.f_mon_get_valor_moneda(3, R.FECHA_REPRO_RCS),0) MONTO_PAGO_INICIAL_P_H
									,D.RUT RUT_H
									,R.COD_DEUDA_RCS
									,R.ORIGEN
									,R.RECIBIDO_DOCTO
									,CONVERT(varchar, R.FECHA_RECIBIDO_DOCTO, 103) FECHA_RECIBIDO_DOCTO
									,(SELECT UU.NOM_USUARIO FROM USUARIO UU WHERE UU.COD_USUARIO = R.USUARIO_RECIBIDO_DOCTO) USUARIO_RECIBIDO_DOCTO
									,R.NRO_REPRO_RCS
							from REPRO_RCS R, USUARIO U, DEUDOR D
							where R.COD_REPRO_RCS = {KEY1}
							  and U.COD_USUARIO = R.COD_USUARIO
							  and D.COD_DEUDOR = R.COD_DEUDOR";
				
				}
				break;
			/////////////////  OUTPUT ///////////////
			case 'OUTPUT':
				if(K_TIPO_BD=="oci")
				{
					$sql = "SELECT R.COD_REPRO_RCS COD_REPRO
									,R.COD_REPRO_RCS
									,to_char(R.FECHA_REPRO_RCS,'dd/mm/yyyy') FECHA_REPRO_RCS
									,ER.NOM_ESTADO_REPRO_RCS NOM_ESTADO_REPRO
									,ER.COD_ESTADO_REPRO_RCS 
									,D.RUT
									,D.DIG_VERIF
									,D.NOMBRE|| ' ' || D.APELLIDO_PATERNO|| ' ' || D.APELLIDO_MATERNO NOMBRE
									,ROUND(R.MONTO_PAGO_INICIAL,4) MONTO_PAGO_INICIAL
									,ROUND(R.MONTO_A_REPROGRAMAR,4) MONTO_A_REPROGRAMAR
									,R.CANT_CUOTA
									,ROUND(R.MONTO_CUOTA,4) MONTO_CUOTA
									,R.NRO_REPRO_RCS
							FROM REPRO_RCS R,ESTADO_REPRO_RCS ER,DEUDOR D
							WHERE R.COD_ESTADO_REPRO_RCS = ER.COD_ESTADO_REPRO_RCS
							AND D.COD_DEUDOR = R.COD_DEUDOR
							ORDER BY R.COD_REPRO_RCS DESC";
				}
				else if (K_TIPO_BD=="mssql")
				{
					$sql = "SELECT R.COD_REPRO_RCS COD_REPRO
									,R.COD_REPRO_RCS
									,CONVERT(varchar, R.FECHA_REPRO_RCS, 103) FECHA_REPRO_RCS
									,ER.NOM_ESTADO_REPRO_RCS NOM_ESTADO_REPRO
									,ER.COD_ESTADO_REPRO_RCS 
									,D.RUT
									,D.DIG_VERIF
									,D.NOMBRE+ ' ' + D.APELLIDO_PATERNO+ ' ' + D.APELLIDO_MATERNO NOMBRE
									,ROUND(R.MONTO_PAGO_INICIAL,4) MONTO_PAGO_INICIAL
									,ROUND(R.MONTO_A_REPROGRAMAR,4) MONTO_A_REPROGRAMAR
									,R.CANT_CUOTA
									,ROUND(R.MONTO_CUOTA,4) MONTO_CUOTA
									,R.NRO_REPRO_RCS
							FROM REPRO_RCS R,ESTADO_REPRO_RCS ER,DEUDOR D
							WHERE R.COD_ESTADO_REPRO_RCS = ER.COD_ESTADO_REPRO_RCS
							AND D.COD_DEUDOR = R.COD_DEUDOR
							ORDER BY R.COD_REPRO_RCS DESC";
				}
				break;
			///////////////// INPUT_DEUDA_REPRO	////////////////
			case 'INPUT_DEUDA_REPRO':
				if(K_TIPO_BD=="oci")
				{
					$sql = "SELECT COD_REPRO_RCS_DEUDA																		RD_COD_REPRO_RCS_DEUDA
										,R.COD_REPRO_RCS																	RD_COD_REPRO
										,D.COD_DEUDA																		RD_COD_DEUDA
										,(SELECT U.NOM_UNIVERSIDAD FROM UNIVERSIDAD U WHERE U.COD_UNIVERSIDAD = RD.COD_UNIVERSIDAD) RD_NOM_UNIVERSIDAD
										,D.COD_TIPO_DEUDA																	RD_TIPO_DEUDA
										,SALDO_CAPITAL																		RD_SALDO_CAPITAL
										,(SALDO_CAPITAL *  f_mon_get_valor_moneda(3, R.FECHA_REPRO_RCS))				RD_SALDO_CAPITAL_P
										,SALDO_MULTA																		RD_SALDO_MULTA
										,(SALDO_MULTA *  f_mon_get_valor_moneda(3, R.FECHA_REPRO_RCS))					RD_SALDO_MULTA_P
										,SALDO_CAPITAL + SALDO_MULTA														RD_SALDO_TOTAL
										,((SALDO_CAPITAL + SALDO_MULTA)* f_mon_get_valor_moneda(3, R.FECHA_REPRO_RCS))	RD_SALDO_TOTAL_P
										,'' D_COD_DEUDA_H
										,'' D_SALDO_CAPITAL_H
										,'' D_SALDO_MULTA_H
										,'' D_COD_UNIVERSIDAD_H
								FROM REPRO_RCS_DEUDA RD,REPRO_RCS R,DEUDA D
								WHERE R.COD_REPRO_RCS = RD.COD_REPRO_RCS
								AND D.COD_DEUDA = RD.COD_DEUDA
								AND RD.COD_REPRO_RCS = {KEY1}";
				}
				else if (K_TIPO_BD=="mssql")
				{
					$sql = "SELECT COD_REPRO_RCS_DEUDA																		RD_COD_REPRO_RCS_DEUDA
										,R.COD_REPRO_RCS																	RD_COD_REPRO
										,D.COD_DEUDA																		RD_COD_DEUDA
										,(SELECT U.NOM_UNIVERSIDAD FROM UNIVERSIDAD U WHERE U.COD_UNIVERSIDAD = RD.COD_UNIVERSIDAD) RD_NOM_UNIVERSIDAD
										,D.COD_TIPO_DEUDA																	RD_TIPO_DEUDA
										,SALDO_CAPITAL																		RD_SALDO_CAPITAL
										,(SALDO_CAPITAL * dbo.f_mon_get_valor_moneda(3, R.FECHA_REPRO_RCS))				RD_SALDO_CAPITAL_P
										,SALDO_MULTA																		RD_SALDO_MULTA
										,(SALDO_MULTA * dbo.f_mon_get_valor_moneda(3, R.FECHA_REPRO_RCS))					RD_SALDO_MULTA_P
										,SALDO_CAPITAL + SALDO_MULTA														RD_SALDO_TOTAL
										,((SALDO_CAPITAL + SALDO_MULTA)*dbo.f_mon_get_valor_moneda(3, R.FECHA_REPRO_RCS))	RD_SALDO_TOTAL_P
										,'' D_COD_DEUDA_H
										,'' D_SALDO_CAPITAL_H
										,'' D_SALDO_MULTA_H
										,'' D_COD_UNIVERSIDAD_H
								FROM REPRO_RCS_DEUDA RD,REPRO_RCS R,DEUDA D
								WHERE R.COD_REPRO_RCS = RD.COD_REPRO_RCS
								AND D.COD_DEUDA = RD.COD_DEUDA
								AND RD.COD_REPRO_RCS = {KEY1}";
				}
				break;
			/////////////////  DD_COD_ESTADO_REPRO_RCS /////////
			case 'DD_COD_ESTADO_REPRO_RCS':
				
				if (K_TIPO_BD=="mssql" || K_TIPO_BD=="oci")
				{
					$sql = "select ER.COD_ESTADO_REPRO_RCS COD_ESTADO_REPRO
									,ER.NOM_ESTADO_REPRO_RCS NOM_ESTADO_REPRO 
							from ESTADO_REPRO_RCS ER 
							ORDER BY ER.COD_ESTADO_REPRO_RCS";
				}
				break;
			/////////////////  AJAX_CARGA_DEUDA /////////
			case 'AJAX_CARGA_DEUDA':
				if(K_TIPO_BD=="oci")
				{
					$sql = "";
				}
				else if (K_TIPO_BD=="mssql")
				{
					$sql = "sp_repro_rcs_carga_deuda {PARAM1}";
				}
				break;
			/////////////////  AJAX_CARGA_DEUDOR /////////
			case 'AJAX_CARGA_DEUDOR':
				if(K_TIPO_BD=="oci")
				{
					$sql = "SELECT COD_DEUDOR
								,(NOMBRE||' '||APELLIDO_PATERNO||' '||APELLIDO_MATERNO) NOMBRE 
							FROM DEUDOR WHERE RUT ={PARAM1}
              				AND ROWNUM = 1";
				}
				else if (K_TIPO_BD=="mssql")
				{
					$sql = "SELECT top 1 COD_DEUDOR
								,(NOMBRE+' '+APELLIDO_PATERNO+' '+APELLIDO_MATERNO) NOMBRE 
							FROM DEUDOR WHERE RUT ={PARAM1}";
				}
				break;
			/////////////////  AJAX_MONTO_UTM /////////
			case 'AJAX_MONTO_UTM':
				if(K_TIPO_BD=="oci")
				{
					$sql = "SELECT f_mon_get_valor_moneda(3, SYSDATE)MONTO FROM DUAL";
				}
				else if (K_TIPO_BD=="mssql")
				{
					$sql = "SELECT dbo.f_mon_get_valor_moneda(3, GETDATE()) MONTO";
				}
				break;
			/////////////////  AJAX_CUOTA /////////
			case 'AJAX_CUOTA':
				if (K_TIPO_BD=="mssql" || K_TIPO_BD=="oci")
				{
					$sql = array(15);
					for($i=1; $i<15;$i++)
					{
						$sql[$i] =15-$i;
					} 
				}
				break;
			/////////////////  AJAX_CANDIDATO_A_REPRO_RCS /////////
			case 'AJAX_CANDIDATO_A_REPRO_RCS':
				if(K_TIPO_BD=="oci")
				{
					if(K_CLIENTE == "USACH")
						$sql = "SELECT f_repro_rcs_es_candidato ({PARAM1},'INPUT_DEUDOR') FROM dual";
					else
						$sql = "SELECT f_repro_rcs_es_candidato ({PARAM1}) FROM dual";
				}
				else if (K_TIPO_BD=="mssql")
				{
					if(K_CLIENTE == "ULAGOS")
							$sql = "SELECT dbo.f_repro_rcs_es_candidato ( {PARAM1},'INPUT_DEUDOR' )"; 
					else
						$sql = "SELECT dbo.f_repro_rcs_es_candidato ( {PARAM1} )"; 
				}
				break;
			/////////////////  AJAX_EXISTE_CANDIDATO /////////
			case 'AJAX_EXISTE_CANDIDATO':
				if (K_TIPO_BD=="mssql" || K_TIPO_BD=="oci")
				{
					$sql = "SELECT COUNT(*) CANTIDAD
							FROM REPRO_RCS
							where COD_DEUDOR ={PARAM1}
							AND COD_ESTADO_REPRO_RCS <>3"; 
				}
				break;
			/////////////////  NUEVA_BOLETA /////////
			case 'NUEVA_BOLETA':
				if(K_TIPO_BD=="oci")
				{
					$sql = "select MAX(COD_BOLETA) COD_BOLETA 
							from BOLETA 
							where TO_CHAR(FECHA_BOLETA,'dd/mm/yyyy')  
								= To_Char(SYSDATE,'dd/mm/yyyy')";
				}
				else if (K_TIPO_BD=="mssql")
				{
					$sql = "select MAX(COD_BOLETA) COD_BOLETA 
							from BOLETA 
							where CONVERT(varchar, FECHA_BOLETA, 103)  
								= CONVERT(varchar, GETDATE(), 103)"; 
				}
				break;
			/////////////////  PRINT_BOLETA /////////
			case 'PRINT_BOLETA':
				if(K_TIPO_BD=="oci")
				{
					//JV menciona que no es necesario, falla por funcion llave_operacion
					$sql = "SELECT	B.COD_BOLETA
									,'SANTANDER' NOM_BANCO
									,case TD.COD_TIPO_DEUDA 
									 when 'CI' then 524
									 else 7120 
									 end COD_CONVENIO
									,DR.NOMBRE
									,DR.APELLIDO_PATERNO
									,DR.APELLIDO_MATERNO
									,DR.RUT 
									,DIG_VERIF
									,SB.NRO_SUB_BOLETA
									,decode(SB.NRO_SUB_BOLETA,NULL,9999,SB.NRO_SUB_BOLETA) NRO_CUOTA
									,decode(B.CANT,NULL,9999,B.CANT) CANT_CUOTAS
									,to_char(SB.FECHA_VENCTO, 'dd/mm/yyyy') FECHA_VENCTO
									,P.ANO_COBRO ANO
									,SB.COD_SUB_BOLETA
									,SB.MONTO_CAPITAL
									,SB.MONTO_INTERES
									,SB.MONTO_MULTA
									,TD.NOM_TIPO_DEUDA
									,(SB.MONTO_CAPITAL * f_mon_get_valor_moneda(TD.COD_MONEDA, isnull(FECHA_PAGO, getdate()))) MONTO_CAPITAL_PESOS
									,(SB.MONTO_MULTA * f_mon_get_valor_moneda(TD.COD_MONEDA, isnull(FECHA_PAGO, getdate()))) MONTO_MULTA_PESOS 
									,round((SB.MONTO_CAPITAL * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, isnull(FECHA_PAGO, getdate())))  +
									(SB.MONTO_INTERES *  f_mon_get_valor_moneda(TD.COD_MONEDA, isnull(FECHA_PAGO, getdate()))) + 	
									(SB.MONTO_MULTA *  f_mon_get_valor_moneda(TD.COD_MONEDA, isnull(FECHA_PAGO, getdate()))), 0) TOTAL_PESOS
									,U.NOM_UNIVERSIDAD
			        				,to_char(B.FECHA_BOLETA,'dd/mm/yyyy') FECHA_BOLETA
			        				,f_llave_operacion(B.COD_BOLETA) LLAVE_OPERACION_DIG_VERIF
									,(SB.MONTO_CAPITAL *  f_mon_get_valor_moneda(TD.COD_MONEDA, decode(FECHA_PAGO,NULL, SYSDATE,FECHA_PAGO)))  +
			        				(SB.MONTO_INTERES *  f_mon_get_valor_moneda(TD.COD_MONEDA, decode(FECHA_PAGO,NULL, SYSDATE,FECHA_PAGO)))	MONTO_CUOTA
									,(SB.MONTO_MULTA *  f_mon_get_valor_moneda(TD.COD_MONEDA, decode(FECHA_PAGO,NULL, SYSDATE,FECHA_PAGO)))	MONTO_MULTA
									,CANT CANTIDAD_SUB_BOLETA	
									,D.COD_DEUDA	
							FROM 	BOLETA B left outer join PLAN_PAGO P on B.COD_PLAN_PAGO = P.COD_PLAN_PAGO, SUB_BOLETA SB, DEUDA D, DEUDOR DR, TIPO_DEUDA TD, UNIVERSIDAD U
							WHERE 	B.COD_BOLETA = 2
							AND 	B.COD_BOLETA = SB.COD_BOLETA
							AND 	D.COD_DEUDA = B.COD_DEUDA
							AND 	DR.COD_DEUDOR = D.COD_DEUDOR
							AND		D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
							AND		D.COD_UNIVERSIDAD = U.COD_UNIVERSIDAD
							AND		P.COD_PLAN_PAGO = B.COD_PLAN_PAGO
							ORDER BY DR.APELLIDO_PATERNO, DR.APELLIDO_MATERNO, DR.NOMBRE ASC";
				}
				else if (K_TIPO_BD=="mssql")
				{
					$sql = "SELECT	B.COD_BOLETA
									,'SANTANDER' NOM_BANCO
									,case TD.COD_TIPO_DEUDA 
									 when 'CI' then 524
									 else 7120 
									 end COD_CONVENIO
									,DR.NOMBRE
									,DR.APELLIDO_PATERNO
									,DR.APELLIDO_MATERNO
									,DR.RUT 
									,DIG_VERIF
									,SB.NRO_SUB_BOLETA
									,isnull(SB.NRO_SUB_BOLETA,9999) NRO_CUOTA
									,isnull(B.CANT,9999) CANT_CUOTAS
									,convert(varchar(20), SB.FECHA_VENCTO, 103) FECHA_VENCTO
									,P.ANO_COBRO ANO
									,SB.COD_SUB_BOLETA
									,SB.MONTO_CAPITAL
									,SB.MONTO_INTERES
									,SB.MONTO_MULTA
									,TD.NOM_TIPO_DEUDA
									,(SB.MONTO_CAPITAL * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, isnull(FECHA_PAGO, getdate()))) MONTO_CAPITAL_PESOS
									,(SB.MONTO_MULTA * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, isnull(FECHA_PAGO, getdate()))) MONTO_MULTA_PESOS 
									,round((SB.MONTO_CAPITAL * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, isnull(FECHA_PAGO, getdate())))  +
									(SB.MONTO_INTERES * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, isnull(FECHA_PAGO, getdate()))) + 	
									(SB.MONTO_MULTA * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, isnull(FECHA_PAGO, getdate()))), 0) TOTAL_PESOS
									,U.NOM_UNIVERSIDAD
			        				,convert(varchar(20), B.FECHA_BOLETA, 103) FECHA_BOLETA
			        				,dbo.f_llave_operacion(B.COD_BOLETA) LLAVE_OPERACION_DIG_VERIF
									,(SB.MONTO_CAPITAL * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, isnull(FECHA_PAGO, getdate())))  +
			        				(SB.MONTO_INTERES * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, isnull(FECHA_PAGO, getdate())))	MONTO_CUOTA
									,(SB.MONTO_MULTA * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, isnull(FECHA_PAGO, getdate())))	MONTO_MULTA
									,CANT CANTIDAD_SUB_BOLETA	
									,D.COD_DEUDA	
							FROM 	BOLETA B left outer join PLAN_PAGO P on B.COD_PLAN_PAGO = P.COD_PLAN_PAGO, SUB_BOLETA SB, DEUDA D, DEUDOR DR, TIPO_DEUDA TD, UNIVERSIDAD U
							WHERE 	B.COD_BOLETA = {PARAM1}
							AND 	B.COD_BOLETA = SB.COD_BOLETA
							AND 	D.COD_DEUDA = B.COD_DEUDA
							AND 	DR.COD_DEUDOR = D.COD_DEUDOR
							AND		D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
							AND		D.COD_UNIVERSIDAD = U.COD_UNIVERSIDAD
							AND		P.COD_PLAN_PAGO = B.COD_PLAN_PAGO
							ORDER BY DR.APELLIDO_PATERNO, DR.APELLIDO_MATERNO, DR.NOMBRE ASC";
				}
				break;
			/////////////////  NRO_REPRO_RCS /////////
			case 'NRO_REPRO_RCS':
				if (K_TIPO_BD=="mssql" || K_TIPO_BD=="oci")
				{
					$sql = "SELECT NRO_REPRO_RCS
							FROM REPRO_RCS
							WHERE COD_REPRO_RCS ={PARAM1} "; 
				}
				break;
			/////////////////  ERROR ///////////////
			default:
			base::error("sql_repro_rcs::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
	function select_deuda($cod_deudor)
	{
		$sql = "";
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql_es_cruzado ="SELECT COUNT(*) CANTIDAD
							FROM DEUDA DA,CRUZADO C,TIPO_DEUDA t
							WHERE COD_DEUDOR = $cod_deudor
							AND DA.COD_DEUDA = C.COD_DEUDA
							and t.COD_TIPO_DEUDA = da.COD_TIPO_DEUDA
							and da.COD_ESTADO_DEUDA = 2 --EXIGIBLE
							and da.COD_TIPO_DEUDA not in ('CSR', 'CRPI', 'CR12','CFR','CRI')
							AND (SELECT  nvl(SUM (f_pp_saldo_mora(pp.cod_plan_pago, sysdate)), 0) SALDO_MORA
				        	FROM plan_pago pp
						    WHERE cod_deuda = da.COD_DEUDA
						    and FECHA_VENCTO < add_months(sysdate,-18)) > 0.0000
							and t.COD_FAMILIA_DEUDA = 1 --FONDO_SOLIDARIO";
		$result_es_cruzado	= $db->build_results($sql_es_cruzado);
		
		if($result_es_cruzado[0]["CANTIDAD"] > 1)
		{
			$sql = "select da.COD_DEUDA
							 ,DA.COD_TIPO_DEUDA
							 ,CR.COD_UNIVERSIDAD
							 ,(SELECT U.NOM_CORTO_UNIVERSIDAD FROM UNIVERSIDAD U WHERE U.COD_UNIVERSIDAD = CR.COD_UNIVERSIDAD) NOM_UNIVERSIDAD
							 ,ROUND((CR.PORC*sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)))/100,4) SALDO_CAPITAL
							 ,ROUND((f_mon_get_valor_moneda(3, sysdate) * ROUND((CR.PORC*sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)))/100,4)),0) SALDO_CAPITAL_P
							 ,ROUND((CR.PORC*sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate)))/100,4) SALDO_MULTA
							 ,ROUND(f_mon_get_valor_moneda(3, sysdate) *ROUND((CR.PORC*sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate)))/100,4) ,0) SALDO_MULTA_P
							 ,ROUND((CR.PORC*sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)))/100,4)+
							  ROUND((CR.PORC*sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate)))/100,4) SALDO_TOTAL
							 ,ROUND(f_mon_get_valor_moneda(3, sysdate) *
							  (ROUND((CR.PORC*sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)))/100,4)+
							  ROUND((CR.PORC*sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate)))/100,4)),0) SALDO_TOTAL_P
							  ,CR.PORC
					from TIPO_DEUDA t, PLAN_PAGO p,
					DEUDA da LEFT OUTER JOIN CRUZADO CR ON CR.COD_DEUDA = DA.COD_DEUDA
					where da.COD_DEUDOR = $cod_deudor
					and da.COD_ESTADO_DEUDA = 2 --EXIGIBLE
					and da.COD_TIPO_DEUDA not in ('CSR', 'CRPI', 'CR12','CFR','CRI') 
					and t.COD_TIPO_DEUDA = da.COD_TIPO_DEUDA
					and t.COD_FAMILIA_DEUDA = 1 --FONDO_SOLIDARIO
					and p.COD_DEUDA = da.COD_DEUDA
					AND (SELECT  nvl(SUM (f_pp_saldo_mora(pp.cod_plan_pago, sysdate)), 0) SALDO_MORA
				        FROM plan_pago pp
					    WHERE cod_deuda = da.COD_DEUDA
					    and FECHA_VENCTO < add_months(sysdate,-18)) > 0.0000
					group by da.COD_DEUDA,DA.COD_TIPO_DEUDA,CR.PORC, CR.COD_UNIVERSIDAD
					UNION
					select da.COD_DEUDA
							 ,DA.COD_TIPO_DEUDA
							 ,U.COD_UNIVERSIDAD
							 ,U.NOM_CORTO_UNIVERSIDAD NOM_UNIVERSIDAD
							 ,sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)) SALDO_CAPITAL
							 ,ROUND(f_mon_get_valor_moneda(3, sysdate) * 
									(sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end))),0) SALDO_CAPITAL_P
							 ,sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate)) SALDO_MULTA 
							 ,ROUND(f_mon_get_valor_moneda(3, sysdate) *(sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate))) ,0) SALDO_MULTA_P
							 ,sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)) +
							  sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate)) SALDO_TOTAL
							 ,ROUND(f_mon_get_valor_moneda(3, sysdate) *
							  (sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)) +
							  sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate))),0) SALDO_TOTAL_P
							  ,NULL
					from DEUDA da, TIPO_DEUDA t, PLAN_PAGO p,UNIVERSIDAD U
					where da.COD_DEUDOR = $cod_deudor
					and da.COD_ESTADO_DEUDA = 2 --EXIGIBLE
					and da.COD_TIPO_DEUDA not in ('CSR', 'CRPI', 'CR12','CFR','CRI') 
					and t.COD_TIPO_DEUDA = da.COD_TIPO_DEUDA
					AND DA.COD_UNIVERSIDAD = U.COD_UNIVERSIDAD
					and t.COD_FAMILIA_DEUDA = 1 --FONDO_SOLIDARIO
					and p.COD_DEUDA = da.COD_DEUDA
					AND DA.COD_DEUDA NOT IN (SELECT COD_DEUDA FROM CRUZADO)
					AND (SELECT  nvl(SUM (f_pp_saldo_mora(pp.cod_plan_pago, sysdate)), 0) SALDO_MORA
				        FROM plan_pago pp
					    WHERE cod_deuda = da.COD_DEUDA
					    and FECHA_VENCTO < add_months(sysdate,-18)) > 0.0000
					group by da.COD_DEUDA,DA.COD_TIPO_DEUDA,U.NOM_CORTO_UNIVERSIDAD,U.COD_UNIVERSIDAD";
		}
		else
		{
			$sql = "select da.COD_DEUDA
							 ,DA.COD_TIPO_DEUDA
							 ,U.COD_UNIVERSIDAD
							 ,U.NOM_CORTO_UNIVERSIDAD NOM_UNIVERSIDAD
							 ,sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)) SALDO_CAPITAL
							 ,ROUND(f_mon_get_valor_moneda(3, sysdate) * 
									(sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end))),0) SALDO_CAPITAL_P
							 ,sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate)) SALDO_MULTA 
							 ,ROUND(f_mon_get_valor_moneda(3, sysdate) *(sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate))) ,0) SALDO_MULTA_P
							 ,sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)) +
							  sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate)) SALDO_TOTAL
							 ,ROUND(f_mon_get_valor_moneda(3, sysdate) *
							  (sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)) +
							  sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate))),0) SALDO_TOTAL_P
					from DEUDA da, TIPO_DEUDA t, PLAN_PAGO p,UNIVERSIDAD U
					where da.COD_DEUDOR = $cod_deudor
					and da.COD_ESTADO_DEUDA = 2 --EXIGIBLE
					and da.COD_TIPO_DEUDA not in ('CSR', 'CRPI', 'CR12','CFR','CRI') 
					and t.COD_TIPO_DEUDA = da.COD_TIPO_DEUDA
					AND DA.COD_UNIVERSIDAD = U.COD_UNIVERSIDAD
					and t.COD_FAMILIA_DEUDA = 1 --FONDO_SOLIDARIO
					and p.COD_DEUDA = da.COD_DEUDA
					AND (SELECT  nvl(SUM (f_pp_saldo_mora(pp.cod_plan_pago, sysdate)), 0) SALDO_MORA
				        FROM plan_pago pp
					    WHERE cod_deuda = da.COD_DEUDA
					    and FECHA_VENCTO < add_months(sysdate,-18)) > 0.0000
					group by da.COD_DEUDA,DA.COD_TIPO_DEUDA,U.NOM_CORTO_UNIVERSIDAD,U.COD_UNIVERSIDAD";
		}
		return $sql;
	}
}

/////////////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_sql_repro_rcs.php";
if (file_exists($file_name))
    require_once($file_name);
else {
    class sql_repro_rcs extends sql_repro_rcs_base {
    }
}
?>