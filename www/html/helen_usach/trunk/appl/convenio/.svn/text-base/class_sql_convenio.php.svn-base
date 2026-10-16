<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_convenio extends sql_tabla {
	const K_ESTADO_CONVENIO_EMITIDA 	= 1;
	const K_ESTADO_CONVENIO_CONFIRMADO = 2;
	const K_ESTADO_CONVENIO_ANULADA 	= 3;
	const K_EST_DEUDA_COBRANZA_NORMAL		= 2;
	const K_EST_PP_PAGADA					= 3;
	
	
	const K_PARAM_PORC_PAGO_CONV 		= 31;
	
	static function get_sql($selector_sql) {
		switch ($selector_sql) {
			
			///////////////// INPUT ///////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql =	"SELECT COD_CONVENIO
									,To_Char(CN.FECHA_CONVENIO,'dd/mm/yyyy') FECHA_CONVENIO
									, U.NOM_USUARIO
									, CN.COD_ESTADO_CONVENIO
									, f_last_mod('NOM_USUARIO', 'CONVENIO', 'COD_ESTADO_CONVENIO', CN.COD_CONVENIO) NOM_USUARIO_CAMBIO
							    	, f_last_mod('FECHA_CAMBIO', 'CONVENIO', 'COD_ESTADO_CONVENIO', CN.COD_CONVENIO) FECHA_CAMBIO
							    	,D.COD_DEUDOR
							    	,D.RUT
							    	,D.DIG_VERIF
									,D.NOMBRE || ' ' || D.APELLIDO_PATERNO || ' ' || D.APELLIDO_MATERNO  NOM_DEUDOR
									,DA.COD_TIPO_DEUDA
									,DA.COD_DEUDA
									,DA.COD_DEUDA COD_DEUDA_H
									,C.NOM_CARTERA
									,E.NOM_ESTADO_DEUDA
									,DA.CANT_CUOTA CANT_CUOTA_DEUDA
									,M.SIMBOLO
									,f_get_parametro(".self::K_PARAM_PORC_PAGO_CONV.") PORC_PAGO_SUGERIDO
									,f_get_parametro(".self::K_PARAM_PORC_PAGO_CONV.") PORC_PAGO_SUGERIDO_H
									,CN.PAGO_INICIAL_SUGERIDO
									,CN.PAGO_INICIAL_SUGERIDO PAGO_INICIAL_SUGERIDO_H
									,CN.PAGO_INICIAL
									,CN.PAGO_INICIAL CU_PAGO_INICIAL
									,CN.MONTO_CONVENIO
									,CN.MONTO_CONVENIO MONTO_CONVENIO_H
									,CN.MONTO_CONVENIO_MONEDA
									,CN.MONTO_CONVENIO_MONEDA MONTO_CONVENIO_MONEDA_H
									,To_Char(CN.FECHA_INICIO_COBRO, 'dd/mm/yyyy') FECHA_INICIO_COBRO
									,CN.MONTO_CONVENIO_ACT
									,CN.MONTO_CONVENIO_ACT MONTO_CONVENIO_ACT_H
									,f_mon_get_valor_moneda(T.COD_MONEDA, FECHA_CONVENIO) VALOR_MONEDA
									,CN.CANT_CUOTA
									,round(CN.CUOTA_MENSUAL_MONEDA * f_mon_get_valor_moneda(T.COD_MONEDA, FECHA_CONVENIO), 0) CUOTA_MENSUAL
									,CN.CUOTA_MENSUAL_MONEDA
									,CN.CUOTA_MENSUAL_MONEDA CUOTA_MENSUAL_MONEDA_H
									,CN.OBS
									,decode(CN.COD_ESTADO_CONVENIO, 2, '', 'none') DISPLAY_CUOTAS
									,f_conv_saldo_pago_inicial(CN.COD_CONVENIO) SALDO_CONVENIO_H
									,'' COD_INGRESO  
									,f_conv_saldo_pago_inicial(CN.COD_CONVENIO) SALDO_ACTUAL
									,'' COD_USUARIO_H
									,CN.AUTORIZADO
									,f_last_mod('NOM_USUARIO', 'CONVENIO', 'AUTORIZADO', CN.COD_CONVENIO) USUARIO_AUTORIZA
                        			,f_last_mod('FECHA_CAMBIO', 'CONVENIO', 'AUTORIZADO', CN.COD_CONVENIO) FECHA_AUTORIZA
							FROM 	CONVENIO CN, USUARIO U, DEUDA DA, TIPO_DEUDA T, MONEDA M, DEUDOR D, CARTERA C, ESTADO_DEUDA E 
							WHERE 	CN.COD_CONVENIO = {KEY1}
							AND		U.COD_USUARIO = CN.COD_USUARIO
							AND		DA.COD_DEUDA = CN.COD_DEUDA
							AND		D.COD_DEUDOR = DA.COD_DEUDOR
			 				and 	C.COD_CARTERA = DA.COD_CARTERA
					  		and 	E.COD_ESTADO_DEUDA = DA.COD_ESTADO_DEUDA
					  		and		T.COD_TIPO_DEUDA = DA.COD_TIPO_DEUDA
							and		M.COD_MONEDA = T.COD_MONEDA";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT COD_CONVENIO
									,convert(varchar, CN.FECHA_CONVENIO, 103) FECHA_CONVENIO
									, U.NOM_USUARIO
									, CN.COD_ESTADO_CONVENIO
									, dbo.f_last_mod('NOM_USUARIO', 'CONVENIO', 'COD_ESTADO_CONVENIO', CN.COD_CONVENIO) NOM_USUARIO_CAMBIO
							        , dbo.f_last_mod('FECHA_CAMBIO', 'CONVENIO', 'COD_ESTADO_CONVENIO', CN.COD_CONVENIO) FECHA_CAMBIO
							        ,D.COD_DEUDOR
							        ,D.RUT
							        ,D.DIG_VERIF
									,D.NOMBRE + ' ' + D.APELLIDO_PATERNO + ' ' + D.APELLIDO_MATERNO  NOM_DEUDOR
									,DA.COD_TIPO_DEUDA
									,DA.COD_DEUDA
									,DA.COD_DEUDA COD_DEUDA_H
									,C.NOM_CARTERA
									,E.NOM_ESTADO_DEUDA
									,DA.CANT_CUOTA CANT_CUOTA_DEUDA
									,M.SIMBOLO
									,dbo.f_get_parametro(".self::K_PARAM_PORC_PAGO_CONV.") PORC_PAGO_SUGERIDO
									,dbo.f_get_parametro(".self::K_PARAM_PORC_PAGO_CONV.") PORC_PAGO_SUGERIDO_H
									,CN.PAGO_INICIAL_SUGERIDO
									,CN.PAGO_INICIAL_SUGERIDO PAGO_INICIAL_SUGERIDO_H
									,CN.PAGO_INICIAL
									,CN.PAGO_INICIAL CU_PAGO_INICIAL
									,CN.MONTO_CONVENIO
									,CN.MONTO_CONVENIO MONTO_CONVENIO_H
									,CN.MONTO_CONVENIO_MONEDA
									,CN.MONTO_CONVENIO_MONEDA MONTO_CONVENIO_MONEDA_H
									,convert(varchar, CN.FECHA_INICIO_COBRO, 103) FECHA_INICIO_COBRO
									,CN.MONTO_CONVENIO_ACT
									,CN.MONTO_CONVENIO_ACT MONTO_CONVENIO_ACT_H
									,dbo.f_mon_get_valor_moneda(T.COD_MONEDA, FECHA_CONVENIO) VALOR_MONEDA
									,CN.CANT_CUOTA
									,round(CN.CUOTA_MENSUAL_MONEDA * dbo.f_mon_get_valor_moneda(T.COD_MONEDA, FECHA_CONVENIO), 0) CUOTA_MENSUAL
									,CN.CUOTA_MENSUAL_MONEDA
									,CN.CUOTA_MENSUAL_MONEDA CUOTA_MENSUAL_MONEDA_H
									,CN.OBS
									,case CN.COD_ESTADO_CONVENIO 
										when 2 then ''
										else 'none'
									end DISPLAY_CUOTAS
									,dbo.f_conv_saldo_pago_inicial(CN.COD_CONVENIO) SALDO_CONVENIO_H
									,'' COD_INGRESO
									,dbo.f_conv_saldo_pago_inicial(CN.COD_CONVENIO) SALDO_ACTUAL
									,CN.COD_ESTADO_CONVENIO COD_ESTADO_ACTUAL
									,'' COD_USUARIO_H
									,CN.AUTORIZADO
									,case autorizado when 'N' then 
										''
									else
										dbo.f_last_mod('NOM_USUARIO', 'CONVENIO', 'AUTORIZADO', CN.COD_CONVENIO)
									end USUARIO_AUTORIZA							
									,case autorizado when 'N' then 
										''
									else
										dbo.f_last_mod('FECHA_CAMBIO', 'CONVENIO', 'AUTORIZADO', CN.COD_CONVENIO) 
									end FECHA_AUTORIZA
							FROM 	CONVENIO CN, USUARIO U, DEUDA DA, TIPO_DEUDA T, MONEDA M, DEUDOR D, CARTERA C, ESTADO_DEUDA E 
							WHERE 	CN.COD_CONVENIO = {KEY1}
							AND		U.COD_USUARIO = CN.COD_USUARIO
							AND		DA.COD_DEUDA = CN.COD_DEUDA
							AND		D.COD_DEUDOR = DA.COD_DEUDOR
			 				and 	C.COD_CARTERA = DA.COD_CARTERA
					  		and 	E.COD_ESTADO_DEUDA = DA.COD_ESTADO_DEUDA
					  		and		T.COD_TIPO_DEUDA = DA.COD_TIPO_DEUDA
							and		M.COD_MONEDA = T.COD_MONEDA";
				}
				break;

				///////////////// OUTPUT///////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	C.COD_CONVENIO
									,To_Char(C.FECHA_CONVENIO, 'dd/mm/yyyy') FECHA_CONVENIO
									,DR.RUT 
									,DR.DIG_VERIF
									,DR.NOMBRE
									,DR.APELLIDO_PATERNO
									,DR.APELLIDO_MATERNO
									,DA.COD_DEUDA
									,DA.COD_TIPO_DEUDA
									,USUARIO.NOM_USUARIO
									--,EC.COD_ESTADO_CONVENIO
									,NOM_ESTADO_CONVENIO
									,C.AUTORIZADO
							FROM 	CONVENIO C
									,DEUDOR DR
									,DEUDA DA
									,ESTADO_CONVENIO EC
									,USUARIO
							WHERE 	C.COD_DEUDA = DA.COD_DEUDA
										AND DA.COD_DEUDOR = DR.COD_DEUDOR
										AND EC.COD_ESTADO_CONVENIO = C.COD_ESTADO_CONVENIO
										AND USUARIO.COD_USUARIO = C.COD_USUARIO
						  	ORDER BY	C.COD_CONVENIO DESC";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT C.COD_CONVENIO
									 ,convert(varchar, C.FECHA_CONVENIO, 103) FECHA_CONVENIO
									 ,DR.RUT
									 ,DR.DIG_VERIF
									 ,DR.NOMBRE
									 ,DR.APELLIDO_PATERNO
									 ,DR.APELLIDO_MATERNO
									 ,DA.COD_DEUDA
									 ,DA.COD_TIPO_DEUDA
									 ,NOM_ESTADO_CONVENIO
									 ,NOM_USUARIO
									 ,C.AUTORIZADO
							FROM CONVENIO C, DEUDOR DR, DEUDA DA, ESTADO_CONVENIO EC, USUARIO U
							WHERE C.COD_DEUDA = DA.COD_DEUDA
							  AND DA.COD_DEUDOR = DR.COD_DEUDOR
							  AND EC.COD_ESTADO_CONVENIO = C.COD_ESTADO_CONVENIO
							  AND C.COD_USUARIO = U.COD_USUARIO
						  	ORDER BY C.COD_CONVENIO DESC";
				}
				break;
				
				///////////////// DW_PLAN_PAGO_CONVENIO///////////////
			case 'DW_PLAN_PAGO_CONVENIO' :
				if (K_TIPO_BD=="oci"){
					$sql = "select 'S' SELECCION
									,PP.COD_PLAN_PAGO
									,PP.ANO_COBRO
									,TO_CHAR(PP.FECHA_VENCTO, 'DD/MM/YYYY') FECHA_VENCTO
									,PPC.SALDO_CUOTA
									,PPC.SALDO_MULTA
									,PPC.TOTAL_SALDO_MONEDA
									,PPC.TOTAL_SALDO_PESOS
									,PP.COD_PLAN_PAGO COD_PLAN_PAGO_H
									,PPC.SALDO_CUOTA SALDO_CUOTA_H
									,PPC.SALDO_MULTA SALDO_MULTA_H
									,PPC.TOTAL_SALDO_MONEDA TOTAL_SALDO_MONEDA_H
									,PPC.TOTAL_SALDO_PESOS TOTAL_SALDO_PESOS_H
							from PLAN_PAGO_CONVENIO PPC, PLAN_PAGO PP
							where PPC.COD_CONVENIO = {KEY1}
							  and PP.COD_PLAN_PAGO = PPC.COD_PLAN_PAGO";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select 'S' SELECCION
									,PP.COD_PLAN_PAGO
									,PP.ANO_COBRO
									,convert(varchar, PP.FECHA_VENCTO, 103) FECHA_VENCTO
									,PPC.SALDO_CUOTA
									,PPC.SALDO_MULTA
									,PPC.TOTAL_SALDO_MONEDA
									,PPC.TOTAL_SALDO_PESOS
									,PP.COD_PLAN_PAGO COD_PLAN_PAGO_H
									,PPC.SALDO_CUOTA SALDO_CUOTA_H
									,PPC.SALDO_MULTA SALDO_MULTA_H
									,PPC.TOTAL_SALDO_MONEDA TOTAL_SALDO_MONEDA_H
									,PPC.TOTAL_SALDO_PESOS TOTAL_SALDO_PESOS_H
							from PLAN_PAGO_CONVENIO PPC, PLAN_PAGO PP
							where PPC.COD_CONVENIO = {KEY1}
							  and PP.COD_PLAN_PAGO = PPC.COD_PLAN_PAGO";
				}
				break;
				
				//////////////////DW_SUMA_PLAN_PAGO//////////////////////////
			case 'DW_SUMA_PLAN_PAGO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT SUM(PPC.SALDO_CUOTA) SUMA_SALDO_CUOTA
									,SUM(PPC.SALDO_MULTA) SUMA_SALDO_MULTA
									,SUM(PPC.TOTAL_SALDO_MONEDA) SUMA_TOTAL_SALDO_MONEDA
									,SUM(PPC.TOTAL_SALDO_PESOS) SUMA_TOTAL_SALDO_PESOS
							FROM PLAN_PAGO_CONVENIO PPC
							WHERE PPC.COD_CONVENIO = {KEY1}";
				}
				break;
				
				
				
				
					
				///////////////// DD_ESTADO_PAGO ///////////////
			case 'DD_ESTADO_PAGO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT		COD_ESTADO_CONVENIO
										,NOM_ESTADO_CONVENIO
							FROM		ESTADO_CONVENIO
							ORDER BY	COD_ESTADO_CONVENIO";
				}
				break;
					
				///////////////// INICIO_COBRO_MES ///////////////
			case 'INICIO_COBRO_MES' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT TO_CHAR(ADD_MONTHS(SYSDATE, 1),'DD/MM/YYYY') FECHA_INICIO_COBRO
							 FROM DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT CONVERT(VARCHAR(10),DATEADD(MONTH, 1, GETDATE()), 103) FECHA_INICIO_COBRO";
				}
				break;
			
			///////////////// DW_CU_CUOTAS ///////////////
			case 'DW_CU_CUOTAS' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	 NRO_CUOTA	CU_NRO_CUOTA           
									,TO_CHAR(FECHA_CUOTA, 'dd/mm/yyyy')	CU_FECHA_CUOTA
									,MONTO_CUOTA_MONEDA	CU_MONTO_CUOTA_MONEDA
									,null	CU_COD_ABONO
									,f_conv_cuota_ingreso(COD_CUOTA_CONVENIO) CUOTA_INGRESO
									,f_conv_saldo_cuota(COD_CUOTA_CONVENIO) SALDO_CUOTA_CONVENIO
							FROM	CUOTA_CONVENIO
							WHERE	COD_CONVENIO = {KEY1}";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	 NRO_CUOTA	CU_NRO_CUOTA           
									,CONVERT(VARCHAR, FECHA_CUOTA,103)	CU_FECHA_CUOTA
									,MONTO_CUOTA_MONEDA	CU_MONTO_CUOTA_MONEDA
									,null	CU_COD_ABONO
									,dbo.f_conv_cuota_ingreso(COD_CUOTA_CONVENIO) CUOTA_INGRESO
									,dbo.f_conv_saldo_cuota(COD_CUOTA_CONVENIO) SALDO_CUOTA_CONVENIO
							FROM	CUOTA_CONVENIO
							WHERE	COD_CONVENIO = {KEY1}";
				}
				break;				
				///////////////// OUTPUT_DD_COD_ESTADO_CONVENIO ///////////////
			case 'OUTPUT_DD_COD_ESTADO_CONVENIO' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT		COD_ESTADO_CONVENIO
										,NOM_ESTADO_CONVENIO
							FROM		ESTADO_CONVENIO
							ORDER BY	COD_ESTADO_CONVENIO";
				}
				break;
					
				///////////////// DD_TIPO_DEUDA ///////////////
			case 'DD_TIPO_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT		COD_DEUDA
										,COD_TIPO_DEUDA
							FROM		DEUDA
							where COD_DEUDOR = {KEY1}
							ORDER BY	COD_TIPO_DEUDA";
				}
				break;
					
				////////////////////	VALOR_MONEDA		///////////////////////////
			case 'VALOR_MONEDA' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT F_MON_GET_VALOR_MONEDA(3, SYSDATE) VALOR_MONEDA
						   FROM DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT DBO.F_MON_GET_VALOR_MONEDA(3, GETDATE()) VALOR_MONEDA";
				}
				break;
				
				/////////////////////////AJAX_CALCULO_CUOTA///////////////////////////
			case 'AJAX_CALCULO_CUOTA' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT F_CONV_MONTO_ACTUALIZADO({PARAM1}, {PARAM2}, {PARAM3}) MONTO_ACT
							,F_CONV_CALCULO_CUOTA({PARAM1}, {PARAM4}, {PARAM2}, {PARAM3}) MONTO_CUOTA
							FROM DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT DBO.F_CONV_MONTO_ACTUALIZADO({PARAM1}, {PARAM2}, {PARAM3}) MONTO_ACT
							,DBO.F_CONV_CALCULO_CUOTA({PARAM1}, {PARAM4}, {PARAM2}, {PARAM3}) MONTO_CUOTA";
				}
				break;
				
				///////////////////////////AJAX_CHANGE_RUT///////////////////////////
			case 'AJAX_CHANGE_RUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT D.COD_DEUDOR
								,D.NOMBRE || ' ' || D.APELLIDO_PATERNO || ' ' || D.APELLIDO_MATERNO  NOM_DEUDOR
								,D.DIG_VERIF
								,DA.COD_DEUDA
								,DA.COD_TIPO_DEUDA
							FROM 	DEUDOR D, DEUDA DA
							WHERE 	D.RUT = {PARAM1}
							  and DA.COD_DEUDOR = D.COD_DEUDOR
							  and DA.COD_ESTADO_DEUDA =".self::K_EST_DEUDA_COBRANZA_NORMAL;
				}
				else if(K_TIPO_BD=="mssql"){
					$sql = "SELECT D.COD_DEUDOR
									,D.NOMBRE + ' ' + D.APELLIDO_PATERNO + ' ' + D.APELLIDO_MATERNO  NOM_DEUDOR
									,D.DIG_VERIF
									,DA.COD_DEUDA
									,DA.COD_TIPO_DEUDA
								FROM 	DEUDOR D, DEUDA DA
								WHERE 	D.RUT = {PARAM1}
								  and DA.COD_DEUDOR = D.COD_DEUDOR
								  and DA.COD_ESTADO_DEUDA =".self::K_EST_DEUDA_COBRANZA_NORMAL;
				}
				break;
				
				/////////////////////////AJAX_CHANGE_TIPO_DEUDA///////////////////////////
				case 'AJAX_CHANGE_TIPO_DEUDA':
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT DA.COD_DEUDA
									,C.NOM_CARTERA
									,E.NOM_ESTADO_DEUDA
									,DA.CANT_CUOTA
									,M.SIMBOLO
									,'N' SELECCION
									,P.COD_PLAN_PAGO
									,P.ANO_COBRO
									,To_Char(P.FECHA_VENCTO,'DD/MM/YYYY') FECHA_VENCTO
									,F_PP_SALDO_CAPITAL(P.COD_PLAN_PAGO, SYSDATE) + F_PP_SALDO_INTERES_NORMAL(P.COD_PLAN_PAGO, SYSDATE) SALDO_CUOTA
									,F_PP_SALDO_MULTA(P.COD_PLAN_PAGO, SYSDATE) SALDO_MULTA
									,F_PP_SALDO_PLAN_PAGO(P.COD_PLAN_PAGO, SYSDATE) TOTAL_SALDO_MONEDA
									,F_PP_SALDO_PLAN_PAGO(P.COD_PLAN_PAGO, SYSDATE) * F_MON_GET_VALOR_MONEDA(T.COD_MONEDA, SYSDATE) TOTAL_SALDO_PESOS
							FROM DEUDA DA, CARTERA C, ESTADO_DEUDA E, TIPO_DEUDA T, MONEDA M, PLAN_PAGO P
							WHERE DA.COD_DEUDA = {PARAM1}
							  AND C.COD_CARTERA = DA.COD_CARTERA
							  AND E.COD_ESTADO_DEUDA = DA.COD_ESTADO_DEUDA
							  AND T.COD_TIPO_DEUDA = DA.COD_TIPO_DEUDA 
							  AND M.COD_MONEDA = T.COD_MONEDA
							  AND P.COD_DEUDA = DA.COD_DEUDA
							  AND P.FECHA_VENCTO < SYSDATE	-- SOLO VENCIDAS
							  AND P.COD_PLAN_PAGO NOT IN(SELECT COD_PLAN_PAGO FROM PLAN_PAGO_CONVENIO)
							  AND F_PP_SALDO_PLAN_PAGO(P.COD_PLAN_PAGO, SYSDATE) > 0";
				}
				else if(K_TIPO_BD=="mssql"){
					$sql = "SELECT DA.COD_DEUDA
									,C.NOM_CARTERA
									,E.NOM_ESTADO_DEUDA
									,DA.CANT_CUOTA
									,M.SIMBOLO
									,'N' SELECCION
									,P.COD_PLAN_PAGO
									,P.ANO_COBRO
									,CONVERT(VARCHAR, P.FECHA_VENCTO, 103) FECHA_VENCTO
									,DBO.F_PP_SALDO_CAPITAL(P.COD_PLAN_PAGO, GETDATE()) + DBO.F_PP_SALDO_INTERES_NORMAL(P.COD_PLAN_PAGO, GETDATE()) SALDO_CUOTA
									,DBO.F_PP_SALDO_MULTA(P.COD_PLAN_PAGO, GETDATE()) SALDO_MULTA
									,DBO.F_PP_SALDO_PLAN_PAGO(P.COD_PLAN_PAGO, GETDATE()) TOTAL_SALDO_MONEDA
									,DBO.F_PP_SALDO_PLAN_PAGO(P.COD_PLAN_PAGO, GETDATE()) * DBO.F_MON_GET_VALOR_MONEDA(T.COD_MONEDA, GETDATE()) TOTAL_SALDO_PESOS
							FROM DEUDA DA, CARTERA C, ESTADO_DEUDA E, TIPO_DEUDA T, MONEDA M, PLAN_PAGO P
							WHERE DA.COD_DEUDA = {PARAM1}
							  AND C.COD_CARTERA = DA.COD_CARTERA
							  AND E.COD_ESTADO_DEUDA = DA.COD_ESTADO_DEUDA
							  AND T.COD_TIPO_DEUDA = DA.COD_TIPO_DEUDA 
							  AND M.COD_MONEDA = T.COD_MONEDA
							  AND P.COD_DEUDA = DA.COD_DEUDA
							  AND P.FECHA_VENCTO < GETDATE()	-- SOLO VENCIDAS
							  AND P.COD_PLAN_PAGO NOT IN(SELECT COD_PLAN_PAGO FROM PLAN_PAGO_CONVENIO)
							  AND DBO.F_PP_SALDO_PLAN_PAGO(P.COD_PLAN_PAGO, GETDATE()) > 0";
				}
				break;
				
					///////////////////////////PRINT_CONVENIO///////////////////////////
			case 'PRINT_CONVENIO' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT COD_CONVENIO
									,C.NRO_CUENTA_CORRIENTE
									,C.NOM_CUENTA_CORRIENTE
									,TO_CHAR(CN.FECHA_CONVENIO, 'DD/MM/YYYY') FECHA_CONVENIO
								  	,TO_CHAR(CN.FECHA_CONVENIO, 'DD') DIA
									,TO_CHAR(CN.FECHA_CONVENIO, 'YYYY') ANIO
									,f_get_nom_mes(TO_CHAR(CN.FECHA_CONVENIO, 'MM')) MES
									,D.COD_DEUDOR
								    ,D.RUT
								    ,D.DIG_VERIF
									,D.NOMBRE || ' ' || D.APELLIDO_PATERNO || ' ' || D.APELLIDO_MATERNO  NOM_DEUDOR
						            ,decode(DI.COD_DEUDOR, 0, 'NO ENCONTRADO', LTRIM(RTRIM(DI.DIRECCION)) || ' de ' || LTRIM(RTRIM(CI.NOM_CIUDAD))) DIRECCION_DEUDOR
						            ,T.NOM_TIPO_DEUDA
									,CN.MONTO_CONVENIO_MONEDA MONTO_CONVENIO_MONEDA_H
									,TO_CHAR(CN.FECHA_INICIO_COBRO,'YYYY') ANO_INICIO_COBRO
									,CN.MONTO_CONVENIO_MONEDA
									,CN.CANT_CUOTA
									,CN.CUOTA_MENSUAL_MONEDA
							FROM 	CONVENIO CN,  DEUDA DA, TIPO_DEUDA T, MONEDA M, 
									DEUDOR D LEFT OUTER JOIN DIRECCION DI ON DI.COD_DEUDOR = D.COD_DEUDOR
									LEFT OUTER JOIN CIUDAD CI ON DI.COD_CIUDAD = CI.COD_CIUDAD,
									FAMILIA_DEUDA FD, CUENTA_CORRIENTE C
							WHERE 	CN.COD_CONVENIO = {PARAM1}
							AND		DA.COD_DEUDA = CN.COD_DEUDA
							AND		D.COD_DEUDOR = DA.COD_DEUDOR
			 				and		T.COD_TIPO_DEUDA = DA.COD_TIPO_DEUDA
							and		M.COD_MONEDA = T.COD_MONEDA
							and 	C.COD_FAMILIA_DEUDA = FD.COD_FAMILIA_DEUDA
							AND 	T.COD_FAMILIA_DEUDA = FD.COD_FAMILIA_DEUDA";
				}
				else if(K_TIPO_BD=="mssql"){
					$sql = "SELECT COD_CONVENIO
									,C.NRO_CUENTA_CORRIENTE
									,C.NOM_CUENTA_CORRIENTE
									,convert(varchar, CN.FECHA_CONVENIO, 103) FECHA_CONVENIO
									,day(CN.FECHA_CONVENIO) DIA
									,year(CN.FECHA_CONVENIO) ANIO
									,dbo.f_get_nom_mes(month(CN.FECHA_CONVENIO)) MES
									,D.COD_DEUDOR
							        ,D.RUT
							        ,D.DIG_VERIF
									,D.NOMBRE + ' ' + D.APELLIDO_PATERNO + ' ' + D.APELLIDO_MATERNO  NOM_DEUDOR
									,CASE ISNULL(DI.COD_DEUDOR,0) 
			                         WHEN       0
			                         THEN       'NO ENCONTRADO'
			                         ELSE       LTRIM(RTRIM(DI.DIRECCION)) + ' de ' + LTRIM(RTRIM(CI.NOM_CIUDAD))
			                         END DIRECCION_DEUDOR
									,T.NOM_TIPO_DEUDA
									,CN.MONTO_CONVENIO_MONEDA MONTO_CONVENIO_MONEDA_H
									,year(CN.FECHA_INICIO_COBRO) ANO_INICIO_COBRO
									,CN.MONTO_CONVENIO_MONEDA
									,CN.CANT_CUOTA
									,CN.CUOTA_MENSUAL_MONEDA
							FROM 	CONVENIO CN,  DEUDA DA, TIPO_DEUDA T, MONEDA M, 
									DEUDOR D LEFT OUTER JOIN DIRECCION DI ON DI.COD_DEUDOR = D.COD_DEUDOR
									LEFT OUTER JOIN CIUDAD CI ON DI.COD_CIUDAD = CI.COD_CIUDAD, 
									CUENTA_CORRIENTE C, FAMILIA_DEUDA FD
							WHERE 	CN.COD_CONVENIO = {PARAM1}
							AND		DA.COD_DEUDA = CN.COD_DEUDA
							AND		D.COD_DEUDOR = DA.COD_DEUDOR
			 				and		T.COD_TIPO_DEUDA = DA.COD_TIPO_DEUDA
							and		M.COD_MONEDA = T.COD_MONEDA
							and 	C.COD_FAMILIA_DEUDA = FD.COD_FAMILIA_DEUDA
							AND 	T.COD_FAMILIA_DEUDA = FD.COD_FAMILIA_DEUDA";
				}
				break;
				
				///////////////////////////PRINT_PARAMETROS///////////////////////////
			case 'PRINT_PARAMETROS' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	f_get_parametro(9) 	 DIRECCION
									,f_get_parametro(5) NOM_UNIVERSIDAD
									,f_get_parametro(10) TELEFONO
									,f_get_parametro(11) COMUNA
									,f_get_parametro(12) CIUDAD
									,f_get_parametro(15) FAX
									,f_get_parametro(19) ADMINISTRADOR
									,f_get_parametro(30) INTERES_CONVENIO
									,f_get_parametro(31) PAGO_SUGERIDO_CONVENIO
							FROM DUAL";
				}
				else if(K_TIPO_BD=="mssql"){
					$sql = "SELECT	dbo.f_get_parametro(9) 	 DIRECCION
									,dbo.f_get_parametro(5) NOM_UNIVERSIDAD
									,dbo.f_get_parametro(10) TELEFONO
									,dbo.f_get_parametro(11) COMUNA
									,dbo.f_get_parametro(12) CIUDAD
									,dbo.f_get_parametro(15) FAX
									,dbo.f_get_parametro(19) ADMINISTRADOR
									,dbo.f_get_parametro(30) INTERES_CONVENIO
									,dbo.f_get_parametro(31) PAGO_SUGERIDO_CONVENIO";
				}
				break;
				
				///////////////// ANO_COBRO ///////////////
			case 'ANO_COBRO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT PP.ANO_COBRO
							FROM PLAN_PAGO_CONVENIO PPC, PLAN_PAGO PP
							WHERE PPC.COD_CONVENIO ={PARAM1}
							  AND PP.COD_PLAN_PAGO = PPC.COD_PLAN_PAGO";
				}
				break;
				
				
				///////////////////////////ANO_CUOTAS///////////////////////////
			case 'ANO_CUOTAS' :
				if (K_TIPO_BD=="oci"){
					$sql = "select To_Char(f_get_ultimo_dia_habil(ADD_MONTHS(FECHA_INICIO_COBRO,{PARAM2})),'DD/MM/YYYY') FECHA
              				FROM CONVENIO 
							where COD_CONVENIO ={PARAM1}";
				}
				else if(K_TIPO_BD=="mssql"){
					$sql = "select convert(varchar(20),dbo.f_get_ultimo_dia_habil(dateadd(month,{PARAM2},FECHA_INICIO_COBRO)),103) FECHA
							FROM CONVENIO 
							where COD_CONVENIO ={PARAM1}";
				}
				break;
				
				///////////////////////////SQL_TIPO///////////////////////////
			case 'SQL_TIPO' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 1 FROM DUAL";
				}
				else if(K_TIPO_BD=="mssql"){
					$sql = "SELECT 1";
				}
				break;

				
				///////////////////////////DD_ESTADO_CONVENIO///////////////////////////
			case 'DD_ESTADO_CONVENIO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT COD_ESTADO_CONVENIO
									,NOM_ESTADO_CONVENIO
							FROM ESTADO_CONVENIO
							WHERE COD_ESTADO_CONVENIO <> 1 --emitido
							ORDER BY	COD_ESTADO_CONVENIO";

				}
				break;
				
					///////////////////////////CONVENIO_INGRESOS///////////////////////////
			case 'CONVENIO_INGRESOS' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT PC.COD_INGRESO
							 FROM CONVENIO CN LEFT OUTER JOIN PAGO_INICIAL_CONVENIO PC ON CN.COD_CONVENIO = PC.COD_CONVENIO
							 WHERE CN.COD_CONVENIO ={PARAM1}";

				}
				break;
				
						///////////////////////////AJAX_VALIDA_APROVADA///////////////////////////
			case 'AJAX_VALIDA_APROVADA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT A.AUTORIZA_MENU 
							FROM  USUARIO U, PERFIL P, ITEM_MENU I, AUTORIZA_MENU A
							WHERE U.COD_USUARIO = {PARAM1}
								AND	U.COD_PERFIL = P.COD_PERFIL
								AND A.COD_PERFIL = P.COD_PERFIL
								AND A.COD_ITEM_MENU = I.COD_ITEM_MENU
								AND I.COD_ITEM_MENU = {PARAM2}";

				}
				break;

				////////////////////////////// DD_AUTORIZADO ///////////////////////
			case 'DD_AUTORIZADO' :
			if (K_TIPO_BD=="oci" || K_TIPO_BD == "mssql"){
				$sql = "SELECT 'S' AUTORIZADO
								, 'SI' NOM_AUTORIZADO
						UNION 
						SELECT 'N' AUTORIZADO
								, 'NO' NOM_AUTORIZADO";	
			}
			break;
				
				
				/////////////////  ERROR ///////////////
			default:
				base::error("sql_convenio::get_sql, selector: '$selector_sql' no definido");
				
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>