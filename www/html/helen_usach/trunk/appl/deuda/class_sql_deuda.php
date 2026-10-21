<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_deuda_base extends sql_tabla {
	///////////////		DECLARACION DE CONSTANTES		/////////////////////
	const K_ESTADO_DEUDA_NO_EXIGIBLE= 1;
	const K_ESTADO_DEUDA_EXIGIBLE 	= 2;
	const K_ESTADO_DEUDA_CANCELADA 	= 4;
	
	const K_COMP_DEUDA_NO_EXIGIBLE	= 1;
	const K_COMP_DEUDA_VENCIDA	 	= 2;
	const K_COMP_DEUDA_VENCIDA_MORA	= 5;
	const K_COMP_DEUDA_VENCIDA_MULTA = 6;
	const K_COMP_DEUDA_VIGENTE	 	= 3;
	const K_COMP_DEUDA_FUTURA	 	= 4;
	
	const K_PARAM_RUT_U				= 4;
	const K_PARAM_NOM_U 			= 5;
	const K_PARAM_DIRECCCION 		= 9;
	const K_PARAM_FONO 				= 10;
	const K_PARAM_COMUNA 			= 11;
	const K_PARAM_CIUDAD 			= 12;
	const K_PARAM_E_MAIL 			= 13;
	const K_PARAM_SITIO_WEB 		= 14;
	const K_PARAM_FAX 				= 15;
	const K_PARAM_PAIS 				= 16;
	const K_ESTADO_PAGO_VIGENTE 	= 1;
	const K_COMPONENTE_NO_EXIGIBLE	= 1;
	const K_ESTADO_PP_MOROSO		= 4;
	const K_COD_CARTERA_UACH		= 7;
	const K_ESTADO_DOC_PROTESTADO	= 3;
	const K_ESTADO_PAGARE_PROTESTADO	= 2;
	
	
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			/////////////////// INPUT ////////////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	DA.COD_DEUDA
						  			, DO.RUT
						  			, DO.COD_DEUDOR
						  			, DO.DIG_VERIF
						  			, DO.NOMBRE
						  			, DO.APELLIDO_PATERNO
						  			, DO.APELLIDO_MATERNO
						  			, DA.COD_TIPO_DEUDA
						  			, TD.NOM_TIPO_DEUDA
						  			, DA.COD_ESTADO_DEUDA
						  			, DA.COD_ESTADO_DEUDA COD_ESTADO_DEUDA_H
						  			, ED.NOM_ESTADO_DEUDA
						  			, DA.COD_CARTERA
						  			, DA.COD_UNIVERSIDAD
						  			, TO_CHAR(DA.FECHA_INICIO_COBRO,'dd/mm/yyyy') FECHA_INICIO_COBRO  
						  			, DA.MONTO_INICIO_COBRO
						  			, M.SIMBOLO
						  			, DA.CANT_CUOTA
						  			, f_deuda_cat(DA.COD_DEUDA,To_Number(To_Char(SYSDATE,'yyyy'))) CAT
						  			, f_deuda_saldo_componente(DA.COD_DEUDA, 1) SALDO_NO_EXIGIBLE
						  			, f_deuda_saldo_componente(DA.COD_DEUDA, 1) * f_mon_get_valor_moneda(TD.COD_MONEDA) SALDO_NO_EXIGIBLE_PESOS				  
						  			, f_deuda_saldo_componente(DA.COD_DEUDA, 2) SALDO_VENCIDA
						  			, f_deuda_saldo_componente(DA.COD_DEUDA, 2) * f_mon_get_valor_moneda(TD.COD_MONEDA) SALDO_VENCIDA_PESOS				  
						  			, f_deuda_saldo_componente(DA.COD_DEUDA, 3) SALDO_VIGENTE
						  			, f_deuda_saldo_componente(DA.COD_DEUDA, 3) * f_mon_get_valor_moneda(TD.COD_MONEDA) SALDO_VIGENTE_PESOS				  
						  			, f_deuda_saldo_componente(DA.COD_DEUDA, 4) SALDO_FUTURO
						  			, f_deuda_saldo_componente(DA.COD_DEUDA, 4) * f_mon_get_valor_moneda(TD.COD_MONEDA) SALDO_FUTURO_PESOS
						  			, f_deuda_saldo_total(DA.COD_DEUDA) SALDO_TOTAL
						  			, f_deuda_saldo_total(DA.COD_DEUDA) * f_mon_get_valor_moneda(TD.COD_MONEDA) SALDO_TOTAL_PESOS
						  			, decode(DA.COD_ESTADO_DEUDA, 1, ''
						  										, 2, ''
						  										, 'none') DISPLAY_BOLETA
						  			, decode(DA.COD_ESTADO_DEUDA, 3, ''
						  										, 4, ''
						  										, 5, ''
						  										,21, ''
						  										, '') DISPLAY_DEVOLUCION
						  			, 'DEUDA INTERNALIZADA' TITULO_DEUDA_MOROSA
						  			, 'none' VISIBLE_CAMPO
						  			, '' TITULO_DEUDOR_CRUZADO
									, 'none' VISIBLE_DEUDOR_CRUZADO
						  			, 'N' SELECCIONA_DICOM
									, '' COD_EMPRESA_COBRANZA
									, 0 SUMA_MONTO_ACTUALIZADO
									, 0 SUMA_MONTO_PORC
									, 0 VALOR_DICOM_H
									, 0 VALOR_EM_CO_H
									, 'DEUDOR CON DOCUMENTOS PROTESTADOS' TITULO_DOC_PROTES
									, 'DEUDOR CON PAGARES PROTESTADOS' TITULO_PAGARE_PROTES
									, 'none' VISIBLE_CAMPO_PROTES
									, 'none' VISIBLE_PAGARE_PROTES						
						  	 FROM 	DEUDA DA, DEUDOR  DO, TIPO_DEUDA TD, ESTADO_DEUDA ED, MONEDA M
				             WHERE  DA.COD_DEUDOR=DO.COD_DEUDOR 
				     				AND DA.COD_DEUDA= {KEY1}
				     				AND TD.COD_TIPO_DEUDA = DA.COD_TIPO_DEUDA
				     				AND ED.COD_ESTADO_DEUDA = DA.COD_ESTADO_DEUDA
				     				AND M.COD_MONEDA = TD.COD_MONEDA";
					
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 	DA.COD_DEUDA
						  			, DO.RUT
						  			, DO.COD_DEUDOR
						  			, DO.DIG_VERIF
						  			, DO.NOMBRE
						  			, DO.APELLIDO_PATERNO
						  			, DO.APELLIDO_MATERNO
						  			, DA.COD_TIPO_DEUDA
						  			, TD.NOM_TIPO_DEUDA
						  			, DA.COD_ESTADO_DEUDA
						  			, DA.COD_ESTADO_DEUDA COD_ESTADO_DEUDA_H
						  			, ED.NOM_ESTADO_DEUDA
						  			, DA.COD_CARTERA
						  			, DA.COD_UNIVERSIDAD
						  			, convert(varchar(20),DA.FECHA_INICIO_COBRO,103) FECHA_INICIO_COBRO  
						  			, DA.MONTO_INICIO_COBRO
						  			, M.SIMBOLO
						  			, DA.CANT_CUOTA
						  			, dbo.f_deuda_CAT(DA.COD_DEUDA, year(getdate())) CAT
									, dbo.f_deuda_saldo_componente(DA.COD_DEUDA, 1, getdate()) SALDO_NO_EXIGIBLE
						  			, dbo.f_deuda_saldo_componente(DA.COD_DEUDA, 1, getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()) SALDO_NO_EXIGIBLE_PESOS				  
						  			, dbo.f_deuda_saldo_componente(DA.COD_DEUDA, 2, getdate()) SALDO_VENCIDA
						  			, dbo.f_deuda_saldo_componente(DA.COD_DEUDA, 2, getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()) SALDO_VENCIDA_PESOS				  
						  			, dbo.f_deuda_saldo_componente(DA.COD_DEUDA, 3, getdate()) SALDO_VIGENTE
						  			, dbo.f_deuda_saldo_componente(DA.COD_DEUDA, 3, getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()) SALDO_VIGENTE_PESOS				  
						  			, dbo.f_deuda_saldo_componente(DA.COD_DEUDA, 4, getdate()) SALDO_FUTURO
						  			, dbo.f_deuda_saldo_componente(DA.COD_DEUDA, 4, getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()) SALDO_FUTURO_PESOS
						  			, dbo.f_deuda_saldo_total(DA.COD_DEUDA, getdate()) SALDO_TOTAL
						  			, dbo.f_deuda_saldo_total(DA.COD_DEUDA, getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()) SALDO_TOTAL_PESOS
						  			, case DA.COD_ESTADO_DEUDA
										when 1 then ''
											else case DA.COD_ESTADO_DEUDA
												when 2 then ''
											else 'none'
										end
									end DISPLAY_BOLETA
									, case DA.COD_ESTADO_DEUDA
										when 3 then ''
										when 4 then ''
										when 5 then ''
										when 21 then ''
										else ''
									end DISPLAY_DEVOLUCION
									, '' TITULO_DEUDOR_CRUZADO
									, 'none' VISIBLE_DEUDOR_CRUZADO
									, 'DEUDA INTERNALIZADA' TITULO_DEUDA_MOROSA
									, 'none' VISIBLE_CAMPO
									, 'N' SELECCIONA_DICOM
									, '' COD_EMPRESA_COBRANZA
									, 0 SUMA_MONTO_ACTUALIZADO
									, 0 SUMA_MONTO_PORC
									, 0 VALOR_DICOM_H
									, 0 VALOR_EM_CO_H
									, 'DEUDOR CON DOCUMENTOS PROTESTADOS' TITULO_DOC_PROTES
									, 'DEUDOR CON PAGARES PROTESTADOS' TITULO_PAGARE_PROTES
									, 'none' VISIBLE_CAMPO_PROTES
									, 'none' VISIBLE_PAGARE_PROTES
									, '' DISPLAY_AGREGAR_PAGARE
									, (select top 1 dbo.f_pp_saldo_capital(COD_PLAN_PAGO, getdate())	
									   from PLAN_PAGO where cod_deuda= DA.cod_deuda and nro_cuota=99) SALDO_CUOTA_99_H
									,(select sum(MONTO_CAPITAL) + sum(MONTO_INTERES)
									  from PLAN_PAGO where cod_deuda= DA.cod_deuda and cod_concepto_cobro = 11 
									  and cod_plan_pago not in (select cod_plan_pago from abono where cod_estado_pago = 1
									  and COD_DEUDA = DA.cod_deuda and COD_PLAN_PAGO is not null)) SALDO_CUOTA_SDJ_H
									, 0 SUM_SALDO_CUOTA_99_H
							 FROM 	DEUDA DA, DEUDOR  DO, TIPO_DEUDA TD, ESTADO_DEUDA ED, MONEDA M
				             WHERE  DA.COD_DEUDOR=DO.COD_DEUDOR 
				     				AND DA.COD_DEUDA= {KEY1}
				     				AND TD.COD_TIPO_DEUDA = DA.COD_TIPO_DEUDA
				     				AND ED.COD_ESTADO_DEUDA = DA.COD_ESTADO_DEUDA
				     				AND M.COD_MONEDA = TD.COD_MONEDA";
				}
				break;
				
			////////////////// OUTPUT ///////////////////	
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	DA.COD_DEUDA
									/*INI_CAMPOS*/
						  			,DO.RUT
						  			,DO.DIG_VERIF
						  			,DO.NOMBRE 
						  			,DO.APELLIDO_PATERNO	
						  			,DO.APELLIDO_MATERNO 
						  			,TD.COD_TIPO_DEUDA
						  			,TD.NOM_TIPO_DEUDA
						  			,to_char(DA.FECHA_INICIO_COBRO, 'dd/mm/yyyy') FECHA_INICIO_COBRO
									,DA.FECHA_INICIO_COBRO DATE_INICIO_COBRO
						  			,ED.NOM_ESTADO_DEUDA
						  			,U.NOM_CORTO_UNIVERSIDAD
						  			,CA.NOM_CORTO_CARTERA
						  	FROM	DEUDA DA
				 					, DEUDOR  DO
				 					, TIPO_DEUDA TD
				 					, ESTADO_DEUDA ED
				 					, UNIVERSIDAD U
				 					,CARTERA CA                 
				 			WHERE   DA.COD_DEUDOR=DO.COD_DEUDOR
				 					AND DA.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
				 					AND ED.COD_ESTADO_DEUDA = DA.COD_ESTADO_DEUDA
				 					AND DA.COD_UNIVERSIDAD = U.COD_UNIVERSIDAD
				 					AND DA.COD_CARTERA = CA.COD_CARTERA
				 			ORDER BY 	DA.COD_DEUDA DESC";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	DA.COD_DEUDA
									/*INI_CAMPOS*/ 
						  			,DO.RUT
						  			,DO.DIG_VERIF
						  			,DO.NOMBRE 
						  			,DO.APELLIDO_PATERNO	
						  			,DO.APELLIDO_MATERNO 
						  			,TD.COD_TIPO_DEUDA
						  			,TD.NOM_TIPO_DEUDA
						  			,convert(varchar, DA.FECHA_INICIO_COBRO, 103) FECHA_INICIO_COBRO
									,DA.FECHA_INICIO_COBRO DATE_INICIO_COBRO
						  			,ED.NOM_ESTADO_DEUDA
						  			,U.NOM_CORTO_UNIVERSIDAD
						  			,CA.NOM_CORTO_CARTERA
						  			/*FIN_CAMPOS*/
				 			FROM	DEUDA DA
				 					, DEUDOR  DO
				 					, TIPO_DEUDA TD
				 					, ESTADO_DEUDA ED
				 					, UNIVERSIDAD U
				 					,CARTERA CA                 
				 			WHERE   DA.COD_DEUDOR=DO.COD_DEUDOR
				 					AND DA.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
				 					AND ED.COD_ESTADO_DEUDA = DA.COD_ESTADO_DEUDA
				 					AND DA.COD_UNIVERSIDAD = U.COD_UNIVERSIDAD
				 					AND DA.COD_CARTERA = CA.COD_CARTERA
				 					/*FIN_CONDICION*/
				 					ORDER BY 	DA.COD_DEUDA DESC";
				}
				break;

			//////////////////// DD_COD_CARTERA ///////////////////////	
			case 'DD_COD_CARTERA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_CARTERA
									, NOM_CARTERA 
							FROM 	CARTERA 
							ORDER BY NOM_CARTERA";
				}
				break;

			/////////////////////// PRINT_RECORD ///////////////////////////	
			case 'PRINT_RECORD' :
				if (K_TIPO_BD=="oci"){
					
					$sql = "SELECT  DEUDA.COD_DEUDA
							      , TO_CHAR(DEUDA.FECHA_INICIO_COBRO, 'dd/mm/yyyy') FECHA_INICIO_COBRO
							      , DEUDOR.RUT
							      , DEUDOR.DIG_VERIF
							      , DEUDOR.NOMBRE||' '||DEUDOR.APELLIDO_PATERNO||' '||DEUDOR.APELLIDO_MATERNO NOMBRE
							      , TD.COD_TIPO_DEUDA
							      , ED.NOM_ESTADO_DEUDA
							      , P.FOLIO_PAGARE
							      , P.MONTO_PESOS - NVL(MONTO_PESOS_REBAJA, 0) MONTO_PESOS
						          , P.MONTO_MONEDA - NVL(MONTO_MONEDA_REBAJA, 0) MONTO_MONEDA
						          , f_pagare_actualizado(COD_PAGARE, sysdate) MONTO_MONEDA_ACT
						          , TO_CHAR(P.FECHA_PAGARE, 'dd/mm/yyyy') FECHA_PAGARE
						          , EP.NOM_ESTADO_PAGARE
				                  , M.SIMBOLO
		                		  , Nvl(f_mon_get_valor_moneda(TD.COD_MONEDA),0)VALOR_MONEDA
		               			  , f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_NO_EXIGIBLE.")NO_EXIGIBLE
		               			  , f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VENCIDA.")VENCIDA
					              , f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VIGENTE.")VIGENTE
					              , f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_FUTURA.")FUTURA
					              , nvl(f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_NO_EXIGIBLE.") * f_mon_get_valor_moneda(TD.COD_MONEDA),0)NO_EXIGIBLE_PESOS
					              , Nvl(f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VENCIDA.") * f_mon_get_valor_moneda(TD.COD_MONEDA),0) VENCIDA_PESOS
								  , Nvl(f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VIGENTE.") * f_mon_get_valor_moneda(TD.COD_MONEDA),0) VIGENTE_PESOS
					              , Nvl(f_deuda_saldo_componente(DEUDA.COD_DEUDA, ".self::K_COMP_DEUDA_FUTURA.") * f_mon_get_valor_moneda(TD.COD_MONEDA),0) FUTURA_PESOS	
					              , f_deuda_saldo_total(DEUDA.COD_DEUDA)SALDO_TOTAL
					              , nvl(f_deuda_saldo_total(DEUDA.COD_DEUDA)* f_mon_get_valor_moneda(TD.COD_MONEDA),0)SALDO_TOTAL_PESOS
						          ,to_char (SYSDATE, 'DD/MM/YYYY') FECHA_IMPRESO
		    					  ,to_char (SYSDATE , 'HH24:MI:SS') HORA_IMPRESO 
							FROM    DEUDA DEUDA, DEUDOR DEUDOR, ESTADO_DEUDA ED, TIPO_DEUDA TD, PAGARE P,
		                			ESTADO_PAGARE EP, MONEDA M
							WHERE   DEUDA.COD_DEUDA = {PARAM1} AND
								    DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR AND
						          	DEUDA.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA AND
						          	TD.COD_TIPO_DEUDA = DEUDA.COD_TIPO_DEUDA AND
		                			DEUDA.COD_DEUDA = P.COD_DEUDA (+) AND
		                			P.COD_ESTADO_PAGARE = EP.COD_ESTADO_PAGARE (+) AND
		                  			TD.COD_MONEDA = M.COD_MONEDA
		                  	ORDER BY P.FECHA_PAGARE";
				}
				else if (K_TIPO_BD=="mssql"){
					if(K_CLIENTE == "UTEM"){
						$sql = "SELECT  DEUDA.COD_DEUDA
							      , convert(varchar(20),DEUDA.FECHA_INICIO_COBRO, 103) FECHA_INICIO_COBRO
							      , DEUDOR.RUT
							      , DEUDOR.DIG_VERIF
							      , DEUDOR.NOMBRE + ' ' + DEUDOR.APELLIDO_PATERNO + ' ' + DEUDOR.APELLIDO_MATERNO NOMBRE
							      , TD.COD_TIPO_DEUDA
							      , ED.NOM_ESTADO_DEUDA
							      , P.FOLIO_PAGARE
							      , P.MONTO_PESOS
						          , P.MONTO_MONEDA - isnull(MONTO_MONEDA_REBAJA, 0) MONTO_MONEDA
						          , dbo.f_pagare_actualizado(COD_PAGARE, dateadd(day,-1,DEUDA.FECHA_INICIO_COBRO)) MONTO_MONEDA_ACT
						          , convert(varchar(20),P.FECHA_PAGARE, 103) FECHA_PAGARE
						          , EP.NOM_ESTADO_PAGARE
				                  , M.SIMBOLO
		                		  , isnull(dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()),0)VALOR_MONEDA
		               			  , dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_NO_EXIGIBLE.", getdate())NO_EXIGIBLE
		               			  , dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VENCIDA.", getdate())VENCIDA
					              , dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VIGENTE.", getdate())VIGENTE
					              , dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_FUTURA.", getdate())FUTURA
					              , isnull(dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_NO_EXIGIBLE.", getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()),0)NO_EXIGIBLE_PESOS
					              , isnull(dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VENCIDA.", getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()),0) VENCIDA_PESOS
								  , isnull(dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VIGENTE.", getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()),0) VIGENTE_PESOS
					              , isnull(dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA, ".self::K_COMP_DEUDA_FUTURA.", getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()),0) FUTURA_PESOS	
					              , dbo.f_deuda_saldo_total(DEUDA.COD_DEUDA, getdate())SALDO_TOTAL
					              , isnull(dbo.f_deuda_saldo_total(DEUDA.COD_DEUDA, getdate())* dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()),0)SALDO_TOTAL_PESOS
						          , convert(varchar,getdate(), 103) FECHA_IMPRESO
		    					  , convert(varchar,getdate(), 8) HORA_IMPRESO 
							FROM    DEUDA DEUDA left outer join (PAGARE P left outer join ESTADO_PAGARE EP on P.COD_ESTADO_PAGARE = EP.COD_ESTADO_PAGARE)on DEUDA.COD_DEUDA = P.COD_DEUDA
									,DEUDOR DEUDOR, ESTADO_DEUDA ED, TIPO_DEUDA TD, MONEDA M
							WHERE   DEUDA.COD_DEUDA = {PARAM1} AND
								    DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR AND
						          	DEUDA.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA AND
						          	TD.COD_TIPO_DEUDA = DEUDA.COD_TIPO_DEUDA AND
		                			TD.COD_MONEDA = M.COD_MONEDA
		                	ORDER BY P.FECHA_PAGARE";
					}else{
						$sql = "SELECT  DEUDA.COD_DEUDA
							      , convert(varchar(20),DEUDA.FECHA_INICIO_COBRO, 103) FECHA_INICIO_COBRO
							      , DEUDOR.RUT
							      , DEUDOR.DIG_VERIF
							      , DEUDOR.NOMBRE + ' ' + DEUDOR.APELLIDO_PATERNO + ' ' + DEUDOR.APELLIDO_MATERNO NOMBRE
							      , TD.COD_TIPO_DEUDA
							      , ED.NOM_ESTADO_DEUDA
							      , P.FOLIO_PAGARE
							      , P.MONTO_PESOS
						          , P.MONTO_MONEDA - isnull(MONTO_MONEDA_REBAJA, 0) MONTO_MONEDA
						          , dbo.f_pagare_actualizado(COD_PAGARE, getdate()) MONTO_MONEDA_ACT
						          , convert(varchar(20),P.FECHA_PAGARE, 103) FECHA_PAGARE
						          , EP.NOM_ESTADO_PAGARE
				                  , M.SIMBOLO
		                		  , isnull(dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()),0)VALOR_MONEDA
		               			  , dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_NO_EXIGIBLE.", getdate())NO_EXIGIBLE
		               			  , dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VENCIDA.", getdate())VENCIDA
					              , dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VIGENTE.", getdate())VIGENTE
					              , dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_FUTURA.", getdate())FUTURA
					              , isnull(dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_NO_EXIGIBLE.", getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()),0)NO_EXIGIBLE_PESOS
					              , isnull(dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VENCIDA.", getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()),0) VENCIDA_PESOS
								  , isnull(dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VIGENTE.", getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()),0) VIGENTE_PESOS
					              , isnull(dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA, ".self::K_COMP_DEUDA_FUTURA.", getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()),0) FUTURA_PESOS	
					              , dbo.f_deuda_saldo_total(DEUDA.COD_DEUDA, getdate())SALDO_TOTAL
					              , isnull(dbo.f_deuda_saldo_total(DEUDA.COD_DEUDA, getdate())* dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()),0)SALDO_TOTAL_PESOS
						          , convert(varchar,getdate(), 103) FECHA_IMPRESO
		    					  , convert(varchar,getdate(), 8) HORA_IMPRESO 
										
							FROM    DEUDA DEUDA left outer join (PAGARE P left outer join ESTADO_PAGARE EP on P.COD_ESTADO_PAGARE = EP.COD_ESTADO_PAGARE)on DEUDA.COD_DEUDA = P.COD_DEUDA
									,DEUDOR DEUDOR, ESTADO_DEUDA ED, TIPO_DEUDA TD, MONEDA M
						       
							WHERE   DEUDA.COD_DEUDA = {PARAM1} AND
								    DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR AND
						          	DEUDA.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA AND
						          	TD.COD_TIPO_DEUDA = DEUDA.COD_TIPO_DEUDA AND
		                			TD.COD_MONEDA = M.COD_MONEDA
		                	ORDER BY P.FECHA_PAGARE";
					}
				}
				break;

			////////////////////// PRINT_RECORD_1 ////////////////////////	
			case 'PRINT_RECORD_1' :
				if (K_TIPO_BD=="oci"){
					
					$sql = "SELECT  DEUDA.COD_DEUDA
							        , TO_CHAR(DEUDA.FECHA_INICIO_COBRO, 'dd/mm/yyyy') FECHA_INICIO_COBRO
							        , DEUDOR.RUT
							        , DEUDOR.DIG_VERIF
							        , DEUDOR.NOMBRE||' '||DEUDOR.APELLIDO_PATERNO||' '||DEUDOR.APELLIDO_MATERNO NOMBRE
							        , TD.COD_TIPO_DEUDA
							        , ED.NOM_ESTADO_DEUDA
							        , PP.ANO_COBRO
							        , PP.NRO_CUOTA
							        , TO_CHAR(PP.FECHA_VENCTO, 'dd/mm/yyyy') FECHA_VENCTO
							        , PP.MONTO_CAPITAL
                     				, PP.MONTO_INTERES
                      				, f_pp_monto_abono(PP.COD_PLAN_PAGO, 'MONEDA') ABONO
                      				, f_pp_monto_dscto(PP.COD_PLAN_PAGO,'MONEDA') DESCUENTOS
                      				, NVL(f_pp_saldo_capital(cod_plan_pago) + f_pp_saldo_interes_normal(cod_plan_pago), 0) SALDO_K
									, f_pp_saldo_multa(PP.COD_PLAN_PAGO) MULTA
									, Nvl(f_pp_saldo_mora(PP.COD_PLAN_PAGO) + f_pp_saldo_multa(PP.COD_PLAN_PAGO),0) SALDO_VENCIDO
									, f_pp_saldo_plan_pago(PP.COD_PLAN_PAGO) * f_mon_get_valor_moneda(TD.COD_MONEDA) SALDO_PESOS
									, CC.NOM_CONCEPTO_COBRO
									, EPP.NOM_ESTADO_PLAN_PAGO
									, to_char (SYSDATE, 'DD/MM/YYYY') FECHA_IMPRESO
					        		, to_char (SYSDATE , 'HH24:MI:SS') HORA_IMPRESO
			        			    , M.SIMBOLO
			            			, Nvl(f_mon_get_valor_moneda(TD.COD_MONEDA),0)MONTO_MONEDA
						            , f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VENCIDA.")VENCIDA
						            , f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VIGENTE.")VIGENTE
						            , f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_FUTURA.")FUTURA
						            , Nvl(f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VENCIDA.") * f_mon_get_valor_moneda(TD.COD_MONEDA),0) VENCIDA_PESOS
									, Nvl(f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VIGENTE.") * f_mon_get_valor_moneda(TD.COD_MONEDA),0) VIGENTE_PESOS
						            , Nvl(f_deuda_saldo_componente(DEUDA.COD_DEUDA, ".self::K_COMP_DEUDA_FUTURA.") * f_mon_get_valor_moneda(TD.COD_MONEDA),0) FUTURA_PESOS	
						            , f_deuda_saldo_total(DEUDA.COD_DEUDA)SALDO_TOTAL
						            , nvl(f_deuda_saldo_total(DEUDA.COD_DEUDA)* f_mon_get_valor_moneda(TD.COD_MONEDA),0)SALDO_TOTAL_PESOS
						            	
							FROM    DEUDA DEUDA, DEUDOR DEUDOR, ESTADO_DEUDA ED, PLAN_PAGO PP, TIPO_DEUDA TD,
							        CONCEPTO_COBRO CC, ESTADO_PLAN_PAGO EPP, MONEDA M
							WHERE   DEUDA.COD_DEUDA = {PARAM1} AND
									DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR AND
							        DEUDA.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA AND
							        DEUDA.COD_DEUDA = PP.COD_DEUDA (+) AND
							        TD.COD_TIPO_DEUDA = DEUDA.COD_TIPO_DEUDA AND
							        PP.COD_CONCEPTO_COBRO = CC.COD_CONCEPTO_COBRO (+) AND
							        PP.COD_ESTADO_PLAN_PAGO = EPP.COD_ESTADO_PLAN_PAGO AND
			                		TD.COD_MONEDA = M.COD_MONEDA
							        ORDER BY NRO_CUOTA";
				}
				else if (K_TIPO_BD=="mssql"){
					if(K_CLIENTE == 'UTEM'){
						$sql = "SELECT  DEUDA.COD_DEUDA
							        , convert(varchar(20),DEUDA.FECHA_INICIO_COBRO, 103) FECHA_INICIO_COBRO
							        , DEUDOR.RUT
							        , DEUDOR.DIG_VERIF
							        , DEUDOR.NOMBRE + ' ' + DEUDOR.APELLIDO_PATERNO + ' ' + DEUDOR.APELLIDO_MATERNO NOMBRE
							        , TD.COD_TIPO_DEUDA
							        , ED.NOM_ESTADO_DEUDA
							        , PP.ANO_COBRO
							        , PP.NRO_CUOTA
							        , convert(varchar(20), PP.FECHA_VENCTO, 103) FECHA_VENCTO
							        , PP.MONTO_CAPITAL
									, PP.MONTO_INTERES
									, PP.MONTO_CAPITAL + PP.MONTO_INTERES A_PAGAR
									, dbo.f_pp_monto_abono(PP.COD_PLAN_PAGO, 'MONEDA', getdate()) ABONO
									, dbo.f_pp_monto_abono(PP.COD_PLAN_PAGO, 'MULTA', getdate()) ABONO_MULTA
									, isnull(dbo.f_pp_monto_abono(PP.COD_PLAN_PAGO, 'CAPITAL', getdate())+ dbo.f_pp_monto_abono(PP.COD_PLAN_PAGO, 'INTERES', getdate()),0) ABONO_CAPITAL
									, dbo.f_pp_monto_dscto(PP.COD_PLAN_PAGO,'MONEDA',getdate()) DESCUENTOS
									, isnull(dbo.f_pp_saldo_capital(cod_plan_pago, getdate()) + dbo.f_pp_saldo_interes_normal(cod_plan_pago, getdate()),0) SALDO_K
									, dbo.f_pp_saldo_multa(PP.COD_PLAN_PAGO, getdate()) MULTA
									, isnull(dbo.f_pp_saldo_mora(PP.COD_PLAN_PAGO, getdate()) + dbo.f_pp_saldo_multa(PP.COD_PLAN_PAGO, getdate()), 0) SALDO_VENCIDO
									, isnull(dbo.f_pp_saldo_capital(cod_plan_pago, getdate()) + dbo.f_pp_saldo_interes_normal(cod_plan_pago, getdate()) + dbo.f_pp_saldo_multa(PP.COD_PLAN_PAGO, getdate()), 0) SALDO
									, dbo.f_pp_saldo_plan_pago(PP.COD_PLAN_PAGO, getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()) SALDO_PESOS
									, CC.NOM_CONCEPTO_COBRO
									, dbo.f_estado_plan_pago_reneg(PP.COD_PLAN_PAGO) NOM_ESTADO_PLAN_PAGO
									, convert(varchar,getdate(), 103) FECHA_IMPRESO
									, convert(varchar,getdate(), 8) HORA_IMPRESO 
								    , M.SIMBOLO
									, isnull(dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()),0)MONTO_MONEDA
									, dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_NO_EXIGIBLE.", getdate())NO_EXIGIBLE
									, dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VENCIDA.", getdate())VENCIDA
									, dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VENCIDA_MORA.", getdate())VENCIDA_MORA
									, dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VENCIDA_MULTA.", getdate())VENCIDA_MULTA
							        , dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VIGENTE.", getdate())VIGENTE
							        , dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_FUTURA.", getdate())FUTURA
							        , isnull(dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_NO_EXIGIBLE.", getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()),0)NO_EXIGIBLE_PESOS
							        , isnull(dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VENCIDA.", getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()),0) VENCIDA_PESOS
							        , isnull(dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VENCIDA_MORA.", getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()),0) VENCIDA_MORA_PESOS
							        , isnull(dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VENCIDA_MULTA.", getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()),0) VENCIDA_MULTA_PESOS
									, isnull(dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VIGENTE.", getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()),0) VIGENTE_PESOS
							        , isnull(dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA, ".self::K_COMP_DEUDA_FUTURA.", getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()),0) FUTURA_PESOS	
									, dbo.f_deuda_saldo_total(DEUDA.COD_DEUDA, getdate())SALDO_TOTAL
							        , isnull(dbo.f_deuda_saldo_total(DEUDA.COD_DEUDA, getdate())* dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()),0)SALDO_TOTAL_PESOS
							        
							FROM    DEUDA DEUDA left outer join PLAN_PAGO PP on DEUDA.COD_DEUDA = PP.COD_DEUDA 
										left outer join CONCEPTO_COBRO CC on PP.COD_CONCEPTO_COBRO = CC.COD_CONCEPTO_COBRO 
											left outer join ESTADO_PLAN_PAGO EPP on PP.COD_ESTADO_PLAN_PAGO = EPP.COD_ESTADO_PLAN_PAGO 
									,DEUDOR DEUDOR,TIPO_DEUDA TD, ESTADO_DEUDA ED, MONEDA M
							        
							WHERE   DEUDA.COD_DEUDA = {PARAM1} AND
									DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR AND
							        TD.COD_TIPO_DEUDA = DEUDA.COD_TIPO_DEUDA AND
									DEUDA.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA AND
									TD.COD_MONEDA = M.COD_MONEDA 
							        ORDER BY ANO_COBRO";
					}
					else{
						$sql = "SELECT  DEUDA.COD_DEUDA
							        , convert(varchar(20),DEUDA.FECHA_INICIO_COBRO, 103) FECHA_INICIO_COBRO
							        , DEUDOR.RUT
							        , DEUDOR.DIG_VERIF
							        , DEUDOR.NOMBRE + ' ' + DEUDOR.APELLIDO_PATERNO + ' ' + DEUDOR.APELLIDO_MATERNO NOMBRE
							        , TD.COD_TIPO_DEUDA
							        , ED.NOM_ESTADO_DEUDA
							        , PP.ANO_COBRO
							        , PP.NRO_CUOTA
							        , convert(varchar(20), PP.FECHA_VENCTO, 103) FECHA_VENCTO
							        , PP.MONTO_CAPITAL
									, PP.MONTO_INTERES
									, dbo.f_pp_monto_abono(PP.COD_PLAN_PAGO, 'MONEDA', getdate()) ABONO
									, dbo.f_pp_monto_dscto(PP.COD_PLAN_PAGO,'MONEDA',getdate()) DESCUENTOS
									, isnull(dbo.f_pp_saldo_capital(cod_plan_pago, getdate()) + dbo.f_pp_saldo_interes_normal(cod_plan_pago, getdate()),0) SALDO_K
									, dbo.f_pp_saldo_multa(PP.COD_PLAN_PAGO, getdate()) MULTA
									, isnull(dbo.f_pp_saldo_mora(PP.COD_PLAN_PAGO, getdate()) + dbo.f_pp_saldo_multa(PP.COD_PLAN_PAGO, getdate()), 0) SALDO_VENCIDO
									, dbo.f_pp_saldo_plan_pago(PP.COD_PLAN_PAGO, getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()) SALDO_PESOS
									, CC.NOM_CONCEPTO_COBRO
									, EPP.NOM_ESTADO_PLAN_PAGO
									, convert(varchar,getdate(), 103) FECHA_IMPRESO
									, convert(varchar,getdate(), 8) HORA_IMPRESO 
								    , M.SIMBOLO
									, isnull(dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()),0)MONTO_MONEDA
									, dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VENCIDA.", getdate())VENCIDA
							        , dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VIGENTE.", getdate())VIGENTE
							        , dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_FUTURA.", getdate())FUTURA
							        , isnull(dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VENCIDA.", getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()),0) VENCIDA_PESOS
									, isnull(dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_VIGENTE.", getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()),0) VIGENTE_PESOS
							        , isnull(dbo.f_deuda_saldo_componente(DEUDA.COD_DEUDA, ".self::K_COMP_DEUDA_FUTURA.", getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()),0) FUTURA_PESOS		
							        , dbo.f_deuda_saldo_total(DEUDA.COD_DEUDA, getdate())SALDO_TOTAL
							        , isnull(dbo.f_deuda_saldo_total(DEUDA.COD_DEUDA, getdate())* dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()),0)SALDO_TOTAL_PESOS
							        
							FROM    DEUDA DEUDA left outer join PLAN_PAGO PP on DEUDA.COD_DEUDA = PP.COD_DEUDA 
										left outer join CONCEPTO_COBRO CC on PP.COD_CONCEPTO_COBRO = CC.COD_CONCEPTO_COBRO 
											left outer join ESTADO_PLAN_PAGO EPP on PP.COD_ESTADO_PLAN_PAGO = EPP.COD_ESTADO_PLAN_PAGO 
									,DEUDOR DEUDOR,TIPO_DEUDA TD, ESTADO_DEUDA ED, MONEDA M
							        
							WHERE   DEUDA.COD_DEUDA = {PARAM1} AND
									DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR AND
							        TD.COD_TIPO_DEUDA = DEUDA.COD_TIPO_DEUDA AND
									DEUDA.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA AND
									TD.COD_MONEDA = M.COD_MONEDA 
							        ORDER BY ANO_COBRO";
					}
					
				}
				break;
				
			////////////////////// PRINT_RECORD_2 /////////////////////	
			case 'PRINT_RECORD_2' :
				if (K_TIPO_BD=="oci"){
					
					$sql = "SELECT  DEUDA.COD_DEUDA
								    , TO_CHAR(DEUDA.FECHA_INICIO_COBRO, 'dd/mm/yyyy') FECHA_INICIO_COBRO
								    , DEUDOR.RUT
								    , DEUDOR.DIG_VERIF
								    , DEUDOR.NOMBRE||' '||DEUDOR.APELLIDO_PATERNO||' '||DEUDOR.APELLIDO_MATERNO NOMBRE
								    , TD.COD_TIPO_DEUDA
								    , ED.NOM_ESTADO_DEUDA
								  	, to_char (SYSDATE, 'DD/MM/YYYY') FECHA_IMPRESO
						  		    , to_char (SYSDATE, 'HH24:MI:SS') HORA_IMPRESO
							FROM    DEUDA DEUDA, DEUDOR DEUDOR, ESTADO_DEUDA ED, TIPO_DEUDA TD
		
							WHERE   DEUDA.COD_DEUDA = {PARAM1} AND
									DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR AND
			        				DEUDA.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA AND
			        				TD.COD_TIPO_DEUDA = DEUDA.COD_TIPO_DEUDA";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT  DEUDA.COD_DEUDA
								    , convert(varchar(20),DEUDA.FECHA_INICIO_COBRO, 103) FECHA_INICIO_COBRO
								    , DEUDOR.RUT
								    , DEUDOR.DIG_VERIF
								    , DEUDOR.NOMBRE + ' ' + DEUDOR.APELLIDO_PATERNO + ' ' + DEUDOR.APELLIDO_MATERNO NOMBRE
								    , TD.COD_TIPO_DEUDA
								    , ED.NOM_ESTADO_DEUDA
								  	, convert(varchar,getdate(), 103) FECHA_IMPRESO
						  		    , convert(varchar,getdate(), 8) HORA_IMPRESO 
		    	
							FROM    DEUDA DEUDA, DEUDOR DEUDOR, ESTADO_DEUDA ED, TIPO_DEUDA TD
		
							WHERE   DEUDA.COD_DEUDA = {PARAM1} AND
									DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR AND
			        				DEUDA.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA AND
			        				TD.COD_TIPO_DEUDA = DEUDA.COD_TIPO_DEUDA";
				}
				break;
			
				////////////////////// PRINT_RECORD_2 /////////////////////	
			case 'PRINT_RECORD_3' :
				if (K_TIPO_BD=="oci"){
					
					$sql = "SELECT  DEUDA.COD_DEUDA
								    , TO_CHAR(DEUDA.FECHA_INICIO_COBRO, 'dd/mm/yyyy') FECHA_INICIO_COBRO
								    , DEUDOR.RUT
								    , DEUDOR.DIG_VERIF
								    , DEUDOR.NOMBRE||' '||DEUDOR.APELLIDO_PATERNO||' '||DEUDOR.APELLIDO_MATERNO NOMBRE
								    , TD.COD_TIPO_DEUDA
								    , ED.NOM_ESTADO_DEUDA
								  	, to_char (SYSDATE, 'DD/MM/YYYY') FECHA_IMPRESO
						  		    , to_char (SYSDATE, 'HH24:MI:SS') HORA_IMPRESO
		    						, f_deuda_saldo_componente(DEUDA.COD_DEUDA,".self::K_COMP_DEUDA_NO_EXIGIBLE.") NO_EXIGIBLE
							FROM    DEUDA DEUDA, DEUDOR DEUDOR, ESTADO_DEUDA ED, TIPO_DEUDA TD
		
							WHERE   DEUDA.COD_DEUDA = {PARAM1} AND
									DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR AND
			        				DEUDA.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA AND
			        				TD.COD_TIPO_DEUDA = DEUDA.COD_TIPO_DEUDA";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT  DEUDA.COD_DEUDA
								    , convert(varchar(20),DEUDA.FECHA_INICIO_COBRO, 103) FECHA_INICIO_COBRO
								    , DEUDOR.RUT
								    , DEUDOR.DIG_VERIF
								    , DEUDOR.NOMBRE + ' ' + DEUDOR.APELLIDO_PATERNO + ' ' + DEUDOR.APELLIDO_MATERNO NOMBRE
								    , TD.COD_TIPO_DEUDA
								    , ED.NOM_ESTADO_DEUDA
								  	, convert(varchar,getdate(), 103) FECHA_IMPRESO
						  		    , convert(varchar,getdate(), 8) HORA_IMPRESO 
		    	
							FROM    DEUDA DEUDA, DEUDOR DEUDOR, ESTADO_DEUDA ED, TIPO_DEUDA TD
		
							WHERE   DEUDA.COD_DEUDA = {PARAM1} AND
									DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR AND
			        				DEUDA.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA AND
			        				TD.COD_TIPO_DEUDA = DEUDA.COD_TIPO_DEUDA";
				}
				break;
				
			/////////////////////// DW_DEUDA_PLAN_PAGO ///////////////////////////	
			case 'DW_DEUDA_PLAN_PAGO' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	TO_CHAR (ANO_COBRO) || '|' || TO_CHAR (COD_PLAN_PAGO) ANO_COBRO
									, NRO_CUOTA 
									, TO_CHAR(FECHA_VENCTO,'dd/mm/yyyy') FECHA_VENCTO 
									, PP.MONTO_CAPITAL
					                , PP.MONTO_INTERES
					                , (PP.MONTO_CAPITAL + PP.MONTO_INTERES) CUOTA_ANUAL 
					                , f_pp_monto_abono(COD_PLAN_PAGO, 'MONEDA') ABONO
									, f_pp_monto_dscto(COD_PLAN_PAGO,'MONEDA') DESCUENTOS
                  					, f_pp_saldo_capital(cod_plan_pago) + f_pp_saldo_interes_normal(cod_plan_pago) SALDO_K 
                  					, f_pp_saldo_multa(COD_PLAN_PAGO) MULTA
									, f_pp_saldo_mora(COD_PLAN_PAGO) + f_pp_saldo_multa(COD_PLAN_PAGO) SALDO_VENCIDO 
									, f_pp_saldo_plan_pago(COD_PLAN_PAGO) * f_mon_get_valor_moneda(TD.COD_MONEDA) SALDO_PESOS
									, CC.NOM_CONCEPTO_COBRO
									, EP.NOM_ESTADO_PLAN_PAGO 
									, PP.COD_ESTADO_PLAN_PAGO COD_ESTADO_PLAN_PAGO_H
									, PP.COD_EMPRESA_COBRANZA
									, NOM_EMPRESA_COBRANZA
									, PUBLICA_DICOM
									, PUBLICA_DICOM PUBLICA_DICOM_H
									, PP.COD_PLAN_PAGO COD_PLAN_PAGO_H
									
							FROM 	DEUDA D, PLAN_PAGO PP, TIPO_DEUDA TD, CONCEPTO_COBRO CC, ESTADO_PLAN_PAGO EP, EMPRESA_COBRANZA EC
							WHERE 	D.COD_DEUDA=PP.COD_DEUDA 
						  	AND 	D.COD_DEUDA={KEY1}
						  	AND 	TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA
						  	AND 	CC.COD_CONCEPTO_COBRO (+)= PP.COD_CONCEPTO_COBRO
						  	AND 	EP.COD_ESTADO_PLAN_PAGO = PP.COD_ESTADO_PLAN_PAGO
						  	AND 	PP.COD_EMPRESA_COBRANZA = EC.COD_EMPRESA_COBRANZA (+)
							ORDER BY NRO_CUOTA";
				}
				else if (K_TIPO_BD=="mssql"){
					if(K_CLIENTE == 'UTEM'){
						$sql = "SELECT 	convert(varchar(20),ANO_COBRO) + '|' + convert(varchar(20),COD_PLAN_PAGO) ANO_COBRO
									, NRO_CUOTA 
									, convert(varchar(20),FECHA_VENCTO,103) FECHA_VENCTO 
									, PP.MONTO_CAPITAL
									, PP.MONTO_INTERES
									, (PP.MONTO_CAPITAL + PP.MONTO_INTERES) CUOTA_ANUAL
									, dbo.f_pp_monto_abono(COD_PLAN_PAGO, 'MONEDA', getdate()) ABONO
									, PP.MONTO_CAPITAL + PP.MONTO_INTERES - (dbo.f_pp_monto_dscto(COD_PLAN_PAGO,'MONEDA',getdate())) A_PAGAR
									, dbo.f_pp_monto_abono(PP.COD_PLAN_PAGO, 'MULTA', getdate()) ABONO_MULTA
									, isnull(dbo.f_pp_monto_abono(PP.COD_PLAN_PAGO, 'CAPITAL', getdate())+ dbo.f_pp_monto_abono(PP.COD_PLAN_PAGO, 'INTERES', getdate()),0) ABONO_CAPITAL
									, dbo.f_pp_monto_dscto(COD_PLAN_PAGO,'MONEDA',getdate()) DESCUENTOS
									, dbo.f_pp_saldo_capital(COD_PLAN_PAGO, getdate()) + dbo.f_pp_saldo_interes_normal(COD_PLAN_PAGO, getdate()) SALDO_K
									, dbo.f_pp_saldo_multa(COD_PLAN_PAGO, getdate()) MULTA
									, dbo.f_pp_saldo_mora(COD_PLAN_PAGO, getdate()) + dbo.f_pp_saldo_multa(COD_PLAN_PAGO, getdate()) SALDO_VENCIDO
									, isnull(dbo.f_pp_saldo_capital(cod_plan_pago, getdate()) + dbo.f_pp_saldo_interes_normal(cod_plan_pago, getdate()) + dbo.f_pp_saldo_multa(PP.COD_PLAN_PAGO, getdate()), 0) SALDO
									, dbo.f_pp_saldo_plan_pago(COD_PLAN_PAGO, getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()) SALDO_PESOS
									, CC.NOM_CONCEPTO_COBRO
									, dbo.f_estado_plan_pago_reneg(PP.COD_PLAN_PAGO) NOM_ESTADO_PLAN_PAGO
									, PP.COD_ESTADO_PLAN_PAGO COD_ESTADO_PLAN_PAGO_H 
									, PP.COD_EMPRESA_COBRANZA
									, NOM_EMPRESA_COBRANZA
									, PUBLICA_DICOM
									, PUBLICA_DICOM PUBLICA_DICOM_H
									, PP.COD_PLAN_PAGO COD_PLAN_PAGO_H
									,(select top 1 COD_TIPO_SUSPENSION from SUSPENSION 
					 				  where COD_DEUDA = D.COD_DEUDA
					 				  and ANO_SUSPENSION = PP.ANO_COBRO) COD_TIPO_SUSPENSION  
							FROM 	DEUDA D, PLAN_PAGO PP left outer join CONCEPTO_COBRO CC on PP.COD_CONCEPTO_COBRO = CC.COD_CONCEPTO_COBRO 
										left outer join EMPRESA_COBRANZA EC on PP.COD_EMPRESA_COBRANZA = EC.COD_EMPRESA_COBRANZA
									, TIPO_DEUDA TD, ESTADO_PLAN_PAGO EP
							WHERE 	D.COD_DEUDA=PP.COD_DEUDA 
							AND 	D.COD_DEUDA= {KEY1}
							AND 	TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA
							AND 	EP.COD_ESTADO_PLAN_PAGO = PP.COD_ESTADO_PLAN_PAGO
							ORDER BY NRO_CUOTA";
					}
					else{
						$sql = "SELECT 	convert(varchar(20),ANO_COBRO) + '|' + convert(varchar(20),COD_PLAN_PAGO) ANO_COBRO
									, NRO_CUOTA 
									, convert(varchar(20),FECHA_VENCTO,103) FECHA_VENCTO 
									, PP.MONTO_CAPITAL
									, PP.MONTO_INTERES
									, (PP.MONTO_CAPITAL + PP.MONTO_INTERES) CUOTA_ANUAL
									, dbo.f_pp_monto_abono(COD_PLAN_PAGO, 'MONEDA', getdate()) ABONO
									, dbo.f_pp_monto_dscto(COD_PLAN_PAGO,'MONEDA',getdate()) DESCUENTOS
									, dbo.f_pp_saldo_capital(COD_PLAN_PAGO, getdate()) + dbo.f_pp_saldo_interes_normal(COD_PLAN_PAGO, getdate()) SALDO_K
									, dbo.f_pp_saldo_multa(COD_PLAN_PAGO, getdate()) MULTA
									, dbo.f_pp_saldo_mora(COD_PLAN_PAGO, getdate()) + dbo.f_pp_saldo_multa(COD_PLAN_PAGO, getdate()) SALDO_VENCIDO
									, dbo.f_pp_saldo_plan_pago(COD_PLAN_PAGO, getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()) SALDO_PESOS
									, CC.NOM_CONCEPTO_COBRO
									, EP.NOM_ESTADO_PLAN_PAGO
									, PP.COD_ESTADO_PLAN_PAGO COD_ESTADO_PLAN_PAGO_H 
									, PP.COD_EMPRESA_COBRANZA
									, NOM_EMPRESA_COBRANZA
									, PUBLICA_DICOM
									, PUBLICA_DICOM PUBLICA_DICOM_H
									, PP.COD_PLAN_PAGO COD_PLAN_PAGO_H
									,(select COD_TIPO_SUSPENSION from SUSPENSION 
					 				  where COD_DEUDA = D.COD_DEUDA
					 				  and ANO_SUSPENSION = PP.ANO_COBRO) COD_TIPO_SUSPENSION  
							FROM 	DEUDA D, PLAN_PAGO PP left outer join CONCEPTO_COBRO CC on PP.COD_CONCEPTO_COBRO = CC.COD_CONCEPTO_COBRO 
										left outer join EMPRESA_COBRANZA EC on PP.COD_EMPRESA_COBRANZA = EC.COD_EMPRESA_COBRANZA
									, TIPO_DEUDA TD, ESTADO_PLAN_PAGO EP
							WHERE 	D.COD_DEUDA=PP.COD_DEUDA 
							AND 	D.COD_DEUDA= {KEY1}
							AND 	TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA
							AND 	EP.COD_ESTADO_PLAN_PAGO = PP.COD_ESTADO_PLAN_PAGO
							ORDER BY NRO_CUOTA";
					}
				}
				break;
			//////////////////// DW_DEUDA_PAGARES /////////////////////////	
			case 'DW_DEUDA_PAGARES' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	 P.COD_PAGARE COD_PAGARE_H
									,TO_CHAR (FOLIO_PAGARE) || '|' || TO_CHAR (COD_PAGARE) FOLIO_PAGARE
									,TO_CHAR(P.FECHA_PAGARE,'dd/mm/yyyy') FECHA_PAGARE
									,P.MONTO_MONEDA PAGARE_MONTO_MONEDA
									,P.MONTO_PESOS PAGARE_MONTO_PESOS
									,EP.NOM_ESTADO_PAGARE
									,P.COD_ESTADO_PAGARE COD_ESTADO_PAGARE_H
									,TO_CHAR(P.FECHA_PROTESTO,'dd/mm/yyyy') FECHA_PROTESTO
									,P.COD_TIPO_REBAJA_PAGARE
									,P.MONTO_MONEDA_REBAJA
									,P.MONTO_PESOS_REBAJA
									,(P.MONTO_MONEDA - NVL(P.MONTO_MONEDA_REBAJA,0)) MONTO_MONEDA
									,(P.MONTO_PESOS - NVL(P.MONTO_PESOS_REBAJA,0)) MONTO_PESOS 
							FROM 	PAGARE P left outer join ESTADO_PAGARE EP on P.COD_ESTADO_PAGARE = EP.COD_ESTADO_PAGARE
							WHERE 	P.COD_DEUDA={KEY1}
							ORDER BY P.FECHA_PAGARE";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT  P.COD_PAGARE COD_PAGARE_H
									, convert(varchar(20), FOLIO_PAGARE) + '|' + convert(varchar(20), COD_PAGARE) FOLIO_PAGARE
									, convert(varchar(20), P.FECHA_PAGARE, 103) FECHA_PAGARE
									, P.MONTO_MONEDA PAGARE_MONTO_MONEDA
									, P.MONTO_PESOS PAGARE_MONTO_PESOS
									, EP.NOM_ESTADO_PAGARE 
									, P.COD_ESTADO_PAGARE COD_ESTADO_PAGARE_H
									, convert(varchar(20), P.FECHA_PROTESTO, 103) FECHA_PROTESTO
									,P.COD_TIPO_REBAJA_PAGARE
									,P.MONTO_MONEDA_REBAJA
									,P.MONTO_PESOS_REBAJA
									,(P.MONTO_MONEDA - ISNULL(P.MONTO_MONEDA_REBAJA,0)) MONTO_MONEDA
									,(P.MONTO_PESOS - ISNULL(P.MONTO_PESOS_REBAJA,0)) MONTO_PESOS 
							FROM 	PAGARE P left outer join ESTADO_PAGARE EP on P.COD_ESTADO_PAGARE = EP.COD_ESTADO_PAGARE
							WHERE 	P.COD_DEUDA={KEY1}
							ORDER BY P.FECHA_PAGARE";
				}
				break;

			///////////////////////	DW_DEUDA_ABONOS /////////////////////////
			case 'DW_DEUDA_ABONOS' :
	if (K_TIPO_BD=="oci"){
		$sql = "SELECT COD_ABONO
							, TO_CHAR(FECHA_ABONO,'dd/mm/yyyy') FECHA_ABONO
							, O.NOM_ORIGEN_PAGO NOM_ORIGEN_ABONO 
							, C.NOM_COMPONENTE_DEUDA
							, E.NOM_ESTADO_PAGO NOM_ESTADO_ABONO
							, MONTO_MONEDA ABONO_MONTO_MONEDA
							, MONTO_PESOS ABONO_MONTO_PESOS
							, decode(A.COD_ESTADO_PAGO, ".self::K_ESTADO_PAGO_VIGENTE.", MONTO_MONEDA, 0) ABONO_MONTO_MONEDA_V
							, decode(A.COD_ESTADO_PAGO, ".self::K_ESTADO_PAGO_VIGENTE.", MONTO_PESOS, 0) ABONO_MONTO_PESOS_V
						FROM 	ABONO A, COMPONENTE_DEUDA C, ESTADO_PAGO E, ORIGEN_PAGO O
						WHERE 	A.COD_DEUDA={KEY1} 
						  AND 	(
						  			A.COD_COMPONENTE_DEUDA = ".self::K_COMPONENTE_NO_EXIGIBLE."
						  			OR A.COD_PLAN_PAGO IS NULL
						  		)
						  AND 	C.COD_COMPONENTE_DEUDA=A.COD_COMPONENTE_DEUDA
						  AND 	E.COD_ESTADO_PAGO = A.COD_ESTADO_PAGO
						  AND 	O.COD_ORIGEN_PAGO = A.COD_ORIGEN_PAGO
						
						UNION
							
							SELECT NULL COD_ABONO
		                   		, TO_CHAR(FECHA_ABONO,'dd/mm/yyyy') FECHA_ABONO
		                   		, 'COMPL OTRA UES' NOM_ORIGEN_PAGO
		                   		, 'NO EXIGIBLE' NOM_COMPONENTE_DEUDA
		                   		, 'VIGENTE' NOM_ESTADO_PAGO
		                   		, MONTO_MONEDA ABONO_MONTO_MONEDA
								, MONTO_PESOS ABONO_MONTO_PESOS 
								, decode(1, 1, MONTO_MONEDA, 0) ABONO_MONTO_MONEDA_V
								, decode(1, 1, MONTO_PESOS, 0) ABONO_MONTO_PESOS_V                  		
							FROM ABONO_FICTICIO 
		              		WHERE cod_plan_pago IS NULL
		              		  AND ABONO_FICTICIO.COD_DEUDA = {KEY1}
		              		  AND EXISTS (SELECT * FROM ABONO WHERE ABONO.COD_ABONO = ABONO_FICTICIO.COD_ABONO)
		              		ORDER BY FECHA_ABONO DESC";
	}
	else if (K_TIPO_BD=="mssql"){
		$sql = "SELECT COD_ABONO
							, convert(varchar(20),FECHA_ABONO,103) FECHA_ABONO
							, FECHA_ABONO ORDEN_FECHA
							, O.NOM_ORIGEN_PAGO NOM_ORIGEN_ABONO 
							, C.NOM_COMPONENTE_DEUDA
							, E.NOM_ESTADO_PAGO NOM_ESTADO_ABONO
							, MONTO_MONEDA ABONO_MONTO_MONEDA
							, MONTO_PESOS ABONO_MONTO_PESOS
							, case A.COD_ESTADO_PAGO
								when ".self::K_ESTADO_PAGO_VIGENTE." then MONTO_MONEDA
								else 0
							  end ABONO_MONTO_MONEDA_V
							, case A.COD_ESTADO_PAGO
								when ".self::K_ESTADO_PAGO_VIGENTE." then MONTO_PESOS
								else 0
							  end ABONO_MONTO_PESOS_V
						FROM 	ABONO A, COMPONENTE_DEUDA C, ESTADO_PAGO E, ORIGEN_PAGO O
						WHERE 	A.COD_DEUDA={KEY1} 
						  AND 	(
						  			A.COD_COMPONENTE_DEUDA = ".self::K_COMPONENTE_NO_EXIGIBLE."
						  			OR A.COD_PLAN_PAGO IS NULL
						  		)
							AND 	C.COD_COMPONENTE_DEUDA=A.COD_COMPONENTE_DEUDA
						  AND 	E.COD_ESTADO_PAGO = A.COD_ESTADO_PAGO
						  AND 	O.COD_ORIGEN_PAGO = A.COD_ORIGEN_PAGO
						ORDER BY ORDEN_FECHA";
	}
	break;
				
			//////////////////////// DW_DEUDA_DESCUENTOS //////////////////////////	
			case 'DW_DEUDA_DESCUENTOS' :
	if (K_TIPO_BD=="oci"){
		$sql = "SELECT	COD_DESCUENTO
							, TO_CHAR(FECHA_DESCUENTO,'dd/mm/yyyy') FECHA_DESCUENTO
							, O.NOM_ORIGEN_PAGO NOM_ORIGEN_DESCUENTO
							, C.NOM_COMPONENTE_DEUDA
							, E.NOM_ESTADO_PAGO NOM_ESTADO_DESCUENTO
							, MONTO_MONEDA DESCUENTO_MONTO_MONEDA
							, MONTO_PESOS DESCUENTO_MONTO_PESOS
							, decode(D.COD_ESTADO_PAGO, ".self::K_ESTADO_PAGO_VIGENTE.", MONTO_MONEDA, 0) DESCUENTO_MONTO_MONEDA_V
							, decode(D.COD_ESTADO_PAGO, ".self::K_ESTADO_PAGO_VIGENTE.", MONTO_PESOS, 0) DESCUENTO_MONTO_PESOS_V
						FROM 	DESCUENTO D, COMPONENTE_DEUDA C, ESTADO_PAGO E, ORIGEN_PAGO O
						WHERE 	D.COD_DEUDA={KEY1}
						  AND 	(
						  			D.COD_COMPONENTE_DEUDA = ".self::K_COMPONENTE_NO_EXIGIBLE."
						  			OR D.COD_PLAN_PAGO IS NULL
						  		)
						  AND 	C.COD_COMPONENTE_DEUDA = D.COD_COMPONENTE_DEUDA
						  AND 	E.COD_ESTADO_PAGO = D.COD_ESTADO_PAGO
						  AND 	O.COD_ORIGEN_PAGO = D.COD_ORIGEN_PAGO
							
						UNION

						  SELECT NULL COD_ABONO
		                   		, TO_CHAR(FECHA_DESCUENTO,'dd/mm/yyyy') FECHA_DESCUENTO
		                   		, 'COMPL OTRA UES' NOM_ORIGEN_PAGO
		                   		, 'NO EXIGIBLE' NOM_COMPONENTE_DEUDA
		                   		, 'VIGENTE' NOM_ESTADO_PAGO
		                   		, MONTO_MONEDA DESCUENTO_MONTO_MONEDA
								, MONTO_PESOS DESCUENTO_MONTO_PESOS 
								, decode(1, 1, MONTO_MONEDA, 0) ABONO_MONTO_MONEDA_V
								, decode(1, 1, MONTO_PESOS, 0) ABONO_MONTO_PESOS_V                  		
							FROM DESCUENTO_FICTICIO 
		              		WHERE cod_plan_pago IS NULL
		              		  AND DESCUENTO_FICTICIO.COD_DEUDA = {KEY1}
		              		  AND EXISTS (SELECT * FROM DESCUENTO WHERE DESCUENTO.COD_DESCUENTO = DESCUENTO_FICTICIO.COD_DESCUENTO)
		              		ORDER BY FECHA_DESCUENTO DESC";
	}
	else if (K_TIPO_BD=="mssql"){
		$sql = "SELECT	COD_DESCUENTO
							, convert(varchar(20),FECHA_DESCUENTO,103) FECHA_DESCUENTO
							, O.NOM_ORIGEN_PAGO NOM_ORIGEN_DESCUENTO
							, C.NOM_COMPONENTE_DEUDA
							, E.NOM_ESTADO_PAGO NOM_ESTADO_DESCUENTO
							, MONTO_MONEDA DESCUENTO_MONTO_MONEDA
							, MONTO_PESOS DESCUENTO_MONTO_PESOS
							, case D.COD_ESTADO_PAGO
								when ".self::K_ESTADO_PAGO_VIGENTE." then MONTO_MONEDA
								else 0
							  end DESCUENTO_MONTO_MONEDA_V
							, case D.COD_ESTADO_PAGO
								when ".self::K_ESTADO_PAGO_VIGENTE." then MONTO_PESOS
								else 0
							  end DESCUENTO_MONTO_PESOS_V
						FROM 	DESCUENTO D, COMPONENTE_DEUDA C, ESTADO_PAGO E, ORIGEN_PAGO O
						WHERE 	D.COD_DEUDA={KEY1}
						  AND 	(
						  			D.COD_COMPONENTE_DEUDA = ".self::K_COMPONENTE_NO_EXIGIBLE."
						  			OR D.COD_PLAN_PAGO IS NULL
						  		)
						  AND 	C.COD_COMPONENTE_DEUDA = D.COD_COMPONENTE_DEUDA
						  AND 	E.COD_ESTADO_PAGO = D.COD_ESTADO_PAGO
						  AND 	O.COD_ORIGEN_PAGO = D.COD_ORIGEN_PAGO
						ORDER BY FECHA_DESCUENTO";
	}
	break;
				
			//////////////////// DW_DEUDA_CRUZADO ///////////////////
			case 'DW_DEUDA_CRUZADO' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	 C.COD_CRUZADO
									,U.COD_UNIVERSIDAD CRUZADO_COD_UNIVERSIDAD
									,U.NOM_UNIVERSIDAD
									,MONTO_ACTUALIZADO
									,PORC
									,PORC PORC_H
									,TO_CHAR(FECHA_CONSOLIDACION,'dd/mm/yyyy') FECHA_CONSOLIDACION
							FROM 	CRUZADO C, UNIVERSIDAD U
							WHERE 	C.COD_DEUDA = {KEY1}
						  	AND 	U.COD_UNIVERSIDAD = C.COD_UNIVERSIDAD
							ORDER BY C.COD_UNIVERSIDAD";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	 C.COD_CRUZADO
									,U.COD_UNIVERSIDAD CRUZADO_COD_UNIVERSIDAD
									,U.NOM_UNIVERSIDAD
									,MONTO_ACTUALIZADO
									,PORC
									,PORC PORC_H
									,convert(varchar(20), FECHA_CONSOLIDACION, 103) FECHA_CONSOLIDACION
							FROM 	CRUZADO C, UNIVERSIDAD U
							WHERE 	C.COD_DEUDA = {KEY1}
						  	AND 	U.COD_UNIVERSIDAD = C.COD_UNIVERSIDAD
							ORDER BY C.COD_UNIVERSIDAD";
				}
				break;
				
				//////////////////// DW_DEUDA_CRUZADO_INTERNO ///////////////////
			case 'DW_DEUDA_CRUZADO_INTERNO' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT AI.NOM_ACREEDOR_INTERNO NOM_ACREEDOR_INTERNO
									, MONTO_ACTUALIZADO MONTO_ACTUALIZADO_INT
									, PORC PORC_INT   
									, TO_CHAR(FECHA_CONSOLIDACION,'dd/mm/yyyy') FECHA_CONSOLIDACION_INT
							FROM 	CRUZADO_INTERNO CI, ACREEDOR_INTERNO AI
							WHERE 	CI.COD_DEUDA = {KEY1}
						  	AND 	AI.COD_ACREEDOR_INTERNO = CI.COD_ACREEDOR_INTERNO
							ORDER BY CI.COD_ACREEDOR_INTERNO";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT AI.NOM_ACREEDOR_INTERNO NOM_ACREEDOR_INTERNO
									, MONTO_ACTUALIZADO MONTO_ACTUALIZADO_INT
									, PORC PORC_INT   
									, convert(varchar(20), FECHA_CONSOLIDACION, 103) FECHA_CONSOLIDACION_INT
							FROM 	CRUZADO_INTERNO CI, ACREEDOR_INTERNO AI
							WHERE 	CI.COD_DEUDA = {KEY1}
						  	AND 	AI.COD_ACREEDOR_INTERNO = CI.COD_ACREEDOR_INTERNO
							ORDER BY CI.COD_ACREEDOR_INTERNO";
				}
				break;
				
			///////////////////	DD_COD_TIPO_DEUDA /////////////////
			case 'DD_COD_TIPO_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_TIPO_DEUDA COD_TIPO_DEUDA1
									, COD_TIPO_DEUDA
							FROM	TIPO_DEUDA 
							ORDER BY COD_TIPO_DEUDA
					";
				}
				break;
			
			///////////////////////	DD_COD_TIPO_REBAJA_PAGARE ////////////////////
			case 'DD_COD_TIPO_REBAJA_PAGARE' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_TIPO_REBAJA_PAGARE 
									,NOM_TIPO_REBAJA_PAGARE 
							FROM 	TIPO_REBAJA_PAGARE
							ORDER BY COD_TIPO_REBAJA_PAGARE ASC";
				}
				break;
				
			///////////////////////	DD_COD_UNIVERSIDAD ////////////////////
			case 'DD_COD_UNIVERSIDAD' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_UNIVERSIDAD 
									,NOM_UNIVERSIDAD 
							FROM 	UNIVERSIDAD 
							ORDER BY COD_UNIVERSIDAD ASC";
				}
				break;

			///////////////////////	DD_COD_ESTADO_DEUDA ////////////////////
			case 'DD_COD_ESTADO_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select COD_ESTADO_DEUDA, NOM_ESTADO_DEUDA 
							from ESTADO_DEUDA 
							order by COD_ESTADO_DEUDA ASC";
				}
				break;
								
			/////////////////// PRINT_DEVUELTO ///////////////////////
			case 'PRINT_DEVUELTO' :
				if (K_TIPO_BD=="oci"){
					
					$sql = "SELECT  DEUDA.COD_DEUDA
						      , DEUDOR.RUT
						      , DEUDOR.DIG_VERIF
						      , DEUDOR.NOMBRE||' '||DEUDOR.APELLIDO_PATERNO||' '||DEUDOR.APELLIDO_MATERNO NOMBRE
						      , TD.COD_TIPO_DEUDA
						      , ED.NOM_ESTADO_DEUDA
						      , P.FOLIO_PAGARE
						      , P.MONTO_PESOS
					          , P.MONTO_MONEDA MONTO_MONEDA
					          , TO_CHAR(P.FECHA_PAGARE, 'dd/mm/yyyy') FECHA_PAGARE
					          , EP.NOM_ESTADO_PAGARE
			                  , M.SIMBOLO
					          ,to_char (SYSDATE, 'DD/MM/YYYY') FECHA_IMPRESO
		    				  ,to_char (SYSDATE, 'HH24:MI:SS') HORA_IMPRESO 
							  , f_get_parametro(5) NOM_UNIVERSIDAD -- NOMBRE_UNIVERSIDAD	
							  , P.DEVOLUCION_A
							  , P.MOTIVO_DEVOLUCION
							  , P.RUT_DEVOLUCION
						FROM    DEUDA DEUDA, DEUDOR DEUDOR, ESTADO_DEUDA ED, TIPO_DEUDA TD, PAGARE P,
		                		ESTADO_PAGARE EP, MONEDA M
					    WHERE   DEUDA.COD_DEUDA = {PARAM1}
						 AND
							    DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR AND
					          	DEUDA.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA AND
					          	TD.COD_TIPO_DEUDA = DEUDA.COD_TIPO_DEUDA AND
		                		DEUDA.COD_DEUDA = P.COD_DEUDA (+) AND
		                		P.COD_ESTADO_PAGARE = EP.COD_ESTADO_PAGARE (+) AND
		                  		TD.COD_MONEDA = M.COD_MONEDA";
					
					
				}
				else if (K_TIPO_BD=="mssql"){
					$sql ="SELECT DEUDA.COD_DEUDA
						      , DEUDOR.RUT
						      , DEUDOR.DIG_VERIF
						      , DEUDOR.NOMBRE + ' ' + DEUDOR.APELLIDO_PATERNO + ' ' + DEUDOR.APELLIDO_MATERNO NOMBRE
						      , TD.COD_TIPO_DEUDA
						      , ED.NOM_ESTADO_DEUDA
						      , P.FOLIO_PAGARE
						      , P.MONTO_PESOS
					          , P.MONTO_MONEDA MONTO_MONEDA
							  , convert(varchar(20), FECHA_PAGARE, 103) FECHA_PAGARE
							  , EP.NOM_ESTADO_PAGARE
			                  , M.SIMBOLO
							  , convert(varchar,getdate(), 103) FECHA_IMPRESO
							  , convert(varchar,getdate(), 8) HORA_IMPRESO 
							  , dbo.f_get_parametro(5) NOM_UNIVERSIDAD -- NOMBRE_UNIVERSIDAD
							  , P.DEVOLUCION_A
							  , P.MOTIVO_DEVOLUCION
							  , P.RUT_DEVOLUCION
						FROM    DEUDA DEUDA, DEUDOR DEUDOR, ESTADO_DEUDA ED, TIPO_DEUDA TD, PAGARE P,
		                		ESTADO_PAGARE EP, MONEDA M
					    WHERE   DEUDA.COD_DEUDA = {PARAM1} AND
							    DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR AND
					          	DEUDA.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA AND
					          	TD.COD_TIPO_DEUDA = DEUDA.COD_TIPO_DEUDA AND
		                		DEUDA.COD_DEUDA = P.COD_DEUDA AND
		                		P.COD_ESTADO_PAGARE = EP.COD_ESTADO_PAGARE AND
		                  		TD.COD_MONEDA = M.COD_MONEDA";
				}
				break;
				
				
				/////////////////// GET_DEUDA_MOROSA ///////////////////////
			case 'GET_DEUDA_MOROSA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					
					$sql = "SELECT 	* 
							FROM  	DEUDA DA
							WHERE 	DA.COD_DEUDA IN (SELECT COD_DEUDA FROM PLAN_PAGO WHERE COD_EMPRESA_COBRANZA IS NOT null)
							AND 	COD_DEUDA = {PARAM1}";
				}
				break;

				/////////////////// DD_EMPRESA_COBRANZA ///////////////////////				
			case 'DD_EMPRESA_COBRANZA' :	
				if (K_TIPO_BD=="oci") {
						$sql = "select 0 COD_EMPRESA_COBRANZA
										,'(sin empresa)' NOM_EMPRESA_COBRANZA
								from DUAL
								union
								SELECT	COD_EMPRESA_COBRANZA
										,NOM_EMPRESA_COBRANZA
								FROM	EMPRESA_COBRANZA
								ORDER BY	COD_EMPRESA_COBRANZA";
					}
					else if (K_TIPO_BD=="mssql") {
						$sql = "select 0 COD_EMPRESA_COBRANZA
										,'(sin empresa)' NOM_EMPRESA_COBRANZA
								union
								SELECT	COD_EMPRESA_COBRANZA
										,NOM_EMPRESA_COBRANZA
								FROM	EMPRESA_COBRANZA
								ORDER BY	COD_EMPRESA_COBRANZA";
					}
				break;
				
				/////////////////// AJAX_EMPRESA_COBRANZA	 ///////////////////////				
			case 'AJAX_EMPRESA_COBRANZA' :	
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	NOM_EMPRESA_COBRANZA 
							FROM 	EMPRESA_COBRANZA
							WHERE 	COD_EMPRESA_COBRANZA = {PARAM1}";
				}
				break;
				/////////////////// GET_EMPRESA_COBRANZA ///////////////////////
			case 'GET_EMPRESA_COBRANZA' :	
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_EMPRESA_COBRANZA
							FROM	PLAN_PAGO
							WHERE	COD_DEUDA = {PARAM1}";
				}
				break;
				
			////////////// AJAX_VALIDA_CARTERA ///////////////
					case 'AJAX_VALIDA_CARTERA' :
						if (K_TIPO_BD=="oci"){
							$sql_uach = "select *
										from cartera
										where cod_cartera = F_GET_PARAMETRO(2)";
						}else if(K_TIPO_BD=="mssql"){
							$sql = "select *
									from cartera
									where cod_cartera = dbo.F_GET_PARAMETRO(2)";
						}
						break;

		
						
			////////////// DEUDA_CARTERA ////////////////
			case 'DEUDA_CARTERA':
				if((K_TIPO_BD=="oci" || K_TIPO_BD=="mssql")){
					$sql ="SELECT COUNT(*) CANT
							FROM DEUDOR DR, DEUDA DA 
							WHERE DR.COD_DEUDOR = DA.COD_DEUDOR 
							AND DA.COD_CARTERA  <> ".self::K_COD_CARTERA_UACH."
							AND DA.COD_DEUDA = {PARAM1}
							AND COD_TIPO_DEUDA IN ('CS', 'CSR')";
				}
				break;
				
				/////////////////// GET_PUBLICA_DICOM ///////////////////////
			case 'GET_PUBLICA_DICOM' :	
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	PUBLICA_DICOM 
							FROM 	PLAN_PAGO
							WHERE 	COD_DEUDA = {PARAM1}
							AND 	COD_ESTADO_PLAN_PAGO = ".self::K_ESTADO_PP_MOROSO."
							AND 	PUBLICA_DICOM = 'S'";
				}
				break;
				
				
				/////////////////// VALIDA_DEVOLUCION ///////////////////////
			case 'VALIDA_DEVOLUCION' :	
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT COUNT(*) CANT
								FROM DOCUMENTO_PAGO DP, DEUDOR DO,ESTADO_DOCUMENTO_PAGO ED, TIPO_DOCUMENTO_PAGO TD
								WHERE DP.COD_DEUDOR = DO.COD_DEUDOR
								AND DP.COD_TIPO_DOCUMENTO_PAGO = TD.COD_TIPO_DOCUMENTO_PAGO
								AND DP.COD_ESTADO_DOCUMENTO_PAGO = ED.COD_ESTADO_DOCUMENTO_PAGO
								AND DO.COD_DEUDOR = {PARAM1}
								AND ED.COD_ESTADO_DOCUMENTO_PAGO IN (1,3)
								AND TD.COD_TIPO_DOCUMENTO_PAGO IN (1,5)";
				}
				break;
			
				/////////////////// DOCTO_PROTESTADO ///////////////////////
			case 'DOCTO_PROTESTADO' :	
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT count(*) CANT
							FROM DOCUMENTO_PAGO DP, DEUDA D 
							WHERE D.COD_DEUDA = {PARAM1}
								AND DP.COD_DEUDOR = D.COD_DEUDOR										
								AND DP.COD_ESTADO_DOCUMENTO_PAGO = ".self::K_ESTADO_DOC_PROTESTADO;
				}
				break;
				
				/////////////////// PAGARE_PROTESTADO ///////////////////////
			case 'PAGARE_PROTESTADO' :	
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT count(*) CANT
							FROM DEUDA DE, DEUDOR DEU, PAGARE PA
							WHERE DE.COD_DEUDA = {PARAM1}
								AND DE.COD_DEUDOR = DEU.COD_DEUDOR
								AND DE.COD_DEUDA = PA.COD_DEUDA
								AND PA.COD_ESTADO_PAGARE = ".self::K_ESTADO_PAGARE_PROTESTADO;
				}
				break;
				
				/////////////////  DW_SIMULACION  ///////////////
				case 'DW_SIMULACION' :
				if (K_TIPO_BD=="oci") {
					$sql = "SELECT 	I.COD_INGRESO COD_SIMULACION
									,to_char(I.FECHA_INGRESO, 'dd/mm/yyyy') FECHA_SIMULACION
									,f_ingreso_deudas(I.COD_INGRESO) DEUDAS
									,f_ingreso_doc_al_dia(I.COD_INGRESO) TOT_DOC_AL_DIA
									,f_ingreso_doc_a_fecha(I.COD_INGRESO) TOT_DOC_AFECHA
									,f_ingreso_doc_al_dia(I.COD_INGRESO) + f_ingreso_doc_a_fecha(COD_INGRESO) TOT_DOC
									,f_ingreso_get_desde_simulacion(I.COD_INGRESO) COD_INGRESO_REAL
							FROM 	INGRESO I
							WHERE 	I.COD_DEUDOR ={KEY1}
							  and	I.COD_ESTADO_INGRESO = ".self::K_ESTADO_SIMULACION." 
							order by I.COD_INGRESO desc";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 	I.COD_INGRESO COD_SIMULACION
									,convert(varchar, I.FECHA_INGRESO, 103) FECHA_SIMULACION
									,dbo.f_ingreso_deudas(I.COD_INGRESO) DEUDAS
									,dbo.f_ingreso_doc_al_dia(I.COD_INGRESO) TOT_DOC_AL_DIA
									,dbo.f_ingreso_doc_a_fecha(I.COD_INGRESO) TOT_DOC_AFECHA
									,dbo.f_ingreso_doc_al_dia(I.COD_INGRESO) + dbo.f_ingreso_doc_a_fecha(COD_INGRESO) TOT_DOC
									,dbo.f_ingreso_get_desde_simulacion(I.COD_INGRESO) COD_INGRESO_REAL
							FROM 	INGRESO I
							WHERE 	I.COD_DEUDOR = {KEY1}
							  and	I.COD_ESTADO_INGRESO = ".self::K_ESTADO_SIMULACION." 
							order by I.COD_INGRESO desc";
				}
				break;	

			/////////////////  ERROR ///////////////
			default: 
				base::error("sql_deuda::get_sql, selector: '$selector_sql' no definido");
			
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}

/////////////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_sql_deuda.php";
if (file_exists($file_name))
    require_once($file_name);
else {
    class sql_deuda extends sql_deuda_base {
    }
}
?>