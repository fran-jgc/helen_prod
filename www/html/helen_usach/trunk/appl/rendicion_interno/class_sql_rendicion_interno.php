<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_rendicion_interno extends sql_tabla {
	static function get_sql($selector_sql) {
		$K_SOLICITADA = 1;
		switch ($selector_sql) {
			///////////////// INPUT ///////////////
			case 'INPUT' :
				///////////////// ORACLE ///////////////
				if (K_TIPO_BD=="oci"){
					$sql =	"SELECT  R.COD_RENDICION_INTERNO
							         ,To_Char(R.FECHA_RENDICION, 'dd/mm/yyyy') FECHA_RENDICION
							         ,R.COD_USUARIO
							         ,U.NOM_USUARIO
							         ,To_Char(R.FECHA_INICIO, 'dd/mm/yyyy') FECHA_INICIO
							         ,To_Char(R.FECHA_TERMINO, 'dd/mm/yyyy') FECHA_TERMINO
							         ,R.COD_TIPO_DEUDA
							         ,R.COD_ESTADO_RENDICION
							         ,ER.NOM_ESTADO_RENDICION
							         ,f_last_mod('NOM_USUARIO', 'RENDICION_INTERNO', 'COD_ESTADO_RENDICION', R.COD_RENDICION_INTERNO) NOM_USUARIO_CAMBIO
							         ,f_last_mod('FECHA_CAMBIO', 'RENDICION_INTERNO', 'COD_ESTADO_RENDICION', R.COD_RENDICION_INTERNO) FECHA_CAMBIO
							         ,R.COD_CUENTA_CORRIENTE
							         ,'' DISPLAY_EXPORT
								FROM RENDICION_INTERNO R, USUARIO U, ESTADO_RENDICION ER
							   WHERE R.COD_RENDICION_INTERNO =  {KEY1}
							     AND R.COD_USUARIO = U.COD_USUARIO
							     AND R.COD_ESTADO_RENDICION = ER.COD_ESTADO_RENDICION";
				}
				///////////////// SQL-SERVER ///////////////
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT  R.COD_RENDICION_INTERNO
							         ,convert(varchar, R.FECHA_RENDICION, 103) FECHA_RENDICION
							         ,R.COD_USUARIO
							         ,U.NOM_USUARIO
							         ,convert(varchar, R.FECHA_INICIO, 103) FECHA_INICIO
							         ,convert(varchar, R.FECHA_TERMINO, 103) FECHA_TERMINO
							         ,R.COD_TIPO_DEUDA
							         ,R.COD_ESTADO_RENDICION_INTERNO
							         ,ER.NOM_ESTADO_RENDICION_INTERNO
							         ,dbo.f_last_mod('NOM_USUARIO', 'RENDICION_INTERNO', 'COD_ESTADO_RENDICION_INTERNO', R.COD_RENDICION_INTERNO) NOM_USUARIO_CAMBIO
							         ,dbo.f_last_mod('FECHA_CAMBIO', 'RENDICION_INTERNO', 'COD_ESTADO_RENDICION_INTERNO', R.COD_RENDICION_INTERNO) FECHA_CAMBIO
							         ,R.COD_CUENTA_CORRIENTE
								FROM RENDICION_INTERNO R, USUARIO U, ESTADO_RENDICION_INTERNO ER
							   WHERE R.COD_RENDICION_INTERNO =  {KEY1}
							     AND R.COD_USUARIO = U.COD_USUARIO
							     AND R.COD_ESTADO_RENDICION_INTERNO = ER.COD_ESTADO_RENDICION_INTERNO";
				}
				break;

				///////////////// INPUT_DD_COD_DEUDA ///////////////
			case 'DD_ESTADO_RENDICION' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	ER.COD_ESTADO_RENDICION
								   ,ER.NOM_ESTADO_RENDICION
							FROM	ESTADO_RENDICION ER  
             			   ORDER BY COD_ESTADO_RENDICION";
				}
				break;

				///////////////// OUTPUT///////////////
			case 'OUTPUT' :
				///////////////// ORACLE ///////////////
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT   R.COD_RENDICION_INTERNO
							         ,TO_CHAR(R.FECHA_RENDICION, 'DD/MM/YYYY') FECHA_RENDICION
							         ,R.COD_TIPO_DEUDA
							         ,TO_CHAR(R.FECHA_INICIO, 'DD/MM/YYYY') FECHA_INICIO
									 ,TO_CHAR(R.FECHA_TERMINO, 'DD/MM/YYYY') FECHA_TERMINO
							         ,ER.NOM_ESTADO_RENDICION
							         ,F_REND_INT_TOT_UTM(COD_RENDICION_INTERNO) MONTO_MONEDA
							         ,F_REND_INT_TOT_PESOS(COD_RENDICION_INTERNO) MONTO_PESOS
							  FROM 	 RENDICION_INTERNO R,  ESTADO_RENDICION ER
							WHERE 	 R.COD_ESTADO_RENDICION = ER.COD_ESTADO_RENDICION
						  	ORDER BY R.COD_RENDICION_INTERNO DESC";
				}
				///////////////// SQL-SERVER  ///////////////
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT   R.COD_RENDICION_INTERNO
							         ,convert(varchar, R.FECHA_RENDICION_INTERNO, 103) FECHA_RENDICION_INTERNO
							         ,R.COD_TIPO_DEUDA
							         ,convert(varchar, R.FECHA_INICIO, 103) FECHA_INICIO
									 ,convert(varchar, R.FECHA_TERMINO, 103) FECHA_TERMINO
							         ,ER.NOM_ESTADO_RENDICION_INTERNO
							         ,dbo.f_rend_tot_utm_abono_dscto(COD_RENDICION_INTERNO) MONTO_MONEDA
							         ,dbo.f_rend_tot_pesos_abono_dscto(COD_RENDICION_INTERNO) MONTO_PESOS
							  FROM 	 RENDICION_INTERNO R,  ESTADO_RENDICION_INTERNO ER
							WHERE 	 R.COD_ESTADO_RENDICION_INTERNO = ER.COD_ESTADO_RENDICION_INTERNO
						  	ORDER BY R.COD_RENDICION_INTERNO DESC";
				}
				break;
					
				//////////////// DW_DETALLE_ABONO///////////////
			case 'DW_DETALLE_ABONO' :
				///////////////// ORACLE ///////////////
				if (K_TIPO_BD=="oci"){
					$sql = "select  'S' SELECCION
									,'ABONO' TIPO_DATO
									,'ABONO' TIPO_DATO_H
									,RA.COD_RENDICION_INTERNO_ABONO
									,RA.COD_RENDICION_INTERNO   
									,RA.COD_ABONO CODIGO
									,RA.COD_ABONO CODIGO_H
									,to_char(A.FECHA_ABONO, 'dd/mm/yyyy') FECHA
									,DE.RUT
									,DE.DIG_VERIF
							        ,DE.NOMBRE || ' ' || DE.APELLIDO_PATERNO || ' ' || DE.APELLIDO_MATERNO NOM_DEUDOR
									,A.COD_DEUDA
									,RA.MONTO_MONEDA
									,RA.MONTO_MONEDA MONTO_MONEDA_H
									,RA.MONTO_PESOS
									,RA.MONTO_PESOS MONTO_PESOS_H
									,RA.PORC
									,RA.PORC PORC_H
									,RA.COD_ACREEDOR_INTERNO COD_ACREEDOR_INTERNO_H
					      			,AI.NOM_ACREEDOR_INTERNO
									,RA.MONTO_RENDIDO_MONEDA 
							      	,RA.MONTO_RENDIDO_MONEDA MONTO_RENDIDO_MONEDA_H
					      			,RA.MONTO_RENDIDO_PESOS
					      			,RA.MONTO_RENDIDO_PESOS MONTO_RENDIDO_PESOS_H
							FROM RENDICION_INTERNO_ABONO RA, ABONO A, DEUDA DA, DEUDOR DE, ACREEDOR_INTERNO AI
							WHERE RA.COD_RENDICION_INTERNO = {KEY1}
							  and RA.COD_ABONO is not null
							  and A.COD_ABONO = RA.COD_ABONO 
							  and DA.COD_DEUDA = A.COD_DEUDA
                              AND RA.COD_ACREEDOR_INTERNO = AI.COD_ACREEDOR_INTERNO
							  and DE.COD_DEUDOR = DA.COD_DEUDOR
					  		union
							select  'S' SELECCION
									,'DSCTO' TIPO_DATO
									,'DSCTO' TIPO_DATO_H
									,RA.COD_RENDICION_INTERNO_ABONO
									,RA.COD_RENDICION_INTERNO   
									,RA.COD_DESCUENTO CODIGO
									,RA.COD_DESCUENTO CODIGO_H
									,to_char(D.FECHA_DESCUENTO, 'dd/mm/yyyy') FECHA
									,DE.RUT
									,DE.DIG_VERIF
							        ,DE.NOMBRE || ' ' || DE.APELLIDO_PATERNO || ' ' || DE.APELLIDO_MATERNO NOM_DEUDOR
									,D.COD_DEUDA
									,RA.MONTO_MONEDA
									,RA.MONTO_MONEDA MONTO_MONEDA_H
									,RA.MONTO_PESOS
									,RA.MONTO_PESOS MONTO_PESOS_H
									,RA.PORC
									,RA.PORC PORC_H
									,RA.COD_ACREEDOR_INTERNO COD_ACREEDOR_INTERNO_H
					      			,AI.NOM_ACREEDOR_INTERNO
									,RA.MONTO_RENDIDO_MONEDA
									,RA.MONTO_RENDIDO_MONEDA MONTO_RENDIDO_MONEDA_H
					      			,RA.MONTO_RENDIDO_PESOS
					      			,RA.MONTO_RENDIDO_PESOS MONTO_RENDIDO_PESOS_H
							FROM RENDICION_INTERNO_ABONO RA, DESCUENTO D, DEUDA DA, DEUDOR DE, ACREEDOR_INTERNO AI
							WHERE RA.COD_RENDICION_INTERNO = {KEY1}
							  and RA.COD_DESCUENTO is not null
							  and D.COD_DESCUENTO = RA.COD_DESCUENTO 
							  and DA.COD_DEUDA = D.COD_DEUDA
                              and RA.COD_ACREEDOR_INTERNO = AI.COD_ACREEDOR_INTERNO
							  and DE.COD_DEUDOR = DA.COD_DEUDOR";
				}
				///////////////// SQL-SERVER  ///////////////
				else if (K_TIPO_BD=="mssql"){
					$sql = "select  'ABONO' TIPO_DATO
									,'ABONO' TIPO_DATO_H
									,RA.COD_RENDICION_INTERNO_ABONO
									,RA.COD_RENDICION_INTERNO   
									,RA.COD_ABONO CODIGO
									,RA.COD_ABONO CODIGO_H
									,convert(varchar, A.FECHA_ABONO, 103) FECHA
									,DE.RUT
									,DE.DIG_VERIF
							        ,DE.NOMBRE + ' ' + DE.APELLIDO_PATERNO + ' ' + DE.APELLIDO_MATERNO NOM_DEUDOR
									,A.COD_DEUDA
									,RA.MONTO_MONEDA
									,RA.MONTO_MONEDA MONTO_MONEDA_H
									,RA.MONTO_PESOS
									,RA.MONTO_PESOS MONTO_PESOS_H
									,RA.PORC
									,RA.PORC PORC_H
									,RA.COD_UNIVERSIDAD COD_UNIVERSIDAD_H
									,U.NOM_UNIVERSIDAD 
									,RA.MONTO_RENDIDO_MONEDA 
							      	,RA.MONTO_RENDIDO_MONEDA MONTO_RENDIDO_MONEDA_H
					      			,RA.MONTO_RENDIDO_PESOS
					      			,RA.MONTO_RENDIDO_PESOS MONTO_RENDIDO_PESOS_H
							FROM RENDICION_INTERNO_ABONO RA, ABONO A, DEUDA DA, DEUDOR DE, UNIVERSIDAD U
							WHERE RA.COD_RENDICION_INTERNO = {KEY1}
							  and RA.COD_ABONO is not null
							  and A.COD_ABONO = RA.COD_ABONO 
							  and DA.COD_DEUDA = A.COD_DEUDA
							  and DE.COD_DEUDOR = DA.COD_DEUDOR
					  		  AND U.COD_UNIVERSIDAD = RA.COD_UNIVERSIDAD
					  		union
							select  'DSCTO' TIPO_DATO
									,'DSCTO' TIPO_DATO_H
									,RA.COD_RENDICION_INTERNO_ABONO
									,RA.COD_RENDICION_INTERNO   
									,RA.COD_DESCUENTO CODIGO
									,RA.COD_DESCUENTO CODIGO_H
									,convert(varchar, D.FECHA_DESCUENTO, 103) FECHA
									,DE.RUT
									,DE.DIG_VERIF
							        ,DE.NOMBRE + ' ' + DE.APELLIDO_PATERNO + ' ' + DE.APELLIDO_MATERNO NOM_DEUDOR
									,D.COD_DEUDA
									,RA.MONTO_MONEDA
									,RA.MONTO_MONEDA MONTO_MONEDA_H
									,RA.MONTO_PESOS
									,RA.MONTO_PESOS MONTO_PESOS_H
									,RA.PORC
									,RA.PORC PORC_H
									,RA.COD_UNIVERSIDAD COD_UNIVERSIDAD_H
									,U.NOM_UNIVERSIDAD 
									,RA.MONTO_RENDIDO_MONEDA
									,RA.MONTO_RENDIDO_MONEDA MONTO_RENDIDO_MONEDA_H
					      			,RA.MONTO_RENDIDO_PESOS
					      			,RA.MONTO_RENDIDO_PESOS MONTO_RENDIDO_PESOS_H
							FROM RENDICION_INTERNO_ABONO RA, DESCUENTO D, DEUDA DA, DEUDOR DE, UNIVERSIDAD U
							WHERE RA.COD_RENDICION_INTERNO = {KEY1}
							  and RA.COD_DESCUENTO is not null
							  and D.COD_DESCUENTO = RA.COD_DESCUENTO 
							  and DA.COD_DEUDA = D.COD_DEUDA
							  and DE.COD_DEUDOR = DA.COD_DEUDOR
					  		  AND U.COD_UNIVERSIDAD = RA.COD_UNIVERSIDAD";
				}
				break;

				///////////////// AJAX_DETALLE_ABONOS ///////////////
			case 'AJAX_DETALLE_ABONOS' :
				///////////////// ORACLE ///////////////
				if (K_TIPO_BD=="oci"){
					$sql = "select  'ABONO' TIPO_DATO
									,'ABONO' TIPO_DATO_H
									,null COD_RENDICION_INTERNO_ABONO
									,null COD_RENDICION_INTERNO
									,A.COD_ABONO CODIGO
									,A.COD_ABONO CODIGO_H
									,to_char(A.FECHA_ABONO, 'dd/mm/yyyy') FECHA
									,DE.RUT
									,DE.DIG_VERIF
									,DE.NOMBRE || ' ' || DE.APELLIDO_PATERNO || ' ' || DE.APELLIDO_MATERNO NOM_DEUDOR
									,A.COD_DEUDA
									,A.MONTO_MONEDA
									,A.MONTO_MONEDA MONTO_MONEDA_H
									,A.MONTO_PESOS
									,A.MONTO_PESOS MONTO_PESOS_H
					      			,C.PORC
					      			,C.PORC PORC_H
					      			,C.COD_ACREEDOR_INTERNO COD_ACREEDOR_INTERNO_H
					      			,AI.NOM_ACREEDOR_INTERNO
							      	,Round(A.MONTO_MONEDA * C.PORC/100, 4) MONTO_RENDIDO_MONEDA
							      	,Round(A.MONTO_MONEDA * C.PORC/100, 4) MONTO_RENDIDO_MONEDA_H 
					      			,Round(A.MONTO_PESOS * C.PORC/100, 0) MONTO_RENDIDO_PESOS
					      			,Round(A.MONTO_PESOS * C.PORC/100, 0) MONTO_RENDIDO_PESOS_H 
							FROM ABONO A, CRUZADO_INTERNO C, DEUDA DA, DEUDOR DE, ACREEDOR_INTERNO AI
							WHERE A.FECHA_ABONO BETWEEN {PARAM1} AND {PARAM2}
						      AND A.COD_ESTADO_PAGO = 1
							  AND A.COD_ABONO NOT IN (SELECT NVL(RA.COD_ABONO, 0) 
							                          FROM RENDICION_INTERNO_ABONO RA, RENDICION_INTERNO R 
							                          WHERE R.COD_RENDICION_INTERNO = RA.COD_RENDICION_INTERNO
							                            AND R.COD_ESTADO_RENDICION <> 3)
							  AND C.COD_DEUDA = A.COD_DEUDA
                              AND C.COD_ACREEDOR_INTERNO = AI.COD_ACREEDOR_INTERNO
                              AND C.COD_ACREEDOR_INTERNO = 2
                              AND DA.COD_DEUDA = A.COD_DEUDA
							  AND DA.COD_TIPO_DEUDA = '{PARAM3}'
							  AND DE.COD_DEUDOR = DA.COD_DEUDOR
							  AND COD_COMPROBANTE IS NOT NULL
                              AND A.COD_ORIGEN_PAGO IN(1,2,3,25) --CAJA, TESORERIA, WEBPAY Y FUNCIONARIOS
                            UNION
							select  'DSCTO' TIPO_DATO
									,'DSCTO' TIPO_DATO_H
									,null COD_RENDICION_INTERNO_ABONO
									,null COD_RENDICION_INTERNO
									,D.COD_DESCUENTO CODIGO
									,D.COD_DESCUENTO CODIGO_H
									,to_char(D.FECHA_DESCUENTO, 'dd/mm/yyyy') FECHA
									,DE.RUT
									,DE.DIG_VERIF
									,DE.NOMBRE || ' ' || DE.APELLIDO_PATERNO || ' ' || DE.APELLIDO_MATERNO NOM_DEUDOR
									,D.COD_DEUDA
									,D.MONTO_MONEDA
									,D.MONTO_MONEDA MONTO_MONEDA_H
									,D.MONTO_PESOS
									,D.MONTO_PESOS MONTO_PESOS_H
					      			,C.PORC
					      			,C.PORC PORC_H
					      			,C.COD_ACREEDOR_INTERNO COD_ACREEDOR_INTERNO_H
					      			,AI.NOM_ACREEDOR_INTERNO
					      			,Round(D.MONTO_MONEDA * C.PORC/100, 4) MONTO_RENDIDO_MONEDA
					      			,Round(D.MONTO_MONEDA * C.PORC/100, 4) MONTO_RENDIDO_MONEDA_H 
					      			,Round(D.MONTO_PESOS * C.PORC/100, 0) MONTO_RENDIDO_PESOS
					      			,Round(D.MONTO_PESOS * C.PORC/100, 0) MONTO_RENDIDO_PESOS_H
							FROM DESCUENTO D, CRUZADO_INTERNO C, DEUDA DA, DEUDOR DE, ACREEDOR_INTERNO AI
							WHERE D.FECHA_DESCUENTO BETWEEN {PARAM1} AND {PARAM2}
							  AND D.COD_ESTADO_PAGO = 1
							  AND D.COD_DESCUENTO NOT IN (SELECT NVL(RA.COD_DESCUENTO, 0) 
							                          FROM RENDICION_INTERNO_ABONO RA, RENDICION_INTERNO R 
							                          WHERE R.COD_RENDICION_INTERNO = RA.COD_RENDICION_INTERNO
							                            AND R.COD_ESTADO_RENDICION <> 3)  
							  AND C.COD_DEUDA = D.COD_DEUDA
                              AND C.COD_ACREEDOR_INTERNO = AI.COD_ACREEDOR_INTERNO
                              AND C.COD_ACREEDOR_INTERNO = 2
                              AND DA.COD_DEUDA = D.COD_DEUDA
							  AND DA.COD_TIPO_DEUDA = '{PARAM3}'
							  AND DE.COD_DEUDOR = DA.COD_DEUDOR
							  AND COD_COMPROBANTE IS NOT NULL
							  AND D.COD_ORIGEN_PAGO IN(1,2,3,25)";
				}

				///////////////// SQL-SERVER  ///////////////
				else if (K_TIPO_BD=="mssql"){
					$sql = "select  'ABONO' TIPO_DATO
									,'ABONO' TIPO_DATO_H
									,null COD_RENDICION_INTERNO_ABONO
									,null COD_RENDICION_INTERNO
									,A.COD_ABONO CODIGO
									,A.COD_ABONO CODIGO_H
									,convert(varchar, A.FECHA_ABONO, 103) FECHA
									,DE.RUT
									,DE.DIG_VERIF
									,DE.NOMBRE + ' ' + DE.APELLIDO_PATERNO + ' ' + DE.APELLIDO_MATERNO NOM_DEUDOR
									,A.COD_DEUDA
									,A.MONTO_MONEDA
									,A.MONTO_MONEDA MONTO_MONEDA_H
									,A.MONTO_PESOS
									,A.MONTO_PESOS MONTO_PESOS_H
					      			,C.PORC
					      			,C.PORC PORC_H
					      			,C.COD_UNIVERSIDAD COD_UNIVERSIDAD_H
					      			,U.NOM_UNIVERSIDAD
							      	,Round(A.MONTO_MONEDA * C.PORC/100, 4) MONTO_RENDIDO_MONEDA
							      	,Round(A.MONTO_MONEDA * C.PORC/100, 4) MONTO_RENDIDO_MONEDA_H 
					      			,Round(A.MONTO_PESOS * C.PORC/100, 0) MONTO_RENDIDO_PESOS
					      			,Round(A.MONTO_PESOS * C.PORC/100, 0) MONTO_RENDIDO_PESOS_H 
							FROM ABONO A, CRUZADO C, DEUDA DA, DEUDOR DE, UNIVERSIDAD U
							WHERE A.FECHA_ABONO BETWEEN {PARAM1} AND {PARAM2}
							  AND A.COD_ESTADO_PAGO = {PARAM3}
							  AND A.COD_ABONO NOT IN (SELECT RA.COD_ABONO 
							                          FROM RENDICION_INTERNO_ABONO RA, RENDICION_INTERNO R 
							                          WHERE R.COD_RENDICION_INTERNO = RA.COD_RENDICION_INTERNO
							                            AND R.COD_ESTADO_RENDICION_INTERNO <> {PARAM4})  
							  AND C.COD_DEUDA = A.COD_DEUDA
							  AND C.COD_UNIVERSIDAD <> dbo.f_get_parametro({PARAM5})
							  AND DA.COD_DEUDA = A.COD_DEUDA
							  AND DA.COD_TIPO_DEUDA = '{PARAM6}'
							  AND DE.COD_DEUDOR = DA.COD_DEUDOR
							  AND U.COD_UNIVERSIDAD = C.COD_UNIVERSIDAD
							union
							select  'DSCTO' TIPO_DATO
									,'DSCTO' TIPO_DATO_H
									,null COD_RENDICION_INTERNO_ABONO
									,null COD_RENDICION_INTERNO
									,D.COD_DESCUENTO CODIGO
									,D.COD_DESCUENTO CODIGO_H
									,convert(varchar, D.FECHA_DESCUENTO, 103) FECHA
									,DE.RUT
									,DE.DIG_VERIF
									,DE.NOMBRE + ' ' + DE.APELLIDO_PATERNO + ' ' + DE.APELLIDO_MATERNO NOM_DEUDOR
									,D.COD_DEUDA
									,D.MONTO_MONEDA
									,D.MONTO_MONEDA MONTO_MONEDA_H
									,D.MONTO_PESOS
									,D.MONTO_PESOS MONTO_PESOS_H
					      			,C.PORC
					      			,C.PORC PORC_H
					      			,C.COD_UNIVERSIDAD COD_UNIVERSIDAD_H
					      			,U.NOM_UNIVERSIDAD
					      			,Round(D.MONTO_MONEDA * C.PORC/100, 4) MONTO_RENDIDO_MONEDA
					      			,Round(D.MONTO_MONEDA * C.PORC/100, 4) MONTO_RENDIDO_MONEDA_H 
					      			,Round(D.MONTO_PESOS * C.PORC/100, 0) MONTO_RENDIDO_PESOS
					      			,Round(D.MONTO_PESOS * C.PORC/100, 0) MONTO_RENDIDO_PESOS_H
							FROM DESCUENTO D, CRUZADO C, DEUDA DA, DEUDOR DE, UNIVERSIDAD U
							WHERE D.FECHA_DESCUENTO BETWEEN {PARAM1} AND {PARAM2}
							  AND D.COD_ESTADO_PAGO = {PARAM3}
							  AND D.COD_DESCUENTO NOT IN (SELECT RA.COD_DESCUENTO 
							                          FROM RENDICION_INTERNO_ABONO RA, RENDICION_INTERNO R 
							                          WHERE R.COD_RENDICION_INTERNO = RA.COD_RENDICION_INTERNO
							                            AND R.COD_ESTADO_RENDICION_INTERNO <> {PARAM4})  
							  AND C.COD_DEUDA = D.COD_DEUDA
							  AND C.COD_UNIVERSIDAD <> dbo.f_get_parametro({PARAM5})
							  AND DA.COD_DEUDA = D.COD_DEUDA
							  AND DA.COD_TIPO_DEUDA = '{PARAM6}'
							  AND DE.COD_DEUDOR = DA.COD_DEUDOR
							  AND U.COD_UNIVERSIDAD = C.COD_UNIVERSIDAD";
				}
				break;

				///////////////// INPUT_DD_COD_DEUDA ///////////////
			case 'CUENTA_RENDICION_INTERNOES' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT COD_RENDICION_INTERNO
					FROM   RENDICION_INTERNO
					WHERE  COD_ESTADO_RENDICION_INTERNO = $K_SOLICITADA";
				}
				break;
					
			case 'COUNT_ACREEDOR_INTERNO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	DISTINCT RA.COD_ACREEDOR_INTERNO, AI.NOM_ACREEDOR_INTERNO
							FROM 	RENDICION_INTERNO_ABONO RA, ACREEDOR_INTERNO AI
							WHERE 	COD_RENDICION_INTERNO = {PARAM1}
                			AND   	RA.COD_ACREEDOR_INTERNO = AI.COD_ACREEDOR_INTERNO
                			ORDER BY COD_ACREEDOR_INTERNO";
				}
				break;

			case 'FILTRO_RENDICION_INTERNO' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	R.COD_RENDICION_INTERNO
									,To_Char(R.FECHA_RENDICION,'dd/mm/yyyy') FECHA_RENDICION
									,U.NOM_USUARIO
									,To_Char(R.FECHA_INICIO,'dd/mm/yyyy') FECHA_INICIO
									,To_Char(R.FECHA_TERMINO,'dd/mm/yyyy')FECHA_TERMINO
									,ER.NOM_ESTADO_RENDICION_INTERNO
									,CASE R.COD_TIPO_DEUDA
          								WHEN 'CS' THEN 'LEY 19287'
          								WHEN 'CSR' THEN 'LEY 19848'
          								WHEN 'CRI' THEN 'LEY 19287 17BITS'
        								END COD_TIPO_DEUDA
							FROM	RENDICION_INTERNO R
									,ESTADO_RENDICION_INTERNO ER
									,USUARIO U
							WHERE 	R.cod_rendicion_interno = {PARAM1}
									AND R.COD_ESTADO_RENDICION_INTERNO = ER.COD_ESTADO_RENDICION_INTERNO
									AND R.COD_USUARIO = U.COD_USUARIO";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	R.COD_RENDICION_INTERNO
									,convert(varchar, R.FECHA_RENDICION_INTERNO, 103) FECHA_RENDICION_INTERNO
									,U.NOM_USUARIO
									,convert(varchar, R.FECHA_INICIO, 103) FECHA_INICIO
									,convert(varchar, R.FECHA_TERMINO, 103)FECHA_TERMINO
									,ER.NOM_ESTADO_RENDICION_INTERNO
									,CASE R.COD_TIPO_DEUDA
          								WHEN 'CS' THEN 'LEY 19287'
          								WHEN 'CSR' THEN 'LEY 19848'
        								END COD_TIPO_DEUDA
							FROM	RENDICION_INTERNO R
									,ESTADO_RENDICION_INTERNO ER
									,USUARIO U
							WHERE 	R.cod_rendicion_interno = {PARAM1}
									AND R.COD_ESTADO_RENDICION_INTERNO = ER.COD_ESTADO_RENDICION_INTERNO
									AND R.COD_USUARIO = U.COD_USUARIO";
				}
				break;
					
			case 'PRINT_RECORD' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT    'ABONO' TIPO_DATO
							          ,To_Char(DE.RUT) RUT
							          ,To_Char(DE.DIG_VERIF) DIG_VERIF
							          ,DE.NOMBRE || ' ' || DE.APELLIDO_PATERNO || ' ' || DE.APELLIDO_MATERNO NOM_DEUDOR
							          ,ANO_COBRO
							          ,NRO_CUOTA
							          ,PP.MONTO_CAPITAL + PP.MONTO_INTERES MONTO_CUOTA
							          ,to_char(A.FECHA_ABONO, 'dd/mm/yyyy') FECHA_PAGO
							          ,A.MONTO_CAPITAL + A.MONTO_INTERES CAPITAL_UTM
							          ,A.MONTO_MULTA INTERES_PENAL_UTM
							          ,RA.MONTO_MONEDA TOTAL_UTM
							          ,RA.MONTO_PESOS TOTAL_PESOS
							          ,RA.PORC
							          ,RA.MONTO_RENDIDO_MONEDA
							          ,RA.MONTO_RENDIDO_PESOS
							          ,AI.NOM_ACREEDOR_INTERNO
							          ,RA.COD_ABONO CODIGO
							          ,OP.NOM_ORIGEN_PAGO
							          ,DA.COD_TIPO_DEUDA
							FROM      RENDICION_INTERNO R
							          ,ESTADO_RENDICION ER
							          ,RENDICION_INTERNO_ABONO RA
							          ,ABONO A left outer join PLAN_PAGO PP on PP.COD_PLAN_PAGO = A.COD_PLAN_PAGO
							          ,DEUDA DA
							          ,DEUDOR DE
							          ,ACREEDOR_INTERNO AI
							          ,ORIGEN_PAGO OP
							WHERE     RA.COD_RENDICION_INTERNO = {PARAM1}
								      AND RA.COD_ACREEDOR_INTERNO = {PARAM2}
							          AND RA.COD_ABONO IS NOT NULL
								      AND A.COD_ABONO = RA.COD_ABONO 
								      AND DA.COD_DEUDA = A.COD_DEUDA
								      AND DE.COD_DEUDOR = DA.COD_DEUDOR
							          AND AI.COD_ACREEDOR_INTERNO = RA.COD_ACREEDOR_INTERNO	
							          AND OP.COD_ORIGEN_PAGO = A.COD_ORIGEN_PAGO						          
							UNION
							SELECT    'DSCTO' TIPO_DATO
							          ,To_Char(DE.RUT) RUT
							          ,To_Char(DE.DIG_VERIF) DIG_VERIF
							          ,DE.NOMBRE || ' ' || DE.APELLIDO_PATERNO || ' ' || DE.APELLIDO_MATERNO NOM_DEUDOR
							          ,ANO_COBRO
							          ,NRO_CUOTA
							          ,PP.MONTO_CAPITAL + PP.MONTO_INTERES MONTO_CUOTA
							          ,to_char(D.FECHA_DESCUENTO, 'dd/mm/yyyy') FECHA_PAGO
							          ,Round(D.MONTO_CAPITAL + D.MONTO_INTERES,4) CAPITAL_UTM
							          ,D.MONTO_MULTA INTERES_PENAL_UTM
							          ,RA.MONTO_MONEDA TOTAL_UTM
							          ,RA.MONTO_PESOS TOTAL_PESOS
									  ,RA.PORC
									  ,RA.MONTO_RENDIDO_MONEDA
							          ,RA.MONTO_RENDIDO_PESOS
							          ,AI.NOM_ACREEDOR_INTERNO
							          ,RA.COD_DESCUENTO CODIGO
							          ,OP.NOM_ORIGEN_PAGO
							          ,DA.COD_TIPO_DEUDA
							FROM      RENDICION_INTERNO R
							          ,ESTADO_RENDICION ER
							          ,RENDICION_INTERNO_ABONO RA
							          ,DESCUENTO D left outer join PLAN_PAGO PP on PP.COD_PLAN_PAGO = D.COD_PLAN_PAGO
							          ,DEUDA DA
							          ,DEUDOR DE
							          ,ACREEDOR_INTERNO AI
							          ,ORIGEN_PAGO OP
							WHERE     RA.COD_RENDICION_INTERNO = {PARAM1} 
								      AND RA.COD_ACREEDOR_INTERNO = {PARAM2}                         
								      AND RA.COD_DESCUENTO IS NOT NULL
								      AND D.COD_DESCUENTO = RA.COD_DESCUENTO 
								      AND DA.COD_DEUDA = D.COD_DEUDA
								      AND DE.COD_DEUDOR = DA.COD_DEUDOR
									  AND AI.COD_ACREEDOR_INTERNO = RA.COD_ACREEDOR_INTERNO
									  AND OP.COD_ORIGEN_PAGO = D.COD_ORIGEN_PAGO
							ORDER BY  RUT
							          ,FECHA_PAGO                                                   
							          ,TIPO_DATO";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT    'ABONO' TIPO_DATO
							          ,CONVERT(VARCHAR(20),DE.RUT) + '-' + DE.DIG_VERIF RUT
							          ,DE.NOMBRE + ' ' + DE.APELLIDO_PATERNO + ' ' + DE.APELLIDO_MATERNO NOM_DEUDOR
							          ,ANO_COBRO
							          ,NRO_CUOTA
							          ,PP.MONTO_CAPITAL + PP.MONTO_INTERES MONTO_CUOTA
							          ,convert(varchar, A.FECHA_ABONO, 103) FECHA_PAGO
							          ,A.MONTO_CAPITAL + A.MONTO_INTERES CAPITAL_UTM
							          ,A.MONTO_MULTA INTERES_PENAL_UTM
							          ,A.MONTO_MONEDA TOTAL_UTM
							          ,A.MONTO_PESOS TOTAL_PESOS
							          ,RA.PORC
							          ,Round((A.MONTO_CAPITAL + A.MONTO_INTERES) * RA.PORC / 100,4) PORC_CAPITAL_UTM
							          ,Round((A.MONTO_MULTA) * RA.PORC / 100,4) PORC_INTERES_PENAL_UTM
							          ,Round((A.MONTO_MONEDA) * RA.PORC / 100,4) PORC_TOTAL_UTM
							          ,Round((A.MONTO_PESOS) * RA.PORC / 100,0) PORC_TOTAL_PESOS
							          ,U.NOM_UNIVERSIDAD
							          ,RA.COD_ABONO CODIGO
							FROM      RENDICION_INTERNO R
							          ,ESTADO_RENDICION_INTERNO ER
							          ,RENDICION_INTERNO_ABONO RA
							          ,ABONO A left outer join PLAN_PAGO PP on PP.COD_PLAN_PAGO = A.COD_PLAN_PAGO
							          ,DEUDA DA
							          ,DEUDOR DE
							          ,UNIVERSIDAD U
							WHERE     RA.COD_RENDICION_INTERNO = {PARAM1}
								      AND RA.COD_UNIVERSIDAD = {PARAM2}
							          AND RA.COD_ABONO IS NOT NULL
								      AND A.COD_ABONO = RA.COD_ABONO 
								      AND DA.COD_DEUDA = A.COD_DEUDA
								      AND DE.COD_DEUDOR = DA.COD_DEUDOR
							          AND U.COD_UNIVERSIDAD = RA.COD_UNIVERSIDAD							          
							UNION
							SELECT    'DSCTO' TIPO_DATO
							          ,CONVERT(VARCHAR(20),DE.RUT) + '-' + DE.DIG_VERIF RUT
							          ,DE.NOMBRE + ' ' + DE.APELLIDO_PATERNO + ' ' + DE.APELLIDO_MATERNO NOM_DEUDOR
							          ,ANO_COBRO
							          ,NRO_CUOTA
							          ,PP.MONTO_CAPITAL + PP.MONTO_INTERES MONTO_CUOTA
							          ,convert(varchar, D.FECHA_DESCUENTO, 103) FECHA_PAGO
							          ,Round(D.MONTO_CAPITAL + D.MONTO_INTERES,4) CAPITAL_UTM
							          ,D.MONTO_MULTA INTERES_PENAL_UTM
							          ,D.MONTO_MONEDA TOTAL_UTM
							          ,D.MONTO_PESOS TOTAL_PESOS
									  ,RA.PORC
							          ,Round((D.MONTO_CAPITAL + D.MONTO_INTERES) * RA.PORC / 100,4) PORC_CAPITAL_UTM
							          ,Round((D.MONTO_MULTA) * RA.PORC / 100,4) PORC_INTERES_PENAL_UTM
							          ,Round((D.MONTO_MONEDA) * RA.PORC / 100,4) PORC_TOTAL_UTM
							          ,Round((D.MONTO_PESOS) * RA.PORC / 100,0) PORC_TOTAL_PESOS
							          ,U.NOM_UNIVERSIDAD
							          ,RA.COD_DESCUENTO CODIGO
							FROM      RENDICION_INTERNO R
							          ,ESTADO_RENDICION_INTERNO ER
							          ,RENDICION_INTERNO_ABONO RA
							          ,DESCUENTO D left outer join PLAN_PAGO PP on PP.COD_PLAN_PAGO = D.COD_PLAN_PAGO
							          ,DEUDA DA
							          ,DEUDOR DE
							          ,UNIVERSIDAD U
							WHERE     RA.COD_RENDICION_INTERNO = {PARAM1} 
								      AND RA.COD_UNIVERSIDAD = {PARAM2}                         
								      AND RA.COD_DESCUENTO IS NOT NULL
								      AND D.COD_DESCUENTO = RA.COD_DESCUENTO 
								      AND DA.COD_DEUDA = D.COD_DEUDA
								      AND DE.COD_DEUDOR = DA.COD_DEUDOR
									  AND U.COD_UNIVERSIDAD = RA.COD_UNIVERSIDAD
							ORDER BY  RUT
							          ,FECHA_PAGO                                                   
							          ,TIPO_DATO";
				}
				break;
				///////////////// DD_COD_CUENTA_CORRIENTE /////////////////
			case 'DD_COD_CUENTA_CORRIENTE' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= 	"SELECT 	COD_CUENTA_CORRIENTE
										,NOM_CUENTA_CORRIENTE
							FROM 		CUENTA_CORRIENTE
							ORDER BY 	NOM_CUENTA_CORRIENTE";
				}
				break;
					
			case 'DW_DETALLE_ABONO_NEWRECORD' :
				if (K_TIPO_BD=="oci") {
					$sql = "select  'S' SELECCION
									,null TIPO_DATO
									,null TIPO_DATO_H
									,null COD_RENDICION_INTERNO_ABONO
									,null COD_RENDICION_INTERNO   
									,null CODIGO
									,null CODIGO_H
									,null FECHA
									,null RUT
									,null DIG_VERIF
									,null NOM_DEUDOR
									,null COD_DEUDA
                                    ,null COD_DEUDA_H
									,null MONTO_MONEDA
									,null MONTO_MONEDA_H
									,null MONTO_PESOS
									,null MONTO_PESOS_H
									,null PORC
									,null PORC_H
									,null COD_ACREEDOR_INTERNO_H
					      			,null NOM_ACREEDOR_INTERNO
							      	,null MONTO_RENDIDO_MONEDA 
							      	,null MONTO_RENDIDO_MONEDA_H
					      			,null MONTO_RENDIDO_PESOS
					      			,null MONTO_RENDIDO_PESOS_H
							FROM DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select  'S' SELECCION
									,null TIPO_DATO
									,null TIPO_DATO_H
									,null COD_RENDICION_INTERNO_ABONO
									,null COD_RENDICION_INTERNO   
									,null CODIGO
									,null CODIGO_H
									,null FECHA
									,null RUT
									,null DIG_VERIF
									,null NOM_DEUDOR
									,null COD_DEUDA
                                    ,null COD_DEUDA_H
									,null MONTO_MONEDA
									,null MONTO_MONEDA_H
									,null MONTO_PESOS
									,null MONTO_PESOS_H
									,null PORC
									,null PORC_H
									,null COD_UNIVERSIDAD_H
									,null NOM_UNIVERSIDAD 
							      	,null MONTO_RENDIDO_MONEDA 
							      	,null MONTO_RENDIDO_MONEDA_H
					      			,null MONTO_RENDIDO_PESOS
					      			,null MONTO_RENDIDO_PESOS_H";
				}
				break;
					
				/////////////////  ERROR ///////////////
			default:
				base::error("sql_rendicion_interno::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>