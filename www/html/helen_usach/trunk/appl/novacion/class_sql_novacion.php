<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_novacion extends sql_tabla {
	static function get_sql($selector_sql) {
		switch ($selector_sql) {
			/////////////////  INPUT ///////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	N.COD_NOVACION
							        ,TO_CHAR(N.FECHA_NOVACION,'dd/mm/yyyy') FECHA_NOVACION
							        ,N.COD_USUARIO
							        ,N.MONTO_SVP 
							        ,N.MONTO_SVP MONTO_SVP_H
							        ,N.MONTO_MORA 
							        ,N.MONTO_MORA MONTO_MORA_H 
							        ,N.MONTO_CP
							        ,N.MONTO_CP MONTO_CP_H
							        ,N.MONTO_LP
							        ,N.MONTO_LP MONTO_LP_H
							        ,N.COD_COMPROBANTE
							        ,N.COD_DEUDA_NUEVA
							        ,N.OBS
							        ,N.COD_ESTADO_NOVACION
							        ,N.COD_USUARIO_CONFIRMA
			                		,UC.NOM_USUARIO NOM_USUARIO_CONFIRMA
							        ,TO_CHAR(N.FECHA_CONFIRMA,'dd/mm/yyyy')FECHA_CONFIRMA
							        ,N.COD_DEUDA_ANTIGUA COD_DEUDA
							        ,N.COD_DEUDA_ANTIGUA COD_DEUDA_H
							        ,U.NOM_USUARIO
							        ,DEUDOR.RUT
							        ,DEUDOR.DIG_VERIF
							        ,DEUDOR.NOMBRE
							        ,DEUDOR.APELLIDO_PATERNO
							        ,DEUDOR.APELLIDO_MATERNO
							        ,DEUDA.COD_TIPO_DEUDA
							        ,DEUDA.CANT_CUOTA
							        ,TO_CHAR(DEUDA.FECHA_INICIO_COBRO,'dd/mm/yyyy') FECHA_INICIO_COBRO
							        ,DEUDA.MONTO_INICIO_COBRO
							        ,M.SIMBOLO
							        ,C.NOM_CARTERA
							        ,ED.NOM_ESTADO_DEUDA
							        ,U.NOM_USUARIO
							        ,CASE 
							        	WHEN DEUDA.CANT_CUOTA > 0
							        		THEN DEUDA.MONTO_INICIO_COBRO / DEUDA.CANT_CUOTA
							        	ELSE 0
							        END CAT		
							        ,f_deuda_saldo_total(DEUDA.COD_DEUDA) * f_mon_get_valor_moneda(TD.COD_MONEDA) TOTAL_PESOS
							        ,NRO_ANOS
							        ,'none' VISIBLE_CAMPO --SOLO USACH
							FROM 	NOVACION N
									,USUARIO U
									,DEUDA
									,DEUDOR
									,TIPO_DEUDA TD
									,MONEDA M
									,CARTERA C
									,ESTADO_DEUDA ED
									,ESTADO_NOVACION EN
									,USUARIO UC
							WHERE 	N.COD_NOVACION = {KEY1}
									AND N.COD_USUARIO = U.COD_USUARIO
									AND N.COD_DEUDA_ANTIGUA = DEUDA.COD_DEUDA
									AND DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR
									AND DEUDA.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
									AND M.COD_MONEDA = TD.COD_MONEDA
									AND C.COD_CARTERA = DEUDA.COD_CARTERA
									AND ED.COD_ESTADO_DEUDA = DEUDA.COD_ESTADO_DEUDA
									AND N.COD_ESTADO_NOVACION = EN.COD_ESTADO_NOVACION
									AND UC.COD_USUARIO(+) = N.COD_USUARIO_CONFIRMA";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 	N.COD_NOVACION
							        ,convert(varchar(20), N.FECHA_NOVACION, 103) FECHA_NOVACION
							        ,N.COD_USUARIO
							        ,N.MONTO_SVP 
							        ,N.MONTO_SVP MONTO_SVP_H
							        ,N.MONTO_MORA 
							        ,N.MONTO_MORA MONTO_MORA_H 
							        ,N.MONTO_CP
							        ,N.MONTO_CP MONTO_CP_H
							        ,N.MONTO_LP
							        ,N.MONTO_LP MONTO_LP_H
							        ,N.COD_COMPROBANTE
							        ,N.COD_DEUDA_NUEVA
							        ,N.OBS
							        ,N.COD_ESTADO_NOVACION
							        ,N.COD_USUARIO_CONFIRMA
			                		,UC.NOM_USUARIO NOM_USUARIO_CONFIRMA
							        ,convert(varchar(20), N.FECHA_CONFIRMA, 103)FECHA_CONFIRMA
							        ,N.COD_DEUDA_ANTIGUA COD_DEUDA
							        ,N.COD_DEUDA_ANTIGUA COD_DEUDA_H
							        ,U.NOM_USUARIO
							        ,DEUDOR.RUT
							        ,DEUDOR.DIG_VERIF
							        ,DEUDOR.NOMBRE
							        ,DEUDOR.APELLIDO_PATERNO
							        ,DEUDOR.APELLIDO_MATERNO
							        ,DEUDA.COD_TIPO_DEUDA
							        ,DEUDA.CANT_CUOTA
							        ,convert(varchar(20), DEUDA.FECHA_INICIO_COBRO, 103) FECHA_INICIO_COBRO
							        ,DEUDA.MONTO_INICIO_COBRO
							        ,M.SIMBOLO
							        ,C.NOM_CARTERA
							        ,ED.NOM_ESTADO_DEUDA
							        ,U.NOM_USUARIO
							        ,CASE 
							        	WHEN DEUDA.CANT_CUOTA > 0
							        		THEN DEUDA.MONTO_INICIO_COBRO / DEUDA.CANT_CUOTA
							        	ELSE 0
							        END CAT		
							        ,dbo.f_deuda_saldo_total(DEUDA.COD_DEUDA, getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()) TOTAL_PESOS
							        ,NRO_ANOS
							FROM 	NOVACION N left outer join USUARIO UC on N.COD_USUARIO_CONFIRMA = UC.COD_USUARIO
									,USUARIO U
									,DEUDA
									,DEUDOR
									,TIPO_DEUDA TD
									,MONEDA M
									,CARTERA C
									,ESTADO_DEUDA ED
									,ESTADO_NOVACION EN
							WHERE 	N.COD_NOVACION = {KEY1}
									AND N.COD_USUARIO = U.COD_USUARIO
									AND N.COD_DEUDA_ANTIGUA = DEUDA.COD_DEUDA
									AND DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR
									AND DEUDA.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
									AND M.COD_MONEDA = TD.COD_MONEDA
									AND C.COD_CARTERA = DEUDA.COD_CARTERA
									AND ED.COD_ESTADO_DEUDA = DEUDA.COD_ESTADO_DEUDA
									AND N.COD_ESTADO_NOVACION = EN.COD_ESTADO_NOVACION";
				}
				break;
				
			/////////////////  OUTPUT ///////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	N.COD_NOVACION
					               	,TO_CHAR(N.FECHA_NOVACION,'dd/mm/yyyy') FECHA_NOVACION
					               	,N.COD_DEUDA_ANTIGUA
					               	,N.COD_DEUDA_NUEVA
					               	,DEUDOR.RUT
					               	,DEUDOR.DIG_VERIF
					               	,DEUDOR.NOMBRE
					               	,DEUDOR.APELLIDO_PATERNO
					               	,DEUDOR.APELLIDO_MATERNO
					               	,N.COD_ESTADO_NOVACION
					               	,EN.NOM_ESTADO_NOVACION 
					          FROM 	NOVACION N LEFT OUTER JOIN ESTADO_NOVACION EN ON N.COD_ESTADO_NOVACION = EN.COD_ESTADO_NOVACION,
					          		DEUDA DEUDA, DEUDOR DEUDOR
					         WHERE 	N.COD_DEUDA_ANTIGUA = DEUDA.COD_DEUDA AND
					               	DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR 
					               	ORDER BY COD_NOVACION DESC";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 	N.COD_NOVACION
					               	,CONVERT(VARCHAR(20),N.FECHA_NOVACION,103) FECHA_NOVACION
					               	,N.COD_DEUDA_ANTIGUA
					               	,N.COD_DEUDA_NUEVA
					               	,DEUDOR.RUT
					               	,DEUDOR.DIG_VERIF
					               	,DEUDOR.NOMBRE
					               	,DEUDOR.APELLIDO_PATERNO
					               	,DEUDOR.APELLIDO_MATERNO
					               	,N.COD_ESTADO_NOVACION
					               	,EN.NOM_ESTADO_NOVACION 
					          FROM 	NOVACION N LEFT OUTER JOIN ESTADO_NOVACION EN ON N.COD_ESTADO_NOVACION = EN.COD_ESTADO_NOVACION,
					          		DEUDA DEUDA, DEUDOR DEUDOR
					         WHERE 	N.COD_DEUDA_ANTIGUA = DEUDA.COD_DEUDA AND
					               	DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR 
					               	ORDER BY COD_NOVACION DESC";
				}
				break;
				
			/////////////////  INPUT_DD ///////////////
			case 'INPUT_DD' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select 		COD_ESTADO_NOVACION
										,NOM_ESTADO_NOVACION
							from 		ESTADO_NOVACION
							order by 	COD_ESTADO_NOVACION";
				}
				 break;
				
			/////////////////  AJAX_EXISTE_DEUDOR ///////////////
			case 'AJAX_EXISTE_DEUDOR' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	count(*) CUANTO
							FROM 	DEUDOR
							WHERE	RUT = {PARAM1}";
				}
				break;	

			/////////////////  AJAX_TIPO_DEUDA ///////////////
			case 'AJAX_TIPO_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	count(*) COUNT_TIPO_DEUDA
   							FROM 	DEUDA, DEUDOR
   							WHERE 	DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR
          					AND 	COD_TIPO_DEUDA = '{PARAM2}' 
          					AND 	RUT = {PARAM1}";
				}
				break;				
				
			/////////////////  AJAX_COD_DEUDOR ///////////////
			case 'AJAX_ESTADO_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	count(*)CANT
	    					FROM 	DEUDA DE, DEUDOR DO
    						WHERE 	DE.COD_DEUDOR = DO.COD_DEUDOR
        	  				AND 	DE.COD_ESTADO_DEUDA IN ({PARAM2},{PARAM3})
        	  				AND 	DO.RUT = {PARAM1}";
				}
				break;
				
			/////////////////  AJAX_UNA_DEUDA_NOVADA ///////////////
			case 'AJAX_UNA_DEUDA_NOVADA' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	DIG_VERIF
									,NOMBRE || ' ' || APELLIDO_PATERNO || ' ' ||APELLIDO_MATERNO  NOMBRE
									,DE.COD_DEUDA
									,DE.COD_TIPO_DEUDA
									,M.SIMBOLO
									,C.NOM_CARTERA
									,ED.NOM_ESTADO_DEUDA
									,DE.CANT_CUOTA
									,TO_CHAR(DE.FECHA_INICIO_COBRO,'dd/mm/yyyy') FECHA_INICIO_COBRO
									,DE.MONTO_INICIO_COBRO
									,CASE
										WHEN DE.CANT_CUOTA > 0
											THEN DE.MONTO_INICIO_COBRO / DE.CANT_CUOTA
									   	ELSE 0
									END CAT		
									,f_deuda_saldo_componente(DE.COD_DEUDA, {PARAM4}) * f_mon_get_valor_moneda(TD.COD_MONEDA) MONTO_SVP				  
									,f_deuda_saldo_componente(DE.COD_DEUDA, {PARAM5}) * f_mon_get_valor_moneda(TD.COD_MONEDA) MONTO_MORA				  
									,f_deuda_saldo_componente(DE.COD_DEUDA, {PARAM6}) * f_mon_get_valor_moneda(TD.COD_MONEDA) MONTO_CP				  
									,f_deuda_saldo_componente(DE.COD_DEUDA, {PARAM7}) * f_mon_get_valor_moneda(TD.COD_MONEDA) MONTO_LP
									,f_deuda_saldo_total(DE.COD_DEUDA) * f_mon_get_valor_moneda(TD.COD_MONEDA) TOTAL_PESOS
							FROM 	DEUDOR DO, DEUDA DE, TIPO_DEUDA TD, MONEDA M, CARTERA C, ESTADO_DEUDA ED
							WHERE 	DO.RUT={PARAM1}
							AND		DE.COD_DEUDA = {PARAM2}
							AND		DE.COD_DEUDOR = DO.COD_DEUDOR
							AND		DE.COD_CARTERA = C.COD_CARTERA
							AND		DE.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA
							AND		DE.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
							AND		TD.COD_MONEDA = M.COD_MONEDA";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 	DIG_VERIF
		   							,NOMBRE + ' ' + APELLIDO_PATERNO + ' ' + APELLIDO_MATERNO  NOMBRE
		   							,DE.COD_DEUDA
		   							,DE.COD_TIPO_DEUDA
		   							,M.SIMBOLO
		   							,C.NOM_CARTERA
		   							,ED.NOM_ESTADO_DEUDA
		   							,DE.CANT_CUOTA
		   							,convert(varchar(20), DE.FECHA_INICIO_COBRO, 103) FECHA_INICIO_COBRO
		   							,DE.MONTO_INICIO_COBRO
		   							,CASE 
			        					WHEN DE.CANT_CUOTA > 0
			        						THEN DE.MONTO_INICIO_COBRO / DE.CANT_CUOTA
			  					      	ELSE 0
			        				END CAT		
		   							,dbo.f_deuda_saldo_componente(DE.COD_DEUDA, {PARAM4}, getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()) MONTO_SVP				  
   									,dbo.f_deuda_saldo_componente(DE.COD_DEUDA, {PARAM5}, getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()) MONTO_MORA				  
   									,dbo.f_deuda_saldo_componente(DE.COD_DEUDA, {PARAM6}, getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()) MONTO_CP				  
   									,dbo.f_deuda_saldo_componente(DE.COD_DEUDA, {PARAM7}, getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()) MONTO_LP
   									,dbo.f_deuda_saldo_total(DE.COD_DEUDA, getdate()) * dbo.f_mon_get_valor_moneda(TD.COD_MONEDA, getdate()) TOTAL_PESOS
							FROM	DEUDOR DO, DEUDA DE, TIPO_DEUDA TD, MONEDA M, CARTERA C, ESTADO_DEUDA ED
							WHERE	DO.RUT = {PARAM1}
							AND		DE.COD_DEUDA = {PARAM2}
							AND 	DE.COD_DEUDOR = DO.COD_DEUDOR
							AND 	DE.COD_CARTERA = C.COD_CARTERA
							AND 	DE.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA
							AND 	DE.COD_TIPO_DEUDA = '{PARAM3}'
							AND 	DE.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
							AND 	TD.COD_MONEDA = M.COD_MONEDA";
				}
				break;
				
				/////////////////  AJAX_NOVAR_DEUDA ///////////////
				case 'AJAX_NOVAR_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT  COD_DEUDA FROM DEUDOR DO, DEUDA DE
							WHERE DE.COD_DEUDOR = DO.COD_DEUDOR
							AND DE.COD_TIPO_DEUDA = '{PARAM2}'
							AND DO.RUT = {PARAM1}
							AND DE.COD_DEUDA NOT IN (SELECT N.COD_DEUDA_ANTIGUA FROM NOVACION N, DEUDA DE
													WHERE DE.COD_DEUDOR = DO.COD_DEUDOR
													AND DE.COD_DEUDA = N.COD_DEUDA_ANTIGUA
													AND N.COD_ESTADO_NOVACION IN ({PARAM3},{PARAM4}))";
				}
				break;
				
				
				/////////////////  DD_ESTADO_NOVACION ///////////////
				case 'DD_ESTADO_NOVACION' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 		COD_ESTADO_NOVACION 
										,NOM_ESTADO_NOVACION 
							FROM 		ESTADO_NOVACION 
							ORDER BY 	COD_ESTADO_NOVACION";
				}
				break;

				//////////////////////////////////NRO_ANOS_DD//////////////////////////////////
				case 'NRO_ANOS_DD' :
					if (K_TIPO_BD=="oci"){
						$sql = "SELECT 1 FROM dual UNION
					            SELECT 2 FROM dual UNION
					            SELECT 3 FROM dual UNION
					            SELECT 4 FROM dual UNION
					            SELECT 5 FROM dual UNION
					            SELECT 6 FROM dual UNION
					            SELECT 7 FROM dual UNION
					            SELECT 8 FROM dual UNION
					            SELECT 9 FROM dual UNION
					            SELECT 10 FROM dual";						
					}
					else if (K_TIPO_BD=="mssql"){
						$sql = "SELECT 1, 1 	NRO_ANOS UNION
								SELECT 2, 2 	NRO_ANOS UNION
								SELECT 3, 3  	NRO_ANOS UNION
								SELECT 4, 4 	NRO_ANOS UNION
								SELECT 5, 5 	NRO_ANOS UNION
								SELECT 6, 6 	NRO_ANOS UNION
								SELECT 7, 7 	NRO_ANOS UNION
								SELECT 8, 8 	NRO_ANOS UNION
								SELECT 9, 9 	NRO_ANOS UNION
								SELECT 10, 10	NRO_ANOS
								ORDER BY 		NRO_ANOS";
					}
				break;
				///////////////////print//////////////////////
			case 'PRINT_RECORD' :
				if (K_TIPO_BD=="oci"){
					
					$sql=	"SELECT N.COD_NOVACION
									, P.COD_PAGARE
									, P.FOLIO_PAGARE
									, TO_CHAR(P.FECHA_PAGARE,'dd/mm/yyyy') FECHA_PAGARE
									, DD.RUT
									, DD.DIG_VERIF		
									, DD.NOMBRE||' '||DD.APELLIDO_PATERNO||' '||DD.APELLIDO_MATERNO  NOMBRE
									, P.COD_DEUDA
									, TD.NOM_TIPO_DEUDA
									, TD.COD_TIPO_DEUDA
									, P.MONTO_MONEDA
									, P.MONTO_PESOS
									, EP.NOM_ESTADO_PAGARE
									, D.CANT_CUOTA
									, NRO_ANOS
				                    , TD.TASA_INTERES_CREDITO
				                    , TD.TASA_INTERES_PENAL
				                    , replace(To_Number (PP.MONTO_CAPITAL+PP.MONTO_INTERES),'.',',')   MONTO_CUOTA
		                  			, SUBSTR(DIR.DIRECCION||', '||CO.NOM_COMUNA||','||CI.NOM_CIUDAD,1,60) DIRECCION
		                  			, DIR.TELEFONO		                  			
		                  			, replace(f_deuda_saldo_total(n.COD_DEUDA_NUEVA,D.FECHA_INICIO_COBRO),'.',',')  SALDO_UTM		                  			
		                  			, To_Number(To_Char(D.FECHA_INICIO_COBRO, 'yyyy')-1) ANO
                              		,To_Char(D.FECHA_INICIO_COBRO, 'yyyy') ANO2
					                ,D.MONTO_INICIO_COBRO
							        ,'{PARAM2}' ENTERO_EN_PALABRAS
							        ,(SELECT CA.NOM_CARRERA FROM CARRERA CA WHERE CA.COD_CARRERA = f_deudor_carrera(DD.COD_DEUDOR)) NOM_CARRERA
							        , replace({PARAM3},'.',',') MONTO_PAGARE
							        , replace({PARAM4},'.',',') MONTO_MONEDA 
							FROM	NOVACION N		
								,DEUDA D
								,DEUDOR DD LEFT OUTER JOIN DIRECCION DIR ON DIR.COD_DEUDOR  =  DD.COD_DEUDOR AND   DIR.ES_VALIDA   =   'S'
											LEFT OUTER JOIN COMUNA CO ON CO.COD_COMUNA = DIR.COD_COMUNA
											LEFT OUTER JOIN CIUDAD CI ON CI.COD_CIUDAD = DIR.COD_CIUDAD
								,TIPO_DEUDA TD
								,PAGARE P LEFT OUTER JOIN ESTADO_PAGARE EP ON EP.COD_ESTADO_PAGARE = P.COD_ESTADO_PAGARE
								,PLAN_PAGO PP
							WHERE	N.COD_NOVACION = {PARAM1}
								AND	D.COD_DEUDA = N.COD_DEUDA_NUEVA
								AND	DD.COD_DEUDOR = D.COD_DEUDOR
								AND   P.COD_DEUDA = D.COD_DEUDA
								AND TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA
								AND  PP.COD_DEUDA = D.COD_DEUDA
								AND   PP.NRO_CUOTA  =   1";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql="SELECT N.COD_NOVACION
									, P.COD_PAGARE
									, P.FOLIO_PAGARE
									, convert(varchar(20), P.FECHA_PAGARE, 103) FECHA_PAGARE
									, day(P.FECHA_PAGARE) DIA_PAGARE
									, dbo.f_get_nom_mes(month(P.FECHA_PAGARE)) MES_PAGARE
									, year(P.FECHA_PAGARE) ANO_PAGARE
									, day(GETDATE()) DIA_IMPRESION
									, dbo.f_get_nom_mes(month(GETDATE())) MES_IMPRESION
									, year(GETDATE()) ANO_IMPRESION
									, dbo.f_get_parametro(12) CIUDAD_UNIVERSIDAD
									, DD.RUT
									, DD.DIG_VERIF		
									, DD.NOMBRE + ' ' + DD.APELLIDO_PATERNO + ' ' + DD.APELLIDO_MATERNO  NOMBRE
									, P.COD_DEUDA
									, TD.NOM_TIPO_DEUDA
									, TD.COD_TIPO_DEUDA
									, P.MONTO_PESOS
									, EP.NOM_ESTADO_PAGARE
									, D.CANT_CUOTA
									, NRO_ANOS
				                    , TD.TASA_INTERES_CREDITO
				                    , TD.TASA_INTERES_PENAL
				                    , replace((PP.MONTO_CAPITAL + PP.MONTO_INTERES),'.',',')   MONTO_CUOTA
		                  			, SUBSTRING(DIR.DIRECCION + ', ' + CO.NOM_COMUNA + ',' + CI.NOM_CIUDAD, 1, 60) DIRECCION
		                  			, DIR.TELEFONO		                  			
		                  			, replace(dbo.f_deuda_saldo_total(N.COD_DEUDA_NUEVA,D.FECHA_INICIO_COBRO),'.',',')  SALDO_UTM		                  			
		                  			, convert(numeric,(convert(varchar(4), datepart(year, D.FECHA_INICIO_COBRO))-1)) ANO 
                              		, convert(varchar(4),datepart(year, D.FECHA_INICIO_COBRO)) ANO2
					                , D.MONTO_INICIO_COBRO
							        , '{PARAM2}' ENTERO_EN_PALABRAS
							        , (SELECT CA.NOM_CARRERA FROM CARRERA CA WHERE CA.COD_CARRERA = dbo.f_deudor_carrera(DD.COD_DEUDOR)) NOM_CARRERA
									, replace({PARAM3},'.',',') MONTO_PAGARE
							        , replace({PARAM4},'.',',') MONTO_MONEDA 
							FROM	PAGARE P
								,NOVACION N		
								,DEUDA D
								,DEUDOR DD LEFT OUTER JOIN DIRECCION DIR ON DIR.COD_DEUDOR  =  DD.COD_DEUDOR AND   DIR.ES_VALIDA   =   'S'
											LEFT OUTER JOIN COMUNA CO ON CO.COD_COMUNA = DIR.COD_COMUNA
											LEFT OUTER JOIN CIUDAD CI ON CI.COD_CIUDAD = DIR.COD_CIUDAD
								,TIPO_DEUDA TD
								,ESTADO_PAGARE EP
								,PLAN_PAGO PP
							WHERE	N.COD_NOVACION = {PARAM1}
								AND	D.COD_DEUDA = N.COD_DEUDA_NUEVA
								AND	DD.COD_DEUDOR = D.COD_DEUDOR
								AND   P.COD_DEUDA = D.COD_DEUDA
								AND TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA 
								AND EP.COD_ESTADO_PAGARE = P.COD_ESTADO_PAGARE 
								AND  PP.COD_DEUDA = D.COD_DEUDA
								AND   PP.NRO_CUOTA  =   1";
				}
			break;	
					
			//////////////// PRINT_PARAM ////////////////
			case 'PRINT_PARAM' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	f_get_parametro(5) NOM_UNIVERSIDAD
					                ,f_get_parametro(13) MAIL_UNIVERSIDAD
					                ,f_get_parametro(9) DIR_UNIVERSIDAD
					                ,f_get_parametro(10) FONO_UNIVERSIDAD
					                ,f_get_parametro(15) FAX_UNIVERSIDAD
							FROM 	DUAL";
				}else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	dbo.f_get_parametro(5) NOM_UNIVERSIDAD
					                ,dbo.f_get_parametro(13) MAIL_UNIVERSIDAD
					                ,dbo.f_get_parametro(9) DIR_UNIVERSIDAD
					                ,dbo.f_get_parametro(10) FONO_UNIVERSIDAD
					                ,dbo.f_get_parametro(15) FAX_UNIVERSIDAD";
				}
			break;
			
			case 'TOTAL_UTM' :
				if (K_TIPO_BD=="oci"){
					$sql=	"SELECT	 distinct REPLACE(f_deuda_saldo_total(n.cod_deuda_nueva,D.FECHA_INICIO_COBRO),',','.') SALDO_TOTAL
								    ,(PP.MONTO_CAPITAL + PP.MONTO_INTERES) MONTO_PAGARE
									,P.MONTO_MONEDA
							FROM 	NOVACION  N, DEUDA D, PLAN_PAGO PP, PAGARE P
							WHERE 	N.COD_NOVACION = {PARAM1}
							  AND	N.COD_DEUDA_NUEVA = D.COD_DEUDA
							  AND	N.COD_DEUDA_NUEVA = PP.COD_DEUDA
							  AND	N.COD_DEUDA_NUEVA = P.COD_DEUDA";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql=	"SELECT	distinct dbo.f_deuda_saldo_total(n.cod_deuda_nueva,D.FECHA_INICIO_COBRO)  SALDO_TOTAL
									, (PP.MONTO_CAPITAL + PP.MONTO_INTERES) MONTO_PAGARE
									, P.MONTO_MONEDA
							FROM 	NOVACION  N, DEUDA D, PLAN_PAGO PP, PAGARE P
							WHERE 	N.COD_NOVACION = {PARAM1}
							  AND	N.COD_DEUDA_NUEVA = D.COD_DEUDA
							  AND	N.COD_DEUDA_NUEVA = PP.COD_DEUDA
							  AND	N.COD_DEUDA_NUEVA = P.COD_DEUDA";
				}
			break;	
			
			//////////////////////////		DEUDA_CARTERA_DIFERENTE		//////////////////////
			case 'DEUDA_CARTERA_DIFERENTE' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = 	"SELECT COUNT(*) CANT
								FROM DEUDOR DR, DEUDA DA 
								WHERE DR.COD_DEUDOR = DA.COD_DEUDOR 
								AND DA.COD_CARTERA  <> {PARAM1}
								AND DR.RUT = {PARAM2}
								AND COD_TIPO_DEUDA IN ('CS', 'CSR')";
				}
			break;
			
			/////////////////////////PRINT_NOVACION//////////////////////
			case 'PRINT_NOVACION':
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT N.COD_NOVACION
					      		,D.RUT
					      		,D.DIG_VERIF
					      		,D.NOMBRE
					      		,D.APELLIDO_PATERNO
					      		,D.APELLIDO_MATERNO
	              		  		,TD.TASA_INTERES_CREDITO
	                      		,TD.TASA_INTERES_PENAL
					      		,DI.DIRECCION
	                      		,CO.NOM_COMUNA
					      		,DI.TELEFONO
					      		,U.NOM_UNIVERSIDAD
	                      		,P.COD_UNIVERSIDAD
					      		,To_Char(DN.FECHA_INICIO_COBRO - 1, 'dd') DIA_CONSOLIDA
					      		,M.NOM_MES MES_CONSOLIDA
					      		,To_Char(DN.FECHA_INICIO_COBRO - 1, 'yyyy') ANO_CONSOLIDA
					      		,P.MONTO_MONEDA
					      		,NULL MONTO_MONEDA_PALABRAS
					      		,DN.CANT_CUOTA
					      		,To_Char(DN.FECHA_INICIO_COBRO, 'yyyy') ANO_INICIO
					      		,(SELECT MONTO_CAPITAL + MONTO_INTERES FROM PLAN_PAGO PP WHERE PP.COD_DEUDA = DN.COD_DEUDA AND ROWNUM = 1) MONTO_CUOTA
							from NOVACION N, DEUDA DA, DEUDOR D, DEUDA DN, PAGARE P, UNIVERSIDAD U, TIPO_DEUDA TD, DIRECCION DI, COMUNA CO
								WHERE N.COD_NOVACION = {PARAM1}
					  			AND DA.COD_DEUDA = N.COD_DEUDA_ANTIGUA
					  			AND D.COD_DEUDOR = DA.COD_DEUDOR
					  			AND DN.COD_DEUDA = N.COD_DEUDA_NUEVA
					  			AND P.COD_DEUDA = DN.COD_DEUDA
	                  			AND TD.COD_TIPO_DEUDA = DN.COD_TIPO_DEUDA
					  			AND U.COD_UNIVERSIDAD = P.COD_UNIVERSIDAD
	                  			AND D.COD_DEUDOR = DI.COD_DEUDOR
	                  			AND DI.COD_COMUNA = CO.COD_COMUNA
	                  			AND DI.ES_VALIDA = 'S'";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT N.COD_NOVACION
						      ,D.RUT
						      ,D.DIG_VERIF
						      ,D.NOMBRE
						      ,D.APELLIDO_PATERNO
						      ,D.APELLIDO_MATERNO
		              		  ,TD.TASA_INTERES_CREDITO
		                      ,TD.TASA_INTERES_PENAL
						      ,DI.DIRECCION
		                      ,CO.NOM_COMUNA
						      ,DI.TELEFONO
						      ,U.NOM_UNIVERSIDAD
		                      ,P.COD_UNIVERSIDAD
						      ,CONVERT(VARCHAR(2),DN.FECHA_INICIO_COBRO,103) DIA_CONSOLIDA
						      --,M.NOM_MES MES_CONSOLIDA
						      ,CONVERT(VARCHAR(4), DATEPART(YEAR, DN.FECHA_INICIO_COBRO)) ANO_CONSOLIDA
						      ,P.MONTO_MONEDA
						      ,NULL MONTO_MONEDA_PALABRAS
						      ,DN.CANT_CUOTA
						      ,CONVERT(VARCHAR(4), DATEPART(YEAR, DN.FECHA_INICIO_COBRO)) ANO_INICIO
						      ,(SELECT TOP 1 MONTO_CAPITAL + MONTO_INTERES FROM PLAN_PAGO PP WHERE PP.COD_DEUDA = DN.COD_DEUDA) MONTO_CUOTA
						      ,FOLIO_PAGARE
							FROM NOVACION N, DEUDA DA, DEUDOR D LEFT OUTER JOIN DIRECCION DI LEFT OUTER JOIN COMUNA CO ON DI.COD_COMUNA = CO.COD_COMUNA ON D.COD_DEUDOR = DI.COD_DEUDOR AND DI.ES_VALIDA = 'S'
							, DEUDA DN
							,PAGARE P LEFT OUTER JOIN UNIVERSIDAD U ON U.COD_UNIVERSIDAD = P.COD_UNIVERSIDAD, TIPO_DEUDA TD
							WHERE N.COD_NOVACION = {PARAM1}
								AND DA.COD_DEUDA = N.COD_DEUDA_ANTIGUA
								AND D.COD_DEUDOR = DA.COD_DEUDOR
								AND DN.COD_DEUDA = N.COD_DEUDA_NUEVA
								AND P.COD_DEUDA = DN.COD_DEUDA
								AND TD.COD_TIPO_DEUDA = DN.COD_TIPO_DEUDA";
			}
			break;
			
			/////////////////  ERROR ///////////////
			default: 
				base::error("sql_novacion::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>