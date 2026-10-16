<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_comprobante_base extends sql_tabla {
	/////////////	DECLARACION DE CONSTANTES		/////////////////////////
	const K_ESTADO_COMPROBANTE_APROBADO		= 2;
	const K_ESTADO_COMPROBANTE_ANULADO		= 3;
	const K_ESTADO_COMPROBANTE_PENDIENTE	= 1;
	const K_COD_ORIGEN_COMPROBANTE_MANUAL	= 1;
	const K_PARAM_RUT_U						= 4;
	const K_PARAM_NOM_U 					= 5;
	const K_PARAM_DIRECCCION 				= 9;
	const K_PARAM_FONO 						= 10;
	const K_PARAM_COMUNA 					= 11;
	const K_PARAM_CIUDAD 					= 12;
	const K_PARAM_E_MAIL 					= 13;
	const K_PARAM_SITIO_WEB 				= 14;
	const K_PARAM_FAX 						= 15;
	const K_PARAM_PAIS 						= 16;
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			//////////////////		INPUT		//////////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT C.COD_COMPROBANTE
									,NRO_COMPROBANTE
				        			,TO_CHAR(C.FECHA_COMPROBANTE, 'dd/mm/yyyy') FECHA_COMPROBANTE  
									,C.COD_ESTADO_COMPROBANTE
									,EC.NOM_ESTADO_COMPROBANTE
				       			 	,C.COD_FAMILIA_DEUDA
					   			 	,C.NOM_COMPROBANTE 
				        			,C.COD_TIPO_COMPROBANTE 
				        			,C.COD_USUARIO
				        			,U.NOM_USUARIO
				        			,C.COD_ORIGEN_COMPROBANTE COD_ORIGEN_COMPROBANTE_H
				        			,OC.NOM_ORIGEN_COMPROBANTE 
				        			,C.COD_USUARIO_ESTADO COD_USUARIO_ESTADO_H
				        			,UE.NOM_USUARIO NOM_USUARIO_ESTADO
				        			,TO_CHAR(C.FECHA_ESTADO, 'dd/mm/yyyy') FECHA_ESTADO
									,TO_CHAR(C.FECHA_ESTADO, 'dd/mm/yyyy') FECHA_ESTADO_H
									,'none' DISPLAY_ANALITICO
									,'' DISPLAY_MENSAJE_ANALITICO
									FROM 	COMPROBANTE C
									, ESTADO_COMPROBANTE EC
									, FAMILIA_DEUDA FD
									, TIPO_COMPROBANTE TC
									, USUARIO U
									, ORIGEN_COMPROBANTE OC
									,  USUARIO UE
							WHERE 	C.COD_COMPROBANTE = {KEY1}
									AND C.COD_ESTADO_COMPROBANTE = EC.COD_ESTADO_COMPROBANTE
									AND C.COD_FAMILIA_DEUDA = FD.COD_FAMILIA_DEUDA
									AND C.COD_TIPO_COMPROBANTE = TC.COD_TIPO_COMPROBANTE
									AND C.COD_USUARIO = U.COD_USUARIO
									AND C.COD_ORIGEN_COMPROBANTE = OC.COD_ORIGEN_COMPROBANTE
									AND C.COD_USUARIO_ESTADO = UE.COD_USUARIO (+)";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT C.COD_COMPROBANTE
									,NRO_COMPROBANTE
				        			,convert(varchar(10), C.FECHA_COMPROBANTE, 103) FECHA_COMPROBANTE  
									,C.COD_ESTADO_COMPROBANTE
									,EC.NOM_ESTADO_COMPROBANTE
				       			 	,C.COD_FAMILIA_DEUDA
					   			 	,C.NOM_COMPROBANTE 
				        			,C.COD_TIPO_COMPROBANTE 
				        			,C.COD_USUARIO
				        			,U.NOM_USUARIO
				        			,C.COD_ORIGEN_COMPROBANTE COD_ORIGEN_COMPROBANTE_H
				        			,OC.NOM_ORIGEN_COMPROBANTE 
				        			,C.COD_USUARIO_ESTADO COD_USUARIO_ESTADO_H
				        			,UE.NOM_USUARIO NOM_USUARIO_ESTADO
				        			,convert(varchar(10), C.FECHA_ESTADO, 103) FECHA_ESTADO
									,CONVERT(varchar(10), C.FECHA_ESTADO, 103) FECHA_ESTADO_H
									,'none' DISPLAY_ANALITICO
									,'' DISPLAY_MENSAJE_ANALITICO
							FROM 	COMPROBANTE C LEFT OUTER JOIN USUARIO UE ON C.COD_USUARIO_ESTADO = UE.COD_USUARIO
									, ESTADO_COMPROBANTE EC
									, FAMILIA_DEUDA FD
									, TIPO_COMPROBANTE TC
									, USUARIO U
									, ORIGEN_COMPROBANTE OC
							WHERE 	C.COD_COMPROBANTE = {KEY1}
									AND C.COD_ESTADO_COMPROBANTE = EC.COD_ESTADO_COMPROBANTE
									AND C.COD_FAMILIA_DEUDA = FD.COD_FAMILIA_DEUDA
									AND C.COD_TIPO_COMPROBANTE = TC.COD_TIPO_COMPROBANTE
									AND C.COD_USUARIO = U.COD_USUARIO
									AND C.COD_ORIGEN_COMPROBANTE = OC.COD_ORIGEN_COMPROBANTE";
				}
			break;
			/////////////////////		OUTPUT		///////////////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT   C.COD_COMPROBANTE COD_COMPROBANTE
									   , NRO_COMPROBANTE
								       , TO_CHAR (C.FECHA_COMPROBANTE, 'dd/mm/yyyy') FECHA_COMPROBANTE
								       , C.COD_ESTADO_COMPROBANTE
								       , EC.NOM_ESTADO_COMPROBANTE
								       , C.COD_FAMILIA_DEUDA
								       , FM.NOM_FAMILIA_DEUDA
								       , C.NOM_COMPROBANTE
								       , C.COD_TIPO_COMPROBANTE
								       , TC.NOM_TIPO_COMPROBANTE
								       , C.COD_USUARIO
								       , U.NOM_USUARIO
								       , CASE
								            WHEN F_COMP_GET_MONTO_DEBE (C.COD_COMPROBANTE) > 0
								               THEN F_COMP_GET_MONTO_DEBE (C.COD_COMPROBANTE)
								            ELSE 0
								         END MONTO
								    FROM COMPROBANTE C
								       , ESTADO_COMPROBANTE EC
								       , FAMILIA_DEUDA FM
								       , TIPO_COMPROBANTE TC
								       , USUARIO U
								   WHERE EC.COD_ESTADO_COMPROBANTE = C.COD_ESTADO_COMPROBANTE
								     AND FM.COD_FAMILIA_DEUDA = C.COD_FAMILIA_DEUDA
								     AND TC.COD_TIPO_COMPROBANTE = C.COD_TIPO_COMPROBANTE
								     AND U.COD_USUARIO = C.COD_USUARIO
								ORDER BY nvl(NRO_COMPROBANTE, 9999999999) DESC, COD_COMPROBANTE desc";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT   C.COD_COMPROBANTE COD_COMPROBANTE
									   , NRO_COMPROBANTE
										, convert(varchar(20),C.FECHA_COMPROBANTE, 103) FECHA_COMPROBANTE
								       , C.COD_ESTADO_COMPROBANTE
								       , EC.NOM_ESTADO_COMPROBANTE
								       , C.COD_FAMILIA_DEUDA
								       , FM.NOM_FAMILIA_DEUDA
								       , C.NOM_COMPROBANTE
								       , C.COD_TIPO_COMPROBANTE
								       , TC.NOM_TIPO_COMPROBANTE
								       , C.COD_USUARIO
								       , U.NOM_USUARIO
								       , CASE
								            WHEN dbo.F_COMP_GET_MONTO_DEBE (C.COD_COMPROBANTE) > 0
								               THEN dbo.F_COMP_GET_MONTO_DEBE (C.COD_COMPROBANTE)
								            ELSE 0
								         END MONTO
								    FROM COMPROBANTE C
								       , ESTADO_COMPROBANTE EC
								       , FAMILIA_DEUDA FM
								       , TIPO_COMPROBANTE TC
								       , USUARIO U
								   WHERE EC.COD_ESTADO_COMPROBANTE = C.COD_ESTADO_COMPROBANTE
								     AND FM.COD_FAMILIA_DEUDA = C.COD_FAMILIA_DEUDA
								     AND TC.COD_TIPO_COMPROBANTE = C.COD_TIPO_COMPROBANTE
								     AND U.COD_USUARIO = C.COD_USUARIO
								ORDER BY isnull(NRO_COMPROBANTE, 9999999999) DESC, COD_COMPROBANTE desc";
				}
			break;
			////////////////////////		DW_ANALITICO_COMPROBANTE		///////////////////////
			case 'DW_ANALITICO_COMPROBANTE' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT	COD_COMPROBANTE
				       				,RUT
    		           				,DIG_VERIF
				       				,AC.COD_DEUDA
				       				,AC.MONTO_DEBE
				       				,AC.MONTO_HABER
				       				,AC.MONTO_DEBE MONTO_DEBE_H
				       				,AC.MONTO_HABER MONTO_HABER_H
				       				,TD.NOM_TIPO_DEUDA
				       				,TD.COD_TIPO_DEUDA
				       				,CC.COD_CUENTA_CONTABLE COD_CUENTA_CONTABLE_CC
				       				,CC.NOM_CUENTA_CONTABLE NOM_CUENTA_CONTABLE_CC
				       				,AC.COD_ANALITICO_COMPROBANTE
						FROM   		ANALITICO_COMPROBANTE AC LEFT OUTER JOIN DEUDA D ON AC.COD_DEUDA = D.COD_DEUDA  
															LEFT OUTER JOIN DEUDOR DEU ON AC.COD_DEUDOR = DEU.COD_DEUDOR
															LEFT OUTER JOIN TIPO_DEUDA TD ON D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
									, CUENTA_CONTABLE CC
						WHERE  		AC.COD_COMPROBANTE = {KEY1} 
									AND AC.COD_CUENTA_CONTABLE = CC.COD_CUENTA_CONTABLE
						ORDER BY	COD_ANALITICO_COMPROBANTE";
				}
				if (K_TIPO_BD=="mssql"){
					$sql= "SELECT	COD_COMPROBANTE
				       				,RUT
    		           				,DIG_VERIF
				       				,AC.COD_DEUDA
				       				,AC.MONTO_DEBE
				       				,AC.MONTO_HABER
				       				,AC.MONTO_DEBE MONTO_DEBE_H
				       				,AC.MONTO_HABER MONTO_HABER_H
				       				,TD.NOM_TIPO_DEUDA
				       				,TD.COD_TIPO_DEUDA
				       				,CC.COD_CUENTA_CONTABLE COD_CUENTA_CONTABLE_CC
						FROM   		ANALITICO_COMPROBANTE AC LEFT OUTER JOIN DEUDA D ON AC.COD_DEUDA = D.COD_DEUDA  
															LEFT OUTER JOIN DEUDOR DEU ON AC.COD_DEUDOR = DEU.COD_DEUDOR
															LEFT OUTER JOIN TIPO_DEUDA TD ON D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
									, CUENTA_CONTABLE CC
						WHERE  		AC.COD_COMPROBANTE = {KEY1} 
									AND AC.COD_CUENTA_CONTABLE = CC.COD_CUENTA_CONTABLE
						ORDER BY	COD_ANALITICO_COMPROBANTE";
				}
			break;
			///////////////////		DW_DETALLE_CUENTA		/////////////////////////		
			case 'DW_DETALLE_CUENTA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT	COD_ITEM_COMPROBANTE COD_ITEM_COMPROBANTE_H
							 		, COD_CUENTA_CONTABLE
							 		, MONTO_DEBE DEBE
							 		, MONTO_HABER HABER
							 		, MONTO_DEBE DEBE2
							 		, MONTO_HABER HABER2
							 		, C.COD_COMPROBANTE COD_COMPROBANTE_H
				 		FROM		COMPROBANTE C
				 					, ITEM_COMPROBANTE IC
				 		WHERE 		C.COD_COMPROBANTE=IC.COD_COMPROBANTE
				 					AND C.COD_COMPROBANTE={KEY1}
				 		ORDER BY	MONTO_DEBE DESC";
				}
			break;
			//////////////////		DD_COD_CUENTA_CONTABLE		///////////////////
			case 'DD_COD_CUENTA_CONTABLE' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT   COD_CUENTA_CONTABLE
									, To_Char(COD_CUENTA_CONTABLE) || ' ' || NOM_CUENTA_CONTABLE 
						FROM 		CUENTA_CONTABLE sss
						ORDER BY 	COD_CUENTA_CONTABLE";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT   COD_CUENTA_CONTABLE
									, convert(varchar(10), COD_CUENTA_CONTABLE) + ' ' + NOM_CUENTA_CONTABLE
						FROM 		CUENTA_CONTABLE 
						ORDER BY 	COD_CUENTA_CONTABLE";
				}
			break;
			//////////////////////			DD_COD_FAMILIA_DEUDA		///////////////////////
			case 'DD_COD_FAMILIA_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 	COD_FAMILIA_DEUDA
									,NOM_FAMILIA_DEUDA  
						FROM 		FAMILIA_DEUDA";
				}
			break;
			///////////////////////			DD_COD_TIPO_COMPROBANTE		///////////////////////
			case 'DD_COD_TIPO_COMPROBANTE' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 	COD_TIPO_COMPROBANTE
									,NOM_TIPO_COMPROBANTE 	
							FROM 	TIPO_COMPROBANTE";
				}
			break;
			////////////////////////		DD_COD_ESTADO_COMPROBANTE_1		///////////////////////			
			case 'DD_COD_ESTADO_COMPROBANTE_1' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 	COD_ESTADO_COMPROBANTE
									,NOM_ESTADO_COMPROBANTE 
					 	FROM 		ESTADO_COMPROBANTE
					 	WHERE 		COD_ESTADO_COMPROBANTE = 1";
				}
			break;
			//////////////////////////		DD_COD_ESTADO_COMPROBANTE_2		//////////////////////
			case 'DD_COD_ESTADO_COMPROBANTE_2' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_ESTADO_COMPROBANTE
									,NOM_ESTADO_COMPROBANTE 
					 	FROM 		ESTADO_COMPROBANTE
					 	WHERE 		COD_ESTADO_COMPROBANTE > 1";
				}
			break;
			///////////////////////////		DD_COD_ESTADO_COMPROBANTE_3		///////////////////////
			case 'DD_COD_ESTADO_COMPROBANTE_3' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_ESTADO_COMPROBANTE
									,NOM_ESTADO_COMPROBANTE 
					 	FROM 		ESTADO_COMPROBANTE";
				}
			break;
			///////////////////////////		DD_COD_ESTADO_COMPROBANTE		////////////////////////
			case 'DD_COD_ESTADO_COMPROBANTE' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_ESTADO_COMPROBANTE
									,NOM_ESTADO_COMPROBANTE 
					FROM 			ESTADO_COMPROBANTE 
					ORDER BY		COD_ESTADO_COMPROBANTE";
				}
			break;
			///////////////////////////		DD_COD_FAMILIA_DEUDA_1		/////////////////////////////
			case 'DD_COD_FAMILIA_DEUDA_1' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_FAMILIA_DEUDA 
									,NOM_FAMILIA_DEUDA 
					FROM 			FAMILIA_DEUDA 
					ORDER BY		COD_FAMILIA_DEUDA";
				}
			break;
			///////////////////////////		DD_COD_TIPO_COMPROBANTE_1		//////////////////////////		
			case 'DD_COD_TIPO_COMPROBANTE_1' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_TIPO_COMPROBANTE 
									,NOM_TIPO_COMPROBANTE 
					FROM 			TIPO_COMPROBANTE 
					ORDER BY 		COD_TIPO_COMPROBANTE";
				}
			break;
			////////////////////////////		DD_COD_USUARIO			/////////////////////////////
			case 'DD_COD_USUARIO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_USUARIO, 
									NOM_USUARIO 
					FROM 			USUARIO 
					ORDER BY 		COD_USUARIO";
				}
			break;
			///////////////////////////			PRINT COMPROBANTE		////////////////////////////
			case 'PRINT_COMPROBANTE' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT C.COD_COMPROBANTE
							,NRO_COMPROBANTE
					        ,TO_CHAR(C.FECHA_COMPROBANTE, 'dd/mm/yyyy') FECHA_COMPROBANTE  
					        ,NOM_COMPROBANTE
					        ,FD.NOM_FAMILIA_DEUDA
					        ,TC.NOM_TIPO_COMPROBANTE
					        ,EC.NOM_ESTADO_COMPROBANTE
					        ,OC.NOM_ORIGEN_COMPROBANTE
					        ,IC.COD_CUENTA_CONTABLE
					        ,IC.MONTO_DEBE
					        ,IC.MONTO_HABER
                 			,NOM_USUARIO
					        ,CC.NOM_CUENTA_CONTABLE
       				        ,(select to_char (SYSDATE, 'DD/MM/YYYY') from dual) FECHA_IMPRESO
                  			,(select to_char (SYSDATE +(1/24), 'HH24: MI: SS') from dual)HORA_IMPRESO
                  			,f_get_obs_comprobante(C.COD_COMPROBANTE, C.COD_ORIGEN_COMPROBANTE) OBSERVACION
					 FROM   COMPROBANTE C, FAMILIA_DEUDA FD, TIPO_COMPROBANTE TC, ESTADO_COMPROBANTE EC,
					        ORIGEN_COMPROBANTE OC, ITEM_COMPROBANTE IC, CUENTA_CONTABLE CC, USUARIO U 
					 WHERE  C.COD_COMPROBANTE = {PARAM1} AND
					 		C.COD_FAMILIA_DEUDA = FD.COD_FAMILIA_DEUDA AND
					        C.COD_TIPO_COMPROBANTE = TC.COD_TIPO_COMPROBANTE AND
					        C.COD_ESTADO_COMPROBANTE = EC.COD_ESTADO_COMPROBANTE AND
					        C.COD_ORIGEN_COMPROBANTE = OC.COD_ORIGEN_COMPROBANTE AND
					        C.COD_COMPROBANTE = IC.COD_COMPROBANTE (+) AND
                  			C.COD_USUARIO = U.COD_USUARIO AND
					        IC.COD_CUENTA_CONTABLE = CC.COD_CUENTA_CONTABLE (+)
					 ORDER BY	IC.MONTO_DEBE DESC";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT C.COD_COMPROBANTE
					        ,convert(varchar(10),C.FECHA_COMPROBANTE, 103) FECHA_COMPROBANTE
					        ,NRO_COMPROBANTE 
					        ,NOM_COMPROBANTE
					        ,FD.NOM_FAMILIA_DEUDA
					        ,TC.NOM_TIPO_COMPROBANTE
					        ,EC.NOM_ESTADO_COMPROBANTE
					        ,OC.NOM_ORIGEN_COMPROBANTE
					        ,IC.COD_CUENTA_CONTABLE
					        ,IC.MONTO_DEBE
					        ,IC.MONTO_HABER
					        ,CC.NOM_CUENTA_CONTABLE
					        ,NOM_USUARIO
       				        ,(select convert(varchar(10), getdate(), 103)) FECHA_IMPRESO
                  			,(select convert(varchar(20), getdate(), 108))HORA_IMPRESO
                  			,dbo.f_get_obs_comprobante(C.COD_COMPROBANTE, C.COD_ORIGEN_COMPROBANTE) OBSERVACION
					 FROM   COMPROBANTE C LEFT OUTER JOIN ITEM_COMPROBANTE IC ON C.COD_COMPROBANTE = IC.COD_COMPROBANTE
											LEFT OUTER JOIN CUENTA_CONTABLE CC ON IC.COD_CUENTA_CONTABLE = CC.COD_CUENTA_CONTABLE
							, FAMILIA_DEUDA FD, TIPO_COMPROBANTE TC, ESTADO_COMPROBANTE EC,
					        ORIGEN_COMPROBANTE OC, USUARIO U
					 WHERE  C.COD_COMPROBANTE = {PARAM1} AND
					 		C.COD_FAMILIA_DEUDA = FD.COD_FAMILIA_DEUDA AND
					        C.COD_TIPO_COMPROBANTE = TC.COD_TIPO_COMPROBANTE AND
					        C.COD_ESTADO_COMPROBANTE = EC.COD_ESTADO_COMPROBANTE AND
					        C.COD_ORIGEN_COMPROBANTE = OC.COD_ORIGEN_COMPROBANTE AND
					        C.COD_USUARIO = U.COD_USUARIO
					 ORDER BY	IC.MONTO_DEBE DESC";
				}
			break;
			//////////////////////////		PRINT_ANALITICO		/////////////////////
			case 'PRINT_ANALITICO' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT C.COD_COMPROBANTE
					      ,TO_CHAR(C.FECHA_COMPROBANTE, 'dd/mm/yyyy') FECHA_COMPROBANTE  
					      ,NOM_COMPROBANTE
					      ,FD.NOM_FAMILIA_DEUDA
					      ,TC.NOM_TIPO_COMPROBANTE
					      ,EC.NOM_ESTADO_COMPROBANTE
					      ,OC.NOM_ORIGEN_COMPROBANTE
					      ,DEU.RUT
					      ,DEU.DIG_VERIF
					      ,D.COD_DEUDA
					      ,TD.NOM_TIPO_DEUDA
					      ,CC.NOM_CUENTA_CONTABLE
					      ,CC.COD_CUENTA_CONTABLE
					      ,AC.MONTO_DEBE
					      ,AC.MONTO_HABER
					      ,(select to_char (SYSDATE, 'DD/MM/YYYY') from dual) FECHA_IMPRESO
					      ,(select to_char (SYSDATE +(1/24), 'HH24: MI: SS') from dual)HORA_IMPRESO
					FROM  COMPROBANTE C, FAMILIA_DEUDA FD, TIPO_COMPROBANTE TC, ESTADO_COMPROBANTE EC,
						  ORIGEN_COMPROBANTE OC , ANALITICO_COMPROBANTE AC,DEUDA D, DEUDOR DEU, TIPO_DEUDA TD, CUENTA_CONTABLE CC
					WHERE C.COD_COMPROBANTE = {PARAM1} AND
						  C.COD_FAMILIA_DEUDA = FD.COD_FAMILIA_DEUDA AND
						  C.COD_TIPO_COMPROBANTE = TC.COD_TIPO_COMPROBANTE AND
						  C.COD_ESTADO_COMPROBANTE = EC.COD_ESTADO_COMPROBANTE AND
						  C.COD_ORIGEN_COMPROBANTE = OC.COD_ORIGEN_COMPROBANTE AND
					      C.COD_COMPROBANTE = AC.COD_COMPROBANTE (+) AND
					      AC.COD_DEUDA = D.COD_DEUDA (+) AND
					      D.COD_DEUDOR = DEU.COD_DEUDOR (+) AND
					      D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA (+) AND
					      AC.COD_CUENTA_CONTABLE = CC.COD_CUENTA_CONTABLE (+)
					      order  by RUT";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT C.COD_COMPROBANTE
					      ,convert(varchar(10), C.FECHA_COMPROBANTE, 103) FECHA_COMPROBANTE  
					      ,NOM_COMPROBANTE
					      ,FD.NOM_FAMILIA_DEUDA
					      ,TC.NOM_TIPO_COMPROBANTE
					      ,EC.NOM_ESTADO_COMPROBANTE
					      ,OC.NOM_ORIGEN_COMPROBANTE
					      ,DEU.RUT
					      ,DEU.DIG_VERIF
					      ,D.COD_DEUDA
					      ,TD.NOM_TIPO_DEUDA
					      ,CC.NOM_CUENTA_CONTABLE
					      ,CC.COD_CUENTA_CONTABLE
					      ,AC.MONTO_DEBE
					      ,AC.MONTO_HABER
					      ,(select convert(varchar(10), getdate(), 103)) FECHA_IMPRESO
					      ,(select convert(varchar(8), getdate(), 108))HORA_IMPRESO
					FROM  COMPROBANTE C LEFT OUTER JOIN ANALITICO_COMPROBANTE AC ON C.COD_COMPROBANTE = AC.COD_COMPROBANTE
										LEFT OUTER JOIN DEUDA D ON AC.COD_DEUDA = D.COD_DEUDA
										LEFT OUTER JOIN DEUDOR DEU ON D.COD_DEUDOR = DEU.COD_DEUDOR
										LEFT OUTER JOIN TIPO_DEUDA TD ON D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
										LEFT OUTER JOIN CUENTA_CONTABLE CC ON AC.COD_CUENTA_CONTABLE = CC.COD_CUENTA_CONTABLE
						  , FAMILIA_DEUDA FD, TIPO_COMPROBANTE TC, ESTADO_COMPROBANTE EC, ORIGEN_COMPROBANTE OC
					WHERE C.COD_COMPROBANTE = {PARAM1} AND
						  C.COD_FAMILIA_DEUDA = FD.COD_FAMILIA_DEUDA AND
						  C.COD_TIPO_COMPROBANTE = TC.COD_TIPO_COMPROBANTE AND
						  C.COD_ESTADO_COMPROBANTE = EC.COD_ESTADO_COMPROBANTE AND
						  C.COD_ORIGEN_COMPROBANTE = OC.COD_ORIGEN_COMPROBANTE
						  order  by RUT";
				}
			break;	
			///////////////////////////		DD_CUENTA_CONTABLE		/////////////////////////////
			case 'DD_CUENTA_CONTABLE' :
				if (K_TIPO_BD=="oci" ){
					$sql = "SELECT   COD_CUENTA_CONTABLE
							, To_Char(COD_CUENTA_CONTABLE) || ' ' || NOM_CUENTA_CONTABLE 
					FROM 	CUENTA_CONTABLE 
					order by COD_CUENTA_CONTABLE";
				}
		
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT   COD_CUENTA_CONTABLE
							, convert(varchar(10),COD_CUENTA_CONTABLE) + ' ' + NOM_CUENTA_CONTABLE 
					FROM 	CUENTA_CONTABLE 
					order by COD_CUENTA_CONTABLE";
				}
			break;
			/////////////////////////		PRINT_COMPROBANTE		////////////////////
			case 'PRINT_COMPROBANTE' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT  COD_COMPROBANTE       
					FROM    COMPROBANTE
					WHERE	 COD_COMPROBANTE = {PARAM1}";
				}
			break;
			/////////////////////////		OBTIENE_USUARIO		////////////////////
			case 'OBTIENE_USUARIO' :
				if (K_TIPO_BD=="oci" ){
					$sql = "SELECT NOM_USUARIO
							, To_Char(SYSDATE, 'DD/MM/YYYY')FECHA_DIA 
					FROM 	usuario 
					WHERE 	COD_USUARIO={PARAM1}";
				}
		
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT NOM_USUARIO
							, convert(varchar(10),getdate(), 103)FECHA_DIA 
					FROM 	usuario 
					WHERE 	COD_USUARIO={PARAM1}";
				}
			break;
			/////////////////////////EXISTE_CIERRE/////////////////////////
			case 'EXISTE_CIERRE' :
				if (K_TIPO_BD=="oci" ){
					$sql = "SELECT	Count(*) COUNT_EXISTE_CIERRE
                        	FROM 	CIERRE_CONTABLE
                              		,COMPROBANTE
                        	WHERE 	COD_COMPROBANTE = {PARAM1}
                              		AND To_Char(FECHA_COMPROBANTE,'yyyy') = ANO_CIERRE_CONTABLE";
				}
		
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	Count(*) COUNT_EXISTE_CIERRE
                        	FROM 	CIERRE_CONTABLE
                              		,COMPROBANTE
                        	WHERE 	COD_COMPROBANTE = {PARAM1}
                              		AND datepart(YYYY, FECHA_COMPROBANTE) = ANO_CIERRE_CONTABLE";
				}
			break;
			/////////////////////////////		ERROR			/////////////////////////////////////
			default:
			base::error("sql_comprobante::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}



/////////////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_sql_comprobante.php";
if (file_exists($file_name))
    require_once($file_name);
else {
    class sql_comprobante extends sql_comprobante_base {
    }
}
?>