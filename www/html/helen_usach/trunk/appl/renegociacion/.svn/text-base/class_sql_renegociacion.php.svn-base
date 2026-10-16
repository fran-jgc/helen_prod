<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_renegociacion extends sql_tabla {
	const K_ESTADO_RENEGOCIACION_EMITIDA 	= 1;
	const K_ESTADO_RENEGOCIACION_CONFIRMADO = 2;
	const K_ESTADO_RENEGOCIACION_ANULADA 	= 3;
	const K_EST_DEUDA_COBRANZA_NORMAL		= 2;
	const K_EST_PP_PAGADA					= 3;
	static function get_sql($selector_sql) {
		switch ($selector_sql) {
			///////////////// INPUT ///////////////
			case 'INPUT' :
				///////////////// ORACLE ///////////////
				if (K_TIPO_BD=="oci"){
					$sql =	"SELECT COD_RENEGOCIACION
									,To_Char(R.FECHA_RENEGOCIACION, 'dd/mm/yyyy') FECHA_RENEGOCIACION
									, R.COD_USUARIO
									, U.NOM_USUARIO
									, R.OBS
									, R.COD_ESTADO_RENEGOCIACION
									, ER.NOM_ESTADO_RENEGOCIACION
									, DE.COD_DEUDA
									, DE.COD_DEUDA COD_DEUDA_DD
									, DE.COD_DEUDA COD_DEUDA_H 
									, D.RUT
									, D.DIG_VERIF
									, D.NOMBRE || ' ' || D.APELLIDO_PATERNO || ' '  || D.APELLIDO_MATERNO NOMBRE
									, DE.COD_TIPO_DEUDA
									, SIMBOLO
									, To_Char(R.FECHA_CARTA1, 'dd/mm/yyyy') FECHA_CARTA1
									, To_Char(R.FECHA_CARTA2, 'dd/mm/yyyy') FECHA_CARTA2
									, To_Char(R.FECHA_CARTA3, 'dd/mm/yyyy') FECHA_CARTA3
									, f_last_mod('NOM_USUARIO', 'RENEGOCIACION', 'COD_ESTADO_RENEGOCIACION', R.COD_RENEGOCIACION) NOM_USUARIO_CAMBIO
							        , f_last_mod('FECHA_CAMBIO', 'RENEGOCIACION', 'COD_ESTADO_RENEGOCIACION', R.COD_RENEGOCIACION) FECHA_CAMBIO
							        ,case R.COD_ESTADO_RENEGOCIACION
			  							when ".self::K_ESTADO_RENEGOCIACION_EMITIDA." then '' 
				  						else 'none'
					    			end TABLE_BOLETA 
					    			,case R.COD_ESTADO_RENEGOCIACION
			  							when ".self::K_ESTADO_RENEGOCIACION_CONFIRMADO." then 'none' 
				  						else ''
					    			end TABLE_PLAN_PAGO 
					    	FROM 	RENEGOCIACION R, USUARIO U, ESTADO_RENEGOCIACION ER, DEUDA DE
									, DEUDOR D, TIPO_DEUDA TD, MONEDA M
							WHERE 	R.COD_RENEGOCIACION = {KEY1}
							AND		R.COD_USUARIO = U.COD_USUARIO
							AND		R.COD_ESTADO_RENEGOCIACION = ER.COD_ESTADO_RENEGOCIACION
							AND		R.COD_DEUDA = DE.COD_DEUDA
							AND		DE.COD_DEUDOR = D.COD_DEUDOR
							AND		DE.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
							AND		TD.COD_MONEDA = M.COD_MONEDA";
				}
				///////////////// SQL-SERVER ///////////////
				else if (K_TIPO_BD=="mssql"){
					$sql = "pasar select oracle a SQL server";
				}
				break;

				///////////////// OUTPUT///////////////
			case 'OUTPUT' :
				///////////////// ORACLE ///////////////
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	R.COD_RENEGOCIACION
									,To_Char(R.FECHA_RENEGOCIACION, 'dd/mm/yyyy') FECHA_RENEGOCIACION
									,DR.RUT 
									,DR.DIG_VERIF
									,DR.NOMBRE
									,DR.APELLIDO_PATERNO
									,DR.APELLIDO_MATERNO
									,DA.COD_DEUDA
									,DA.COD_TIPO_DEUDA
									,ER.COD_ESTADO_RENEGOCIACION
									,NOM_ESTADO_RENEGOCIACION
							FROM 	RENEGOCIACION R
									,DEUDOR DR
									,DEUDA DA
									,ESTADO_RENEGOCIACION ER
							WHERE 	R.COD_DEUDA = DA.COD_DEUDA
										AND DA.COD_DEUDOR = DR.COD_DEUDOR
										AND ER.COD_ESTADO_RENEGOCIACION = R.COD_ESTADO_RENEGOCIACION
							ORDER BY	R.COD_RENEGOCIACION DESC";
				}
				///////////////// SQL-SERVER  ///////////////
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT R.COD_RENEGOCIACION
									 ,convert(varchar, R.FECHA_RENEGOCIACION, 103) FECHA_RENEGOCIACION
									 ,DR.RUT
									 ,DR.DIG_VERIF
									 ,DR.NOMBRE
									 ,DR.APELLIDO_PATERNO
									 ,DR.APELLIDO_MATERNO
									 ,DA.COD_DEUDA
									 ,DA.COD_TIPO_DEUDA
									 ,NOM_ESTADO_RENEGOCIACION
									 ,U.NOM_USUARIO
									 ,R.AUTORIZADO 
							FROM RENEGOCIACION R, DEUDOR DR, DEUDA DA, ESTADO_RENEGOCIACION ER, USUARIO U
							WHERE R.COD_DEUDA = DA.COD_DEUDA
							  AND DA.COD_DEUDOR = DR.COD_DEUDOR
							  AND R.COD_USUARIO = U.COD_USUARIO
							  AND ER.COD_ESTADO_RENEGOCIACION = R.COD_ESTADO_RENEGOCIACION
						  	ORDER BY R.COD_RENEGOCIACION DESC";
				}
				break;
				///////////////// OUTPUT_DD_COD_ESTADO ///////////////
			case 'OUTPUT_DD_COD_ESTADO' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT		COD_ESTADO_RENEGOCIACION
										,NOM_ESTADO_RENEGOCIACION
							FROM		ESTADO_RENEGOCIACION
							ORDER BY	COD_ESTADO_RENEGOCIACION";
				}
				break;
					
				///////////////// DW_PLAN_PAGO_RENEGOCIACION ///////////////
			case 'DW_PLAN_PAGO_RENEGOCIACION' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	PR.COD_PLAN_PAGO_RENEGOCIACION
				                    , R.COD_RENEGOCIACION
				                    , PR.COD_PLAN_PAGO
				                    , PR.COD_BOLETA
				                    , PR.CANT_CUOTA
				                    , to_char(PR.FECHA_PRIMER_PAGO, 'dd/mm/yyyy') FECHA_PRIMER_PAGO
				                    , PP.ANO_COBRO
								    , PP.NRO_CUOTA
								    , to_char(PP.FECHA_VENCTO, 'dd/mm/yyyy') FECHA_VENCTO
								    , PP.MONTO_CAPITAL
								    , PP.MONTO_INTERES
								    , f_pp_saldo_multa(PP.COD_PLAN_PAGO) MULTA
								    , f_pp_saldo_plan_pago(PP.COD_PLAN_PAGO) SALDO
									, f_pp_saldo_plan_pago(PP.COD_PLAN_PAGO) * f_mon_get_valor_moneda(TD.COD_MONEDA) SALDO_PESOS
							      	, PP.COD_PLAN_PAGO
							      	, EPP.NOM_ESTADO_PLAN_PAGO
							      
			              FROM  	PLAN_PAGO_RENEGOCIACION PR, ESTADO_PLAN_PAGO EPP, PLAN_PAGO PP, RENEGOCIACION R, DEUDA D, TIPO_DEUDA TD
			              WHERE 	PR.COD_RENEGOCIACION = {KEY1}
			              AND		PR.COD_RENEGOCIACION = R.COD_RENEGOCIACION
			              AND		PR.COD_PLAN_PAGO = PP.COD_PLAN_PAGO 
			              AND   	PP.COD_DEUDA = D.COD_DEUDA
			              AND   	D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
			              AND   	EPP.COD_ESTADO_PLAN_PAGO = PP.COD_ESTADO_PLAN_PAGO";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "...";
				}
				break;
					
				///////////////////////////      PRINT_RECORD     /////////////////////////
			case 'PRINT_RECORD' :
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
        								,{PARAM2} DECLARACION_DEUDA
        								, B.COD_BOLETA||'-'||404||'-'||(NVL(S.MONTO_CAPITAL, 0) + NVL(S.MONTO_INTERES, 0) + NVL(S.MONTO_MULTA, 0))||'-'||RUT||'-'||To_Char(FECHA_VENCTO,'ddmmyy')||'-'||M.COD_MONEDA      CODIGO 
							FROM 		SUB_BOLETA S, BOLETA B, DEUDA D, DEUDOR DE, TIPO_DEUDA TD, MONEDA M, UNIVERSIDAD U
							WHERE 		S.COD_BOLETA = {PARAM1}
										AND B.COD_BOLETA = S.COD_BOLETA
										AND D.COD_DEUDA = B.COD_DEUDA
										AND DE.COD_DEUDOR = D.COD_DEUDOR
  										AND D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
  										AND TD.COD_MONEDA = M.COD_MONEDA
                      AND D.COD_UNIVERSIDAD = U.COD_UNIVERSIDAD
					";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql="aun  no implementada para mssql";
				}
				break;

				
				///////////////////////////      AJAX_CHANGE_TIPO_DEUDA     /////////////////////////
			case 'AJAX_CHANGE_TIPO_DEUDA' :
				if (K_TIPO_BD=="oci"){
					$sql=	"FALTA ****";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select DA.COD_DEUDA
									,C.NOM_CARTERA
									,E.NOM_ESTADO_DEUDA
									,DA.CANT_CUOTA
									,M.SIMBOLO
									,'N' SELECCION
									,P.COD_PLAN_PAGO
									,P.ANO_COBRO
									,convert(varchar, P.FECHA_VENCTO, 103) FECHA_VENCTO
									,dbo.f_pp_saldo_capital(P.COD_PLAN_PAGO, getdate()) + dbo.f_pp_saldo_interes_normal(P.COD_PLAN_PAGO, getdate()) SALDO_CUOTA
									,dbo.f_pp_saldo_multa(P.COD_PLAN_PAGO, getdate()) SALDO_MULTA
									,dbo.f_pp_saldo_plan_pago(P.COD_PLAN_PAGO, getdate()) TOTAL_SALDO_MONEDA
									,dbo.f_pp_saldo_plan_pago(P.COD_PLAN_PAGO, getdate()) * dbo.f_mon_get_valor_moneda(T.COD_MONEDA, getdate()) TOTAL_SALDO_PESOS
							from DEUDA DA, CARTERA C, ESTADO_DEUDA E, TIPO_DEUDA T, MONEDA M, PLAN_PAGO P
							where DA.COD_DEUDA = {PARAM1}
							  and C.COD_CARTERA = DA.COD_CARTERA
							  and E.COD_ESTADO_DEUDA = DA.COD_ESTADO_DEUDA
							  and T.COD_TIPO_DEUDA = DA.COD_TIPO_DEUDA 
							  and M.COD_MONEDA = T.COD_MONEDA
							  and P.COD_DEUDA = DA.COD_DEUDA
							  and dbo.f_pp_saldo_plan_pago(P.COD_PLAN_PAGO, getdate()) > 0";
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
				base::error("sql_rendicion::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>