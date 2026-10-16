<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_plan_pago extends sql_tabla {
	
	const K_ESTADO_PAGO_VIGENTE = 1;
	
	static function get_sql($selector_sql) {
	
		switch ($selector_sql) {
			/////////////////   INPUT   ///////////////
			case 'INPUT' :
				/////////////////   ORACLE   ///////////////
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	DEUDA.COD_DEUDA
									,DEUDOR.RUT
									,DEUDOR.DIG_VERIF
									,DEUDOR.NOMBRE
									,DEUDOR.APELLIDO_PATERNO
									,DEUDOR.APELLIDO_MATERNO
									,TD.COD_TIPO_DEUDA
									,TD.NOM_TIPO_DEUDA
									,M.SIMBOLO
									,PP.NRO_CUOTA
									,PP.ANO_COBRO
									,TO_CHAR(FECHA_VENCTO,'dd/mm/yyyy') FECHA_VENCTO
									,CC.NOM_CONCEPTO_COBRO
									,PP.COD_PLAN_PAGO
									,PP.COD_EMPRESA_COBRANZA
									,PUBLICA_DICOM
									,PP.COD_ESTADO_PLAN_PAGO
									,MONTO_CAPITAL MONTO_CAPITAL_H
									,MONTO_INTERES MONTO_INTERES_H
									,PP.COD_CONCEPTO_COBRO COD_CONCEPTO_COBRO_H
									,E.NOM_ESTADO_PLAN_PAGO
                            FROM	PLAN_PAGO PP
									, DEUDA DEUDA
									, DEUDOR DEUDOR
									, TIPO_DEUDA TD
									, MONEDA M
									, CONCEPTO_COBRO CC
									, EMPRESA_COBRANZA EC
									, ESTADO_PLAN_PAGO E									
							WHERE	DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR 
							AND		TD.COD_TIPO_DEUDA = DEUDA.COD_TIPO_DEUDA
							AND		M.COD_MONEDA = TD.COD_MONEDA
							AND		PP.COD_DEUDA = DEUDA.COD_DEUDA
							AND		PP.COD_CONCEPTO_COBRO = CC.COD_CONCEPTO_COBRO (+)
							AND		PP.COD_EMPRESA_COBRANZA = EC.COD_EMPRESA_COBRANZA (+)
							AND		PP.COD_PLAN_PAGO = {KEY1}
							AND		E.COD_ESTADO_PLAN_PAGO = PP.COD_ESTADO_PLAN_PAGO";
				}
				/////////////////   SQL-SERVER ///////////////
				else if (K_TIPO_BD=="mssql"){
					if(K_CLIENTE == 'UTEM'){
						$sql = "SELECT 	DEUDA.COD_DEUDA								
									,DEUDOR.RUT
									,DEUDOR.DIG_VERIF
									,DEUDOR.NOMBRE
									,DEUDOR.APELLIDO_PATERNO
									,DEUDOR.APELLIDO_MATERNO
									,TD.COD_TIPO_DEUDA
									,TD.NOM_TIPO_DEUDA
									,M.SIMBOLO
									,PP.NRO_CUOTA
									,PP.ANO_COBRO
									,convert(varchar(20),FECHA_VENCTO,103) FECHA_VENCTO
									,CC.NOM_CONCEPTO_COBRO
									,PP.COD_PLAN_PAGO
									,PP.COD_EMPRESA_COBRANZA
									,PUBLICA_DICOM
									,PP.COD_ESTADO_PLAN_PAGO
									,MONTO_CAPITAL MONTO_CAPITAL_H
									,MONTO_INTERES MONTO_INTERES_H
									,PP.COD_CONCEPTO_COBRO COD_CONCEPTO_COBRO_H
									,dbo.f_estado_plan_pago_reneg(PP.COD_PLAN_PAGO) NOM_ESTADO_PLAN_PAGO
							FROM	PLAN_PAGO PP left outer join CONCEPTO_COBRO CC on PP.COD_CONCEPTO_COBRO = CC.COD_CONCEPTO_COBRO 
										left outer join EMPRESA_COBRANZA EC on PP.COD_EMPRESA_COBRANZA = EC.COD_EMPRESA_COBRANZA
									, DEUDA DEUDA
									, DEUDOR DEUDOR
									, TIPO_DEUDA TD
									, MONEDA M
									, ESTADO_PLAN_PAGO E									
							WHERE	DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR 
							AND		TD.COD_TIPO_DEUDA = DEUDA.COD_TIPO_DEUDA
							AND		M.COD_MONEDA = TD.COD_MONEDA
							AND		PP.COD_DEUDA = DEUDA.COD_DEUDA
							AND		PP.COD_PLAN_PAGO = {KEY1}
							AND		E.COD_ESTADO_PLAN_PAGO = PP.COD_ESTADO_PLAN_PAGO";
					}
					else{
						$sql = "SELECT 	DEUDA.COD_DEUDA								
									,DEUDOR.RUT
									,DEUDOR.DIG_VERIF
									,DEUDOR.NOMBRE
									,DEUDOR.APELLIDO_PATERNO
									,DEUDOR.APELLIDO_MATERNO
									,TD.COD_TIPO_DEUDA
									,TD.NOM_TIPO_DEUDA
									,M.SIMBOLO
									,PP.NRO_CUOTA
									,PP.ANO_COBRO
									,convert(varchar(20),FECHA_VENCTO,103) FECHA_VENCTO
									,CC.NOM_CONCEPTO_COBRO
									,PP.COD_PLAN_PAGO
									,PP.COD_EMPRESA_COBRANZA
									,PUBLICA_DICOM
									,PP.COD_ESTADO_PLAN_PAGO
									,MONTO_CAPITAL MONTO_CAPITAL_H
									,MONTO_INTERES MONTO_INTERES_H
									,PP.COD_CONCEPTO_COBRO COD_CONCEPTO_COBRO_H
									,E.NOM_ESTADO_PLAN_PAGO
							FROM	PLAN_PAGO PP left outer join CONCEPTO_COBRO CC on PP.COD_CONCEPTO_COBRO = CC.COD_CONCEPTO_COBRO 
										left outer join EMPRESA_COBRANZA EC on PP.COD_EMPRESA_COBRANZA = EC.COD_EMPRESA_COBRANZA
									, DEUDA DEUDA
									, DEUDOR DEUDOR
									, TIPO_DEUDA TD
									, MONEDA M
									, ESTADO_PLAN_PAGO E									
							WHERE	DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR 
							AND		TD.COD_TIPO_DEUDA = DEUDA.COD_TIPO_DEUDA
							AND		M.COD_MONEDA = TD.COD_MONEDA
							AND		PP.COD_DEUDA = DEUDA.COD_DEUDA
							AND		PP.COD_PLAN_PAGO = {KEY1}
							AND		E.COD_ESTADO_PLAN_PAGO = PP.COD_ESTADO_PLAN_PAGO";
					}
				}
			break;
			
			/////////////////   OUTPUT   ///////////////
			case 'OUTPUT' :
				/////////////////   ORACLE   ///////////////
				if (K_TIPO_BD=="oci"){
					if(K_CLIENTE =="USACH"){
							$sql = "SELECT  PP.COD_PLAN_PAGO
												, PP.COD_DEUDA
												, PP.NRO_CUOTA
												/*INI_CAMPOS*/
												, DR.RUT
												, DR.DIG_VERIF
												, TD.COD_TIPO_DEUDA
												, TD.NOM_TIPO_DEUDA
												, PP.ANO_COBRO
												, TO_CHAR(FECHA_VENCTO,'DD/MM/YYYY') FECHA_VENCTO
												,(PP.MONTO_CAPITAL + PP.MONTO_INTERES) CUOTA_ANUAL
												, null SALDO_K
												, null SALDO_VENCIDO
												, EPP.COD_ESTADO_PLAN_PAGO
												, EPP.NOM_ESTADO_PLAN_PAGO
												, f_pp_es_cruzado(PP.COD_PLAN_PAGO) ES_CRUZADO
												/*FIN_CAMPOS*/
										FROM	PLAN_PAGO PP 
												, DEUDA   DE
												, DEUDOR  DR 
												, TIPO_DEUDA TD
												, ESTADO_PLAN_PAGO EPP
										WHERE	PP.COD_DEUDA = DE.COD_DEUDA
										AND		DE.COD_DEUDOR = DR.COD_DEUDOR
										AND		DE.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
										AND		EPP.COD_ESTADO_PLAN_PAGO=PP.COD_ESTADO_PLAN_PAGO
										AND		PP.COD_DEUDA <> 1
												/*FIN_CONDICION*/
										ORDER BY PP.COD_DEUDA, PP.NRO_CUOTA ASC";
						}
						else{
							$sql = "SELECT  PP.COD_PLAN_PAGO
												, PP.COD_DEUDA
												, PP.NRO_CUOTA
												/*INI_CAMPOS*/
												, DR.RUT
												, DR.DIG_VERIF
												, TD.COD_TIPO_DEUDA
												, TD.NOM_TIPO_DEUDA
												, PP.ANO_COBRO
												, TO_CHAR(FECHA_VENCTO,'DD/MM/YYYY') FECHA_VENCTO
												,(PP.MONTO_CAPITAL + PP.MONTO_INTERES) CUOTA_ANUAL
												,f_pp_monto_abono(COD_PLAN_PAGO, 'MONEDA') MONTO_ABONO
												,TO_CHAR((select max(FECHA_ABONO) from ABONO AB where AB.COD_PLAN_PAGO = PP.COD_PLAN_PAGO and cod_ESTADO_PAGO = 1),'DD/MM/YYYY') ULTIMO_ABONO
												, null SALDO_K
												, null SALDO_VENCIDO
												, EPP.COD_ESTADO_PLAN_PAGO
												, EPP.NOM_ESTADO_PLAN_PAGO
												, EC.COD_EMPRESA_COBRANZA
												, EC.NOM_EMPRESA_COBRANZA
												/*FIN_CAMPOS*/
										FROM	PLAN_PAGO PP left outer join EMPRESA_COBRANZA EC on EC.COD_EMPRESA_COBRANZA = PP.COD_EMPRESA_COBRANZA
												, DEUDA   DE
												, DEUDOR  DR 
												, TIPO_DEUDA TD
												, ESTADO_PLAN_PAGO EPP
										WHERE	PP.COD_DEUDA = DE.COD_DEUDA
										AND		DE.COD_DEUDOR = DR.COD_DEUDOR
										AND		DE.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
										AND		EPP.COD_ESTADO_PLAN_PAGO=PP.COD_ESTADO_PLAN_PAGO
												/*FIN_CONDICION*/
										ORDER BY PP.COD_DEUDA, PP.NRO_CUOTA ASC";
						}
					}
					/////////////////   SQL-SERVER   ///////////////
					else if (K_TIPO_BD=="mssql"){
						$sql = "SELECT  PP.COD_PLAN_PAGO
										, PP.COD_DEUDA
										, PP.NRO_CUOTA
										/*INI_CAMPOS*/
										, DR.RUT
										, DR.DIG_VERIF
										, TD.COD_TIPO_DEUDA
										, TD.NOM_TIPO_DEUDA
										, PP.ANO_COBRO
										, convert (varchar (20), FECHA_VENCTO, 103) FECHA_VENCTO
										,(PP.MONTO_CAPITAL + PP.MONTO_INTERES) CUOTA_ANUAL
										,dbo.f_pp_monto_abono(PP.COD_PLAN_PAGO, 'MONEDA', getdate()) MONTO_ABONO
										,convert(varchar,(select max(FECHA_ABONO) from ABONO where COD_PLAN_PAGO = PP.COD_PLAN_PAGO and cod_ESTADO_PAGO = 1),103) ULTIMO_ABONO
										, (PP.MONTO_CAPITAL + PP.MONTO_INTERES) VALOR_CUOTA
										, null SALDO_K
										, null SALDO_VENCIDO
										, EPP.COD_ESTADO_PLAN_PAGO
										, EPP.NOM_ESTADO_PLAN_PAGO
										, EC.COD_EMPRESA_COBRANZA
										, EC.NOM_EMPRESA_COBRANZA
										/*FIN_CAMPOS*/
								FROM	PLAN_PAGO PP left outer join EMPRESA_COBRANZA EC on EC.COD_EMPRESA_COBRANZA = PP.COD_EMPRESA_COBRANZA
										, DEUDA   DE
										, DEUDOR  DR
										, TIPO_DEUDA TD
										, ESTADO_PLAN_PAGO EPP
								WHERE	PP.COD_DEUDA = DE.COD_DEUDA
								AND		DE.COD_DEUDOR = DR.COD_DEUDOR
								AND		DE.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
								AND		EPP.COD_ESTADO_PLAN_PAGO=PP.COD_ESTADO_PLAN_PAGO
										/*FIN_CONDICION*/
								ORDER BY PP.COD_DEUDA, PP.NRO_CUOTA ASC";
					}
				break;
			
			/////////////////   INPUT_DW_PLAN_PAGO   ///////////////
			case 'INPUT_DW_PLAN_PAGO' :
				/////////////////   ORACLE   ///////////////
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	MONTO_CAPITAL
									, MONTO_INTERES
									, f_pp_saldo_multa(COD_PLAN_PAGO)+ f_pp_monto_abono(COD_PLAN_PAGO, 'MULTA') + f_pp_monto_dscto(COD_PLAN_PAGO, 'MULTA') MULTA
									, MONTO_CAPITAL + MONTO_INTERES + f_pp_saldo_multa(COD_PLAN_PAGO) + f_pp_monto_abono(COD_PLAN_PAGO, 'MULTA')+ f_pp_monto_dscto(COD_PLAN_PAGO, 'MULTA') TOTAL_PP
									, f_pp_monto_abono(COD_PLAN_PAGO, 'CAPITAL') ABONO_CAPITAL
									, f_pp_monto_abono(COD_PLAN_PAGO, 'INTERES') ABONO_INTERES
									, f_pp_monto_abono(COD_PLAN_PAGO, 'MULTA') ABONO_MULTA
									, f_pp_monto_abono(COD_PLAN_PAGO, 'MONEDA') TOTAL_ABONO
									, f_pp_monto_dscto(COD_PLAN_PAGO, 'CAPITAL') DSCTO_CAPITAL
									, f_pp_monto_dscto(COD_PLAN_PAGO, 'INTERES') DSCTO_INTERES
									, f_pp_monto_dscto(COD_PLAN_PAGO, 'MULTA') DSCTO_MULTA
									, f_pp_monto_dscto(COD_PLAN_PAGO, 'MONEDA')TOTAL_DSCTO
									, f_pp_saldo_capital(COD_PLAN_PAGO)SALDO_CAPITAL
									, f_pp_saldo_interes_normal(COD_PLAN_PAGO)SALDO_INTERES
									, f_pp_saldo_multa(COD_PLAN_PAGO) SALDO_MULTA
									, f_pp_saldo_plan_pago(COD_PLAN_PAGO) TOTAL_SALDO 
							FROM	PLAN_PAGO 
							WHERE	COD_PLAN_PAGO = {KEY1}";
				}
				/////////////////   SQL-SERVER   ///////////////
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	MONTO_CAPITAL
									, MONTO_INTERES
									, dbo.f_pp_saldo_multa(COD_PLAN_PAGO, getdate())+ dbo.f_pp_monto_abono(COD_PLAN_PAGO, 'MULTA', getdate()) + dbo.f_pp_monto_dscto(COD_PLAN_PAGO, 'MULTA', getdate()) MULTA
									, MONTO_CAPITAL + MONTO_INTERES + dbo.f_pp_saldo_multa(COD_PLAN_PAGO, getdate()) + dbo.f_pp_monto_abono(COD_PLAN_PAGO, 'MULTA', getdate()) +  dbo.f_pp_monto_dscto(COD_PLAN_PAGO, 'MULTA', getdate()) TOTAL_PP
									, dbo.f_pp_monto_abono(COD_PLAN_PAGO, 'CAPITAL', getdate()) ABONO_CAPITAL
									, dbo.f_pp_monto_abono(COD_PLAN_PAGO, 'INTERES', getdate()) ABONO_INTERES
									, dbo.f_pp_monto_abono(COD_PLAN_PAGO, 'MULTA', getdate()) ABONO_MULTA
									, dbo.f_pp_monto_abono(COD_PLAN_PAGO, 'MONEDA', getdate()) TOTAL_ABONO
									, dbo.f_pp_monto_dscto(COD_PLAN_PAGO, 'CAPITAL', getdate()) DSCTO_CAPITAL
									, dbo.f_pp_monto_dscto(COD_PLAN_PAGO, 'INTERES', getdate()) DSCTO_INTERES
									, dbo.f_pp_monto_dscto(COD_PLAN_PAGO, 'MULTA', getdate()) DSCTO_MULTA
									, dbo.f_pp_monto_dscto(COD_PLAN_PAGO, 'MONEDA', getdate())TOTAL_DSCTO
									, dbo.f_pp_saldo_capital(COD_PLAN_PAGO, getdate())SALDO_CAPITAL
									, dbo.f_pp_saldo_interes_normal(COD_PLAN_PAGO, getdate())SALDO_INTERES
									, dbo.f_pp_saldo_multa(COD_PLAN_PAGO, getdate()) SALDO_MULTA
									, dbo.f_pp_saldo_plan_pago(COD_PLAN_PAGO, getdate()) TOTAL_SALDO 
							FROM	PLAN_PAGO 
							WHERE	COD_PLAN_PAGO = {KEY1}";
				}
			break;

			/////////////////		PRINT_RECORD	////////////////////	
			case 'PRINT_RECORD' :
				/////////////////   SQL-SERVER   ///////////////
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	COD_PLAN_PAGO
							        ,RUT
							        ,DIG_VERIF
							        ,APELLIDO_PATERNO
							        ,APELLIDO_MATERNO
							        ,NOMBRE
							        ,COD_DEUDA
							        ,COD_TIPO_DEUDA
							        ,ANO_COBRO
							        ,NRO_CUOTA
							        ,FECHA_ABONO_DETALLE_PAGO
							        ,NOM_TIPO_DOCUMENTO_PAGO
							        ,NOM_ESTADO_DOCUMENTO_PAGO
							        ,FECHA_DOCUMENTO_PAGO
							        ,NOM_BANCO
							        ,NRO_DOCUMENTO_PAGO
							        ,NOM_PLAZA
							        ,MONTO_DOCUMENTO
							        ,MONTO_PESOS_ABONO
							FROM 	TEMP_DETALLE_PAGO";
				}
				/////////////////   SQL-SERVER   ///////////////
				else if (K_TIPO_BD=="mssql"){
					$sql = "exec sp_input_dw_pp_detalle_pago {PARAM1}, {PARAM2}, 'PRINT'";
				}
			break;
			/////////////////////////////////////////////
			
			//////////////// PRINT_PARAM ////////////////
			case 'PRINT_PARAM' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	VALOR NOM_UNIVERSIDAD
							FROM 	PARAMETRO
							WHERE	COD_PARAMETRO = {PARAM1}";
				}
			break;

			
			/////////////////   INPUT_DW_PP_DETALLE_PAGO   ///////////////
			case 'INPUT_DW_PP_DETALLE_PAGO' :
				/////////////////   ORACLE   ///////////////
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	COD_PLAN_PAGO
							        ,RUT
							        ,DIG_VERIF
							        ,APELLIDO_PATERNO
							        ,APELLIDO_MATERNO
							        ,NOMBRE
							        ,COD_DEUDA
							        ,COD_TIPO_DEUDA
							        ,ANO_COBRO
							        ,NRO_CUOTA
							        ,FECHA_ABONO_DETALLE_PAGO
							        ,NOM_TIPO_DOCUMENTO_PAGO
							        ,NOM_ESTADO_DOCUMENTO_PAGO
							        ,FECHA_DOCUMENTO_PAGO
							        ,NOM_BANCO
							        ,NRO_DOCUMENTO_PAGO
							        ,NOM_PLAZA
							        ,MONTO_DOCUMENTO
							        ,MONTO_PESOS_ABONO
							FROM 	TEMP_DETALLE_PAGO
							order by to_date(FECHA_ABONO_DETALLE_PAGO, 'dd/mm/yyyy'), NRO_DOCUMENTO_PAGO";
					
				}
				/////////////////   SQL-SERVER   ///////////////
				else if (K_TIPO_BD=="mssql"){
					$sql = "exec sp_input_dw_pp_detalle_pago {KEY1}, {PARAM1}, 'DETALLE_PAGO'";
				}
			break;
			
			/////////////////   OUTPUT_DD_ESTADO_PP   ///////////////
			case 'OUTPUT_DD_ESTADO_PP' :
					/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
					if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
						$sql = "SELECT	COD_ESTADO_PLAN_PAGO
										,NOM_ESTADO_PLAN_PAGO
								FROM	ESTADO_PLAN_PAGO
								ORDER BY	COD_ESTADO_PLAN_PAGO";
					}
			break;
			
			/////////////////   OUTPUT_TIPO_DEUDA   ///////////////
			case 'OUTPUT_TIPO_DEUDA' :
					/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
					if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
						$sql = "SELECT	COD_TIPO_DEUDA
										,NOM_TIPO_DEUDA
								FROM	TIPO_DEUDA
								ORDER BY	COD_TIPO_DEUDA";
					}
			break;
			
			/////////////////   DD_COD_EMPRESA_COBRANZA   ///////////////
			case 'DD_COD_EMPRESA_COBRANZA' :
					/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
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
			
			/////////////////  ERROR ///////////////
			default:
			base::error("sql_plan_pago::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>