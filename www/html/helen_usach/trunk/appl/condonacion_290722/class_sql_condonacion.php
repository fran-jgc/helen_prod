<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_condonacion extends sql_tabla {
	/////////////	DECLARACION DE CONSTANTES		/////////////////////////
	const K_ESTADO_CONDONACION_SOLICITADA	= 1;
	const K_ESTADO_CONDONACION_APROBADA		= 2;
	const K_TIPO_CONDONACION_UNO			= 1;
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			
			//////////////////		INPUT		//////////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= 	"SELECT     COD_CONDONACION
				            			,TO_CHAR(FECHA_REGISTRO,'dd/mm/yyyy')FECHA_REGISTRO
				    					,U.NOM_USUARIO
				    					,C.COD_USUARIO
				    					,D.RUT
				    					,D.DIG_VERIF
						   	 			,D.NOMBRE  || ' ' || D.APELLIDO_PATERNO  || ' ' || D.APELLIDO_MATERNO NOMBRE
						    			,D.COD_DEUDOR
						    			,COD_USUARIO_CONFIRMA
						    			,UC.NOM_USUARIO NOM_USUARIO_CONFIRMA
						    			,COD_TIPO_CONDONACION
						    			,TO_CHAR (FECHA_CONFIRMA, 'dd/mm/yyyy') FECHA_CONFIRMA_NO_ING
						    			,EC.COD_ESTADO_CONDONACION
				    	    			,C.NRO_DECRETO
						    			,c.OBS	
				    	    			,'none' VISIBLE_CAMPO	 -- SOLO_USACH	
				    	    			, TO_CHAR(C.FECHA_SOLICITUD_TRAMITE,'dd/mm/yyyy') FECHA_SOLICITUD_TRAMITE
                                        , FOLIO_CONDONACION			    
							FROM        CONDONACION C
										, DEUDOR  D
										, USUARIO U
										, USUARIO UC
										, ESTADO_CONDONACION EC
							WHERE       C.COD_CONDONACION = {KEY1}
										AND C.COD_DEUDOR=D.COD_DEUDOR
				            			AND U.COD_USUARIO = C.COD_USUARIO
				            			AND C.COD_ESTADO_CONDONACION = EC.COD_ESTADO_CONDONACION
				            			AND C.COD_USUARIO_CONFIRMA = UC.COD_USUARIO(+)
							ORDER BY    COD_CONDONACION DESC";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT     COD_CONDONACION
				            			,convert(varchar(20), FECHA_REGISTRO, 103)FECHA_REGISTRO
				    					,U.NOM_USUARIO
				    					,C.COD_USUARIO
				    					,D.RUT
				    					,D.DIG_VERIF
						   	 			,D.NOMBRE  + ' ' + D.APELLIDO_PATERNO  + ' ' + D.APELLIDO_MATERNO NOMBRE
						    			,D.COD_DEUDOR
						    			,COD_USUARIO_CONFIRMA
						    			,UC.NOM_USUARIO NOM_USUARIO_CONFIRMA
						    			,COD_TIPO_CONDONACION
						    			,convert(varchar(20), FECHA_CONFIRMA, 103) FECHA_CONFIRMA_NO_ING
						    			,EC.COD_ESTADO_CONDONACION
				    	    			,C.NRO_DECRETO
						    			,c.OBS	
						    			,convert(varchar(20), C.FECHA_SOLICITUD_TRAMITE, 103) FECHA_SOLICITUD_TRAMITE					    
							FROM        CONDONACION C left outer join USUARIO UC on C.COD_USUARIO_CONFIRMA = UC.COD_USUARIO
										, DEUDOR  D
										, USUARIO U
										, ESTADO_CONDONACION EC
							WHERE       C.COD_CONDONACION = {KEY1}
										AND C.COD_DEUDOR=D.COD_DEUDOR
				            			AND U.COD_USUARIO = C.COD_USUARIO
				            			AND C.COD_ESTADO_CONDONACION = EC.COD_ESTADO_CONDONACION
							ORDER BY    COD_CONDONACION DESC";
				}
			break;
			
			//////////////////		INPUT_DETALLE_DEUDA		//////////////////////
			case 'INPUT_DETALLE_DEUDA' :
				if (K_TIPO_BD=="oci"){
					$sql= 	"SELECT		'S' SELECCION -- SOLO USACH
										,DC.COD_DEUDA_CONDONACION COD_DEUDA_CONDONACION_H
										,D.COD_DEUDA
										,D.COD_DEUDA COD_DEUDA_H
										,DC.COD_CONDONACION
										,TP.COD_TIPO_DEUDA TIPO_DEUDA 
										,D.COD_TIPO_DEUDA
										,MONTO_SVP NO_EXIGIBLE
										,MONTO_MORA VENCIDA
										,MONTO_CP VIGENTE
										,MONTO_LP FUTURA
										,(MONTO_SVP + MONTO_MORA + MONTO_CP + MONTO_LP) TOTAL		
										,DC.COD_COMPROBANTE							
										,MONTO_SVP NO_EXIGIBLE_H
										,MONTO_MORA VENCIDA_H
										,MONTO_CP VIGENTE_H
										,MONTO_LP FUTURA_H											
							FROM  		DEUDA D
										, CONDONACION C
										, DEUDA_CONDONACION DC
										, COMPROBANTE C
										, TIPO_DEUDA TP				
							WHERE  		C.COD_CONDONACION = DC.COD_CONDONACION
										AND DC.COD_DEUDA = D.COD_DEUDA 
										AND DC.COD_COMPROBANTE = C.COD_COMPROBANTE(+)
										AND C.COD_CONDONACION = {KEY1}
										AND TP.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA
							ORDER BY 	D.COD_DEUDA ASC";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT		DC.COD_DEUDA_CONDONACION COD_DEUDA_CONDONACION_H
										,D.COD_DEUDA
										,D.COD_DEUDA COD_DEUDA_H
										,DC.COD_CONDONACION
										,TP.COD_TIPO_DEUDA TIPO_DEUDA 
										,D.COD_TIPO_DEUDA
										,MONTO_SVP NO_EXIGIBLE
										,MONTO_MORA VENCIDA
										,MONTO_CP VIGENTE
										,MONTO_LP FUTURA
										,(MONTO_SVP + MONTO_MORA + MONTO_CP + MONTO_LP) TOTAL		
										,DC.COD_COMPROBANTE							
										,MONTO_SVP NO_EXIGIBLE_H
										,MONTO_MORA VENCIDA_H
										,MONTO_CP VIGENTE_H
										,MONTO_LP FUTURA_H											
							FROM  		DEUDA D
										, CONDONACION CN
										, DEUDA_CONDONACION DC left outer join COMPROBANTE C on DC.COD_COMPROBANTE = C.COD_COMPROBANTE
										, TIPO_DEUDA TP				
							WHERE  		CN.COD_CONDONACION = DC.COD_CONDONACION
										AND DC.COD_DEUDA = D.COD_DEUDA 
										AND CN.COD_CONDONACION = {KEY1}
										AND TP.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA
							ORDER BY 	D.COD_DEUDA ASC";
				}
			break;
			
			/////////////////////		OUTPUT		///////////////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= 	"SELECT		COD_CONDONACION
										, TO_CHAR(FECHA_REGISTRO,'dd/mm/yyyy') FECHA_REGISTRO
										, NRO_DECRETO
										, NOM_TIPO_CONDONACION 
										, D.RUT
										, D.DIG_VERIF
										, D.NOMBRE
										, D.APELLIDO_PATERNO
										, D.APELLIDO_MATERNO
                    					, C.COD_ESTADO_CONDONACION
                    					, EC.NOM_ESTADO_CONDONACION
							FROM 		CONDONACION C, DEUDOR  D, TIPO_CONDONACION TC, ESTADO_CONDONACION EC
							WHERE   	C.COD_DEUDOR=D.COD_DEUDOR AND
										C.COD_TIPO_CONDONACION = TC.COD_TIPO_CONDONACION AND
										C.COD_ESTADO_CONDONACION = EC.COD_ESTADO_CONDONACION
							ORDER BY 	COD_CONDONACION DESC";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT		COD_CONDONACION
										, CONVERT(VARCHAR(20),FECHA_REGISTRO,103) FECHA_REGISTRO
										, NRO_DECRETO
										, NOM_TIPO_CONDONACION 
										, D.RUT
										, D.DIG_VERIF
										, D.NOMBRE
										, D.APELLIDO_PATERNO
										, D.APELLIDO_MATERNO
                    					, C.COD_ESTADO_CONDONACION
                    					, EC.NOM_ESTADO_CONDONACION
							FROM 		CONDONACION C, DEUDOR  D, TIPO_CONDONACION TC, ESTADO_CONDONACION EC
							WHERE   	C.COD_DEUDOR=D.COD_DEUDOR AND
										C.COD_TIPO_CONDONACION = TC.COD_TIPO_CONDONACION AND
										C.COD_ESTADO_CONDONACION = EC.COD_ESTADO_CONDONACION
							ORDER BY 	COD_CONDONACION DESC";
				}
			break;
			
			////////////////////////		DD_TIPO_CONDONACION		///////////////////////
			case 'DD_TIPO_CONDONACION' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= 	"SELECT 	COD_TIPO_CONDONACION,
										NOM_TIPO_CONDONACION 
							FROM 		TIPO_CONDONACION 
							ORDER BY 	NOM_TIPO_CONDONACION";
				}
			break;
			
			////////////////////////		DD_TIPO_CONDONACION_USACH		///////////////////////
			case 'DD_TIPO_CONDONACION_USACH' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= 	"SELECT 	COD_TIPO_CONDONACION,
										NOM_TIPO_CONDONACION 
							FROM 		TIPO_CONDONACION 
							WHERE 		COD_TIPO_CONDONACION IN({PARAM1},{PARAM2},{PARAM3},{PARAM4}) 
							ORDER BY 	COD_TIPO_CONDONACION";
				}
			break;
			
			///////////////////		DD_ESTADO_CONDONACION		/////////////////////////		
			case 'DD_ESTADO_CONDONACION' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= 	"SELECT 	COD_ESTADO_CONDONACION,
										NOM_ESTADO_CONDONACION 
							FROM 		ESTADO_CONDONACION";
				}
			break;
			
			//////////////////		DD_ESTADO_CONDONACION_1		///////////////////
			case 'DD_ESTADO_CONDONACION_1' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= 	"SELECT 	COD_ESTADO_CONDONACION
										,NOM_ESTADO_CONDONACION
							FROM 		ESTADO_CONDONACION
							ORDER BY 	COD_ESTADO_CONDONACION";
				}
			break;
			//////////////////////			DD_ESTADO_CONDONACION_2		///////////////////////
			case 'DD_ESTADO_CONDONACION_2' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= 	"SELECT		COD_ESTADO_CONDONACION
										, NOM_ESTADO_CONDONACION
							FROM 		ESTADO_CONDONACION
							WHERE 		COD_ESTADO_CONDONACION <> ".self::K_ESTADO_CONDONACION_SOLICITADA;
				}
			break;
			///////////////////////			AJAX_NOMBRE		///////////////////////
			case 'AJAX_NOMBRE' :
				if (K_TIPO_BD=="oci"){
					$sql= 	"SELECT 	COD_DEUDOR
										, NOMBRE || ' ' || APELLIDO_PATERNO || ' ' || APELLIDO_MATERNO NOMBRE
										, DIG_VERIF
							FROM 		DEUDOR
							WHERE		RUT = {PARAM1}";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT 	COD_DEUDOR
										, NOMBRE + ' ' + APELLIDO_PATERNO + ' ' + APELLIDO_MATERNO NOMBRE
										, DIG_VERIF
							FROM 		DEUDOR
							WHERE		RUT = {PARAM1}";
				}
				
			break;
			////////////////////////		AJAX_DEUDA		///////////////////////			
			case 'AJAX_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= 	"SELECT		D.COD_DEUDA
							FROM    	DEUDA D
										, DEUDOR DR
										, TIPO_DEUDA TP
							WHERE   	D.COD_DEUDOR=DR.COD_DEUDOR AND DR.RUT = {PARAM1}
										AND TP.COD_TIPO_DEUDA=D.COD_TIPO_DEUDA
										AND D.COD_ESTADO_DEUDA IN ({PARAM2},{PARAM3})
							ORDER BY	COD_DEUDA";
				}
				
			break;
			//////////////////////////		AJAX_ESTADO_CONDONACION		//////////////////////
			case 'AJAX_ESTADO_CONDONACION' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = 	"SELECT 	COD_ESTADO_CONDONACION
							FROM   		CONDONACION C
										, DEUDOR DR
										, DEUDA D
							WHERE		RUT = {PARAM1}
										AND DR.COD_DEUDOR = D.COD_DEUDOR
										AND DR.COD_DEUDOR = C.COD_DEUDOR";
				}
			break;
			///////////////////////////		AJAX_NOMBRE_1		///////////////////////
			case 'AJAX_NOMBRE_1' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT DR.COD_DEUDOR COD_DEUDOR
										, NOMBRE || ' ' || APELLIDO_PATERNO || ' ' || APELLIDO_MATERNO NOMBRE
										, DIG_VERIF
							FROM 		DEUDOR DR
										, DEUDA D
							WHERE 		RUT = {PARAM1}
										AND DR.COD_DEUDOR = D.COD_DEUDOR
										AND D.COD_DEUDA NOT IN (SELECT COD_DEUDA
							FROM 		DEUDA_CONDONACION)";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT DR.COD_DEUDOR COD_DEUDOR
										, NOMBRE + ' ' + APELLIDO_PATERNO + ' ' + APELLIDO_MATERNO NOMBRE
										, DIG_VERIF
							FROM 		DEUDOR DR
										, DEUDA D
							WHERE 		RUT = {PARAM1}
										AND DR.COD_DEUDOR = D.COD_DEUDOR
										AND D.COD_DEUDA NOT IN (SELECT 	COD_DEUDA
																FROM 	DEUDA_CONDONACION)";
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
			
			
			
			
			///////////////////////////		AJAX_OBTIENE_DEUDA		////////////////////////
			case 'AJAX_OBTIENE_DEUDA' :
				if (K_TIPO_BD=="oci" ){
					$sql = "SELECT      'N' SELECCION -- SOLO USACH
										,D.COD_DEUDA  
										,'' COD_DEUDA_CONDONACION_H                  
										,D.COD_DEUDA COD_DEUDA_H
			            				,TP.NOM_TIPO_DEUDA TIPO_DEUDA                   
										,D.COD_TIPO_DEUDA
										,round(f_deuda_saldo_componente(D.COD_DEUDA,1) * F_MON_GET_VALOR_MONEDA({PARAM1})) NO_EXIGIBLE
										,round(f_deuda_saldo_componente(D.COD_DEUDA,2) * F_MON_GET_VALOR_MONEDA({PARAM1})) VENCIDA
										,round(f_deuda_saldo_componente(D.COD_DEUDA,3) * F_MON_GET_VALOR_MONEDA({PARAM1}))  VIGENTE
										,round(f_deuda_saldo_componente(D.COD_DEUDA,4) * F_MON_GET_VALOR_MONEDA({PARAM1}))  FUTURA
										,round(f_deuda_saldo_componente(D.COD_DEUDA,1) * F_MON_GET_VALOR_MONEDA({PARAM1}))  NO_EXIGIBLE_H
										,round(f_deuda_saldo_componente(D.COD_DEUDA,2) * F_MON_GET_VALOR_MONEDA({PARAM1}))  VENCIDA_H
										,round(f_deuda_saldo_componente(D.COD_DEUDA,3) * F_MON_GET_VALOR_MONEDA({PARAM1}))  VIGENTE_H
										,round(f_deuda_saldo_componente(D.COD_DEUDA,4) * F_MON_GET_VALOR_MONEDA({PARAM1}))  FUTURA_H
										,round(F_DEUDA_SALDO_TOTAL(D.COD_DEUDA) * F_MON_GET_VALOR_MONEDA({PARAM2}))  TOTAL
										,0 COD_COMPROBANTE									
							FROM        DEUDA D
										, DEUDOR DR
										, TIPO_DEUDA TP
							WHERE       D.COD_DEUDOR = DR.COD_DEUDOR AND DR.RUT = {PARAM3}
										AND TP.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA
										AND D.COD_ESTADO_DEUDA IN ({PARAM4},{PARAM5})
							ORDER BY	COD_DEUDA";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT       D.COD_DEUDA  
										,'' COD_DEUDA_CONDONACION_H                  
										,D.COD_DEUDA COD_DEUDA_H
			            				,TP.NOM_TIPO_DEUDA TIPO_DEUDA                   
										,D.COD_TIPO_DEUDA
										,round(dbo.f_deuda_saldo_componente(D.COD_DEUDA, 1, getdate()) * dbo.F_MON_GET_VALOR_MONEDA({PARAM1}, getdate()), 4) NO_EXIGIBLE
										,round(dbo.f_deuda_saldo_componente(D.COD_DEUDA, 2, getdate()) * dbo.F_MON_GET_VALOR_MONEDA({PARAM1}, getdate()), 4) VENCIDA
										,round(dbo.f_deuda_saldo_componente(D.COD_DEUDA, 3, getdate()) * dbo.F_MON_GET_VALOR_MONEDA({PARAM1}, getdate()), 4)  VIGENTE
										,round(dbo.f_deuda_saldo_componente(D.COD_DEUDA, 4, getdate()) * dbo.F_MON_GET_VALOR_MONEDA({PARAM1}, getdate()), 4)  FUTURA
										,round(dbo.f_deuda_saldo_componente(D.COD_DEUDA, 1, getdate()) * dbo.F_MON_GET_VALOR_MONEDA({PARAM1}, getdate()), 4)  NO_EXIGIBLE_H
										,round(dbo.f_deuda_saldo_componente(D.COD_DEUDA, 2, getdate()) * dbo.F_MON_GET_VALOR_MONEDA({PARAM1}, getdate()), 4)  VENCIDA_H
										,round(dbo.f_deuda_saldo_componente(D.COD_DEUDA, 3, getdate()) * dbo.F_MON_GET_VALOR_MONEDA({PARAM1}, getdate()), 4)  VIGENTE_H
										,round(dbo.f_deuda_saldo_componente(D.COD_DEUDA, 4, getdate()) * dbo.F_MON_GET_VALOR_MONEDA({PARAM1}, getdate()), 4)  FUTURA_H
										,round(dbo.F_DEUDA_SALDO_TOTAL(D.COD_DEUDA, getdate()) * dbo.F_MON_GET_VALOR_MONEDA({PARAM2}, getdate()), 4)  TOTAL
										,0 COD_COMPROBANTE									
							FROM        DEUDA D
										, DEUDOR DR
										, TIPO_DEUDA TP
							WHERE       D.COD_DEUDOR = DR.COD_DEUDOR AND DR.RUT = {PARAM3}
										AND TP.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA
										AND D.COD_ESTADO_DEUDA IN ({PARAM4},{PARAM5})
							ORDER BY	COD_DEUDA";
				}
			break;
			///////////////////print//////////////////////
			case 'PRINT_RECORD' :
				if (K_TIPO_BD=="oci"){
					
// 					$sql=	"SELECT		C.COD_CONDONACION
// 										, TC.COD_TIPO_CONDONACION
// 										, TO_CHAR(FECHA_REGISTRO,'dd/mm/yyyy') FECHA_REGISTRO
// 										, NOM_TIPO_CONDONACION 
// 										, D.RUT
// 										, D.DIG_VERIF		
// 										, D.NOMBRE||' '||D.APELLIDO_PATERNO||' '|| D.APELLIDO_MATERNO	NOMBRE
// 					                    , DE.COD_DEUDA
// 					                    , CA.NOM_CARTERA
// 					                    , CA.ENCARGADO
// 					                    , TC.NOM_TIPO_CONDONACION
// 					                    , f_get_parametro({PARAM2}) NOM_ADMIN
// 					                    , CA.COD_CARTERA
// 					                    , To_Char(SYSDATE, 'dd')  DIA,
// 							             CASE 
// 					                        WHEN To_Char(SYSDATE, 'mm') = '01' THEN 'Enero'
// 					                        WHEN To_Char(SYSDATE, 'mm') = '02' THEN 'Febrero'
// 					                        WHEN To_Char(SYSDATE, 'mm') = '03' THEN 'Marzo'
// 					                        WHEN To_Char(SYSDATE, 'mm') = '04' THEN 'Abril'
// 					                        WHEN To_Char(SYSDATE, 'mm') = '05' THEN 'Mayo'
// 					                        WHEN To_Char(SYSDATE, 'mm') = '06' THEN 'Junio'
// 					                        WHEN To_Char(SYSDATE, 'mm') = '07' THEN 'Julio'
// 					                        WHEN To_Char(SYSDATE, 'mm') = '08' THEN 'Agosto'
// 					                        WHEN To_Char(SYSDATE, 'mm') = '09' THEN 'Septiembre'
// 					                        WHEN To_Char(SYSDATE, 'mm') = '10' THEN 'Octubre'
// 					                        WHEN To_Char(SYSDATE, 'mm') = '11' THEN 'Noviembre'
// 					                        WHEN To_Char(SYSDATE, 'mm') = '12' THEN 'Diciembre'        
// 					                    END MES
// 					                    ,To_Char(SYSDATE, 'yyyy') ANO
// 					                    ,'{PARAM3}' NOM_RECEPTOR
// 							FROM 		CONDONACION C, DEUDOR  D, TIPO_CONDONACION TC, CARTERA CA, DEUDA DE, DEUDA_CONDONACION DC
// 							WHERE   	C.COD_CONDONACION = {PARAM1}
//                     				AND C.COD_DEUDOR=D.COD_DEUDOR 
// 									AND C.COD_TIPO_CONDONACION = TC.COD_TIPO_CONDONACION
// 							      	AND DE.COD_DEUDOR = D.COD_DEUDOR
//                     				AND CA.COD_CARTERA = DE.COD_CARTERA
//                     				AND DC.COD_DEUDA(+) = DE.COD_DEUDA
// 							ORDER BY 	COD_CONDONACION DESC";
                     $sql = "SELECT		C.COD_CONDONACION
										, TC.COD_TIPO_CONDONACION
										, TO_CHAR(FECHA_REGISTRO,'dd/mm/yyyy') FECHA_REGISTRO    
										, D.RUT
										, D.DIG_VERIF
										, TD.NOM_TIPO_DEUDA
                    					, DE.COD_DEUDA		
										, D.NOMBRE||' '||D.APELLIDO_PATERNO||' '|| D.APELLIDO_MATERNO	NOMBRE  
					                    , f_get_parametro({PARAM2}) NOM_ADMIN                         
					                    , To_Char(SYSDATE, 'dd')  DIA,
							             CASE 
					                        WHEN To_Char(SYSDATE, 'mm') = '01' THEN 'Enero'
					                        WHEN To_Char(SYSDATE, 'mm') = '02' THEN 'Febrero'
					                        WHEN To_Char(SYSDATE, 'mm') = '03' THEN 'Marzo'
					                        WHEN To_Char(SYSDATE, 'mm') = '04' THEN 'Abril'
					                        WHEN To_Char(SYSDATE, 'mm') = '05' THEN 'Mayo'
					                        WHEN To_Char(SYSDATE, 'mm') = '06' THEN 'Junio'
					                        WHEN To_Char(SYSDATE, 'mm') = '07' THEN 'Julio'
					                        WHEN To_Char(SYSDATE, 'mm') = '08' THEN 'Agosto'
					                        WHEN To_Char(SYSDATE, 'mm') = '09' THEN 'Septiembre'
					                        WHEN To_Char(SYSDATE, 'mm') = '10' THEN 'Octubre'
					                        WHEN To_Char(SYSDATE, 'mm') = '11' THEN 'Noviembre'
					                        WHEN To_Char(SYSDATE, 'mm') = '12' THEN 'Diciembre'        
					                    END MES
					                    ,To_Char(SYSDATE, 'yyyy') ANO  
                                        ,CR.PORC  PORC_CRUZADO
                                        ,DE.CANT_CUOTA ANOS
                                        ,C.FOLIO_CONDONACION
							FROM 		CONDONACION C, DEUDOR  D, TIPO_CONDONACION TC, CARTERA CA, DEUDA DE, DEUDA_CONDONACION DC, CRUZADO CR, TIPO_DEUDA TD
							WHERE   	C.COD_CONDONACION = {PARAM1}
                    				AND C.COD_DEUDOR=D.COD_DEUDOR 
									AND C.COD_TIPO_CONDONACION = TC.COD_TIPO_CONDONACION
							      	AND DE.COD_DEUDOR = D.COD_DEUDOR
                    				AND CA.COD_CARTERA = DE.COD_CARTERA
                    				AND DC.COD_DEUDA(+) = DE.COD_DEUDA
                                    AND CR.COD_DEUDA(+) = DE.COD_DEUDA
                                    AND CR.COD_UNIVERSIDAD(+) = {PARAM3} 
                                    AND DC.COD_CONDONACION = C.COD_CONDONACION
                                    AND DE.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
							ORDER BY 	COD_CONDONACION DESC";
				}
				else if (K_TIPO_BD=="mssql"){
// 					$sql="SELECT		C.COD_CONDONACION
// 										, TC.COD_TIPO_CONDONACION
// 										, convert(varchar(20), FECHA_REGISTRO, 103) FECHA_REGISTRO
// 										, NOM_TIPO_CONDONACION 
// 										, D.RUT
// 										, D.DIG_VERIF		
// 										, D.NOMBRE + ' ' + D.APELLIDO_PATERNO + ' ' + D.APELLIDO_MATERNO	NOMBRE
// 					                    , DE.COD_DEUDA
// 					                    , CA.NOM_CARTERA
// 					                    , CA.ENCARGADO
// 					                    , TC.NOM_TIPO_CONDONACION
// 					                    , dbo.f_get_parametro({PARAM2}) NOM_ADMIN
// 					                    , CA.COD_CARTERA
// 					                    , convert(varchar(2), datepart(day, getdate()))  DIA,
// 							             CASE convert(varchar(2), datepart(mm, getdate())) 
// 					                        WHEN '1' THEN 'Enero'
// 					                        WHEN '2' THEN 'Febrero'
// 					                        WHEN '3' THEN 'Marzo'
// 					                        WHEN '4' THEN 'Abril'
// 					                        WHEN '5' THEN 'Mayo'
// 					                        WHEN '6' THEN 'Junio'
// 					                        WHEN '7' THEN 'Julio'
// 					                        WHEN '8' THEN 'Agosto'
// 					                        WHEN '9' THEN 'Septiembre'
// 					                        WHEN '10' THEN 'Octubre'
// 					                        WHEN '11' THEN 'Noviembre'
// 					                        WHEN '12' THEN 'Diciembre'        
// 					                    END MES
// 					                    ,convert(varchar(20), datepart(year, getdate())) ANO
// 					                    ,'{PARAM3}' NOM_RECEPTOR
// 							FROM 		CONDONACION C, DEUDOR  D, TIPO_CONDONACION TC, CARTERA CA, DEUDA DE left outer join DEUDA_CONDONACION DC on DE.COD_DEUDA = DC.COD_DEUDA
// 							WHERE   	C.COD_CONDONACION = {PARAM1}
//                     				AND C.COD_DEUDOR=D.COD_DEUDOR 
// 									AND C.COD_TIPO_CONDONACION = TC.COD_TIPO_CONDONACION
// 							      	AND DE.COD_DEUDOR = D.COD_DEUDOR
//                     				AND CA.COD_CARTERA = DE.COD_CARTERA
// 							ORDER BY 	COD_CONDONACION DESC";
				    $sql="SELECT		C.COD_CONDONACION
										, TC.COD_TIPO_CONDONACION
										, convert(varchar(20), FECHA_REGISTRO, 103) FECHA_REGISTRO
										, D.RUT
										, D.DIG_VERIF
										, D.NOMBRE + ' ' + D.APELLIDO_PATERNO + ' ' + D.APELLIDO_MATERNO	NOMBRE
					                    , dbo.f_get_parametro({PARAM2}) NOM_ADMIN
					                    , convert(varchar(2), datepart(day, getdate()))  DIA,
							             CASE convert(varchar(2), datepart(mm, getdate()))
					                        WHEN '1' THEN 'Enero'
					                        WHEN '2' THEN 'Febrero'
					                        WHEN '3' THEN 'Marzo'
					                        WHEN '4' THEN 'Abril'
					                        WHEN '5' THEN 'Mayo'
					                        WHEN '6' THEN 'Junio'
					                        WHEN '7' THEN 'Julio'
					                        WHEN '8' THEN 'Agosto'
					                        WHEN '9' THEN 'Septiembre'
					                        WHEN '10' THEN 'Octubre'
					                        WHEN '11' THEN 'Noviembre'
					                        WHEN '12' THEN 'Diciembre'
					                    END MES
					                    ,convert(varchar(20), datepart(year, getdate())) ANO
                                        ,CR.PORC  PORC_CRUZADO
                                        ,DE.CANT_CUOTA ANOS
                                        ,ROUND(((DC.MONTO_SVP + DC.MONTO_MORA + DC.MONTO_CP + DC.MONTO_LP) / dbo.f_mon_get_valor_moneda(3,C.FECHA_CONFIRMA)),3) UTM
							FROM 		CONDONACION C, DEUDOR  D, CRUZADO CR, TIPO_CONDONACION TC, CARTERA CA, DEUDA DE left outer join DEUDA_CONDONACION DC on DE.COD_DEUDA = DC.COD_DEUDA
							WHERE   	C.COD_CONDONACION = {PARAM1}
                    				AND C.COD_DEUDOR=D.COD_DEUDOR
									AND C.COD_TIPO_CONDONACION = TC.COD_TIPO_CONDONACION
							      	AND DE.COD_DEUDOR = D.COD_DEUDOR
                    				AND CA.COD_CARTERA = DE.COD_CARTERA
                                    AND CR.COD_DEUDA = DE.COD_DEUDA
                                    AND CR.COD_UNIVERSIDAD = {PARAM3}
							ORDER BY 	COD_CONDONACION DESC";
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
							FROM 	DUAL;";
				}else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	dbo.f_get_parametro(5) NOM_UNIVERSIDAD
					                ,dbo.f_get_parametro(13) MAIL_UNIVERSIDAD
					                ,dbo.f_get_parametro(9) DIR_UNIVERSIDAD
					                ,dbo.f_get_parametro(10) FONO_UNIVERSIDAD
					                ,dbo.f_get_parametro(15) FAX_UNIVERSIDAD";
				}
			break;
			
			/////////////////////////////		ERROR			/////////////////////////////////////
			default:
			base::error("sql_condonacion::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>