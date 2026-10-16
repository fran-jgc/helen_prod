<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_boleta extends sql_tabla {
	const K_ESTADO_DEUDA_NO_EXIGIBLE 		= 1;
	const K_ESTADO_DEUDA_CO_NORMAL 			= 2;
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			
			/////////////////////     INPUT     /////////////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT  	to_char(SYSDATE, 'dd/mm/yyyy') FECHA_HOY_H
									,B.COD_BOLETA
				        			,B.COD_BOLETA COD_BOLETA_H
									,TO_CHAR(B.FECHA_BOLETA,'dd/mm/yyyy') FECHA_BOLETA				
									,U.NOM_USUARIO
									,DEUDOR.COD_DEUDOR COD_DEUDOR_H
									,DEUDOR.RUT
			            			,DEUDOR.DIG_VERIF                       
			            			,DEUDOR.NOMBRE||' '||DEUDOR.APELLIDO_PATERNO||' '||DEUDOR.APELLIDO_MATERNO NOMBRE            
			            			,D.COD_DEUDA
			            			,D.COD_DEUDA COD_DEUDA_H
			            			,TD.COD_TIPO_DEUDA
			            			,D.COD_TIPO_DEUDA COD_TIPO_DEUDA_H
			            			,TD.NOM_TIPO_DEUDA
			            			,M.SIMBOLO  
			            			,B.COD_PLAN_PAGO B_COD_PLAN_PAGO
			            			,TD.TASA_INTERES_PENAL B_TASA_INTERES_PENAL_H
			            			,0 ANO_COBRO_PP
			            			,0 NRO_CUOTA_PP
			            			,'' FECHA_VENCTO_PP
			            			,case B.COD_PLAN_PAGO
			  							when null then 'none' 
				  						else ''
					    			end TABLE_DISPLAY
					    			,B.CARTERA_CONOCIDA 
									,'none' MOROSA_DISPLAY
									,'' CUOTA_MOROSA
							FROM    BOLETA B
									, USUARIO U
									, DEUDA D
									, DEUDOR DEUDOR
									, TIPO_DEUDA TD
									, MONEDA M       
							WHERE   COD_BOLETA= {KEY1}
							AND B.COD_USUARIO = U.COD_USUARIO
							AND D.COD_DEUDA = B.COD_DEUDA
							AND TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA
							AND M.COD_MONEDA = TD.COD_MONEDA
							AND DEUDOR.COD_DEUDOR = D.COD_DEUDOR";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT  	convert(varchar(20), getdate(), 103) FECHA_HOY_H
									,B.COD_BOLETA
				        			,B.COD_BOLETA COD_BOLETA_H
									,convert(varchar(20), B.FECHA_BOLETA, 103) FECHA_BOLETA				
									,U.NOM_USUARIO
									,DEUDOR.COD_DEUDOR COD_DEUDOR_H
									,DEUDOR.RUT
			            			,DEUDOR.DIG_VERIF                       
			            			,DEUDOR.NOMBRE + ' ' + DEUDOR.APELLIDO_PATERNO + ' ' + DEUDOR.APELLIDO_MATERNO NOMBRE            
			            			,D.COD_DEUDA
			            			,D.COD_DEUDA COD_DEUDA_H
			            			,TD.COD_TIPO_DEUDA
			            			,D.COD_TIPO_DEUDA COD_TIPO_DEUDA_H
			            			,TD.NOM_TIPO_DEUDA
			            			,M.SIMBOLO  
			            			,B.COD_PLAN_PAGO B_COD_PLAN_PAGO
			            			,TD.TASA_INTERES_PENAL B_TASA_INTERES_PENAL_H
			            			,0 ANO_COBRO_PP
			            			,0 NRO_CUOTA_PP
			            			,'' FECHA_VENCTO_PP
			            			,case B.COD_PLAN_PAGO
			  							when null then 'none' 
				  						else ''
					    			end TABLE_DISPLAY
					    			,B.CARTERA_CONOCIDA 
									,'none' MOROSA_DISPLAY
							FROM    BOLETA B
									, USUARIO U
									, DEUDA D
									, DEUDOR DEUDOR
									, TIPO_DEUDA TD
									, MONEDA M       
							WHERE   COD_BOLETA= {KEY1}
							AND B.COD_USUARIO = U.COD_USUARIO
							AND D.COD_DEUDA = B.COD_DEUDA
							AND TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA
							AND M.COD_MONEDA = TD.COD_MONEDA
							AND DEUDOR.COD_DEUDOR = D.COD_DEUDOR";
				}
			break;
			
			//////////////////////    OUTPUT       ///////////////////// 
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci"){
					$sql=	"SELECT	B.COD_BOLETA
		             				,TO_CHAR(B.FECHA_BOLETA,'dd/mm/yyyy') FECHA_BOLETA
		             				,B.MONTO_PESOS
		             				,B.COD_DEUDA
		             				,DEUDOR.RUT
		             				,DEUDOR.DIG_VERIF
		             				,DEUDOR.NOMBRE 
		             				,DEUDOR.APELLIDO_PATERNO 
		             				,DEUDOR.APELLIDO_MATERNO
		             				,case B.CARTERA_CONOCIDA
										when 'S' then 'SI'
										else 'NO'
									end CARTERA_CONOCIDA
									,TO_CHAR(PP.FECHA_VENCTO,'dd/mm/yyyy') FECHA_VENCTO
      						FROM 	BOLETA B LEFT OUTER JOIN PLAN_PAGO PP ON B.COD_PLAN_PAGO = PP.COD_PLAN_PAGO
      								,DEUDA DEUDA, DEUDOR DEUDOR
							WHERE 	B.COD_DEUDA = DEUDA.COD_DEUDA AND
            						DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR
		  					ORDER BY COD_BOLETA DESC";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT	B.COD_BOLETA
		             				,CONVERT(VARCHAR(20), B.FECHA_BOLETA, 103) FECHA_BOLETA
		             				,B.MONTO_PESOS
		             				,B.COD_DEUDA
		             				,DEUDOR.RUT
		             				,DEUDOR.DIG_VERIF
		             				,DEUDOR.NOMBRE 
		             				,DEUDOR.APELLIDO_PATERNO 
		             				,DEUDOR.APELLIDO_MATERNO
		             				,case B.CARTERA_CONOCIDA
										when 'S' then 'SI'
										else 'NO'
									end CARTERA_CONOCIDA 
									,CONVERT(VARCHAR(20), PP.FECHA_VENCTO, 103) FECHA_VENCTO
      						FROM 	BOLETA B LEFT OUTER JOIN PLAN_PAGO PP ON B.COD_PLAN_PAGO = PP.COD_PLAN_PAGO
      								,DEUDA DEUDA, DEUDOR DEUDOR
							WHERE 	B.COD_DEUDA = DEUDA.COD_DEUDA AND
            						DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR
		  					ORDER BY COD_BOLETA DESC";
				}
			break;
			
			//////////////////////    OUTPUT_UCM1      ///////////////////// 
			case 'OUTPUT_UCM1' :
				if (K_TIPO_BD=="oci"){
					$sql=	"SELECT	B.COD_BOLETA
		             				,TO_CHAR(B.FECHA_BOLETA,'dd/mm/yyyy') FECHA_BOLETA
		             				,B.MONTO_PESOS
		             				,B.COD_DEUDA
		             				,DEUDOR.RUT
		             				,DEUDOR.DIG_VERIF
		             				,DEUDOR.NOMBRE 
		             				,DEUDOR.APELLIDO_PATERNO 
		             				,DEUDOR.APELLIDO_MATERNO
		             				,case B.CARTERA_CONOCIDA
										when 'S' then 'SI'
										else 'NO'
									end CARTERA_CONOCIDA 	 
      						FROM 	BOLETA B, DEUDA DEUDA, DEUDOR DEUDOR
							WHERE 	B.COD_DEUDA = DEUDA.COD_DEUDA AND
            						DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR AND
            						DEUDA.COD_TIPO_DEUDA IN (SELECT COD_TIPO_DEUDA FROM TIPO_DEUDA WHERE COD_FAMILIA_DEUDA IN (1,2))
		  					ORDER BY COD_BOLETA DESC";
				}
			break;
			
			//////////////////////    OUTPUT_UCM2      ///////////////////// 
			case 'OUTPUT_UCM2' :
				if (K_TIPO_BD=="oci"){
					$sql=	"SELECT	B.COD_BOLETA
		             				,TO_CHAR(B.FECHA_BOLETA,'dd/mm/yyyy') FECHA_BOLETA
		             				,B.MONTO_PESOS
		             				,B.COD_DEUDA
		             				,DEUDOR.RUT
		             				,DEUDOR.DIG_VERIF
		             				,DEUDOR.NOMBRE 
		             				,DEUDOR.APELLIDO_PATERNO 
		             				,DEUDOR.APELLIDO_MATERNO
		             				,case B.CARTERA_CONOCIDA
										when 'S' then 'SI'
										else 'NO'
									end CARTERA_CONOCIDA 	 
      						FROM 	BOLETA B, DEUDA DEUDA, DEUDOR DEUDOR
							WHERE 	B.COD_DEUDA = DEUDA.COD_DEUDA AND
            						DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR AND
            						DEUDA.COD_TIPO_DEUDA IN (SELECT COD_TIPO_DEUDA FROM TIPO_DEUDA WHERE COD_FAMILIA_DEUDA = 3)
		  					ORDER BY COD_BOLETA DESC";
				}
			break;
			
			///////////////////////       DW_TAB_SUBCUOTA_DETALLE    /////////////////
			case 'DW_TAB_SUBCUOTA_DETALLE' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT	 SB.COD_BOLETA
									,SB.COD_SUB_BOLETA									COD_SUB_BOLETA
									,SB.COD_SUB_BOLETA									SC_COD_SUB_BOLETA_H
									,SB.NRO_SUB_BOLETA 									SC_NRO_SUB_BOLETA
									,SB.NRO_SUB_BOLETA 									SC_NRO_SUB_BOLETA_H
									,TO_CHAR (SB.FECHA_VENCTO, 'dd/mm/yyyy') 			SC_FECHA_VENCTO
									,TO_CHAR (SB.FECHA_VENCTO, 'dd/mm/yyyy') 			SC_FECHA_VENCTO_H
									,Nvl(SB.MONTO_CAPITAL,0) 							SC_MONTO_CAPITAL
									,Nvl(SB.MONTO_CAPITAL,0) 							SC_MONTO_CAPITAL_H
									,Nvl(SB.MONTO_INTERES,0) 							SC_MONTO_INTERES
									,Nvl(SB.MONTO_INTERES,0) 							SC_MONTO_INTERES_H
									,Nvl(SB.MONTO_MULTA,0)								SC_MONTO_MULTA
									,Nvl(SB.MONTO_MULTA,0)								SC_MONTO_MULTA_H
									,(SB.MONTO_CAPITAL + SB.MONTO_INTERES + SB.MONTO_MULTA) 	SC_TOTAL
									,TO_CHAR (FECHA_PAGO, 'DD/MM/YYYY') 				SC_FECHA_PAGO
									,TO_CHAR (FECHA_PAGO, 'DD/MM/YYYY') 				SC_FECHA_PAGO_H
									,0 MONTO_CAPITAL_C
									,0 MONTO_INTERES_C
									,0 TOTAL_C
					FROM 	  SUB_BOLETA SB
					WHERE   SB.COD_BOLETA = {KEY1}";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT	 SB.COD_BOLETA
									,SB.COD_SUB_BOLETA									COD_SUB_BOLETA
									,SB.COD_SUB_BOLETA									SC_COD_SUB_BOLETA_H
									,SB.NRO_SUB_BOLETA 									SC_NRO_SUB_BOLETA
									,SB.NRO_SUB_BOLETA 									SC_NRO_SUB_BOLETA_H
									,convert(varchar(20), SB.FECHA_VENCTO, 103) 		SC_FECHA_VENCTO
									,convert(varchar(20), SB.FECHA_VENCTO, 103) 		SC_FECHA_VENCTO_H
									,isnull(SB.MONTO_CAPITAL,0) 						SC_MONTO_CAPITAL
									,isnull(SB.MONTO_CAPITAL,0) 						SC_MONTO_CAPITAL_H
									,isnull(SB.MONTO_INTERES,0) 						SC_MONTO_INTERES
									,isnull(SB.MONTO_INTERES,0) 						SC_MONTO_INTERES_H
									,isnull(SB.MONTO_MULTA,0)							SC_MONTO_MULTA
									,isnull(SB.MONTO_MULTA,0)							SC_MONTO_MULTA_H
									,(SB.MONTO_CAPITAL + SB.MONTO_INTERES + SB.MONTO_MULTA) 	SC_TOTAL
									,convert(varchar(20), FECHA_PAGO, 103) 				SC_FECHA_PAGO
									,convert(varchar(20), FECHA_PAGO, 103) 				SC_FECHA_PAGO_H
									,0 MONTO_CAPITAL_C
									,0 MONTO_INTERES_C
									,0 TOTAL_C
							FROM 	SUB_BOLETA SB
							WHERE   SB.COD_BOLETA = {KEY1}";
				}
			break;
			
			////////////////////////////     DW_TAB_SUBCUOTA            //////////////////////////
			case 'DW_TAB_SUBCUOTA' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT	TO_CHAR(FECHA_PRIMERA_CUOTA,'dd/mm/yyyy') FECHA_PRIMERA_CUOTA
									,TO_CHAR(FECHA_PRIMERA_CUOTA,'dd/mm/yyyy') FECHA_PRIMERA_CUOTA_H
 									, CANT
 							FROM 	BOLETA
 							WHERE 	COD_BOLETA={KEY1}";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT	convert(varchar(20), FECHA_PRIMERA_CUOTA, 103) FECHA_PRIMERA_CUOTA
									,convert(varchar(20), FECHA_PRIMERA_CUOTA, 103) FECHA_PRIMERA_CUOTA_H
 									, CANT
 							FROM 	BOLETA
 							WHERE 	COD_BOLETA={KEY1}";
				}
			break;
			
			///////////////////              DW_SUBCUOTA         ////////////////////////////////
			case 'DW_SUBCUOTA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT	'' 					D_RECORD_COD_PLAN_PAGO_H
									,'' 				D_FECHA_VENCE_PLAN_H
									,'' 				D_ANO_COBRO_H
									, B.MONTO_CAPITAL	D_MONTO_CAPITAL
									, B.MONTO_CAPITAL	D_MONTO_CAPITAL_H
							       	, B.MONTO_INTERES	D_MONTO_INTERES
					       			, B.MONTO_INTERES	D_MONTO_INTERES_H
						       		, B.MONTO_MULTA		D_MONTO_MULTA
							       	, B.MONTO_MULTA		D_MONTO_MULTA_H
									, B.MONTO_MONEDA	D_MONTO_MONEDA
									, B.MONTO_MONEDA	D_MONTO_MONEDA_H
					       			, B.MONTO_PESOS		D_MONTO_PESOS
					       			, B.MONTO_PESOS		D_MONTO_PESOS_H
							       	, B.DSCTO_CAPITAL	D_DSCTO_CAPITAL
					       			, B.DSCTO_CAPITAL	D_DSCTO_CAPITAL_H
					       			, B.DSCTO_INTERES	D_DSCTO_INTERES
							       	, B.DSCTO_INTERES	D_DSCTO_INTERES_H
					       			, B.DSCTO_MULTA		D_DSCTO_MULTA
					       			, B.DSCTO_MULTA		D_DSCTO_MULTA_H
					       			, B.DSCTO_PESOS		D_DSCTO_PESOS
					       			, B.DSCTO_PESOS		D_DSCTO_PESOS_H
					       			,0		 			D_SALDO_CAPITAL
									,0					D_SALDO_INTERES
				        			,0					D_SALDO_MULTA
				        			,0					D_SALDO_MONEDA
				        			,0					D_SALDO_PESOS
				        			,'none' 			SALDO_DISPLAY
				        			,B.COD_TIPO_DESCUENTO	D_COD_TIPO_DESCUENTO
						FROM 		BOLETA B
					       			, DEUDA D
					       			, MONEDA M
					       			, TIPO_DEUDA TD
						WHERE 		B.COD_DEUDA = D.COD_DEUDA
					     			AND B.COD_BOLETA = {KEY1}
					     			AND TD.COD_MONEDA = M.COD_MONEDA
									AND TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA";
				}
			break;
			
			////////////////////////////////DW_DETALLE_PLAN_PAGO////////////////////////
			case 'DW_DETALLE_PLAN_PAGO' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT 	PP.COD_PLAN_PAGO
									,PP.COD_PLAN_PAGO COD_PLAN_PAGO_H
									,D.COD_DEUDA
									,nvl(PP.ANO_COBRO,9999) ANO_COBRO
									,nvl(PP.ANO_COBRO,9999) ANO_COBRO_H
									,nvl(PP.NRO_CUOTA,9999) NRO_CUOTA
									,TD.COD_TIPO_DEUDA
									,Nvl(TO_CHAR(PP.FECHA_VENCTO,'dd/mm/yyyy'),'31/12/9999') FECHA_VENCTO
									,TO_CHAR(PP.FECHA_VENCTO,'dd/mm/yyyy') FECHA_VENCTO_H
									,F_PP_SALDO_CAPITAL(PP.COD_PLAN_PAGO, B.FECHA_BOLETA) SALDO_CAPITAL
									,F_PP_SALDO_INTERES_NORMAL(PP.COD_PLAN_PAGO, B.FECHA_BOLETA) SALDO_INTERES
				        			,F_PP_SALDO_MULTA (PP.COD_PLAN_PAGO, B.FECHA_BOLETA) SALDO_MULTA
				        			,F_PP_SALDO_PLAN_PAGO(PP.COD_PLAN_PAGO, B.FECHA_BOLETA) SALDO_MONEDA        
				        			,F_MON_GET_VALOR_MONEDA(M.COD_MONEDA, B.FECHA_BOLETA) * F_PP_SALDO_PLAN_PAGO(PP.COD_PLAN_PAGO, B.FECHA_BOLETA) SALDO_PESOS	
									,PP.COD_ESTADO_PLAN_PAGO COD_ESTADO_PLAN_PAGO_H
									,NOM_ESTADO_PLAN_PAGO					
						FROM 		BOLETA B
									,DEUDA D
									,PLAN_PAGO PP
									,MONEDA M
									,TIPO_DEUDA TD
									,ESTADO_PLAN_PAGO EPP
						WHERE		B.COD_DEUDA = D.COD_DEUDA 
									AND B.COD_PLAN_PAGO = PP.COD_PLAN_PAGO(+)
									AND B.COD_BOLETA= {KEY1}
									AND TD.COD_MONEDA = M.COD_MONEDA
									AND TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA
									AND PP.COD_ESTADO_PLAN_PAGO = EPP.COD_ESTADO_PLAN_PAGO(+)
						ORDER BY	PP.NRO_CUOTA";
				}	
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT 	PP.COD_PLAN_PAGO
									,PP.COD_PLAN_PAGO COD_PLAN_PAGO_H
									,D.COD_DEUDA
									,isnull(PP.ANO_COBRO,9999) ANO_COBRO
									,isnull(PP.ANO_COBRO,9999) ANO_COBRO_H
									,isnull(PP.NRO_CUOTA,9999) NRO_CUOTA
									,TD.COD_TIPO_DEUDA
									,isnull(convert(varchar(20), PP.FECHA_VENCTO, 103),'31/12/9999') FECHA_VENCTO
									,convert(varchar(20), PP.FECHA_VENCTO, 103) FECHA_VENCTO_H
									,dbo.f_pp_saldo_capital(PP.COD_PLAN_PAGO, B.FECHA_BOLETA) SALDO_CAPITAL
									,dbo.f_pp_saldo_interes_normal(PP.COD_PLAN_PAGO, B.FECHA_BOLETA) SALDO_INTERES
				        			,dbo.f_pp_saldo_multa (PP.COD_PLAN_PAGO, B.FECHA_BOLETA) SALDO_MULTA
				        			,dbo.f_pp_saldo_plan_pago(PP.COD_PLAN_PAGO, B.FECHA_BOLETA) SALDO_MONEDA        
				        			,dbo.f_mon_get_valor_moneda(M.COD_MONEDA, B.FECHA_BOLETA) * dbo.f_pp_saldo_plan_pago(PP.COD_PLAN_PAGO, B.FECHA_BOLETA) SALDO_PESOS	
									,PP.COD_ESTADO_PLAN_PAGO COD_ESTADO_PLAN_PAGO_H
				        			,NOM_ESTADO_PLAN_PAGO					
						FROM 		BOLETA B left outer join PLAN_PAGO PP on B.COD_PLAN_PAGO = PP.COD_PLAN_PAGO
										left outer join ESTADO_PLAN_PAGO EPP on PP.COD_ESTADO_PLAN_PAGO = EPP.COD_ESTADO_PLAN_PAGO
									,DEUDA D
									,MONEDA M
									,TIPO_DEUDA TD
						WHERE		B.COD_DEUDA = D.COD_DEUDA 
									AND B.COD_BOLETA= {KEY1}
									AND TD.COD_MONEDA = M.COD_MONEDA
									AND TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA
						ORDER BY	PP.NRO_CUOTA";
				}
			break;
			//////////////////////////    DD_COD_TIPO_DEUDA     //////////////////////////////
			case 'DD_COD_TIPO_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql="SELECT 	D.COD_DEUDA 
									,TD.COD_TIPO_DEUDA
									,TD.COD_TIPO_DEUDA COD_TIPO_DEUDA_H
						FROM	    DEUDA D
									, DEUDOR DEUDOR
									, TIPO_DEUDA TD
									, MONEDA M       
						WHERE	   	DEUDOR.RUT = {KEY1}
									AND TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA
									AND M.COD_MONEDA = TD.COD_MONEDA
									AND DEUDOR.COD_DEUDOR = D.COD_DEUDOR
									AND COD_ESTADO_DEUDA IN (1 , 2)";
				}
			break;
			
			/////////////////////    ULTIMO_DIA       ///////////////////////
			case 'ULTIMO_DIA' :
				if (K_TIPO_BD=="oci"){
					$sql="SELECT 	to_char(TRUNC(LAST_DAY(SYSDATE)),'dd/mm/yyyy') ULTIMO_DIA_DEL_MES
						FROM 		DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT 	convert(varchar(20),DATEADD(s,-1,DATEADD(mm, DATEDIFF(m,0,GETDATE())+1,0)),103) ULTIMO_DIA_DEL_MES";
				}
			break;
			
			///////////////////////////      PRINT_RECORD_USACH     /////////////////////////
			case 'PRINT_RECORD_USACH' :
				if (K_TIPO_BD=="oci"){
					$sql=	"SELECT 	DE.NOMBRE
										,DE.APELLIDO_PATERNO
										,DE.APELLIDO_MATERNO
										,DE.RUT
										,DE.DIG_VERIF
                 					    ,U.NOM_UNIVERSIDAD
				        				,CASE 
				            				WHEN 		DIG_VERIF = 'K' 
				          					THEN 		'0'
				            			ELSE 		DIG_VERIF
         								END DIG_VERIF_CB
										,D.COD_TIPO_DEUDA
										,D.COD_DEUDA
										,D.COD_DEUDOR
										,F_DEUDA_SALDO_TOTAL(D.COD_DEUDA, B.FECHA_BOLETA) SALDO_DEUDA
										,(B.MONTO_CAPITAL + B.MONTO_INTERES + B.MONTO_MULTA) - (B.DSCTO_CAPITAL + B.DSCTO_INTERES + B.DSCTO_MULTA) VALOR_CUOTA
										,B.COD_BOLETA
										,B.CANT
										,S.NRO_SUB_BOLETA
										,S.COD_SUB_BOLETA 
										,S.FECHA_VENCTO
										,S.MONTO_CAPITAL
										,S.MONTO_INTERES
										,S.MONTO_MULTA
										,NVL(S.MONTO_CAPITAL, 0) + NVL(S.MONTO_INTERES, 0) + NVL(S.MONTO_MULTA, 0) VALOR_SUB_CUOTA
										,To_Char(FECHA_VENCTO,'dd/mm/yyyy')FECHA_VENCTO
				        				,To_Char(FECHA_VENCTO,'ddmmyyyy')FECHA_VENCTO_CB
        								,M.SIMBOLO
        								,M.COD_MONEDA
        								,D.COD_ESTADO_DEUDA
        								--,{PARAM2} DECLARACION_DEUDA
        								, B.COD_BOLETA||'-'||404||'-'||(NVL(S.MONTO_CAPITAL, 0) + NVL(S.MONTO_INTERES, 0) + NVL(S.MONTO_MULTA, 0))||'-'||RUT||'-'||To_Char(FECHA_VENCTO,'ddmmyy')||'-'||M.COD_MONEDA      CODIGO 
							FROM 		SUB_BOLETA S, BOLETA B, DEUDA D, DEUDOR DE, TIPO_DEUDA TD, MONEDA M, UNIVERSIDAD U
							WHERE 		S.COD_BOLETA = {PARAM1}
										AND B.COD_BOLETA = S.COD_BOLETA
										AND D.COD_DEUDA = B.COD_DEUDA
										AND DE.COD_DEUDOR = D.COD_DEUDOR
  										AND D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
  										AND TD.COD_MONEDA = M.COD_MONEDA
                      					AND D.COD_UNIVERSIDAD = U.COD_UNIVERSIDAD";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql="no usada";
				}
			break;
			
			///////////////////////////     PRINT_RECORD_UCT      /////////////////////////
			case 'PRINT_RECORD_UCT' :
				if (K_TIPO_BD=="oci"){
					$sql=	"";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql="SELECT  B.COD_BOLETA
								  ,DR.RUT
								  ,TD.COD_FAMILIA_DEUDA
								  ,DR.DIG_VERIF
								  ,DR.APELLIDO_PATERNO+' '+DR.APELLIDO_MATERNO+' '+DR.NOMBRE NOMBRE_DEUDOR 
								  ,PP.NRO_CUOTA
								  ,PP.ANO_COBRO
								  ,SB.NRO_SUB_BOLETA
								  ,CONVERT(VARCHAR(20),SB.FECHA_VENCTO,103) FECHA_VCTO
								  ,SB.MONTO_CAPITAL+SB.MONTO_INTERES+SB.MONTO_MULTA MONTO_CUOTA
								  ,PP.MONTO_CAPITAL+PP.MONTO_INTERES CUOTA_ANUAL
								  ,M.SIMBOLO
								  ,dbo.f_mon_get_valor_moneda(M.COD_MONEDA,B.FECHA_BOLETA) VALOR_MONEDA
								  ,DE.COD_TIPO_DEUDA
								  ,dbo.f_get_nro_folio(DE.COD_TIPO_DEUDA,PP.NRO_CUOTA,PP.ANO_COBRO) NRO_FOLIO
								  ,(SB.MONTO_CAPITAL * dbo.f_mon_get_valor_moneda(M.COD_MONEDA,B.FECHA_BOLETA)) VALOR_CUOTA_PESOS
								  ,((SB.MONTO_INTERES+SB.MONTO_MULTA)*dbo.f_mon_get_valor_moneda(M.COD_MONEDA,B.FECHA_BOLETA)) INTERES_PENAL_PESOS
								  ,round(((SB.MONTO_CAPITAL+SB.MONTO_INTERES+SB.MONTO_MULTA)*dbo.f_mon_get_valor_moneda(M.COD_MONEDA,B.FECHA_BOLETA)), 0) VALOR_PAGAR_PESOS
								  , B.MONTO_PESOS
							FROM BOLETA B
								,SUB_BOLETA SB
								,PLAN_PAGO PP
								,DEUDA DE
								,DEUDOR DR
								,MONEDA M
								,TIPO_DEUDA TD
							WHERE	B.COD_BOLETA = {PARAM1}
								AND B.COD_BOLETA = SB.COD_BOLETA
								AND B.COD_PLAN_PAGO = PP.COD_PLAN_PAGO
								AND B.COD_DEUDA = DE.COD_DEUDA
								AND DE.COD_DEUDOR = DR.COD_DEUDOR
								AND DE.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
								AND TD.COD_MONEDA = M.COD_MONEDA";
				}
			break;
			
			
			///////////////////////////      PRINT_RECORD_UCM     /////////////////////////
			case 'PRINT_RECORD_UCM' :
				if (K_TIPO_BD=="oci"){
					$sql=	"SELECT 	DE.NOMBRE
					                  ,DE.APELLIDO_PATERNO
					                  ,DE.APELLIDO_MATERNO
					                  ,DE.RUT
					                  ,DE.DIG_VERIF
					                  ,To_Char(B.FECHA_BOLETA,'dd/mm/yyyy hh:mm') FECHA_BOLETA
					                  ,U.NOM_UNIVERSIDAD
					                  ,CASE 
					                  WHEN 		DIG_VERIF = 'K' 
					                  THEN 		'0'
					                  ELSE 		DIG_VERIF
					                  END DIG_VERIF_CB
					                  ,MA.NOM_CARRERA
					                  ,D.COD_TIPO_DEUDA
					                  ,FM.COD_FAMILIA_DEUDA
					                  ,f_get_nro_comprobante(TD.COD_TIPO_DEUDA,FM.COD_FAMILIA_DEUDA,B.COD_BOLETA) COD_COMPROBANTE
					                  ,D.COD_DEUDA
					                  ,D.COD_DEUDOR
					                  ,F_DEUDA_SALDO_TOTAL(D.COD_DEUDA, B.FECHA_BOLETA) SALDO_DEUDA
					                  ,(B.MONTO_CAPITAL + B.MONTO_INTERES + B.MONTO_MULTA) - (B.DSCTO_CAPITAL + B.DSCTO_INTERES + B.DSCTO_MULTA) VALOR_CUOTA
					                  ,B.COD_BOLETA
					                  ,B.CANT
					                  ,S.NRO_SUB_BOLETA
					                  ,S.COD_SUB_BOLETA
					                  ,PL.NRO_CUOTA 
					                  ,S.MONTO_CAPITAL
					                  ,S.MONTO_INTERES
					                  ,S.MONTO_MULTA
					                  ,NVL(S.MONTO_CAPITAL, 0) + NVL(S.MONTO_INTERES, 0) + NVL(S.MONTO_MULTA, 0) VALOR_SUB_CUOTA
					                  ,To_Char(PL.FECHA_VENCTO,'dd/mm/yyyy')FECHA_VENCTO
					                  ,To_Char(S.FECHA_VENCTO,'ddmmyyyy')FECHA_VENCTO_CB
					                  ,M.SIMBOLO
					                  ,M.COD_MONEDA
					                  ,D.COD_ESTADO_DEUDA
					                  ,0 DECLARACION_DEUDA
					                  ,B.COD_BOLETA||'-'||404||'-'||(NVL(S.MONTO_CAPITAL, 0) + NVL(S.MONTO_INTERES, 0) + NVL(S.MONTO_MULTA, 0))||'-'||RUT||'-'||To_Char(PL.FECHA_VENCTO,'ddmmyy')||'-'||M.COD_MONEDA      CODIGO
					                  ,Round(((B.MONTO_CAPITAL + B.MONTO_INTERES + B.MONTO_MULTA) - (B.DSCTO_CAPITAL + B.DSCTO_INTERES + B.DSCTO_MULTA))* f_mon_get_valor_moneda(3, B.FECHA_BOLETA), 0) VALOR_MONTO_PESOS
					          FROM 		SUB_BOLETA S, BOLETA B, DEUDA D, DEUDOR DE, TIPO_DEUDA TD, MONEDA M, UNIVERSIDAD U, MATRICULA MA, FAMILIA_DEUDA FM, PLAN_PAGO PL
					            WHERE 		S.COD_BOLETA = {PARAM1}
					              AND B.COD_BOLETA = S.COD_BOLETA
					              AND D.COD_DEUDA = B.COD_DEUDA
					              AND DE.COD_DEUDOR = D.COD_DEUDOR
					              AND D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
					              AND TD.COD_MONEDA = M.COD_MONEDA
					              AND D.COD_UNIVERSIDAD = U.COD_UNIVERSIDAD
					              AND MA.COD_MATRICULA ={PARAM3}
					              AND TD.COD_FAMILIA_DEUDA = FM.COD_FAMILIA_DEUDA
					              AND PL.COD_PLAN_PAGO = B.COD_PLAN_PAGO";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql="no usada";
				}
			break;
			///////////////////////////      BOLETA_DECLARADA     /////////////////////////
			
			case 'BOLETA_DECLARADA' :
				if (K_TIPO_BD=="oci"){
					$sql=	"SELECT      Count(DECLA.COD_DECLARACION) DECLARACION_DEUDA
					
							FROM 		DECLARACION DECLA, BOLETA B, DEUDOR DE , DEUDA D, SUB_BOLETA SB
							WHERE 		B.COD_BOLETA = {PARAM1}
							AND			DECLA.COD_DEUDOR = DE.COD_DEUDOR
					        AND     	B.COD_DEUDA = D.COD_DEUDA
					        AND     	D.COD_DEUDOR = DE.COD_DEUDOR
					        AND     	To_Number (TO_CHAR (SB.FECHA_VENCTO, 'yyyy'))=To_Number (TO_CHAR (SB.FECHA_VENCTO, 'yyyy')-1)";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql="no usada";
				}
			break;
			
			//////////////////////////////////////////////////////////////////////////////////
			
			/////////////////////      AJAX_COD_DEUDA           //////////////////////////////
			case 'AJAX_COD_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT	SIMBOLO
		        					,TASA_INTERES_PENAL
							FROM    DEUDA D
		        					, DEUDOR DR
		        					,TIPO_DEUDA TD
		       				 		, MONEDA M
							WHERE   D.COD_DEUDA = {PARAM1}
                        			AND D.COD_DEUDOR = DR.COD_DEUDOR  
		        					AND D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
		        					AND TD.COD_MONEDA = M.COD_MONEDA";
				}
			break;
			
			///////////////////////     AJAX_DATOS          ////////////////////// 
			case 'AJAX_DATOS' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT  	count(*) CUENTA
							FROM    DEUDOR 
							WHERE   RUT = {PARAM1}";
				}
			break;
			///////////////////////AJAX_POSEE_DEUDAS////////////////////// 
			case 'AJAX_POSEE_DEUDAS' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT  		Count(*) TOTAL
							        	,DIG_VERIF
							        	,DEUDOR.NOMBRE||' '||DEUDOR.APELLIDO_PATERNO||' '||DEUDOR.APELLIDO_MATERNO NOMBRE
							FROM    	DEUDOR, DEUDA 
							WHERE   	DEUDOR.RUT = {PARAM1}
										AND DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR
										AND COD_ESTADO_DEUDA IN (".self::K_ESTADO_DEUDA_NO_EXIGIBLE.",".self::K_ESTADO_DEUDA_CO_NORMAL.")
							GROUP BY	DIG_VERIF, NOMBRE, APELLIDO_PATERNO,APELLIDO_MATERNO";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT  		Count(*) TOTAL
									,DIG_VERIF
									,DEUDOR.NOMBRE + ' ' + DEUDOR.APELLIDO_PATERNO + ' ' + DEUDOR.APELLIDO_MATERNO NOMBRE
									,COD_TIPO_DEUDA
									FROM    	DEUDOR, DEUDA 
									WHERE   	DEUDOR.RUT = {PARAM1}
									AND DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR
									AND COD_ESTADO_DEUDA IN (".self::K_ESTADO_DEUDA_NO_EXIGIBLE.",".self::K_ESTADO_DEUDA_CO_NORMAL.")
									GROUP BY	DIG_VERIF, NOMBRE, APELLIDO_PATERNO,APELLIDO_MATERNO, COD_TIPO_DEUDA";

					
					
				}
			break;
			
			
			/////////////////////    AJAX_DEUDAS     /////////////////////
			case 'AJAX_DEUDAS' :
				if (K_TIPO_BD=="oci"||K_TIPO_BD=="mssql"){
					$sql= 	"SELECT	COD_ESTADO_DEUDA
									,COD_TIPO_DEUDA
							FROM  	DEUDA						
							WHERE   COD_DEUDA = {PARAM1}";
				}
			break;
			
			////////////////////////////   AJAX_DEUDAS_1              ////////////////// 
			case 'AJAX_DEUDAS_1' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= 	"SELECT   	null COD_PLAN_PAGO
			        					,null COD_PLAN_PAGO_H
										,9999 ANO_COBRO
										,9999 ANO_COBRO_H
										,9999 NRO_CUOTA
										,9999 NRO_CUOTA_H
										,'31/12/9999' FECHA_VENCTO
										,null FECHA_VENCTO_H
										,{PARAM1} SALDO_CAPITAL
										,({PARAM2} - {PARAM1}) SALDO_INTERES
										,{PARAM3} SALDO_MULTA
										,{PARAM4} SALDO_MONEDA        
										,({PARAM4} * {PARAM5}) SALDO_PESOS					
										,null COD_ESTADO_PLAN_PAGO_H
				   	 					,'No Exigible' NOM_ESTADO_PLAN_PAGO					
							FROM    	DEUDA D
    						WHERE   	D.COD_DEUDA = {PARAM6}					                        
							ORDER BY    ANO_COBRO ASC";
				}
			break;
			
			/////////////////////////      AJAX_DEUDAS_2      //////////////////////////
			case 'AJAX_DEUDAS_2' :
				if (K_TIPO_BD=="oci"){
					$sql= 	"SELECT  	PP.COD_PLAN_PAGO								COD_PLAN_PAGO
										,PP.COD_PLAN_PAGO								COD_PLAN_PAGO_H
										,PP.ANO_COBRO 									ANO_COBRO
										,PP.ANO_COBRO 									ANO_COBRO_H
			        					,PP.NRO_CUOTA									NRO_CUOTA
			        					,TO_CHAR(PP.FECHA_VENCTO,'dd/mm/yyyy') 			FECHA_VENCTO
			        					,TO_CHAR(PP.FECHA_VENCTO,'dd/mm/yyyy') 			FECHA_VENCTO_H
			        					,F_PP_SALDO_CAPITAL(PP.COD_PLAN_PAGO) 			SALDO_CAPITAL
			       	 					,F_PP_SALDO_INTERES_NORMAL(PP.COD_PLAN_PAGO) 	SALDO_INTERES
			        					,F_PP_SALDO_MULTA (PP.COD_PLAN_PAGO) 			SALDO_MULTA
			        					,F_PP_SALDO_PLAN_PAGO(PP.COD_PLAN_PAGO)			SALDO_MONEDA        
			        					,F_MON_GET_VALOR_MONEDA(M.COD_MONEDA) * F_PP_SALDO_PLAN_PAGO(PP.COD_PLAN_PAGO) SALDO_PESOS
			        					,PP.COD_ESTADO_PLAN_PAGO COD_ESTADO_PLAN_PAGO_H
			        					,EPP.NOM_ESTADO_PLAN_PAGO
								FROM    PLAN_PAGO PP
										, DEUDA D
										, MONEDA M
										, TIPO_DEUDA TD
										, ESTADO_PLAN_PAGO EPP
								WHERE   D.COD_DEUDA = {PARAM1}
										AND F_PP_SALDO_PLAN_PAGO (PP.COD_PLAN_PAGO) > 0
										AND D.COD_DEUDA = PP.COD_DEUDA
										AND TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA
										AND TD.COD_MONEDA = M.COD_MONEDA
										AND PP.COD_ESTADO_PLAN_PAGO = EPP.COD_ESTADO_PLAN_PAGO
								ORDER BY ANO_COBRO ASC";
				}
				
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT  		PP.COD_PLAN_PAGO										COD_PLAN_PAGO
										,PP.COD_PLAN_PAGO										COD_PLAN_PAGO_H
										,PP.ANO_COBRO 											ANO_COBRO
										,PP.ANO_COBRO 											ANO_COBRO_H
			        					,PP.NRO_CUOTA											NRO_CUOTA
			        					,convert(varchar(20), PP.FECHA_VENCTO, 103) 			FECHA_VENCTO
			        					,convert(varchar(20), PP.FECHA_VENCTO, 103) 			FECHA_VENCTO_H
			        					,dbo.f_pp_saldo_capital(PP.COD_PLAN_PAGO, getdate()) 	SALDO_CAPITAL
			       	 					,dbo.f_pp_saldo_interes_normal(PP.COD_PLAN_PAGO, getdate())	SALDO_INTERES
			        					,dbo.f_pp_saldo_multa(PP.COD_PLAN_PAGO, getdate())		SALDO_MULTA
			        					,dbo.f_pp_saldo_plan_pago(PP.COD_PLAN_PAGO, getdate()) SALDO_MONEDA        
			        					,dbo.f_mon_get_valor_moneda(M.COD_MONEDA, getdate()) * dbo.f_pp_saldo_plan_pago(PP.COD_PLAN_PAGO, getdate()) SALDO_PESOS
			        					,PP.COD_ESTADO_PLAN_PAGO COD_ESTADO_PLAN_PAGO_H
			        					,EPP.NOM_ESTADO_PLAN_PAGO
								FROM    PLAN_PAGO PP
										, DEUDA D
										, MONEDA M
										, TIPO_DEUDA TD
										, ESTADO_PLAN_PAGO EPP
								WHERE   D.COD_DEUDA = {PARAM1}
										AND dbo.f_pp_saldo_plan_pago (PP.COD_PLAN_PAGO, getdate()) > 0
										AND D.COD_DEUDA = PP.COD_DEUDA
										AND TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA
										AND TD.COD_MONEDA = M.COD_MONEDA
										AND PP.COD_ESTADO_PLAN_PAGO = EPP.COD_ESTADO_PLAN_PAGO
								ORDER BY ANO_COBRO ASC";
				}
			break;
			
			///////////////////////////AJAX_FECHAS///////////////////////////
			case 'AJAX_FECHAS' :
				if (K_TIPO_BD=="oci"){
					$sql= 	"SELECT to_char(Add_Months(to_Date('{PARAM1}','dd/mm/yyyy'), {PARAM2}), 'dd/mm/yyyy') FECHA 
							FROM 	DUAL";
				}				
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	convert(varchar(20), dateadd(mm,{PARAM2},dbo.to_date('{PARAM1}')), 103) FECHA";
				}
			break;
			
			///////////////////////////AJAX_MAX_SUBCUOTA///////////////////////////
			case 'AJAX_MAX_SUBCUOTA' :
				if (K_TIPO_BD=="oci"){
					$sql= 	"SELECT F_GET_PARAMETRO ('{PARAM1}') MAX_SUBCUOTA
							FROM 	DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= 	"SELECT dbo.f_get_parametro('{PARAM1}') MAX_SUBCUOTA";
				}
			break;
			
			//////////////////////////AJAX_VALOR_MONEDA///////////////////////////
			case 'AJAX_VALOR_MONEDA' :
				if (K_TIPO_BD=="oci"){
					$sql=	"SELECT  f_mon_get_valor_moneda(COD_MONEDA) VALOR_MONEDA
							 FROM    TIPO_DEUDA TD
							 WHERE   TD.COD_TIPO_DEUDA = '{PARAM1}'";
				}
				else if(K_TIPO_BD=="mssql"){
					$sql=	"SELECT  dbo.f_mon_get_valor_moneda(COD_MONEDA, getdate()) VALOR_MONEDA
							 FROM    TIPO_DEUDA TD
							 WHERE   TD.COD_TIPO_DEUDA = '{PARAM1}'";
				}
			break;
			
			///////////////////////////AJAX_SALDO_DEUDA///////////////////////////
			case 'AJAX_SALDO_DEUDA' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT F_DEUDA_SALDO_TOTAL({PARAM1}) SALDO_DEUDA FROM DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT dbo.f_deuda_saldo_total({PARAM1}, getdate()) SALDO_DEUDA";
				}
			break;
			///////////////////////////AJAX_SALDO_CAPITAL///////////////////////////
			case 'AJAX_SALDO_CAPITAL' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "select round({PARAM1} / (1 + (td.tasa_interes_credito/100)),4) SALDO_CAPITAL 
					FROM 	tipo_deuda td, deuda d 
					WHERE 	d.cod_deuda = {PARAM2}  
					AND  	td.cod_tipo_deuda = d.cod_tipo_deuda";
				}
			break;
			//////////////////////////////SELECT_BOLETA//////////////////////////////
			case 'SELECT_BOLETA' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT	D.COD_ESTADO_DEUDA 
									,B.COD_PLAN_PAGO
									,P.COD_ESTADO_PLAN_PAGO
									,f_deuda_cuota_vigente(D.COD_DEUDA) COD_PLAN_VIGENTE
							FROM 	BOLETA B
									,DEUDA D
									,PLAN_PAGO P
							WHERE	B.COD_BOLETA = {PARAM1}			
							  		AND D.COD_DEUDA = B.COD_DEUDA
							  		AND P.COD_PLAN_PAGO (+)= B.COD_PLAN_PAGO";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT	D.COD_ESTADO_DEUDA 
									,B.COD_PLAN_PAGO
									,P.COD_ESTADO_PLAN_PAGO
									,dbo.f_deuda_cuota_vigente(D.COD_DEUDA, getdate()) COD_PLAN_VIGENTE
							FROM 	BOLETA B left outer join PLAN_PAGO P on B.COD_PLAN_PAGO = P.COD_PLAN_PAGO
									,DEUDA D
							WHERE	B.COD_BOLETA = {PARAM1}			
							  		AND D.COD_DEUDA = B.COD_DEUDA";
				}
			break;
			////////////////////////////// DD_CARTERA_CONOCIDA ///////////////////////
			case 'DD_CARTERA_CONOCIDA' :
			if (K_TIPO_BD=="oci"){
				$sql = "SELECT 'S' CARTERA_CONOCIDA
								, 'SI' NOM_CARTERA_CONOCIDA 
						from DUAL 
						UNION 
						SELECT 'N' CARTERA_CONOCIDA
								, 'NO' NOM_CARTERA_CONOCIDA	
						from DUAL";
			}
			else if (K_TIPO_BD=="oci" || K_TIPO_BD == "mssql"){
				$sql = "SELECT 'S' CARTERA_CONOCIDA
								, 'SI' NOM_CARTERA_CONOCIDA 
						UNION 
						SELECT 'N' CARTERA_CONOCIDA
								, 'NO' NOM_CARTERA_CONOCIDA";	
			}
			break;
			
			////////////////////////////// DEUDA_MOROSA_USACH ///////////////////////
			case 'DEUDA_MOROSA_USACH' :
			if (K_TIPO_BD=="oci"){
				$sql = "SELECT ANO_COBRO
  						FROM BOLETA BO
        					,PLAN_PAGO PP
  						WHERE BO.COD_BOLETA = {PARAM1}
  							AND BO.COD_DEUDA = PP.COD_DEUDA
  							AND PP.COD_ESTADO_PLAN_PAGO = 4
  						ORDER BY ANO_COBRO ASC";
			}
			break;
			
			////////////////////////////// AJAX_DEUDA_MOROSA ///////////////////////
			case 'AJAX_DEUDA_MOROSA' :
			if (K_TIPO_BD=="oci"){
				$sql = "SELECT ANO_COBRO
  						FROM DEUDA DE
        					,PLAN_PAGO PP
  						WHERE DE.COD_DEUDA = {PARAM1}
                			AND DE.COD_DEUDA = PP.COD_DEUDA
  							AND PP.COD_ESTADO_PLAN_PAGO = 4
  						ORDER BY ANO_COBRO ASC";
			}
			break;
			
			////////////////////////////// OBTIENE_ULT_MATRICULA ///////////////////////
			case 'OBTIENE_ULT_MATRICULA' :
			if (K_TIPO_BD=="oci"){
				$sql = "SELECT  COD_MATRICULA
        						,SEMESTRE
        						,ANO_MATRICULA
						FROM MATRICULA
						WHERE COD_MATRICULA IN (SELECT MAX(COD_MATRICULA) 
						                        FROM MATRICULA
						                        WHERE COD_DEUDOR = {PARAM1})";
			}
			break;
			
			////////////////////////////// OBTIENE_DEUDOR ///////////////////////
			case 'OBTIENE_DEUDOR' :
			if (K_TIPO_BD=="oci"){
				$sql = "SELECT DR.COD_DEUDOR 
						FROM BOLETA B, DEUDOR DR, DEUDA DE
                     	WHERE B.COD_DEUDA = DE.COD_DEUDA
                      		AND DE.COD_DEUDOR = DR.COD_DEUDOR
                      		AND B.COD_BOLETA = {PARAM1}";
			}
			break;
			
			////////////////////////////// AJAX_VALIDA_FAMILIA ///////////////////////
			case 'AJAX_VALIDA_FAMILIA' :
			if (K_TIPO_BD=="oci" || K_TIPO_BD == "mssql"){
				$sql = "SELECT COD_FAMILIA_DEUDA 
				        FROM TIPO_DEUDA
				        WHERE COD_TIPO_DEUDA ='{PARAM1}'";	
			}
			break;
			
			default:
				base::error("sql_boleta::get_sql, selector: '$selector_sql' no definido");			
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
					
		

		return $sql;
	}
}
?>