<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_ingreso_base extends sql_tabla {
	const K_PARAM_MAX_NUM_CH 		= 23;
	const K_ESTADO_DOC_POR_DEPOSITAR= 1;
	const K_ESTADO_DOC_PROTESTADO 	= 3;
	const K_ESTADO_SIMULACION		= 1000;
	const K_COD_CARTERA_UACH			= 7;
	const K_PARAM_MAX_VALIDEZ_SIMULACION = 24;
	const K_ESTADO_PLAN_PAGO_MOROSA = 4;
	const K_DOCUMENTO_PAGO_CH_FECHA = 5;
	
	static function get_sql($selector_sql) {
		switch ($selector_sql) {
			/////////////////  INPUT ///////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT COD_INGRESO
							        ,To_Char(FECHA_INGRESO, 'DD/MM/YYYY') FECHA_INGRESO
							        ,To_Char(FECHA_INGRESO, 'DD/MM/YYYY') FECHA_INGRESO2
							        ,I.COD_DEUDOR
							        ,COD_ESTADO_INGRESO
							        ,I.COD_USUARIO
							        ,U.NOM_USUARIO
							        ,D.RUT
									,D.RUT RUT_H
									,D.DIG_VERIF
									,D.NOMBRE || ' ' || D.APELLIDO_PATERNO || ' ' || D.APELLIDO_MATERNO NOMBRE
							        ,I.TASA_INTERES
									,I.REFERENCIA
									,NVL(MONTO_CHEQUE_FECHA, 0) MONTO_CHEQUE_FECHA 
									,NVL(MONTO_CHEQUE_FECHA, 0) STATIC_MONTO_CHEQUE_FECHA_H
							        ,CANT_CHEQUE_FECHA
							        ,To_Char(FECHA_PRIMER_CHEQUE_FECHA, 'DD/MM/YYYY') FECHA_PRIMER_CHEQUE_FECHA
							        ,To_Char(FECHA_PRIMER_CHEQUE_FECHA, 'DD/MM/YYYY') FECHA_PRIMER_CHEQUE_FECHA_H
							        ,0 VALOR_MONEDA
									,0 SUMA_MONTO
									,0 SUMA_MONTO_H
									,0 SUMA_MONTO_CH_FECHA
									,0 STATIC_TOTAL
									,0 STATIC_MONTO_INTERES
									,0 STATIC_MONTO_INTERES2
									,0 SUMA_MONTO_PAGO
									,0 SUMA_MONTO_PAGO_PESOS
									,0 SUMA_SALDO_TOTAL_PESOS
									,0 SUMA_SALDO_TOTAL
									,0 POR_PAGAR
									,0 POR_PAGAR2
									,'' TR_NEW_RECORD
									,'' TD_RUT_GIRADOR
									,'' TR_LOAD_RECORD
							        , f_get_parametro(".self::K_PARAM_MAX_NUM_CH.") MAX_CH_H
							        ,I.COD_FAMILIA_DEUDA
									,'none' VISIBLE_BOLETA
									,'' VISIBLE_TR
									,'' COD_BOLETA
									,'' COD_DEUDA_BOLETA
									,'' ES_DEPOSITO
									, 'none' VISIBLE_CAMPO
									, 'S' USAR_RUT_DEUDOR_GIRADOR
									,'' DISPLAY_SIMULACION
									,COD_INGRESO_DESDE
							        ,f_ingreso_get_desde_simulacion (COD_INGRESO) COD_INGRESO_SIMULACION
							        ,'' TAB_151510 -- tab cheque a fecha
									,VALIDEZ_SIMULACION
									,0 STATIC_CH_FECHA_INT
									,0 STATIC_TOTAL_PAGAR
						      		,MONTO_OTROS_GASTOS
									,GLOSA_OTROS_GASTOS
									,MONTO_OTROS_GASTOS STATIC_MONTO_OTROS_GASTOS
									,GLOSA_OTROS_GASTOS STATIC_GLOSA_OTROS_GASTOS
									,'' ANO_ACTUAL								
									,f_ingreso_CR_tipo(COD_INGRESO) CR_TIPO
									,f_ingreso_CR_codigo(COD_INGRESO) CR_CODIGO
									,f_ingreso_CR_cuotas(COD_INGRESO) CR_CUOTAS --retorna nro_cuota
									,'' CR_TIPO_H
									,'' CR_CODIGO_H
									,'' CR_CUOTAS_H	--cod_cuota
									,case f_ingreso_CR_tipo(COD_INGRESO)
										when '' then ''
										else 'none'
									end VISIBLE_SIMULA
									,case f_ingreso_CR_tipo(COD_INGRESO)
										when '' then 'none'
										else ''
									end VISIBLE_CR
									,MONTO_EXCEDENTE
									,case f_ingreso_CR_tipo(COD_INGRESO)
										when '' then ''
										else 'none'
									end VISIBLE_CR1
							FROM 	INGRESO I, USUARIO U, DEUDOR D
							WHERE 	COD_INGRESO = {KEY1}
							AND   	I.COD_USUARIO = U.COD_USUARIO
							AND   	I.COD_DEUDOR = D.COD_DEUDOR";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_INGRESO
									,CONVERT(VARCHAR(20), FECHA_INGRESO, 103) FECHA_INGRESO
									,CONVERT(VARCHAR(20), FECHA_INGRESO, 103) FECHA_INGRESO2
									,I.COD_DEUDOR
									,COD_ESTADO_INGRESO
									,I.COD_USUARIO
									,U.NOM_USUARIO
							      	,D.RUT
							      	,D.RUT RUT_H
									,D.DIG_VERIF
									,D.NOMBRE  ' '  D.APELLIDO_PATERNO  ' '  D.APELLIDO_MATERNO NOMBRE
									,I.TASA_INTERES
									,I.REFERENCIA
									,ISNULL(MONTO_CHEQUE_FECHA, 0) MONTO_CHEQUE_FECHA 
									,ISNULL(MONTO_CHEQUE_FECHA, 0) STATIC_MONTO_CHEQUE_FECHA_H
									,CANT_CHEQUE_FECHA
									,CONVERT(VARCHAR(20), FECHA_PRIMER_CHEQUE_FECHA, 103) FECHA_PRIMER_CHEQUE_FECHA
									,CONVERT(VARCHAR(20), FECHA_PRIMER_CHEQUE_FECHA, 103) FECHA_PRIMER_CHEQUE_FECHA_H
									,0 VALOR_MONEDA
									,0 SUMA_MONTO
									,0 SUMA_MONTO_H
									,0 SUMA_MONTO_CH_FECHA
									,0 STATIC_TOTAL
									,0 STATIC_MONTO_INTERES
									,0 STATIC_MONTO_INTERES2
									,0 SUMA_MONTO_PAGO
									,0 SUMA_MONTO_PAGO_PESOS
									,0 SUMA_SALDO_TOTAL_PESOS
									,0 SUMA_SALDO_TOTAL
									,0 POR_PAGAR
									,0 POR_PAGAR2
									,'' TR_NEW_RECORD
									,'' TD_RUT_GIRADOR
									,'' TR_LOAD_RECORD
									, dbo.f_get_parametro(".self::K_PARAM_MAX_NUM_CH.") MAX_CH_H
									,I.COD_FAMILIA_DEUDA
									,'none' VISIBLE_BOLETA
						      		,'' VISIBLE_TR
						      		,'' COD_BOLETA
						      		,'' COD_DEUDA_BOLETA
						      		,'' ES_DEPOSITO
						      		, 'none' VISIBLE_CAMPO
						      		, 'S' USAR_RUT_DEUDOR_GIRADOR
									,'' DISPLAY_SIMULACION
									,COD_INGRESO_DESDE
									,dbo.f_ingreso_get_desde_simulacion (COD_INGRESO) COD_INGRESO_SIMULACION
									,'' TAB_151510 -- tab cheque a fecha
									,VALIDEZ_SIMULACION
									,0 STATIC_CH_FECHA_INT
						      		,0 STATIC_TOTAL_PAGAR
						      		,MONTO_OTROS_GASTOS
									,GLOSA_OTROS_GASTOS
									,MONTO_OTROS_GASTOS STATIC_MONTO_OTROS_GASTOS
									,GLOSA_OTROS_GASTOS STATIC_GLOSA_OTROS_GASTOS
									,'' ANO_ACTUAL
									,dbo.f_ingreso_CR_tipo(COD_INGRESO) CR_TIPO
									,dbo.f_ingreso_CR_codigo(COD_INGRESO) CR_CODIGO
									,dbo.f_ingreso_CR_cuotas(COD_INGRESO) CR_CUOTAS --retorna nro_cuota
									,'' CR_TIPO_H
									,'' CR_CODIGO_H
									,'' CR_CUOTAS_H	--cod_cuota
									,case dbo.f_ingreso_CR_tipo(COD_INGRESO)
										when '' then ''
										else 'none'
									end VISIBLE_SIMULA
									,case dbo.f_ingreso_CR_tipo(COD_INGRESO)
										when '' then 'none'
										else ''
									end VISIBLE_CR
									,MONTO_EXCEDENTE
									,case dbo.f_ingreso_CR_tipo(COD_INGRESO)
										when '' then ''
										else 'none'
									end VISIBLE_CR1
						     FROM	INGRESO I, DEUDOR D, USUARIO U
							WHERE	COD_INGRESO = {KEY1}
							AND		I.COD_DEUDOR = D.COD_DEUDOR
							AND		I.COD_USUARIO = U.COD_USUARIO";
				}
				break;
			

			/////////////////OUTPUT_R_SOLAR///////////////
			case 'OUTPUT_R_SOLAR' :
				if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_INGRESO
									/*INI_CAMPOS*/
								    ,CONVERT(VARCHAR(20), FECHA_INGRESO, 103) FECHA_INGRESO
								    ,FD.NOM_CORTO_FAMILIA_DEUDA
								    ,DE.RUT
								    ,DE.DIG_VERIF
								    ,DE.NOMBRE
								    ,DE.APELLIDO_PATERNO
								    ,DE.APELLIDO_MATERNO
								    ,'' VISIBLE_WO_ADD
								    ,E.NOM_ESTADO_INGRESO
									,I.K_CLIENTE
									/*FIN_CAMPOS*/
							FROM 		INGRESO I, DEUDOR DE, ESTADO_INGRESO E, FAMILIA_DEUDA FD
							WHERE 		DE.COD_DEUDOR = I.COD_DEUDOR
							AND     	(dbo.f_get_tiene_acceso({PARAM1}, I.COD_USUARIO) = 1 OR K_CLIENTE = 'SURCOB_UACH')
							AND			E.COD_ESTADO_INGRESO = I.COD_ESTADO_INGRESO
							AND			FD.COD_FAMILIA_DEUDA = I.COD_FAMILIA_DEUDA
							AND			I.COD_ESTADO_INGRESO <> ".self::K_ESTADO_SIMULACION."
							/*FIN_CONDICION*/
							ORDER BY	COD_INGRESO DESC";
					
				}
				break;

			/////////////////  OUTPUT ///////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT COD_INGRESO
									/*INI_CAMPOS*/
							        ,To_Char(FECHA_INGRESO, 'DD/MM/YYYY') FECHA_INGRESO
							        ,FD.NOM_CORTO_FAMILIA_DEUDA
							        ,DE.RUT
									,DE.DIG_VERIF
									,DE.NOMBRE
									,DE.APELLIDO_PATERNO
									,DE.APELLIDO_MATERNO
        							,'' VISIBLE_WO_ADD
									,E.NOM_ESTADO_INGRESO
									,I.K_CLIENTE
									,I.COD_ESTADO_INGRESO
									/*FIN_CAMPOS*/
							FROM	INGRESO I, DEUDOR DE, ESTADO_INGRESO E, FAMILIA_DEUDA FD
							WHERE   I.COD_DEUDOR = DE.COD_DEUDOR
							AND     f_get_tiene_acceso({PARAM1}, I.COD_USUARIO) = 1
							AND     E.COD_ESTADO_INGRESO = I.COD_ESTADO_INGRESO
							AND		FD.COD_FAMILIA_DEUDA = I.COD_FAMILIA_DEUDA
							AND     I.COD_ESTADO_INGRESO <> ".self::K_ESTADO_SIMULACION."
							/*FIN_CONDICION*/
							ORDER BY COD_INGRESO DESC";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_INGRESO
									/*INI_CAMPOS*/
								    ,CONVERT(VARCHAR(20), FECHA_INGRESO, 103) FECHA_INGRESO
								    ,FD.NOM_CORTO_FAMILIA_DEUDA
								    ,DE.RUT
								    ,DE.DIG_VERIF
								    ,DE.NOMBRE
								    ,DE.APELLIDO_PATERNO
								    ,DE.APELLIDO_MATERNO
								    ,'' VISIBLE_WO_ADD
								    ,E.NOM_ESTADO_INGRESO
								    ,I.K_CLIENTE
								    ,I.COD_ESTADO_INGRESO
								    /*FIN_CAMPOS*/
							FROM 	INGRESO I, DEUDOR DE, ESTADO_INGRESO E, FAMILIA_DEUDA FD
							WHERE 	DE.COD_DEUDOR = I.COD_DEUDOR
									and dbo.f_get_tiene_acceso ({PARAM1}, I.COD_USUARIO) = 1
									and E.COD_ESTADO_INGRESO = I.COD_ESTADO_INGRESO
									AND	FD.COD_FAMILIA_DEUDA = I.COD_FAMILIA_DEUDA
									and I.COD_ESTADO_INGRESO <> ".self::K_ESTADO_SIMULACION."
									/*FIN_CONDICION*/
							ORDER BY COD_INGRESO DESC";
					
				}
				break;
				
			/////////////////		PRINT_RECORD	////////////////////	
			case 'PRINT_RECORD' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	I.COD_INGRESO
									,I.COD_FAMILIA_DEUDA
									,to_char(I.FECHA_INGRESO,'DD/MM/YYYY') FECHA_INGRESO
									,DO.RUT
									,DO.DIG_VERIF
									,DO.APELLIDO_PATERNO || ' ' || DO.APELLIDO_MATERNO || ' ' || DO.NOMBRE  NOMBRE
									,I.REFERENCIA
									,U.NOM_USUARIO
									,I.COD_ESTADO_INGRESO
									,I.VALIDEZ_SIMULACION
									,MONTO_OTROS_GASTOS
									,GLOSA_OTROS_GASTOS
							FROM	INGRESO I, 
									DEUDOR DO, USUARIO U
							WHERE	I.COD_INGRESO = {PARAM1}
							  AND	I.COD_DEUDOR = DO.COD_DEUDOR
							  AND	I.COD_USUARIO = U.COD_USUARIO";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql="SELECT	I.COD_INGRESO
									,I.COD_FAMILIA_DEUDA
									,convert(varchar(10),I.FECHA_INGRESO,103) FECHA_INGRESO
									,DO.RUT
									,DO.DIG_VERIF
									,DO.APELLIDO_PATERNO  ' '  DO.APELLIDO_MATERNO  ' '  DO.NOMBRE  NOMBRE
									,I.REFERENCIA
									,U.NOM_USUARIO
									,I.COD_ESTADO_INGRESO
									,I.VALIDEZ_SIMULACION
									,MONTO_OTROS_GASTOS
									,GLOSA_OTROS_GASTOS
							FROM	INGRESO I, 
									DEUDOR DO, USUARIO U
							WHERE	I.COD_INGRESO = {PARAM1}
							  AND	I.COD_DEUDOR = DO.COD_DEUDOR
							  AND	I.COD_USUARIO = U.COD_USUARIO";
				}
				break;
				
			/////////////////////////////////////////////
			
			//////////////// PRINT_DETALLE_DEUDA ////////////////
			case 'PRINT_DETALLE_DEUDA' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	DA.COD_DEUDA
									,DA.COD_TIPO_DEUDA
									,PP.ANO_COBRO
									,TO_CHAR(PP.FECHA_VENCTO, 'DD/MM/YYYY') FECHA_VENCTO
									,SALDO_CUOTA
									,SALDO_MULTA
									,TD.NOM_TIPO_DESCUENTO
									,MONTO_DESCUENTO
									,MONTO_PAGO
									,MONTO_PAGO_PESOS
							FROM	INGRESO I, ITEM_INGRESO IT	left outer join PLAN_PAGO PP on IT.COD_PLAN_PAGO = PP.COD_PLAN_PAGO 
																left outer join TIPO_DESCUENTO TD on TD.COD_TIPO_DESCUENTO = IT.COD_TIPO_DESCUENTO
									,DEUDA DA, TIPO_DEUDA T, MONEDA M
							WHERE	I.COD_INGRESO = {PARAM1}
							  AND	IT.COD_INGRESO = I.COD_INGRESO 
							  AND	DA.COD_DEUDA = IT.COD_DEUDA
							  AND	T.COD_TIPO_DEUDA = DA.COD_TIPO_DEUDA
							  AND	M.COD_MONEDA = T.COD_MONEDA
							ORDER BY	DA.COD_TIPO_DEUDA, DA.COD_DEUDA, PP.ANO_COBRO";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql="SELECT	DA.COD_DEUDA
									,DA.COD_TIPO_DEUDA
									,PP.ANO_COBRO
									,convert(varchar(10),PP.FECHA_VENCTO, 103) FECHA_VENCTO
									,SALDO_CUOTA
									,SALDO_MULTA
									,TD.NOM_TIPO_DESCUENTO
									,MONTO_DESCUENTO
									,MONTO_PAGO
									,MONTO_PAGO_PESOS
							FROM	INGRESO I, ITEM_INGRESO IT	left outer join PLAN_PAGO PP on IT.COD_PLAN_PAGO = PP.COD_PLAN_PAGO 
																left outer join TIPO_DESCUENTO TD on TD.COD_TIPO_DESCUENTO = IT.COD_TIPO_DESCUENTO
									,DEUDA DA, TIPO_DEUDA T, MONEDA M
							WHERE	I.COD_INGRESO = {PARAM1}
							  AND	IT.COD_INGRESO = I.COD_INGRESO 
							  AND	DA.COD_DEUDA = IT.COD_DEUDA
							  AND	T.COD_TIPO_DEUDA = DA.COD_TIPO_DEUDA
							  AND	M.COD_MONEDA = T.COD_MONEDA
							ORDER BY	DA.COD_TIPO_DEUDA, DA.COD_DEUDA, PP.ANO_COBRO";
				}
				break;
				
			/////////////////////////////////////////////
				
			/////////////////  PRINT_DOCUMENTO_PAGO ///////////////
			case 'PRINT_DOCUMENTO_PAGO' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	DP.COD_DOCUMENTO_PAGO COD_DOCUMENTO_PAGO_H
							    	,TDP.COD_TIPO_DOCUMENTO_PAGO
									,TDP.NOM_TIPO_DOCUMENTO_PAGO
									,TO_CHAR(DP.NRO_DOCUMENTO_PAGO) NRO_DOCUMENTO_PAGO
									,B.NOM_BANCO
									,P.NOM_PLAZA
									,TO_CHAR(DP.FECHA_DOCUMENTO_PAGO, 'DD/MM/YYYY') FECHA_DOCUMENTO_PAGO
									,DP.MONTO
							FROM	DOCUMENTO_INGRESO DI
									,DOCUMENTO_PAGO DP	left outer join BANCO B on DP.COD_BANCO = B.COD_BANCO
														left outer join PLAZA P on DP.COD_PLAZA = P.COD_PLAZA
									,TIPO_DOCUMENTO_PAGO TDP
							WHERE 	DI.COD_INGRESO = {PARAM1}
							  AND 	DP.COD_DOCUMENTO_PAGO = DI.COD_DOCUMENTO_PAGO
							  AND 	TDP.COD_TIPO_DOCUMENTO_PAGO = DP.COD_TIPO_DOCUMENTO_PAGO";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	DP.COD_DOCUMENTO_PAGO COD_DOCUMENTO_PAGO_H
							    	,TDP.COD_TIPO_DOCUMENTO_PAGO
									,TDP.NOM_TIPO_DOCUMENTO_PAGO
									,convert(varchar(20), DP.NRO_DOCUMENTO_PAGO) NRO_DOCUMENTO_PAGO
									,B.NOM_BANCO
									,P.NOM_PLAZA
									,convert(varchar(10), DP.FECHA_DOCUMENTO_PAGO, 103) FECHA_DOCUMENTO_PAGO
									,DP.MONTO
							FROM	DOCUMENTO_INGRESO DI
									,DOCUMENTO_PAGO DP	left outer join BANCO B on DP.COD_BANCO = B.COD_BANCO
														left outer join PLAZA P on DP.COD_PLAZA = P.COD_PLAZA
									,TIPO_DOCUMENTO_PAGO TDP
							WHERE 	DI.COD_INGRESO = {PARAM1}
							  AND 	DP.COD_DOCUMENTO_PAGO = DI.COD_DOCUMENTO_PAGO
							  AND 	TDP.COD_TIPO_DOCUMENTO_PAGO = DP.COD_TIPO_DOCUMENTO_PAGO";
				}
				break;
				
			/////////////////////////////////////////////	
				
				
			/////////////////  PRINT_DOCUMENTO_PAGO_CH_FECHA ///////////////
			case 'PRINT_DOCUMENTO_PAGO_CH_FECHA' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	 TO_CHAR(FECHA_DOCUMENTO_PAGO, 'DD/MM/YYYY') FECHA_DOCUMENTO_PAGO_CH_FECH
									,B.NOM_BANCO NOM_BANCO_CH_FECH
									,NRO_DOCUMENTO_PAGO NRO_DOCUMENTO_PAGO_CH_FECH
								    ,P.NOM_PLAZA NOM_PLAZA_CH_FECH
								    ,MONTO MONTO_CH_FECH
								    ,I.MONTO_CHEQUE_FECHA
							FROM 	DOCUMENTO_INGRESO_CH_FECHA D, INGRESO I
									,DOCUMENTO_PAGO DP	left outer join BANCO B on DP.COD_BANCO = B.COD_BANCO
														left outer join PLAZA P on DP.COD_PLAZA = P.COD_PLAZA  
							WHERE 	D.COD_INGRESO = I.COD_INGRESO
							AND   	D.COD_DOCUMENTO_PAGO = DP.COD_DOCUMENTO_PAGO
              				AND   	I.COD_INGRESO = {PARAM1}";
				}
				else if(K_TIPO_BD=="mssql"){
					$sql ="SELECT 	 convert(varchar(20), FECHA_DOCUMENTO_PAGO, 103) FECHA_DOCUMENTO_PAGO_CH_FECH
									,B.NOM_BANCO NOM_BANCO_CH_FECH
									,NRO_DOCUMENTO_PAGO NRO_DOCUMENTO_PAGO_CH_FECH
								    ,P.NOM_PLAZA NOM_PLAZA_CH_FECH
								    ,MONTO MONTO_CH_FECH
								    ,I.MONTO_CHEQUE_FECHA
							FROM 	DOCUMENTO_INGRESO_CH_FECHA D, INGRESO I
									,DOCUMENTO_PAGO DP	left outer join BANCO B on DP.COD_BANCO = B.COD_BANCO
														left outer join PLAZA P on DP.COD_PLAZA = P.COD_PLAZA  
							WHERE 	D.COD_INGRESO = I.COD_INGRESO
							AND   	D.COD_DOCUMENTO_PAGO = DP.COD_DOCUMENTO_PAGO
							AND   	I.COD_INGRESO = {PARAM1}";
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
				
			/////////////////  DW_ITEM_INGRESO ///////////////
			case 'DW_ITEM_INGRESO' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 'S' SELECCION
							        ,COD_ITEM_INGRESO
							        ,I.COD_INGRESO
							        ,DA.COD_DEUDA
							        ,DA.COD_DEUDA COD_DEUDA_H
							        ,DA.COD_TIPO_DEUDA
							        ,M.SIMBOLO
									,PP.COD_PLAN_PAGO
									,PP.ANO_COBRO
									,PP.NRO_CUOTA NRO_CUOTA_H
									,TO_CHAR(PP.FECHA_VENCTO, 'DD/MM/YYYY') FECHA_VENCTO
							        ,SALDO_CUOTA
									,SALDO_CUOTA SALDO_CUOTA_H
									,SALDO_MULTA
									,SALDO_MULTA SALDO_MULTA_H
									,SALDO_TOTAL
									,SALDO_TOTAL SALDO_TOTAL_H
									,0 SALDO_TOTAL_SIN_DESCTO_H
									,SALDO_TOTAL_PESOS
									,SALDO_TOTAL_PESOS SALDO_TOTAL_PESOS_H
									,MONTO_PAGO
									,MONTO_PAGO MONTO_PAGO_H
									,MONTO_PAGO_PESOS
									,MONTO_PAGO_PESOS MONTO_PAGO_PESOS_H
									,T.COD_FAMILIA_DEUDA COD_FAMILIA_DEUDA_H
									,'' DISPLAY_TR
							        ,COD_TIPO_DESCUENTO
							        ,PORC_DESCUENTO
							        ,MONTO_DESCUENTO
									,MONTO_DESCUENTO MONTO_DESCUENTO_H
									,IT.COD_SUB_BOLETA
									,IT.COD_SUB_BOLETA COD_SUB_BOLETA_H
									,SB.NRO_SUB_BOLETA
									,PP.COD_ESTADO_PLAN_PAGO
									,f_ingreso_pp_en_CR (PP.COD_PLAN_PAGO) EN_CR
							FROM    INGRESO I, ITEM_INGRESO IT left outer join PLAN_PAGO PP on IT.COD_PLAN_PAGO = PP.COD_PLAN_PAGO 
											left outer join SUB_BOLETA SB on IT.COD_SUB_BOLETA = SB.COD_SUB_BOLETA
							        ,DEUDA DA
							        ,TIPO_DEUDA T
							        ,MONEDA M
							WHERE   I.COD_INGRESO = {KEY1}
							AND     I.COD_INGRESO = IT.COD_INGRESO
							AND     DA.COD_DEUDA = IT.COD_DEUDA
							AND     DA.COD_TIPO_DEUDA = T.COD_TIPO_DEUDA
							AND     T.COD_MONEDA = M.COD_MONEDA
							ORDER BY DA.COD_TIPO_DEUDA, DA.COD_DEUDA, PP.ANO_COBRO";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 'S' SELECCION
						      ,COD_ITEM_INGRESO
						      ,I.COD_INGRESO
						      ,DA.COD_DEUDA
						      ,DA.COD_DEUDA COD_DEUDA_H
						      ,DA.COD_TIPO_DEUDA
						      ,M.SIMBOLO
						      ,PP.COD_PLAN_PAGO
						      ,PP.ANO_COBRO
						      ,PP.NRO_CUOTA NRO_CUOTA_H
						      ,convert(varchar(10),PP.FECHA_VENCTO, 103) FECHA_VENCTO
						      ,SALDO_CUOTA
						      ,SALDO_CUOTA SALDO_CUOTA_H
						      ,SALDO_MULTA
						      ,SALDO_MULTA SALDO_MULTA_H
						      ,SALDO_TOTAL
						      ,SALDO_TOTAL SALDO_TOTAL_H
						      ,0 SALDO_TOTAL_SIN_DESCTO_H
						      ,SALDO_TOTAL_PESOS
						      ,SALDO_TOTAL_PESOS SALDO_TOTAL_PESOS_H
						      ,MONTO_PAGO
						      ,MONTO_PAGO MONTO_PAGO_H
						      ,MONTO_PAGO_PESOS
						      ,MONTO_PAGO_PESOS MONTO_PAGO_PESOS_H
						      ,T.COD_FAMILIA_DEUDA COD_FAMILIA_DEUDA_H
				      		  ,'' DISPLAY_TR	
							  ,COD_TIPO_DESCUENTO
							  ,PORC_DESCUENTO
							  ,MONTO_DESCUENTO
							  ,MONTO_DESCUENTO MONTO_DESCUENTO_H
							  ,IT.COD_SUB_BOLETA
							  ,IT.COD_SUB_BOLETA COD_SUB_BOLETA_H
							  ,SB.NRO_SUB_BOLETA
							  ,dbo.f_ingreso_pp_en_CR (PP.COD_PLAN_PAGO) EN_CR
						FROM INGRESO I, ITEM_INGRESO IT left outer join PLAN_PAGO PP on IT.COD_PLAN_PAGO = PP.COD_PLAN_PAGO 
														left outer join SUB_BOLETA SB on IT.COD_SUB_BOLETA = SB.COD_SUB_BOLETA
								,DEUDA DA, TIPO_DEUDA T, MONEDA M
						WHERE I.COD_INGRESO = {KEY1}
			                  AND IT.COD_INGRESO = I.COD_INGRESO 
						      AND DA.COD_DEUDA = IT.COD_DEUDA
						      AND T.COD_TIPO_DEUDA = DA.COD_TIPO_DEUDA
						      AND M.COD_MONEDA = T.COD_MONEDA
					  ORDER BY DA.COD_TIPO_DEUDA, DA.COD_DEUDA, PP.ANO_COBRO";
				}
				break;
				
			/////////////////  DD_ESTADO_INGRESO///////////////
						/////////////////  DD_TIPO_DEUDA ///////////////
			case 'DD_ESTADO_INGRESO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT COD_ESTADO_INGRESO, 
								NOM_ESTADO_INGRESO
							FROM 	ESTADO_INGRESO 
							where COD_ESTADO_INGRESO in (1, 2) /* emitido y anulado*/
							ORDER BY	COD_ESTADO_INGRESO";
				}
				break;	
				
						/////////////////  DW_DOCUMENTO_PAGO ///////////////
			case 'DW_DOCUMENTO_PAGO' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT  DP.COD_DOCUMENTO_PAGO COD_DOCUMENTO_PAGO_H
							        ,TDP.COD_TIPO_DOCUMENTO_PAGO
									,TDP.COD_TIPO_DOCUMENTO_PAGO COD_TIPO_DOCUMENTO_PAGO_H
									,TDP.NOM_TIPO_DOCUMENTO_PAGO
							        ,DP.NRO_DOCUMENTO_PAGO  
									,DP.COD_BANCO
									,DP.COD_PLAZA
									,to_char(DP.FECHA_DOCUMENTO_PAGO, 'DD/MM/YYYY') FECHA_DOCUMENTO_PAGO
									,DP.MONTO
									,'' DISPLAY_ELIMINAR_DOCUMENTO					
							FROM    DOCUMENTO_PAGO DP, TIPO_DOCUMENTO_PAGO TDP, DOCUMENTO_INGRESO DI
							WHERE   DI.COD_INGRESO = {KEY1}
							AND     DP.COD_TIPO_DOCUMENTO_PAGO = TDP.COD_TIPO_DOCUMENTO_PAGO
							AND     DP.COD_DOCUMENTO_PAGO = DI.COD_DOCUMENTO_PAGO";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 	DP.COD_DOCUMENTO_PAGO COD_DOCUMENTO_PAGO_H
							    	,TDP.COD_TIPO_DOCUMENTO_PAGO
							    	,TDP.COD_TIPO_DOCUMENTO_PAGO COD_TIPO_DOCUMENTO_PAGO_H
									,TDP.NOM_TIPO_DOCUMENTO_PAGO
									,DP.NRO_DOCUMENTO_PAGO  
									,DP.COD_BANCO
									,DP.COD_PLAZA
									,convert(varchar(10), DP.FECHA_DOCUMENTO_PAGO, 103) FECHA_DOCUMENTO_PAGO
									,DP.MONTO
									,'' DISPLAY_ELIMINAR_DOCUMENTO
							FROM 	DOCUMENTO_INGRESO DI, DOCUMENTO_PAGO DP, TIPO_DOCUMENTO_PAGO TDP
							WHERE 	DI.COD_INGRESO = {KEY1}
							  AND 	DP.COD_DOCUMENTO_PAGO = DI.COD_DOCUMENTO_PAGO
							  AND 	TDP.COD_TIPO_DOCUMENTO_PAGO = DP.COD_TIPO_DOCUMENTO_PAGO";
				}
				break;
				
				/////////////////  DD_TIPO_DOCUMENTO_PAGO ///////////////
			case 'DD_TIPO_DOCUMENTO_PAGO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					
					IF(K_CLIENTE=='UCM')
					{
					$sql = "SELECT		COD_TIPO_DOCUMENTO_PAGO
										, NOM_TIPO_DOCUMENTO_PAGO
							FROM 		TIPO_DOCUMENTO_PAGO
							WHERE COD_TIPO_DOCUMENTO_PAGO not in (4, 5) --EFECTIVO y CHEQUE A FECHA
							ORDER BY 	COD_TIPO_DOCUMENTO_PAGO";
					}else
					{
					$sql = "SELECT		COD_TIPO_DOCUMENTO_PAGO
										, NOM_TIPO_DOCUMENTO_PAGO
							FROM 		TIPO_DOCUMENTO_PAGO
							WHERE COD_TIPO_DOCUMENTO_PAGO <> 5 --CHEQUE A FECHA
							ORDER BY 	COD_TIPO_DOCUMENTO_PAGO";
					}
			}
			break;
			
			/////////////////  DD_BANCO ///////////////
			case 'DD_BANCO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 		B.COD_BANCO
										, B.NOM_BANCO
										,(select count(*) from CUENTA_CORRIENTE CC where CC.COD_BANCO = B.COD_BANCO) TIENE_CUENTA_CORRIENTE  
							FROM 		BANCO B 
							ORDER BY	B.NOM_BANCO";
				}
				break;
				
			/////////////////  DD_BANCO_DEPOSITO ///////////////
			case 'DD_BANCO_DEPOSITO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 		B.COD_BANCO
										, B.NOM_BANCO
										,1
							FROM 		BANCO B
							where		B.COD_BANCO in (select COD_BANCO from CUENTA_CORRIENTE)
							ORDER BY	B.NOM_BANCO";
				}
				break;
				
			/////////////////  DD_PLAZA ///////////////
			case 'DD_PLAZA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT		COD_PLAZA
										, NOM_PLAZA
							FROM 		PLAZA
							ORDER BY 	NOM_PLAZA";
				}
			break;
			
			/////////////////  DW_DOCUMENTO_PAGO_CH_FECHA ///////////////
			case 'DW_DOCUMENTO_PAGO_CH_FECHA' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	COD_DOCUMENTO_INGRESO_CH_FECHA
									,NRO_DOCUMENTO_PAGO NRO_DOCUMENTO_PAGO_CH_FECH
								    ,COD_BANCO COD_BANCO_CH_FECH
								    ,COD_PLAZA COD_PLAZA_CH_FECH
								    ,TO_CHAR(FECHA_DOCUMENTO_PAGO, 'DD/MM/YYYY') FECHA_DOCUMENTO_PAGO_CH_FECH
								    ,MONTO MONTO_CH_FECH
								    ,DP.RUT_GIRADOR
								  	,DP.DIG_VERIF_GIRADOR
							FROM 	DOCUMENTO_INGRESO_CH_FECHA D, INGRESO I, DOCUMENTO_PAGO DP  
							WHERE 	D.COD_INGRESO = I.COD_INGRESO
							AND   	D.COD_DOCUMENTO_PAGO = DP.COD_DOCUMENTO_PAGO
							AND   	I.COD_INGRESO = {KEY1}";
				}
				else if(K_TIPO_BD=="mssql"){
					$sql ="SELECT 	COD_DOCUMENTO_INGRESO_CH_FECHA
									,NRO_DOCUMENTO_PAGO NRO_DOCUMENTO_PAGO_CH_FECH
								    ,COD_BANCO COD_BANCO_CH_FECH
								    ,COD_PLAZA COD_PLAZA_CH_FECH
								    ,convert(varchar(20), FECHA_DOCUMENTO_PAGO, 103) FECHA_DOCUMENTO_PAGO_CH_FECH
								    ,MONTO MONTO_CH_FECH
								    ,DP.RUT_GIRADOR
								  	,DP.DIG_VERIF_GIRADOR
							FROM 	DOCUMENTO_INGRESO_CH_FECHA D, INGRESO I, DOCUMENTO_PAGO DP  
							WHERE 	D.COD_INGRESO = I.COD_INGRESO
							AND   	D.COD_DOCUMENTO_PAGO = DP.COD_DOCUMENTO_PAGO
							AND   	I.COD_INGRESO = {KEY1}";
				}
			break;
			
			/////////////////  DD_TIPO_DESCUENTO ///////////////
			case 'DD_TIPO_DESCUENTO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT COD_TIPO_DESCUENTO
								  ,NOM_TIPO_DESCUENTO
							      ,PORC_DESCUENTO
							FROM TIPO_DESCUENTO
							ORDER BY COD_TIPO_DESCUENTO";
				}
			break;

			//////////// VALIDACIONES PARA CREAR DESDE SIMULACION  ///////////////////
			/////////////////  AJAX_EXISTE_SIMULACION ////////////////////////////////
			case 'AJAX_EXISTE_SIMULACION' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_INGRESO
							  FROM	INGRESO
							 WHERE	COD_INGRESO = {PARAM1}
							   AND	COD_ESTADO_INGRESO = {PARAM2}";
				}
				break;
			
			/////////////// AJAX_INFO_SIMULACION ///////////////	
			case 'AJAX_INFO_SIMULACION' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	DO.RUT
							      	,DO.DIG_VERIF
							      	,DO.NOMBRE || ' ' || DO.APELLIDO_PATERNO || ' ' || DO.APELLIDO_MATERNO NOMBRE
							FROM 	INGRESO I, DEUDOR DO
							WHERE 	I.COD_INGRESO = {PARAM1}
							  AND	I.COD_DEUDOR = DO.COD_DEUDOR";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	 DO.RUT
									,DO.DIG_VERIF
									,DO.NOMBRE ' '  DO.APELLIDO_PATERNO  ' ' 
									 DO.APELLIDO_MATERNO NOMBRE
							  FROM	INGRESO I
									,DEUDOR DO
							 WHERE	I.COD_INGRESO = {PARAM1}
							   		AND	I.COD_DEUDOR = DO.COD_DEUDOR";
				}
				break;
				
				/////////////// AJAX_INFO_SIMULACION ///////////////	
			case 'AJAX_SIMULACION_TIENE_INGRESO' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	NVL(f_ingreso_get_desde_simulacion (COD_INGRESO), 0) COD_INGRESO_SIMULACION
						    FROM	INGRESO I, DEUDOR D, USUARIO U
							WHERE	COD_INGRESO = {PARAM1}
							AND		I.COD_DEUDOR = D.COD_DEUDOR
							AND		I.COD_USUARIO = U.COD_USUARIO";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT isnull(dbo.f_ingreso_get_desde_simulacion (COD_INGRESO), 0) COD_INGRESO_SIMULACION
						    FROM	INGRESO I, DEUDOR D, USUARIO U
							WHERE	COD_INGRESO = {PARAM1}
							AND		I.COD_DEUDOR = D.COD_DEUDOR
							AND		I.COD_USUARIO = U.COD_USUARIO";
				}
				break;
				
			//////////////////////////////////////////////////////////////////////////
				
			//////////// VALIDACIONES PARA CREAR DESDE BOLETA  ///////////////////
			/////////////////  AJAX_EXISTE_BOLETA ///////////////
			case 'AJAX_EXISTE_BOLETA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT COD_BOLETA
							  FROM BOLETA B
							 WHERE COD_BOLETA = {PARAM1}";
				}
				break;
				
			///////////////  AJAX_EXISTE_SUB_BOLETA ///////////////	
			case 'AJAX_EXISTE_SUB_BOLETA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	SB.COD_SUB_BOLETA
									,SB.NRO_SUB_BOLETA
						    FROM   	SUB_BOLETA SB
							WHERE  	SB.COD_BOLETA = {PARAM1}";
				}
				break;
			
			///////////////  AJAX_EXISTE_SUB_BOLETA ///////////////	
			case 'AJAX_SUB_BOLETA_PAGADA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	SB.COD_SUB_BOLETA
									,SB.NRO_SUB_BOLETA
						    FROM   	SUB_BOLETA SB
							WHERE  	SB.COD_BOLETA = {PARAM1}
							  AND	SB.FECHA_PAGO IS NULL";
				}
				break;
				
			/////////////// AJAX_INFO_BOLETA ///////////////	
			case 'AJAX_INFO_BOLETA' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	DR.RUT
									,DR.DIG_VERIF
									,DR.NOMBRE || ' ' || DR.APELLIDO_PATERNO || ' ' || DR.APELLIDO_MATERNO NOMBRE
									,D.COD_DEUDA
									,D.COD_TIPO_DEUDA
									,NVL(PP.ANO_COBRO, 9999) ANO_COBRO
							FROM    BOLETA B left outer join PLAN_PAGO PP on B.COD_PLAN_PAGO = PP.COD_PLAN_PAGO
									,DEUDA D
									,DEUDOR DR
									,TIPO_DEUDA TD
							WHERE   B.COD_BOLETA = {PARAM1}
							  AND	B.COD_DEUDA = D.COD_DEUDA
							  AND	D.COD_ESTADO_DEUDA in (1, 2)
							  AND	D.COD_DEUDOR = DR.COD_DEUDOR
							  AND	TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	DR.RUT
									,DR.DIG_VERIF
									,DR.NOMBRE  ' '  DR.APELLIDO_PATERNO  ' '  DR.APELLIDO_MATERNO NOMBRE
									,D.COD_DEUDA
									,D.COD_TIPO_DEUDA
									,isnull(PP.ANO_COBRO, 9999) ANO_COBRO
							FROM    BOLETA B left outer join PLAN_PAGO PP on B.COD_PLAN_PAGO = PP.COD_PLAN_PAGO
									,DEUDA D
									,DEUDOR DR
									,TIPO_DEUDA TD
							WHERE   B.COD_BOLETA = {PARAM1}
							  AND	B.COD_DEUDA = D.COD_DEUDA
							  AND	D.COD_ESTADO_DEUDA in (1, 2)
							  AND	D.COD_DEUDOR = DR.COD_DEUDOR
							  AND	TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA";
				}
				break;
				
			/////////////////  DD_SUB_BOLETA ///////////////
			case 'DD_SUB_BOLETA' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	'' COD_SUB_BOLETA
									,'' NRO_SUB_BOLETA 
						  	FROM 	dual";
				}else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 	'' COD_SUB_BOLETA
									,'' NRO_SUB_BOLETA";
				}
			break;

			/////////////////  AJAX_SUB_BOLETA ///////////////
			case 'AJAX_SUB_BOLETA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_SUB_BOLETA
									,NRO_SUB_BOLETA 
						  	FROM 	SUB_BOLETA
						 	WHERE	FECHA_PAGO IS NULL
							  AND	COD_BOLETA = {PARAM1}
						 	ORDER BY	NRO_SUB_BOLETA";
				}
			break;
			
			////////////// ALERT_DOC_PROTESTADO ////////////////	
			case 'ALERT_DOC_PROTESTADO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){	
					$sql = "SELECT	COUNT(*) CANT
							FROM	DOCUMENTO_PAGO DP, DEUDOR DR
							WHERE	RUT = {PARAM1}
								AND DP.COD_DEUDOR = DR.COD_DEUDOR
								AND COD_ESTADO_DOCUMENTO_PAGO = ".self::K_ESTADO_DOC_PROTESTADO;
				}
			break;	
			
			
			////////////// VALIDA_BOLETA_CARTERA ////////////////	
			case 'VALIDA_BOLETA_CARTERA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT COUNT(*) CANT
								FROM BOLETA BO, DEUDA DE
								WHERE BO.COD_BOLETA = {PARAM1}
								AND BO.COD_DEUDA=DE.COD_DEUDA
								AND DE.COD_CARTERA = {PARAM2}";
				}
			break;
			
			////////////// VALIDA_BOLETA_CARTERA_UCM2 ////////////////	
			case 'VALIDA_BOLETA_CARTERA_UCM2' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT COUNT(*) CANT
								FROM BOLETA BO, DEUDA DE
								WHERE BO.COD_BOLETA = {PARAM1}
								AND BO.COD_DEUDA=DE.COD_DEUDA
								AND DE.COD_CARTERA = {PARAM2}";
				}
			break;
			
			////////////// FECHAS ////////////////	
			case 'FECHAS' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	To_Char(trunc(SYSDATE - 60), 'DD/MM/YYYY') FECHA_60_DIAS_ANTES
                            		,f_ultimo_dia(TO_CHAR(SYSDATE, 'MM'),TO_CHAR(SYSDATE, 'YYYY'))||'/'||TO_CHAR(SYSDATE, 'MM')||'/'||TO_CHAR(SYSDATE, 'YYYY') FECHA_ULT_DIA_MES 
							FROM 	DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	convert(varchar(20), DATEADD(day, -60, getdate()), 103) FECHA_60_DIAS_ANTES
									,convert(varchar(2) ,dbo.f_ultimo_dia (month(getdate()), year(getdate())))+'/'+convert(varchar(2),month(getdate()))+'/'+convert(varchar(4),year(getdate())) FECHA_ULT_DIA_MES";
				}
			break;
			
			////////////// AJAX_VALIDEZ_SIMULACION ////////////////	
			case 'AJAX_VALIDEZ_SIMULACION' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	to_char(FECHA_INGRESO, 'dd/mm/yyyy') FECHA_INGRESO
									,CASE SIGN(VALIDEZ_SIMULACION - to_number(TRUNC(SYSDATE) - TRUNC(FECHA_INGRESO)))
										WHEN -1 THEN 'N'
										ELSE 'S'
									END TIENE_VALIDEZ
							FROM 	INGRESO 
							WHERE 	COD_INGRESO = {PARAM1}";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT convert(varchar(10), FECHA_INGRESO, 103) FECHA_INGRESO
								,CASE SIGN(VALIDEZ_SIMULACION - DATEDIFF(DAY, FECHA_INGRESO, GETDATE()))
									WHEN -1 THEN 'N'
									ELSE 'S'
								END TIENE_VALIDEZ
							FROM INGRESO 
							WHERE COD_INGRESO = {PARAM1}";
				}
			break;
			
			////////////// AJAX_VALIDEZ_BOLETA ////////////////	
			case 'AJAX_VALIDEZ_BOLETA' :
				if (K_CLIENTE=="UACH"){
					$sql = "SELECT	COUNT(*) CANT 
							FROM	BOLETA B, SUB_BOLETA SB
							WHERE	B.COD_BOLETA = SB.COD_BOLETA
							AND		B.COD_BOLETA = {PARAM1}
							AND		(FECHA_VENCTO > GETDATE() or B.CARTERA_CONOCIDA='S')";
				}
				else if (K_TIPO_BD=="oci"){
					$sql = "SELECT	COUNT(*) CANT 
							FROM	BOLETA B, SUB_BOLETA SB
							WHERE	B.COD_BOLETA = SB.COD_BOLETA
							AND		B.COD_BOLETA = {PARAM1}
							AND		FECHA_VENCTO > SYSDATE";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	COUNT(*) CANT 
							FROM	BOLETA B, SUB_BOLETA SB
							WHERE	B.COD_BOLETA = SB.COD_BOLETA
							AND		B.COD_BOLETA = {PARAM1}
							AND		FECHA_VENCTO > GETDATE()";
			}
			break;
			
			////////////// PRINT_INGRESO_UACH ////////////////	
			case 'PRINT_INGRESO_UACH' :
				if (K_TIPO_BD=="mssql") {
					$sql = "SELECT	CONVERT(VARCHAR(20), I.FECHA_INGRESO, 103) FECHA_INGRESO
									,CONVERT(VARCHAR(5), I.FECHA_INGRESO, 24) HORA_INGRESO
									,U.NOM_USUARIO
									,I.COD_INGRESO
									,I.COD_FAMILIA_DEUDA
									,DE.RUT
									,DE.DIG_VERIF
									,SUM(DP.MONTO) SUM_MONTO
									,DP.COD_TIPO_DOCUMENTO_PAGO
									,TDP.NOM_TIPO_DOCUMENTO_PAGO
							FROM	INGRESO I
									,USUARIO U
									,DEUDOR DE
									,DOCUMENTO_INGRESO DI
									,DOCUMENTO_PAGO DP
									,TIPO_DOCUMENTO_PAGO TDP
							WHERE	I.COD_INGRESO = {PARAM1}
								AND	I.COD_INGRESO = DI.COD_INGRESO
								AND I.COD_DEUDOR = DE.COD_DEUDOR
								AND I.COD_USUARIO = U.COD_USUARIO
								AND DI.COD_DOCUMENTO_PAGO = DP.COD_DOCUMENTO_PAGO
								AND DP.COD_TIPO_DOCUMENTO_PAGO = TDP.COD_TIPO_DOCUMENTO_PAGO
							GROUP BY FECHA_INGRESO, NOM_USUARIO, I.COD_INGRESO, RUT, DIG_VERIF, NOM_TIPO_DOCUMENTO_PAGO, DP.COD_TIPO_DOCUMENTO_PAGO, I.COD_FAMILIA_DEUDA
							UNION
							SELECT	CONVERT(VARCHAR(20), I.FECHA_INGRESO, 103) FECHA_INGRESO
									,CONVERT(VARCHAR(5), I.FECHA_INGRESO, 24) HORA_INGRESO
									,U.NOM_USUARIO
									,I.COD_INGRESO
									,I.COD_FAMILIA_DEUDA
									,DE.RUT
									,DE.DIG_VERIF
									,SUM(DP.MONTO) SUM_MONTO
									,DP.COD_TIPO_DOCUMENTO_PAGO
									,TDP.NOM_TIPO_DOCUMENTO_PAGO
							FROM	INGRESO I
									,USUARIO U
									,DEUDOR DE
									,DOCUMENTO_PAGO DP
									,DOCUMENTO_INGRESO_CH_FECHA DIF
									,TIPO_DOCUMENTO_PAGO TDP
							WHERE	I.COD_INGRESO = {PARAM1}
								AND	I.COD_INGRESO = DIF.COD_INGRESO
								AND I.COD_DEUDOR = DE.COD_DEUDOR
								AND	I.COD_USUARIO = U.COD_USUARIO
								AND DIF.COD_DOCUMENTO_PAGO = DP.COD_DOCUMENTO_PAGO
								AND DP.COD_TIPO_DOCUMENTO_PAGO = TDP.COD_TIPO_DOCUMENTO_PAGO
							GROUP BY FECHA_INGRESO, NOM_USUARIO, I.COD_INGRESO, RUT, DIG_VERIF, TDP.NOM_TIPO_DOCUMENTO_PAGO, DP.COD_TIPO_DOCUMENTO_PAGO, I.COD_FAMILIA_DEUDA
							ORDER BY DP.COD_TIPO_DOCUMENTO_PAGO ASC";							
				}
			break;
			
			////////////// PRINT_USACH_GENERAL ////////////////	
			case 'PRINT_USACH_GENERAL' :
				if (K_TIPO_BD=="oci") {
					$sql = "SELECT  	ing.cod_ingreso
				          				,deu.rut
				          				,deu.dig_verif
				          				,deu.nombre
				          				,deu.apellido_paterno
				          				,deu.apellido_materno
                  		  				,u.nom_usuario
				          				,ing.referencia
				          				,de.cod_deuda
				          				,de.cod_tipo_deuda
				          				,Sum(it.monto_pago) MONTO_UTM
				          				,Sum(it.monto_pago_pesos) MONTO_PESOS
				          				,de.cod_tipo_deuda
							FROM  	deudor deu
				      				,ingreso ing
				      				,item_ingreso it
				      				,deuda de
              	      				,usuario u
							WHERE ing.cod_ingreso = {PARAM1}
				      				AND ing.cod_deudor = deu.cod_deudor
				      				AND ing.cod_ingreso = it.cod_ingreso
				      				AND it.cod_deuda = de.cod_deuda
              	      				AND u.cod_usuario = ing.cod_usuario
							GROUP BY ing.cod_ingreso, deu.rut, deu.nombre, deu.apellido_paterno, deu.apellido_materno, ing.referencia, de.cod_deuda,de.cod_tipo_deuda, deu.dig_verif, u.nom_usuario";							
			}
			break;
			
			
			////////////// PRINT_USACH_DOCUMENTO_PAGO_2 ////////////////	
			case 'PRINT_USACH_DOCUMENTO_PAGO_DEUDA' :
				if (K_TIPO_BD=="oci") {
					$sql = "SELECT IT.COD_ITEM_INGRESO
							      ,DE.COD_TIPO_DEUDA
							      ,MA.MONTO_ASIGNADO
							      ,MA.COD_DOCUMENTO_PAGO
							      ,DP.COD_TIPO_DOCUMENTO_PAGO
							      ,NOM_TIPO_DOCUMENTO_PAGO
							      ,DP.NRO_DOCUMENTO_PAGO
							      ,B.NOM_BANCO
							FROM ITEM_INGRESO IT
							    ,DEUDA DE
							    ,MONTO_DOC_ASIGNADO MA
							    ,DOCUMENTO_PAGO DP left outer join BANCO B on  DP.COD_BANCO = B.COD_BANCO
							    ,TIPO_DOCUMENTO_PAGO TDP
							WHERE IT.COD_INGRESO =  {PARAM1}
							   AND IT.COD_DEUDA = DE.COD_DEUDA
							   AND DE.COD_TIPO_DEUDA = '{PARAM2}'
							   AND MA.COD_ITEM_INGRESO=IT.COD_ITEM_INGRESO
							   AND MA.COD_DOCUMENTO_PAGO= DP.COD_DOCUMENTO_PAGO
							   AND DP.COD_TIPO_DOCUMENTO_PAGO = TDP.COD_TIPO_DOCUMENTO_PAGO
							   AND DP.COD_TIPO_DOCUMENTO_PAGO NOT IN ".self::K_DOCUMENTO_PAGO_CH_FECHA;							
			}
			break;
			
			////////////// PRINT_USACH_DOCUMENTO_PAGO ////////////////	
			case 'PRINT_USACH_DOCUMENTO_PAGO' :
				if (K_TIPO_BD=="oci") {
					$sql = "SELECT dp.cod_tipo_documento_pago
    								,tdp.nom_tipo_documento_pago
    								,nro_documento_pago
    								,b.nom_banco
								    ,To_Char(i.fecha_ingreso, 'dd/mm/yyyy') FECHA_PAGO
								    ,i.monto_cheque_fecha
								    ,i.monto_otros_gastos
								    ,dp.monto
    						FROM  ingreso i
								    ,documento_ingreso di
								    ,documento_pago dp
								    ,tipo_documento_pago tdp
								    ,banco b
							WHERE b.cod_banco(+) = dp.cod_banco 
							    AND i.cod_ingreso = {PARAM1}
							    AND di.cod_ingreso = i.cod_ingreso
							    AND dp.cod_documento_pago = di.cod_documento_pago
							    AND tdp.cod_tipo_documento_pago = dp.cod_tipo_documento_pago";							
			}
			break;
			
			////////////// PRINT_USACH_CHEQUE_FECHA ////////////////	
			case 'PRINT_USACH_CHEQUE_FECHA' :
				if (K_TIPO_BD=="oci") {
					/*$sql = "SELECT IT.COD_ITEM_INGRESO
							      ,DE.COD_TIPO_DEUDA
							      ,DP.MONTO MONTO_ASIGNADO
							      ,MA.COD_DOCUMENTO_PAGO
							      ,DP.COD_TIPO_DOCUMENTO_PAGO
							      ,NOM_TIPO_DOCUMENTO_PAGO
							      ,DP.NRO_DOCUMENTO_PAGO
							      ,B.NOM_BANCO
							      ,TO_CHAR(DP.FECHA_DOCUMENTO_PAGO, 'DD/MM/YYYY') VENCIMIENTO
							FROM ITEM_INGRESO IT
							    ,DEUDA DE
							    ,MONTO_DOC_ASIGNADO MA
							    ,DOCUMENTO_PAGO DP left outer join BANCO B on  DP.COD_BANCO = B.COD_BANCO
							    ,TIPO_DOCUMENTO_PAGO TDP
							WHERE IT.COD_INGRESO =  {PARAM1}
							   AND IT.COD_DEUDA = DE.COD_DEUDA
							   AND DE.COD_TIPO_DEUDA = '{PARAM2}'
							   AND MA.COD_ITEM_INGRESO=IT.COD_ITEM_INGRESO
							   AND MA.COD_DOCUMENTO_PAGO= DP.COD_DOCUMENTO_PAGO
							   AND DP.COD_TIPO_DOCUMENTO_PAGO = TDP.COD_TIPO_DOCUMENTO_PAGO
							   AND DP.COD_TIPO_DOCUMENTO_PAGO = ".self::K_DOCUMENTO_PAGO_CH_FECHA;*/
					$sql= "SELECT  B.NOM_BANCO
			                       ,DP.NRO_DOCUMENTO_PAGO
			                       ,DP.MONTO MONTO_ASIGNADO 
			                       ,TO_CHAR(DP.FECHA_DOCUMENTO_PAGO, 'DD/MM/YYYY') VENCIMIENTO
			                 FROM documento_ingreso_ch_fecha dicf
			                    , documento_pago dp left outer join BANCO B on  DP.COD_BANCO = B.COD_BANCO
			                 WHERE dicf.cod_documento_pago = dp.cod_documento_pago 
			                 and cod_ingreso = {PARAM1}
			                 ORDER BY DP.FECHA_DOCUMENTO_PAGO asc";
				}
			break;
			
			////////////// PRINT_USACH_MOROSA ////////////////	
			case 'PRINT_USACH_MOROSA':
				if (K_TIPO_BD=="oci") {
					$sql = "SELECT DISTINCT PP.COD_PLAN_PAGO
                  							,PP.ANO_COBRO
                  							,PP.NRO_CUOTA
 							FROM PLAN_PAGO PP
		    						,ITEM_INGRESO IT
		    						,INGRESO ING
                    				,DEUDA DE
 								WHERE ING.COD_INGRESO = {PARAM1}
			  						AND ING.COD_INGRESO = IT.COD_INGRESO
			  						AND IT.COD_PLAN_PAGO = PP.COD_PLAN_PAGO
				                  	AND IT.COD_DEUDA = PP.COD_DEUDA
				                  	AND IT.COD_DEUDA = DE.COD_DEUDA
				                  	AND DE.COD_TIPO_DEUDA = '{PARAM2}'
		    						AND PP.COD_ESTADO_PLAN_PAGO = ".self::K_ESTADO_PLAN_PAGO_MOROSA."
  								ORDER BY ano_cobro ASC";							
			}
			break;
			
			////////////// PRINT_CUOTA_ANO ////////////////	
			case 'PRINT_CUOTA_ANO':
				if (K_TIPO_BD=="oci") {
					$sql = "SELECT DISTINCT PP.COD_PLAN_PAGO
                  							,PP.ANO_COBRO
                  							,PP.NRO_CUOTA
 							FROM PLAN_PAGO PP
		    						,ITEM_INGRESO IT
		    						,INGRESO ING
                    				,DEUDA DE
 								WHERE ING.COD_INGRESO = {PARAM1}
				  					AND ING.COD_INGRESO = IT.COD_INGRESO
				  					AND IT.COD_PLAN_PAGO = PP.COD_PLAN_PAGO
				                  	AND IT.COD_DEUDA = PP.COD_DEUDA
				                  	AND IT.COD_DEUDA = DE.COD_DEUDA
				                  	AND DE.COD_TIPO_DEUDA = '{PARAM2}'
								ORDER BY ano_cobro ASC";							
			}
			break;
			
			//////////////AJAX_VALIDEZ_SIMULACION_USACH////////////////	
			case 'AJAX_VALIDEZ_SIMULACION_USACH':
				if (K_TIPO_BD=="oci") {
					$sql = "SELECT To_Number(To_Char(FECHA_INGRESO,'dd')) + VALIDEZ_SIMULACION DIA_HABIL
      							,TO_CHAR(FECHA_INGRESO,'dd/mm/yyyy') FECHA_INGRESO
      							,f_get_parametro({PARAM1}) UNIVERSIDAD
      							,To_Char(SYSDATE,'dd/mm/yyyy') FECHA_ACTUAL
							FROM INGRESO WHERE COD_INGRESO = {PARAM2}";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT DBO.F_GET_PARAMETRO({PARAM1}) UNIVERSIDAD
							FROM INGRESO WHERE COD_INGRESO ={PARAM2}";
				}
			break;
			
			//////////////DETALLE_CUOTA_ANO////////////////	
			case 'DETALLE_CUOTA_ANO':
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT  NRO_CUOTA
        							,ANO_COBRO
        							,MONTO_CAPITAL
        							,MONTO_INTERES
							FROM PLAN_PAGO PP
      							,ITEM_INGRESO IT
      							,DEUDA DE
							WHERE	IT.COD_PLAN_PAGO = PP.COD_PLAN_PAGO
  									AND   IT.COD_INGRESO = {PARAM1}
  									AND   IT.COD_DEUDA = DE.COD_DEUDA
  									AND   DE.COD_TIPO_DEUDA = {PARAM2}";
				}
			break;
			/////////////////  ERROR ///////////////
			default: 
				base::error("sql_ingreso::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
/////////////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_sql_ingreso.php";
if (file_exists($file_name))
    require_once($file_name);
else {
    class sql_ingreso extends sql_ingreso_base {
    }
}
?>