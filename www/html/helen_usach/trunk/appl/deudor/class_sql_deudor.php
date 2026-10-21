<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_deudor_base extends sql_tabla {
	const K_ESTADO_SIMULACION = 1000;
	const K_ESTADO_DOC_PROTESTADO	= 3;
	
	static function get_sql($selector_sql) {
		switch ($selector_sql) {
			/////////////////  INPUT ///////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	COD_DEUDOR
									,RUT
									,DIG_VERIF 
			   						,NOMBRE
			   						,APELLIDO_PATERNO
			   						,APELLIDO_MATERNO
			   						,SEXO
			   						,D.COD_ESTADO_CIVIL
			   						,NOM_ESTADO_CIVIL
			   						,TO_CHAR(FECHA_NACIM,'dd/mm/yyyy') FECHA_NACIM
			   						,TO_CHAR(FECHA_DEFUNCION,'dd/mm/yyyy') FECHA_DEFUNCION
			   						,0 ANO_DECLARACION_SELECCIONADA
			   						,'' TITULO_DEUDOR_CRUZADO
									,'none' VISIBLE_DEUDOR_CRUZADO
			   						,'none' VISIBLE_CAMPO
			   						,'DEUDA INTERNALIZADA' TITULO_DEUDOR_MOROSO
			   						, 'none' VISIBLE_CAMPO_PROTES
			   						, 'DEUDOR CON DOCUMENTOS PROTESTADOS' TITULO_DOC_PROTES
			   						, f_get_constante('K_CHILE') K_CHILE_H
			   						, D.PASSWORD PASSWORD_DEUDOR
			   						, D.COD_SITUACION_ACADEMICA 
							FROM 	DEUDOR D
									,ESTADO_CIVIL EC
							WHERE 	COD_DEUDOR = {KEY1}
									AND D.COD_ESTADO_CIVIL = EC.COD_ESTADO_CIVIL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_DEUDOR
									,RUT
									,DIG_VERIF 
									,NOMBRE
									,APELLIDO_PATERNO
									,APELLIDO_MATERNO
									,SEXO
			   						,D.COD_ESTADO_CIVIL
									,NOM_ESTADO_CIVIL
									,convert(varchar(20),FECHA_NACIM,103) FECHA_NACIM
									,convert(varchar(20),FECHA_DEFUNCION,103) FECHA_DEFUNCION
									,0 ANO_DECLARACION_SELECCIONADA
									, '' TITULO_DEUDOR_CRUZADO
									, 'none' VISIBLE_DEUDOR_CRUZADO
									, 'none' VISIBLE_CAMPO
									,'DEUDA INTERNALIZADA' TITULO_DEUDOR_MOROSO
									, 'none' VISIBLE_CAMPO_PROTES
			   						, 'DEUDOR CON DOCUMENTOS PROTESTADOS' TITULO_DOC_PROTES
			   						, dbo.f_get_constante('K_CHILE') K_CHILE_H
			   						, D.PASSWORD PASSWORD_DEUDOR
			   						, D.COD_SITUACION_ACADEMICA
							FROM 	DEUDOR D
									,ESTADO_CIVIL EC
							WHERE 	COD_DEUDOR = {KEY1}
									AND D.COD_ESTADO_CIVIL = EC.COD_ESTADO_CIVIL";
				}
				break;
				
			/////////////////  OUTPUT ///////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT   COD_DEUDOR
						/*INI_CAMPOS*/
						,RUT
						,DIG_VERIF
						,NOMBRE
						,APELLIDO_PATERNO
						,APELLIDO_MATERNO
						,(SELECT   D.DIRECCION 
							FROM DIRECCION D 
							where  f_get_direccion_deudor(DEUDOR.COD_DEUDOR ,'P')=D.COD_DIRECCION 
						)DIRECCION
						,( SELECT NOM_CIUDAD FROM CIUDAD CI, DIRECCION D 
							WHERE D.COD_DEUDOR =  DEUDOR.COD_DEUDOR  
							AND D.COD_CIUDAD = CI.COD_CIUDAD
							and f_get_direccion_deudor(DEUDOR.COD_DEUDOR ,'P')=D.COD_DIRECCION)	CIUDAD
						,( SELECT C.NOM_COMUNA FROM COMUNA C, DIRECCION D 
							WHERE D.COD_DEUDOR =  DEUDOR.COD_DEUDOR  
							AND D.COD_COMUNA = C.COD_COMUNA
							and f_get_direccion_deudor(DEUDOR.COD_DEUDOR ,'P')=D.COD_DIRECCION)	COMUNA
						,( SELECT EMAIL FROM  DIRECCION D 
							WHERE D.COD_DEUDOR =  DEUDOR.COD_DEUDOR  
							and f_get_direccion_deudor(DEUDOR.COD_DEUDOR ,'P')=D.COD_DIRECCION)	EMAIL
						,( SELECT TELEFONO FROM  DIRECCION D 
							WHERE D.COD_DEUDOR =  DEUDOR.COD_DEUDOR  
							and f_get_direccion_deudor(DEUDOR.COD_DEUDOR ,'P')=D.COD_DIRECCION)	TELEFONO
							,( SELECT CELULAR FROM  DIRECCION D 
							WHERE D.COD_DEUDOR =  DEUDOR.COD_DEUDOR  
							and f_get_direccion_deudor(DEUDOR.COD_DEUDOR ,'P')=D.COD_DIRECCION)	CELULAR
							/*FIN_CAMPOS*/		
						from DEUDOR
						where DEUDOR.COD_DEUDOR is not null
						/*FIN_CONDICION*/
						ORDER BY COD_DEUDOR
						";
				}
			else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT   COD_DEUDOR
						/*INI_CAMPOS*/
						,RUT
						,DIG_VERIF
						,NOMBRE
						,APELLIDO_PATERNO
						,APELLIDO_MATERNO
						,(SELECT   D.DIRECCION 
							FROM DIRECCION D 
							where  dbo.f_get_direccion_deudor(DEUDOR.COD_DEUDOR ,'P')=D.COD_DIRECCION 
						)DIRECCION
						,( SELECT NOM_CIUDAD FROM CIUDAD CI, DIRECCION D 
							WHERE D.COD_DEUDOR =  DEUDOR.COD_DEUDOR  
							AND D.COD_CIUDAD = CI.COD_CIUDAD
							and dbo.f_get_direccion_deudor(DEUDOR.COD_DEUDOR ,'P')=D.COD_DIRECCION)	CIUDAD
						,( SELECT C.NOM_COMUNA FROM COMUNA C, DIRECCION D 
							WHERE D.COD_DEUDOR =  DEUDOR.COD_DEUDOR  
							AND D.COD_COMUNA = C.COD_COMUNA
							and dbo.f_get_direccion_deudor(DEUDOR.COD_DEUDOR ,'P')=D.COD_DIRECCION)	COMUNA
						,( SELECT EMAIL FROM  DIRECCION D 
							WHERE D.COD_DEUDOR =  DEUDOR.COD_DEUDOR  
							and dbo.f_get_direccion_deudor(DEUDOR.COD_DEUDOR ,'P')=D.COD_DIRECCION)	EMAIL
						,( SELECT TELEFONO FROM  DIRECCION D 
							WHERE D.COD_DEUDOR =  DEUDOR.COD_DEUDOR  
							and dbo.f_get_direccion_deudor(DEUDOR.COD_DEUDOR ,'P')=D.COD_DIRECCION)	TELEFONO
							,( SELECT CELULAR FROM  DIRECCION D 
							WHERE D.COD_DEUDOR =  DEUDOR.COD_DEUDOR  
							and dbo.f_get_direccion_deudor(DEUDOR.COD_DEUDOR ,'P')=D.COD_DIRECCION)	CELULAR
							/*FIN_CAMPOS*/		
						from DEUDOR
						where DEUDOR.COD_DEUDOR is not null
						/*FIN_CONDICION*/
						ORDER BY COD_DEUDOR asc
						";
				}
				break;
				
				
			/////////////////  DW_DETALLE_DEUDA ///////////////
			case 'DW_DETALLE_DEUDA' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT   	COD_DEUDA
							       		, NOM_ESTADO_DEUDA
								   		, TO_CHAR(FECHA_INICIO_COBRO,'dd/mm/yyyy') FECHA_INICIO_COBRO
								   		, D.COD_TIPO_DEUDA 
								   		, NOM_TIPO_DEUDA
								   		, D.CANT_CUOTA CANT_CUOTA
								   		, f_deuda_saldo_componente(COD_DEUDA,1) NO_EXIGIBLE
								   		, f_deuda_saldo_componente(COD_DEUDA,2) VENCIDA
								   		, f_deuda_saldo_componente(COD_DEUDA,3) VIGENTE
								   		, f_deuda_saldo_componente(COD_DEUDA,4) FUTURA
								   		, f_deuda_saldo_total(COD_DEUDA) SALDO
								   		, NOM_CARTERA
							FROM 		DEUDA D
									 	,ESTADO_DEUDA ED
									 	, TIPO_DEUDA TD
									 	, CARTERA C
						   	WHERE 		D.COD_DEUDOR = {KEY1}
							   		 	AND D.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA
									 	AND D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
									 	AND D.COD_CARTERA = C.COD_CARTERA
							ORDER BY	COD_DEUDA ASC";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT   	COD_DEUDA
							       		, NOM_ESTADO_DEUDA
								   		, convert(varchar(20),FECHA_INICIO_COBRO,103) FECHA_INICIO_COBRO
								   		, D.COD_TIPO_DEUDA 
								   		, NOM_TIPO_DEUDA
								   		, D.CANT_CUOTA CANT_CUOTA
								   		, dbo.f_deuda_saldo_componente(COD_DEUDA, 1, getdate()) NO_EXIGIBLE
								   		, dbo.f_deuda_saldo_componente(COD_DEUDA, 2, getdate()) VENCIDA
								   		, dbo.f_deuda_saldo_componente(COD_DEUDA, 3, getdate()) VIGENTE
								   		, dbo.f_deuda_saldo_componente(COD_DEUDA, 4, getdate()) FUTURA
								   		, dbo.f_deuda_saldo_total(COD_DEUDA, GETDATE()) SALDO
								   		, NOM_CARTERA
							FROM 		DEUDA D
									 	,ESTADO_DEUDA ED
									 	, TIPO_DEUDA TD
									 	, CARTERA C
						   	WHERE 		D.COD_DEUDOR = {KEY1}
							   		 	AND D.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA
									 	AND D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
									 	AND D.COD_CARTERA = C.COD_CARTERA
							ORDER BY	COD_DEUDA ASC";
				}
				break;
				
			/////////////////  DW_BITACORA_DEUDOR ///////////////
			case 'DW_BITACORA_DEUDOR' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 		TO_CHAR(FECHA_BITACORA_DEUDOR,'dd/mm/yyyy HH24:mi') FECHA_BITACORA_DEUDOR
										,FECHA_BITACORA_DEUDOR FECHA_BITACORA_DEUDOR2
									    ,COD_TIPO_BITACORA
									    ,NOM_BITACORA_DEUDOR
									    ,BD.COD_USUARIO
									    ,NOM_USUARIO
									    ,COD_DEUDOR
									    ,'N' IS_NEW
							FROM   		BITACORA_DEUDOR BD
			    						, USUARIO U
							WHERE 		COD_DEUDOR = {KEY1}
										AND BD.COD_USUARIO = U.COD_USUARIO 
							ORDER BY	FECHA_BITACORA_DEUDOR2 DESC";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 		CONVERT(VARCHAR(20),FECHA_BITACORA_DEUDOR, 103)+' '+ CONVERT(VARCHAR(20),FECHA_BITACORA_DEUDOR, 8) FECHA_BITACORA_DEUDOR
										,FECHA_BITACORA_DEUDOR FECHA_BITACORA_DEUDOR2
									    ,COD_TIPO_BITACORA
									    ,NOM_BITACORA_DEUDOR
									    ,BD.COD_USUARIO
									    ,NOM_USUARIO
									    ,COD_DEUDOR
									    ,'N' IS_NEW
							FROM   		BITACORA_DEUDOR BD
			    						, USUARIO U
							WHERE 		COD_DEUDOR = {KEY1}
								AND		BD.COD_USUARIO = U.COD_USUARIO 
							ORDER BY	FECHA_BITACORA_DEUDOR2 DESC";
				}
				break;

			/////////////////  DD_TIPO_BITACORA  ///////////////
			case 'DD_TIPO_BITACORA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT		COD_TIPO_BITACORA
			              				,NOM_TIPO_BITACORA
			              				,PERMITE_MANUAL
			        		FROM     	TIPO_BITACORA
			        		ORDER BY	NOM_TIPO_BITACORA";
				}
				break;				
				
			/////////////////  DW_DIRECCION_PARTICULAR_HISTORICA ///////////////
			case 'DW_DIRECCION_PARTICULAR_HISTORICA' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 		COD_DIRECCION
								   		, COD_DEUDOR
								   		, TO_CHAR(FECHA_DIRECCION,'dd/mm/yyyy') FECHA_DIRECCION_NO_ING
								   		,FECHA_DIRECCION
								   		, TIPO_DIRECCION
								   		, DIRECCION
								   		, NOM_COMUNA
								   		, NOM_CIUDAD
								   		, NOM_PAIS
								   		, EMAIL
								   		, TELEFONO
								   		, CELULAR
								   		, ES_VALIDA ES_VALIDA_DIR_PAR_HIST
							FROM   		DIRECCION D
										,PAIS P
										,COMUNA C
										,CIUDAD CC
							WHERE  		COD_DEUDOR = {KEY1}
										AND D.COD_COMUNA = C.COD_COMUNA(+)
										AND D.COD_CIUDAD = CC.COD_CIUDAD(+)
										AND D.COD_PAIS = P.COD_PAIS(+)
										AND TIPO_DIRECCION = 'P'
							ORDER BY	FECHA_DIRECCION desc";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 		COD_DIRECCION
								   		, COD_DEUDOR
								   		, convert(varchar(20),FECHA_DIRECCION,103) FECHA_DIRECCION_NO_ING
								   		, FECHA_DIRECCION
								   		, TIPO_DIRECCION
								   		, DIRECCION
								   		, NOM_COMUNA
								   		, NOM_CIUDAD
								   		, NOM_PAIS
								   		, EMAIL
								   		, TELEFONO
								   		, CELULAR
								   		, ES_VALIDA ES_VALIDA_DIR_PAR_HIST
							FROM   		DIRECCION D left outer join COMUNA C on D.COD_COMUNA = C.COD_COMUNA 
											left outer join CIUDAD CC on D.COD_CIUDAD = CC.COD_CIUDAD 
												left outer join PAIS P on D.COD_PAIS = P.COD_PAIS
							WHERE  		COD_DEUDOR = {KEY1}
										AND TIPO_DIRECCION = 'P'
							ORDER BY	FECHA_DIRECCION desc";
				}
				break;	
				
			/////////////////  DW_DIRECCION_COMERCIAL_HISTORICA ///////////////
			case 'DW_DIRECCION_COMERCIAL_HISTORICA' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 		COD_DIRECCION
								   		, COD_DEUDOR
								   		, TO_CHAR(FECHA_DIRECCION,'dd/mm/yyyy') FECHA_DIRECCION_NO_ING
								   		, FECHA_DIRECCION
								   		, DIRECCION
								   		, NOM_COMUNA
								   		, NOM_CIUDAD
								   		, NOM_PAIS
								   		, EMAIL
								   		, TELEFONO
								   		, CELULAR
								   		, ES_VALIDA ES_VALIDA_DIR_COM_HIST
							FROM   		DIRECCION D
										,PAIS P
										,COMUNA C
										,CIUDAD CC
							WHERE  		COD_DEUDOR = {KEY1}
										AND D.COD_COMUNA = C.COD_COMUNA(+)
										AND D.COD_CIUDAD = CC.COD_CIUDAD(+)
										AND D.COD_PAIS = P.COD_PAIS(+)
										AND TIPO_DIRECCION = 'C'
							ORDER BY	FECHA_DIRECCION DESC";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 		COD_DIRECCION
								   		, COD_DEUDOR
								   		, convert(varchar(20),FECHA_DIRECCION,103) FECHA_DIRECCION_NO_ING
								   		, FECHA_DIRECCION
								   		, DIRECCION
								   		, NOM_COMUNA
								   		, NOM_CIUDAD
								   		, NOM_PAIS
								   		, EMAIL
								   		, TELEFONO
								   		, CELULAR
								   		, ES_VALIDA ES_VALIDA_DIR_COM_HIST
							FROM   		DIRECCION D left outer join COMUNA C on D.COD_COMUNA = C.COD_COMUNA
											left outer join CIUDAD CC on D.COD_CIUDAD = CC.COD_CIUDAD
												left outer join PAIS P on D.COD_PAIS = P.COD_PAIS
							WHERE  		COD_DEUDOR = {KEY1}
										AND TIPO_DIRECCION = 'C'
							ORDER BY	FECHA_DIRECCION DESC";
				}
				break;	

			/////////////////  DW_CARRERA_HISTORICA ///////////////
			case 'DW_CARRERA_HISTORICA' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT		COD_MATRICULA
								       	, COD_DEUDOR
								       	, M.COD_UNIVERSIDAD
								       	, NOM_UNIVERSIDAD
								       	, ANO_MATRICULA
								       	, SEMESTRE
								       	, NOM_CARRERA
								       	, COD_CARRERA
			    			FROM		MATRICULA M
			    						, UNIVERSIDAD U
			   				WHERE 		COD_DEUDOR = {KEY1}
			   							AND M.COD_UNIVERSIDAD = U.COD_UNIVERSIDAD(+)
							ORDER BY 	ANO_MATRICULA,SEMESTRE ASC ";
				}
				else if (K_TIPO_BD=="mssql"){
					if(K_CLIENTE =="UTEM"){
					$sql = "SELECT		COD_MATRICULA
								       	,COD_DEUDOR
								       	,M.COD_UNIVERSIDAD
								       	,NOM_UNIVERSIDAD
								       	,ANO_MATRICULA
								       	,SEMESTRE
								       	,NOM_CARRERA
								     	,COD_CARRERA
							FROM		MATRICULA M left outer join UNIVERSIDAD U on M.COD_UNIVERSIDAD = U.COD_UNIVERSIDAD
							WHERE 		COD_DEUDOR = {KEY1}
							ORDER BY 	ANO_MATRICULA ASC";	
					}else{
					$sql = "SELECT		COD_MATRICULA
								       	,COD_DEUDOR
								       	,M.COD_UNIVERSIDAD
								       	,NOM_UNIVERSIDAD
								       	,ANO_MATRICULA
								       	,SEMESTRE
								       	,NOM_CARRERA
								     	,COD_CARRERA
							FROM		MATRICULA M left outer join UNIVERSIDAD U on M.COD_UNIVERSIDAD = U.COD_UNIVERSIDAD
							WHERE 		COD_DEUDOR = {KEY1}
							ORDER BY 	COD_MATRICULA ASC";
					}
				}
				break;		
					
			/////////////////  DW_RENEGOCIACION ///////////////
			case 'DW_RENEGOCIACION' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT		COD_RENEGOCIACION
			       						, FECHA_RENEGOCIACION
								       	, COD_USUARIO
								       	, OBS
								       	, TO_CHAR(FECHA_CARTA1,'dd/mm/yyyy') FECHA_CARTA1
								       	, TO_CHAR(FECHA_CARTA2,'dd/mm/yyyy') FECHA_CARTA2
								       	, TO_CHAR(FECHA_CARTA3,'dd/mm/yyyy') FECHA_CARTA3
								       	, COD_DEUDA
			    			FROM 		RENEGOCIACION
			   				WHERE 		COD_DEUDA IN (	SELECT COD_DEUDA
														FROM 		DEUDA
														WHERE 		COD_DEUDOR = {KEY1}
														)
							ORDER BY 	COD_RENEGOCIACION DESC";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT		COD_RENEGOCIACION
			       						, FECHA_RENEGOCIACION
								       	, COD_USUARIO
								       	, OBS
								       	, convert(varchar(20),FECHA_CARTA1,103) FECHA_CARTA1
								       	, convert(varchar(20),FECHA_CARTA2,103) FECHA_CARTA2
								       	, convert(varchar(20),FECHA_CARTA3,103) FECHA_CARTA3
								       	, COD_DEUDA
			    			FROM 		RENEGOCIACION
			   				WHERE 		COD_DEUDA IN (	SELECT COD_DEUDA
														FROM 		DEUDA
														WHERE 		COD_DEUDOR = {KEY1}
														)
							ORDER BY 	COD_RENEGOCIACION DESC";
				}
				break;		
				
			/////////////////  DW_DECLARACION ///////////////
			case 'DW_DECLARACION' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT		COD_DECLARACION
								     	, FECHA_REGISTRO
								     	, COD_USUARIO
								     	, COD_DEUDOR
								     	, COD_VIA_DECLARACION
								     	, FECHA_CONFIRMA
								     	, COD_USUARIO_CONFIRMA
								     	, TO_CHAR(FECHA_DECLARACION,'dd/mm/yyyy') FECHA_DECLARACION
								     	, (ANO + 1) ANO 
								     	, CON_HIJO
								     	, COD_AFP
								     	, NOM_ESTADO_CIVIL
								     	, RUT_CONYUGE
								     	, DIG_VERIF_CONYUGE
								     	, NOM_CONYUGE
								     	, CONYUGE_ES_DEUDOR
								     	, CONYUGE_DECLARA
								     	, ENTREGA_F22
								     	, f_get_tot_decl_deudor_utm(COD_DECLARACION) TOT_DECL_DEUDOR_UTM
								     	, f_get_tot_decl_conyuge_utm(COD_DECLARACION) TOT_DECL_CONYUGE_UTM
			  				FROM 		DECLARACION D
			  							,ESTADO_CIVIL EC
							WHERE 		COD_DEUDOR = {KEY1}
										AND D.COD_ESTADO_CIVIL = EC.COD_ESTADO_CIVIL
							ORDER BY 	ANO DESC";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT		COD_DECLARACION
								     	, FECHA_REGISTRO
								     	, COD_USUARIO
								     	, COD_DEUDOR
								     	, COD_VIA_DECLARACION
								     	, FECHA_CONFIRMA
								     	, COD_USUARIO_CONFIRMA
								     	, CONVERT(varchar(20),FECHA_DECLARACION,103) FECHA_DECLARACION
								     	, (ANO + 1)ANO
								     	, CON_HIJO
								     	, COD_AFP
								     	, NOM_ESTADO_CIVIL
								     	, RUT_CONYUGE
								     	, DIG_VERIF_CONYUGE
								     	, NOM_CONYUGE
								     	, CONYUGE_ES_DEUDOR
								     	, CONYUGE_DECLARA
								     	, ENTREGA_F22
								     	, dbo.f_get_tot_decl_deudor_utm(COD_DECLARACION) TOT_DECL_DEUDOR_UTM
								     	, dbo.f_get_tot_decl_conyuge_utm(COD_DECLARACION) TOT_DECL_CONYUGE_UTM
			  				FROM 		DECLARACION D
			  							,ESTADO_CIVIL EC
							WHERE 		COD_DEUDOR = {KEY1}
										AND D.COD_ESTADO_CIVIL = EC.COD_ESTADO_CIVIL
							ORDER BY 	ANO DESC";
				}
				break;		
				
			/////////////////  DW_DETALLE_DECLARACION ///////////////
			case 'DW_DETALLE_DECLARACION' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	COD_DECLARACION_MES
			     					, COD_DECLARACION
									, f_get_nom_mes(MES) MES
									, MONTO_DEUDOR_MONEDA
									, MONTO_DEUDOR_PESOS
									, MONTO_CONYUGE_MONEDA
									, MONTO_CONYUGE_PESOS
			  				FROM 	DECLARACION_MES
			    			WHERE  	DECLARACION_MES.COD_DECLARACION = {KEY1}
			    			order by DECLARACION_MES.MES";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_DECLARACION_MES
			     					, COD_DECLARACION
									, dbo.f_get_nom_mes(MES) MES
									, MONTO_DEUDOR_MONEDA
									, MONTO_DEUDOR_PESOS
									, MONTO_CONYUGE_MONEDA
									, MONTO_CONYUGE_PESOS
			  				FROM 	DECLARACION_MES
			    			WHERE  	DECLARACION_MES.COD_DECLARACION = {KEY1}
			    			order by DECLARACION_MES.MES
			    			";
				}
				break;		
				
			/////////////////  DW_DIRECCION_DEUDOR ///////////////
			case 'DW_DIRECCION_DEUDOR' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	COD_DIRECCION 			COD_DIRECCION_{PARAM1}
									, DIRECCION 			DIRECCION_{PARAM1}
									, TO_CHAR(FECHA_DIRECCION,'DD/MM/YYYY')	FECHA_DIRECCION_{PARAM1}
									, COD_COMUNA 			COD_COMUNA_{PARAM1}
	     							, DIR.COD_CIUDAD 		COD_CIUDAD_{PARAM1}
	     							, COD_REGION			COD_REGION_{PARAM1}
									, DIR.COD_PAIS	  		COD_PAIS_{PARAM1}
									, EMAIL	  				EMAIL_{PARAM1}
									, EMAIL	  				EMAIL_{PARAM1}_H
					     			, TELEFONO	  			TELEFONO_{PARAM1}
					     			, CELULAR	  			CELULAR_{PARAM1}
					     			, DIRECCION 			DIRECCION_{PARAM1}_H
	     							, COD_COMUNA			COD_COMUNA_{PARAM1}_H
	     							, DIR.COD_CIUDAD		COD_CIUDAD_{PARAM1}_H
	     							, COD_REGION			COD_REGION_{PARAM1}_H
	     							, DIR.COD_PAIS	  		COD_PAIS_{PARAM1}_H
	     							, TELEFONO	  			TELEFONO_{PARAM1}_H
	     							, CELULAR 				CELULAR_{PARAM1}_H
					     			, ES_VALIDA				ES_VALIDA_{PARAM1}
					     			, ''		 			EXT_COMUNA_{PARAM1}
	     							, ''					EXT_CIUDAD_{PARAM1}
	     							, ''					EXT_REGION_{PARAM1}
	     							,Decode(DIR.COD_PAIS,f_get_constante('K_CHILE'),'','none')  DISPLAY_PAIS_CHILE_{PARAM1}
									,Decode(DIR.COD_PAIS,f_get_constante('K_CHILE'),'none','')  DISPLAY_EXTRANJERO_{PARAM1}
	  						FROM 	DIRECCION   DIR
        							,CIUDAD         C
	 						WHERE 	COD_DEUDOR = {KEY1}
	 								AND DIR.COD_CIUDAD= C.COD_CIUDAD (+)
	   								AND COD_DIRECCION = F_GET_DIRECCION_DEUDOR (COD_DEUDOR, '{PARAM2}')";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_DIRECCION 			COD_DIRECCION_{PARAM1}
									, DIRECCION 			DIRECCION_{PARAM1}
									, convert(varchar(20),FECHA_DIRECCION,103)	FECHA_DIRECCION_{PARAM1}
									, COD_COMUNA 			COD_COMUNA_{PARAM1}
	     							, DIR.COD_CIUDAD 		COD_CIUDAD_{PARAM1}
	     							, COD_REGION			COD_REGION_{PARAM1}
									, DIR.COD_PAIS	  		COD_PAIS_{PARAM1}
									, EMAIL	  				EMAIL_{PARAM1}
									, EMAIL	  				EMAIL_{PARAM1}_H
					     			, TELEFONO	  			TELEFONO_{PARAM1}
					     			, CELULAR	  			CELULAR_{PARAM1}
					     			, ES_VALIDA				ES_VALIDA_{PARAM1}
					     			, EXT_COMUNA 			EXT_COMUNA_{PARAM1}
	     							, EXT_CIUDAD			EXT_CIUDAD_{PARAM1}
	     							, EXT_REGION			EXT_REGION_{PARAM1}
	     							, DIRECCION 			DIRECCION_{PARAM1}_H
	     							, COD_COMUNA			COD_COMUNA_{PARAM1}_H
	     							, DIR.COD_CIUDAD		COD_CIUDAD_{PARAM1}_H
	     							, COD_REGION			COD_REGION_{PARAM1}_H
	     							, DIR.COD_PAIS	  		COD_PAIS_{PARAM1}_H
	     							, TELEFONO	  			TELEFONO_{PARAM1}_H
	     							, CELULAR 				CELULAR_{PARAM1}_H
	     							,case DIR.COD_PAIS
										when dbo.f_get_constante('K_CHILE') then ''
										else 'none'
									end DISPLAY_PAIS_CHILE_{PARAM1}
									,case DIR.COD_PAIS
										when dbo.f_get_constante('K_CHILE') then 'none'
										else ''
									end DISPLAY_EXTRANJERO_{PARAM1}
							FROM 	DIRECCION   DIR LEFT OUTER JOIN CIUDAD C ON DIR.COD_CIUDAD= C.COD_CIUDAD  
        					WHERE 	COD_DEUDOR = {KEY1}
	 								AND COD_DIRECCION = DBO.F_GET_DIRECCION_DEUDOR (COD_DEUDOR, '{PARAM2}')";
				}
				break;		
				
			/////////////////  DD_PAIS ///////////////
			case 'DD_PAIS' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select 		COD_PAIS
										, NOM_PAIS
							from 		PAIS
							order by	NOM_PAIS";
				}
				break;		
				
			/////////////////  DD_REGION ///////////////
			case 'DD_REGION' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 		COD_REGION,
										NOM_REGION
							FROM 		REGION
							WHERE 		COD_PAIS = {KEY1}
							AND      COD_REGION <> 99999
							ORDER BY	COD_REGION";
				}
				break;		
						
				
			/////////////////  DD_CIUDAD ///////////////
			case 'DD_CIUDAD' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 		COD_CIUDAD,
										NOM_CIUDAD
							FROM 		CIUDAD
							WHERE 		COD_REGION = {KEY1}
							AND      COD_CIUDAD <> 99999
							ORDER BY	NOM_CIUDAD";
				}
				break;		
				
			/////////////////  DD_COMUNA ///////////////
			case 'DD_COMUNA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select 		COD_COMUNA,
										NOM_COMUNA
							from 		COMUNA
							where 		COD_CIUDAD = {KEY1}
							order by	NOM_COMUNA";
				}
				break;		
				
			/////////////////  DETALLE_DECLARACION ///////////////
			case 'DETALLE_DECLARACION' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	MONTO_DEUDOR_MONEDA
									, MONTO_DEUDOR_PESOS
									, MONTO_CONYUGE_MONEDA
									, MONTO_CONYUGE_PESOS
					  		FROM 	DECLARACION_MES DM
									,DECLARACION D
					    	WHERE  	DM.COD_DECLARACION = {PARAM1}
					    			AND DM.COD_DECLARACION = D.COD_DECLARACION";
				}
				break;		
				
			/////////////////  DETALLE_DECLARACION2 ///////////////
			case 'DETALLE_DECLARACION2' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	SUM(MONTO_DEUDOR_MONEDA) TOTAL_DEUDOR_MONEDA
									, SUM(MONTO_DEUDOR_PESOS) TOTAL_DEUDOR_PESOS
									, SUM(MONTO_CONYUGE_MONEDA) TOTAL_CONYUGE_MONEDA
									, SUM(MONTO_CONYUGE_PESOS) TOTAL_CONYUGE_PESOS
			  				FROM 	DECLARACION_MES
			    			WHERE  	COD_DECLARACION = {PARAM1}";
				}
				break;	
				/////////////////  FECHA  ///////////////
				case 'FECHA' :
				if (K_TIPO_BD=="oci" ){
					$sql = "SELECT 	TO_CHAR(SYSDATE,'DD/MM/YYYY') FECHA
							FROM 	DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 	convert(varchar(20),getdate(),103) FECHA";
				}
				break;	

				/////////////////  GET_DEUDOR_MOROSO  ///////////////
				case 'GET_DEUDOR_MOROSO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	* 
							FROM 	DEUDOR
							WHERE 	COD_DEUDOR IN ( SELECT COD_DEUDOR DA FROM DEUDA DA
                    								WHERE DA.COD_DEUDA IN (SELECT COD_DEUDA FROM PLAN_PAGO WHERE COD_EMPRESA_COBRANZA IS NOT null))
							AND 	COD_DEUDOR = {PARAM1}";
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

				/////////////////// DOCTO_PROTESTADO ///////////////////////
				case 'DOCTO_PROTESTADO' :	
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT count(*) CANT
							FROM DOCUMENTO_PAGO
							WHERE COD_DEUDOR = {PARAM1}										
								AND COD_ESTADO_DOCUMENTO_PAGO = ".self::K_ESTADO_DOC_PROTESTADO;
				}
				break;
				
	
				////////////////  ERROR ///////////////
			default: 
				base::error("sql_deudor::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}

/////////////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_sql_deudor.php";
if (file_exists($file_name))
    require_once($file_name);
else {
    class sql_deudor extends sql_deudor_base {
    }
}
?>