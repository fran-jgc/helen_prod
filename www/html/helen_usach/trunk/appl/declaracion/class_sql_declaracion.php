<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../appl.ini");

class sql_declaracion extends sql_tabla {
	const K_VIA_DECLARACION_DIGITADA	= 1;
	const K_EST_DECL_INGRESADA_WEB		= 0;
	const K_EST_DECL_INGRESADA			= 1;
	const K_EST_DECL_APROBADA			= 2;
	const K_EST_DECL_ANULADA			= 3;
	const K_MONEDA_UTM 					= 3;
	const K_ESTADO_DEUDA_EXIGIBLE		= 2;
	const K_COD_CARTERA_UACH			= 7;
	const K_PARAM_NOM_UNIVERSIDAD		= 5;
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
			///////////////// INPUT //////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	COD_DECLARACION
									, COD_DECLARACION COD_DECLARACION_HH
							     	, COD_ESTADO_DECLARACION
							     	, COD_ESTADO_DECLARACION COD_ESTADO_DECLARACION_NO_ING
							     	, DEUDOR.COD_DEUDOR
							     	, DEUDOR.RUT RUT
							     	, DEUDOR.RUT RUT_NO_ING
							     	, DEUDOR.DIG_VERIF
							     	, DEUDOR.DIG_VERIF DIG_VERIF_NO_ING
							     	, DEUDOR.NOMBRE || ' ' || DEUDOR.APELLIDO_PATERNO || ' ' || APELLIDO_MATERNO NOMBRE
							     	, DEUDOR.NOMBRE || ' ' || DEUDOR.APELLIDO_PATERNO || ' ' || APELLIDO_MATERNO NOMBRE_NO_ING
							     	, DECLARACION.COD_VIA_DECLARACION
							     	, TO_CHAR (FECHA_CONFIRMA, 'DD/MM/YYYY') FECHA_CONFIRMA
							     	, TO_CHAR (FECHA_CONFIRMA, 'DD/MM/YYYY') FECHA_CONFIRMA_NO_ING
							     	, COD_USUARIO_CONFIRMA COD_USUARIO_CONFIRMA_NO_ING
							     	, USUARIO_CONFIRMA.NOM_USUARIO NOM_USUARIO_CONFIRMA
							     	, USUARIO_CONFIRMA.NOM_USUARIO NOM_USUARIO_CONFIRMA_NO_ING
							     	, USUARIO.NOM_USUARIO
							     	, TO_CHAR (FECHA_DECLARACION, 'DD/MM/YYYY') FECHA_DECLARACION
							     	, ANO ANO_DECLARACION
							     	, ANO ANO_DECLARACION_NO_ING
							     	, CON_HIJO
							     	, COD_AFP
							     	, DECLARACION.COD_ESTADO_CIVIL COD_ESTADO_CIVIL
							     	, RUT_CONYUGE
							     	, DIG_VERIF_CONYUGE
							     	, NOM_CONYUGE
							     	, CONYUGE_ES_DEUDOR
							     	, CONYUGE_DECLARA
							     	, ENTREGA_F22
							     	, 'none' VISIBLE_TABLE
							     	, 'none' VISIBLE_TABLE_CARTERA
							     	, (ANO + 1) ANO_COBRO
							     	, (ANO + 1) ANO_COBRO_H
							     	, 'none' VISIBLE_BOTON
							     	, '' VISIBLE_TD
							     	, '' DISABLE_BUTTON_CREA_CUOTA
							     	, '' VISIBLE_TR_NO_APROBADA
							     	, 'none' VISIBLE_TR_APROBADA
							     	, 'SIN CUOTA' LABEL_CUOTA
							     	, 0 VALOR_CUOTA
							     	, '' TD_LABEL_CUOTA
							     	, 'none' TD_VALOR_CUOTA
							     	, f_get_constante('K_CHILE') K_CHILE_H
							     	, CERT_SUELDO 
									, CERT_HONORARIO
									, CERT_AFP
									, DECLARA_RENTA
									, CERT_NOTARIAL_ING
									, ANTEC_CONYUGE
									, CERT_ALUM_REGULAR
									,'' DISPLAY_ENVIO_CORREO
							 FROM 	DECLARACION
							     	, DEUDOR
							     	, USUARIO
							     	, USUARIO USUARIO_CONFIRMA
							 WHERE 	DECLARACION.COD_DEUDOR = DEUDOR.COD_DEUDOR
							   		AND DECLARACION.COD_USUARIO_CONFIRMA = USUARIO_CONFIRMA.COD_USUARIO(+)
							   		AND DECLARACION.COD_USUARIO = USUARIO.COD_USUARIO(+)
							   		AND DECLARACION.COD_DECLARACION = {KEY1}";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_DECLARACION
							     	, COD_ESTADO_DECLARACION
							     	, COD_ESTADO_DECLARACION COD_ESTADO_DECLARACION_NO_ING
							     	, DEUDOR.COD_DEUDOR
							     	, DEUDOR.RUT RUT
							     	, DEUDOR.RUT RUT_NO_ING
							     	, DEUDOR.DIG_VERIF
							     	, DEUDOR.DIG_VERIF DIG_VERIF_NO_ING
							     	, DEUDOR.NOMBRE + ' ' + DEUDOR.APELLIDO_PATERNO + ' ' + APELLIDO_MATERNO NOMBRE
							     	, DEUDOR.NOMBRE + ' ' + DEUDOR.APELLIDO_PATERNO + ' ' + APELLIDO_MATERNO NOMBRE_NO_ING
							     	, DECLARACION.COD_VIA_DECLARACION
							     	, convert(varchar(20),FECHA_CONFIRMA, 103) FECHA_CONFIRMA
							     	, convert(varchar(20),FECHA_CONFIRMA, 103) FECHA_CONFIRMA_NO_ING
							     	, COD_USUARIO_CONFIRMA COD_USUARIO_CONFIRMA_NO_ING
							     	, USUARIO_CONFIRMA.NOM_USUARIO NOM_USUARIO_CONFIRMA
							     	, USUARIO_CONFIRMA.NOM_USUARIO NOM_USUARIO_CONFIRMA_NO_ING
							     	, USUARIO.NOM_USUARIO
							     	, convert(varchar(20),FECHA_DECLARACION, 103) FECHA_DECLARACION
							     	, ANO ANO_DECLARACION
							     	, ANO ANO_DECLARACION_NO_ING
							     	, CON_HIJO
							     	, COD_AFP
							     	, DECLARACION.COD_ESTADO_CIVIL COD_ESTADO_CIVIL
							     	, RUT_CONYUGE
							     	, DIG_VERIF_CONYUGE
							     	, NOM_CONYUGE
							     	, CONYUGE_ES_DEUDOR
							     	, CONYUGE_DECLARA
							     	, ENTREGA_F22
							     	, 'none' VISIBLE_TABLE
							     	, 'none' VISIBLE_TABLE_CARTERA
							     	, (ANO + 1) ANO_COBRO
							     	, (ANO + 1) ANO_COBRO_H
							     	, 'none' VISIBLE_BOTON
							     	, '' VISIBLE_TD
							     	, '' DISABLE_BUTTON_CREA_CUOTA
							     	, 'SIN CUOTA' LABEL_CUOTA
							     	, 0 VALOR_CUOTA
							     	, '' TD_LABEL_CUOTA
							     	, 'none' TD_VALOR_CUOTA
							     	, dbo.f_get_constante('K_CHILE') K_CHILE_H
							     	, CERT_SUELDO 
									, CERT_HONORARIO
									, CERT_AFP
									, DECLARA_RENTA
									, CERT_NOTARIAL_ING
									, ANTEC_CONYUGE
									, CERT_ALUM_REGULAR 
							  FROM 	DECLARACION left outer join USUARIO on DECLARACION.COD_USUARIO = USUARIO.COD_USUARIO 
										left outer join USUARIO USUARIO_CONFIRMA on DECLARACION.COD_USUARIO_CONFIRMA = USUARIO_CONFIRMA.COD_USUARIO
							     	, DEUDOR
							 WHERE 	DECLARACION.COD_DEUDOR = DEUDOR.COD_DEUDOR
							   		AND DECLARACION.COD_DECLARACION = {KEY1}";
				}
				break;

				////////////////// OUTPUT ////////////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT   COD_DECLARACION
									   , NOM_ESTADO_DECLARACION
								       , ANO
								       , to_char(FECHA_DECLARACION,'dd/mm/yyyy') FECHA_DECLARACION
								       , DEU.RUT
								       , DEU.DIG_VERIF
								       , DEU.NOMBRE
								       , DEU.APELLIDO_PATERNO
								       , DEU.APELLIDO_MATERNO
								       , NOM_VIA_DECLARACION
								       , (select AVG(MONTO_DEUDOR_PESOS) from DECLARACION_MES DM where DM.COD_DECLARACION = DEC.COD_DECLARACION)INGRESO_PROM_PESOS
								       , f_docs_dj_sin_revisar(DEC.COD_DECLARACION) ARCHIVOS_SIN_REVISAR
								 FROM 	DECLARACION DEC
								       , DEUDOR DEU
								       , VIA_DECLARACION VD
								       , ESTADO_DECLARACION ED
								WHERE 	DEC.COD_DEUDOR = DEU.COD_DEUDOR
								    AND DEC.COD_VIA_DECLARACION = VD.COD_VIA_DECLARACION
								    AND DEC.COD_ESTADO_DECLARACION = ED.COD_ESTADO_DECLARACION
								ORDER BY COD_DECLARACION DESC";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT   	COD_DECLARACION
									   , NOM_ESTADO_DECLARACION
								       , ANO
							   		   , convert(varchar(20), FECHA_DECLARACION, 103) FECHA_DECLARACION
								       , DEU.RUT
								       , DEU.DIG_VERIF
								       , DEU.NOMBRE
								       , DEU.APELLIDO_PATERNO
								       , DEU.APELLIDO_MATERNO
								       , NOM_VIA_DECLARACION
								       , (select AVG(MONTO_DEUDOR_PESOS) from DECLARACION_MES DM where DM.COD_DECLARACION = DEC.COD_DECLARACION)INGRESO_PROM_PESOS
								 FROM 	DECLARACION DEC
								       , DEUDOR DEU
								       , VIA_DECLARACION VD
								       , ESTADO_DECLARACION ED
								 WHERE 	DEC.COD_DEUDOR = DEU.COD_DEUDOR
								   AND 	DEC.COD_VIA_DECLARACION = VD.COD_VIA_DECLARACION
								   AND 	DEC.COD_ESTADO_DECLARACION = ED.COD_ESTADO_DECLARACION
								ORDER BY COD_DECLARACION DESC";
				}
				break;

				/////////////////// DD_COD_TIPO_DEUDA ////////////////////
			case 'DD_COD_TIPO_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT  TD.COD_TIPO_DEUDA
									,TD.COD_TIPO_DEUDA COD_TIPO_DEUDA_H
							FROM    DEUDA D
									, DEUDOR DEUDOR
									, TIPO_DEUDA TD
									, MONEDA M       
							WHERE   DEUDOR.RUT = {KEY1}
									AND TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA
									AND M.COD_MONEDA = TD.COD_MONEDA
									AND DEUDOR.COD_DEUDOR = D.COD_DEUDOR";
				}
				break;
				
					/////////////////// OBTIENE VALOR DE CUOTA  ////////////////////
			case 'VALOR_CUOTA' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	NVL(PP.MONTO_CAPITAL + PP.MONTO_INTERES  - f_pp_monto_dscto(COD_PLAN_PAGO, 'MONEDA', sysdate), 0)  VALOR_CUOTA
							FROM	DECLARACION DE, DEUDA DA, PLAN_PAGO PP
							WHERE	DE.COD_DECLARACION = {PARAM1}
							  AND	DE.COD_DEUDOR = DA.COD_DEUDOR
							  AND	DA.COD_TIPO_DEUDA IN ('CS','CSR','CR12')
							  AND	DA.COD_ESTADO_DEUDA = 2
							  AND	DA.COD_DEUDA = PP.COD_DEUDA 
							  AND	PP.ANO_COBRO = DE.ANO + 1";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	ISNULL(PP.MONTO_CAPITAL + PP.MONTO_INTERES - dbo.f_pp_monto_dscto(COD_PLAN_PAGO, 'MONEDA', getdate()) , 0)  VALOR_CUOTA
							FROM	DECLARACION DE, DEUDA DA, PLAN_PAGO PP
							WHERE	DE.COD_DECLARACION = {PARAM1}
							  AND	DE.COD_DEUDOR = DA.COD_DEUDOR
							  AND	DA.COD_TIPO_DEUDA IN ('CS','CSR','CR12')
							  AND	DA.COD_ESTADO_DEUDA = 2
							  AND	DA.COD_DEUDA = PP.COD_DEUDA 
							  AND	PP.ANO_COBRO = DE.ANO + 1";
				}
				break;

				///////////////////	DW_DECLARACION_MES ///////////////////////
			case 'DW_DECLARACION_MES' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	D1.COD_DECLARACION
									,D1.COD_DECLARACION_MES COD_DECLARACION_MES1  
									,D1.MES MES1
									,f_get_nom_mes(D1.MES) NOM_MES1
									,D1.MONTO_DEUDOR_MONEDA MONTO_DEUDOR_MONEDA1
						       	 	,D1.MONTO_DEUDOR_PESOS MONTO_DEUDOR_PESOS1
						        	,D1.MONTO_CONYUGE_MONEDA MONTO_CONYUGE_MONEDA1
						        	,D1.MONTO_CONYUGE_PESOS MONTO_CONYUGE_PESOS1
						        	,f_mon_get_valor_moneda(".self::K_MONEDA_UTM.", to_date('01/' ||to_char(D1.MES, '00') || '/' || to_char(D.ANO, '0000'),'dd/mm/yyyy')) VALOR_UTM1
						        	,D2.COD_DECLARACION_MES COD_DECLARACION_MES2
						        	,D2.MES MES2
									,f_get_nom_mes(D2.MES) NOM_MES2
						        	,D2.MONTO_DEUDOR_MONEDA MONTO_DEUDOR_MONEDA2
						        	,D2.MONTO_DEUDOR_PESOS MONTO_DEUDOR_PESOS2
						        	,D2.MONTO_CONYUGE_MONEDA MONTO_CONYUGE_MONEDA2
						        	,D2.MONTO_CONYUGE_PESOS MONTO_CONYUGE_PESOS2
						        	,f_mon_get_valor_moneda(".self::K_MONEDA_UTM.", to_date('01/' ||to_char(D2.MES, '00') || '/' || to_char(D.ANO, '0000'),'dd/mm/yyyy')) VALOR_UTM2
						        	,D1.MONTO_DEUDOR_PESOS + D2.MONTO_DEUDOR_PESOS MONTO_DEUDOR_PESOS 
						        	,D1.MONTO_CONYUGE_PESOS + D2.MONTO_CONYUGE_PESOS MONTO_CONYUGE_PESOS 
						        	,D1.MONTO_DEUDOR_MONEDA + D2.MONTO_DEUDOR_MONEDA MONTO_DEUDOR_MONEDA 
						        	,D1.MONTO_CONYUGE_MONEDA + D2.MONTO_CONYUGE_MONEDA MONTO_CONYUGE_MONEDA 
							FROM	DECLARACION_MES D1, DECLARACION_MES D2, DECLARACION D
							WHERE 	D1.COD_DECLARACION = {KEY1}
							  		and D1.MES <=6
							  		and D2.COD_DECLARACION = D1.COD_DECLARACION
							  		and D2.MES > 6
							  		and D1.MES + 6 = D2.MES
							  		and D.COD_DECLARACION = D1.COD_DECLARACION
							  		ORDER BY d1.mes";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 	D1.COD_DECLARACION
									,D1.COD_DECLARACION_MES COD_DECLARACION_MES1  
									,D1.MES MES1
									,dbo.f_get_nom_mes(D1.MES) NOM_MES1
									,D1.MONTO_DEUDOR_MONEDA MONTO_DEUDOR_MONEDA1
						       	 	,D1.MONTO_DEUDOR_PESOS MONTO_DEUDOR_PESOS1
						        	,D1.MONTO_CONYUGE_MONEDA MONTO_CONYUGE_MONEDA1
						        	,D1.MONTO_CONYUGE_PESOS MONTO_CONYUGE_PESOS1
						        	,dbo.f_mon_get_valor_moneda(".self::K_MONEDA_UTM.", dbo.to_date(convert(varchar(3), '01/') + convert(varchar(2), D1.MES)+'/'+ convert(varchar(4),D.ANO))) VALOR_UTM1
									,D2.COD_DECLARACION_MES COD_DECLARACION_MES2
						        	,D2.MES MES2
									,dbo.f_get_nom_mes(D2.MES) NOM_MES2
						        	,D2.MONTO_DEUDOR_MONEDA MONTO_DEUDOR_MONEDA2
						        	,D2.MONTO_DEUDOR_PESOS MONTO_DEUDOR_PESOS2
						        	,D2.MONTO_CONYUGE_MONEDA MONTO_CONYUGE_MONEDA2
						        	,D2.MONTO_CONYUGE_PESOS MONTO_CONYUGE_PESOS2
						        	,dbo.f_mon_get_valor_moneda(".self::K_MONEDA_UTM.", dbo.to_date(convert(varchar(3),'01/') + convert(varchar(2),D2.MES) + '/' + convert(varchar(4),D.ANO))) VALOR_UTM2
						        	,D1.MONTO_DEUDOR_PESOS + D2.MONTO_DEUDOR_PESOS MONTO_DEUDOR_PESOS 
						        	,D1.MONTO_CONYUGE_PESOS + D2.MONTO_CONYUGE_PESOS MONTO_CONYUGE_PESOS 
						        	,D1.MONTO_DEUDOR_MONEDA + D2.MONTO_DEUDOR_MONEDA MONTO_DEUDOR_MONEDA 
						        	,D1.MONTO_CONYUGE_MONEDA + D2.MONTO_CONYUGE_MONEDA MONTO_CONYUGE_MONEDA 
							FROM	DECLARACION_MES D1, DECLARACION_MES D2, DECLARACION D
							WHERE 	D1.COD_DECLARACION = {KEY1}
							  		and D1.MES <=6
							  		and D2.COD_DECLARACION = D1.COD_DECLARACION
							  		and D2.MES > 6
							  		and D1.MES + 6 = D2.MES
							  		and D.COD_DECLARACION = D1.COD_DECLARACION
							  		ORDER BY d1.mes";
				}
				break;

				///////////////////// DD_COD_ESTADO_DECLARACION ////////////////////
			case 'DD_COD_ESTADO_DECLARACION' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_ESTADO_DECLARACION
				        	 ,NOM_ESTADO_DECLARACION
					FROM 	 ESTADO_DECLARACION
					ORDER BY NOM_ESTADO_DECLARACION";
				}
				break;

				////////////////////// DD_COD_VIA_DECLARACION /////////////////////
			case 'DD_COD_VIA_DECLARACION' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT COD_VIA_DECLARACION
							, NOM_VIA_DECLARACION 
					FROM 	VIA_DECLARACION";
				}
				break;

				//////////////////// DD_COD_AFP //////////////////////
			case 'DD_COD_AFP' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_AFP
						        	,NOM_AFP
							FROM 	AFP
							ORDER BY NOM_AFP";
				}
				break;

				/////////////////// DD_COD_ESTADO_CIVIL /////////////////
			case 'DD_COD_ESTADO_CIVIL' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_ESTADO_CIVIL
						        	, NOM_ESTADO_CIVIL
							FROM 	 ESTADO_CIVIL
							ORDER BY NOM_ESTADO_CIVIL";
				}
				break;

				////////////////// DD_COD_ESTADO_DECLARACION_1 ////////////////
			case 'DD_COD_ESTADO_DECLARACION_1' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_ESTADO_DECLARACION
									,NOM_ESTADO_DECLARACION
							FROM 	 ESTADO_DECLARACION
							WHERE COD_ESTADO_DECLARACION <> ".self::K_EST_DECL_INGRESADA_WEB."
							ORDER BY COD_ESTADO_DECLARACION";
				}
				break;

				//////////////// DD_COD_ESTADO_DECLARACION_2 ///////////////
			case 'DD_COD_ESTADO_DECLARACION_2' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_ESTADO_DECLARACION
									, NOM_ESTADO_DECLARACION
							FROM 	ESTADO_DECLARACION
							WHERE COD_ESTADO_DECLARACION not in (".self::K_EST_DECL_INGRESADA.", ".self::K_EST_DECL_INGRESADA_WEB.")";
				}
				break;

				///////////////// DD_COD_VIA_DECLARACION ////////////////
			case 'DD_COD_VIA_DECLARACION' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_ESTADO_DECLARACION
								 	,NOM_ESTADO_DECLARACION 
							FROM 	 ESTADO_DECLARACION 
							ORDER BY COD_ESTADO_DECLARACION";
				}
				break;
					
				//////////////// DD_COD_VIA_DECLARACION_1 /////////////
			case 'DD_COD_VIA_DECLARACION_1' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_VIA_DECLARACION
									 ,NOM_VIA_DECLARACION 
							FROM 	 VIA_DECLARACION 
							ORDER BY COD_VIA_DECLARACION";
				}
				break;

				///////////////	AJAX_DECLARACION_DEUDOR ///////////////
			case 'AJAX_DECLARACION_DEUDOR' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	count(*) CUANTO
        					FROM    DEUDOR
                					,DECLARACION
       						WHERE   RUT = {PARAM1}
	                				AND deudor.cod_deudor = declaracion.cod_deudor
        		        			AND declaracion.ANO = {PARAM4}
                					AND declaracion.COD_ESTADO_DECLARACION IN ({PARAM2}, {PARAM3})";
				}
				break;

			//////////////// AJAX_DEUDA_EXIGIBLE ////////////////
			case 'AJAX_DEUDA_EXIGIBLE' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT  Count(*) CUANTO1
							FROM    DEUDA DA, DEUDOR DR
							WHERE   DA.COD_DEUDOR = DR.COD_DEUDOR
							AND     COD_ESTADO_DEUDA = ".self::K_ESTADO_DEUDA_EXIGIBLE."
							AND     RUT = {PARAM1}";
				}
				break;
				
				//////////////// AJAX_CUANTO ////////////////
			case 'AJAX_CUANTO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	count(*) CUANTO
							FROM 	DEUDOR
							WHERE	RUT = {PARAM1}";
				}
				break;
			
				///////////////////	AJAX_CUANTO_1 ////////////////
			case 'AJAX_CUANTO_1' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	DEUDOR.COD_DEUDOR
									,DIG_VERIF
							     	, NOMBRE || ' ' || APELLIDO_PATERNO || ' ' || APELLIDO_MATERNO NOMBRE
								    , DIRECCION_P.DIRECCION DIRECCION_PARTICULAR
								    , TO_CHAR (DIRECCION_P.FECHA_DIRECCION, 'DD/MM/YYYY') FECHA_DIRECCION_PARTICULAR
								    , DIRECCION_P.COD_COMUNA COD_COMUNA_PARTICULAR
								    , DIRECCION_P.COD_CIUDAD COD_CIUDAD_PARTICULAR
								    , nvl(DIRECCION_P.COD_PAIS, f_get_constante('{PARAM2}')) COD_PAIS_PARTICULAR
								    , DIRECCION_P.EMAIL EMAIL_PARTICULAR
								    , DIRECCION_P.TELEFONO TELEFONO_PARTICULAR
								    , DIRECCION_P.CELULAR CELULAR_PARTICULAR
								    , DIRECCION_C.DIRECCION DIRECCION_COMERCIAL
								    , TO_CHAR (DIRECCION_C.FECHA_DIRECCION, 'DD/MM/YYYY') FECHA_DIRECCION_COMERCIAL
								    , DIRECCION_C.COD_COMUNA COD_COMUNA_COMERCIAL
								    , DIRECCION_C.COD_CIUDAD COD_CIUDAD_COMERCIAL
								    , nvl(DIRECCION_C.COD_PAIS, f_get_constante('{PARAM2}')) COD_PAIS_COMERCIAL
								    , DIRECCION_C.EMAIL EMAIL_COMERCIAL
								    , DIRECCION_C.TELEFONO TELEFONO_COMERCIAL
								    , DIRECCION_C.CELULAR CELULAR_COMERCIAL
								    , f_get_region_deudor (DEUDOR.COD_DEUDOR, 'P') COD_REGION_PARTICULAR
								    , f_get_region_deudor (DEUDOR.COD_DEUDOR, 'C') COD_REGION_COMERCIAL
							  FROM 	DEUDOR
							     	, DIRECCION DIRECCION_P
							     	, DIRECCION DIRECCION_C
							WHERE 	RUT ={PARAM1}
	   						AND 	DIRECCION_P.COD_DIRECCION(+) = F_GET_DIRECCION_DEUDOR (DEUDOR.COD_DEUDOR, 'P')
	   						AND 	DIRECCION_C.COD_DIRECCION(+) = F_GET_DIRECCION_DEUDOR (DEUDOR.COD_DEUDOR, 'C')";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	DEUDOR.COD_DEUDOR
									,DIG_VERIF
							     	, NOMBRE + ' ' + APELLIDO_PATERNO + ' ' + APELLIDO_MATERNO NOMBRE
								    , DIRECCION_P.DIRECCION DIRECCION_PARTICULAR
								    , convert(varchar(20),DIRECCION_P.FECHA_DIRECCION, 103) FECHA_DIRECCION_PARTICULAR
								    , DIRECCION_P.COD_COMUNA COD_COMUNA_PARTICULAR
								    , DIRECCION_P.COD_CIUDAD COD_CIUDAD_PARTICULAR
								    , isnull(DIRECCION_P.COD_PAIS, dbo.f_get_constante('{PARAM2}')) COD_PAIS_PARTICULAR
								    , DIRECCION_P.EMAIL EMAIL_PARTICULAR
								    , DIRECCION_P.TELEFONO TELEFONO_PARTICULAR
								    , DIRECCION_P.CELULAR CELULAR_PARTICULAR
								    , DIRECCION_C.DIRECCION DIRECCION_COMERCIAL
								    , convert(varchar(20),DIRECCION_C.FECHA_DIRECCION, 103) FECHA_DIRECCION_COMERCIAL
								    , DIRECCION_C.COD_COMUNA COD_COMUNA_COMERCIAL
								    , DIRECCION_C.COD_CIUDAD COD_CIUDAD_COMERCIAL
								    , isnull(DIRECCION_C.COD_PAIS, dbo.f_get_constante('{PARAM2}')) COD_PAIS_COMERCIAL
								    , DIRECCION_C.EMAIL EMAIL_COMERCIAL
								    , DIRECCION_C.TELEFONO TELEFONO_COMERCIAL
								    , DIRECCION_C.CELULAR CELULAR_COMERCIAL
								    , dbo.f_get_region_deudor (DEUDOR.COD_DEUDOR, 'P') COD_REGION_PARTICULAR
								    , dbo.f_get_region_deudor (DEUDOR.COD_DEUDOR, 'C') COD_REGION_COMERCIAL
							  FROM 	DEUDOR left outer join DIRECCION DIRECCION_P on dbo.F_GET_DIRECCION_DEUDOR(DEUDOR.COD_DEUDOR, 'P') = DIRECCION_P.COD_DIRECCION
										left outer join DIRECCION DIRECCION_C on dbo.F_GET_DIRECCION_DEUDOR(DEUDOR.COD_DEUDOR, 'C') = DIRECCION_C.COD_DIRECCION
							WHERE 	RUT ={PARAM1}";
				}
				break;
				////////////////////////		DFD		/////////////
			case 'VALOR_MONEDA' :
				if (K_TIPO_BD=="oci" ){
					$sql = "SELECT 	f_mon_get_valor_moneda(".self::K_MONEDA_UTM.", to_date('01/{PARAM1}/{PARAM2}','dd/mm/yyyy')) VALOR_MONEDA
							FROM 	DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select dbo.f_mon_get_valor_moneda(".self::K_MONEDA_UTM.",dbo.f_makedate('01', '{PARAM1}','{PARAM2}')) VALOR_MONEDA";
				}
				break;
				
				//////////////// PRINT_RECORD ////////////////
				case 'PRINT_RECORD' :
					if (K_TIPO_BD=="oci"){
						$sql = "SELECT 	DR.RUT
										,DR.DIG_VERIF
										,DR.NOMBRE
										,DR.APELLIDO_PATERNO
										,DR.APELLIDO_MATERNO
										,D.COD_DECLARACION
										,D.FECHA_REGISTRO
										,U1.NOM_USUARIO
										,D.COD_DEUDOR
										,VD.NOM_VIA_DECLARACION
										,TO_CHAR(D.FECHA_CONFIRMA, 'dd/mm/yyyy') FECHA_CONFIRMA 
										,D.COD_ESTADO_DECLARACION
										,ED.NOM_ESTADO_DECLARACION
										,U2.NOM_USUARIO NOM_USUARIO_CONFIRMA
										,TO_CHAR(D.FECHA_DECLARACION, 'dd/mm/yyyy') FECHA_DECLARACION
										,D.ANO
										,D.ANO + 1 ANO_DECLARACION
										,D.CON_HIJO
										,D.COD_AFP
										,A.NOM_AFP
										,EC.NOM_ESTADO_CIVIL
										,D.COD_ESTADO_CIVIL
										,D.RUT_CONYUGE
										,D.DIG_VERIF_CONYUGE
										,D.NOM_CONYUGE
										,D.CONYUGE_ES_DEUDOR
										,D.CONYUGE_DECLARA
										,D.ENTREGA_F22
										,f_get_parametro(".self::K_PARAM_NOM_UNIVERSIDAD.") NOM_UNIVERSIDAD
										,to_char(FECHA_DECLARACION,'DD/MM/YYYY') FECHA_DECLARACION
								FROM 	DECLARACION D LEFT OUTER JOIN USUARIO U1 ON D.COD_USUARIO = U1.COD_USUARIO
											LEFT OUTER JOIN USUARIO U2 ON D.COD_USUARIO_CONFIRMA = U2.COD_USUARIO,
										DEUDOR DR, VIA_DECLARACION VD, ESTADO_DECLARACION ED, AFP A, ESTADO_CIVIL EC
								WHERE 	D.COD_DECLARACION = {PARAM1}
								AND 	D.COD_DEUDOR = DR.COD_DEUDOR
								AND 	D.COD_VIA_DECLARACION = VD.COD_VIA_DECLARACION
								AND 	D.COD_ESTADO_DECLARACION = ED.COD_ESTADO_DECLARACION
								AND 	D.COD_AFP = A.COD_AFP
								AND 	D.COD_ESTADO_CIVIL = EC.COD_ESTADO_CIVIL";
					}else if (K_TIPO_BD=="mssql"){
						$sql = "SELECT 	DR.RUT
										,DR.DIG_VERIF
										,DR.NOMBRE
										,DR.APELLIDO_PATERNO
										,DR.APELLIDO_MATERNO
										,D.COD_DECLARACION
										,D.FECHA_REGISTRO
										,U1.NOM_USUARIO
										,D.COD_DEUDOR
										,VD.NOM_VIA_DECLARACION
										,convert(varchar(10), D.FECHA_CONFIRMA, 103) FECHA_CONFIRMA 
										,D.COD_ESTADO_DECLARACION
										,ED.NOM_ESTADO_DECLARACION
										,U2.NOM_USUARIO NOM_USUARIO_CONFIRMA
										,convert(varchar(10), D.FECHA_DECLARACION, 103) FECHA_DECLARACION
										,D.ANO
										,D.ANO + 1 ANO_DECLARACION
										,D.CON_HIJO
										,D.COD_AFP
										,A.NOM_AFP
										,EC.NOM_ESTADO_CIVIL
										,D.COD_ESTADO_CIVIL
										,D.RUT_CONYUGE
										,D.DIG_VERIF_CONYUGE
										,D.NOM_CONYUGE
										,D.CONYUGE_ES_DEUDOR
										,D.CONYUGE_DECLARA
										,D.ENTREGA_F22
										,dbo.f_get_parametro(".self::K_PARAM_NOM_UNIVERSIDAD.") NOM_UNIVERSIDAD
										,convert(varchar(20), FECHA_DECLARACION, 103) FECHA_DECLARACION
								FROM 	DECLARACION D LEFT OUTER JOIN USUARIO U1 ON D.COD_USUARIO = U1.COD_USUARIO
											LEFT OUTER JOIN USUARIO U2 ON D.COD_USUARIO_CONFIRMA = U2.COD_USUARIO,
										DEUDOR DR, VIA_DECLARACION VD, ESTADO_DECLARACION ED, AFP A, ESTADO_CIVIL EC
								WHERE 	D.COD_DECLARACION = {PARAM1}
								AND 	D.COD_DEUDOR = DR.COD_DEUDOR
								AND 	D.COD_VIA_DECLARACION = VD.COD_VIA_DECLARACION
								AND 	D.COD_ESTADO_DECLARACION = ED.COD_ESTADO_DECLARACION
								AND 	D.COD_AFP = A.COD_AFP
								AND 	D.COD_ESTADO_CIVIL = EC.COD_ESTADO_CIVIL";	
					}
					break;
					
					//////////////// PRINT_DIRECCION_PARTICULAR ////////////////
					case 'PRINT_DIRECCION_PARTICULAR' :
						if (K_TIPO_BD=="oci"){
							$sql = "SELECT  DI.DIRECCION DIRECCION_P
											,To_Char(DI.FECHA_DIRECCION, 'dd/mm/yyyy') FECHA_DIRECCION_P 
											,PA.NOM_PAIS NOM_PAIS_P
											,RE.NOM_REGION NOM_REGION_P
											,CI.NOM_CIUDAD NOM_CIUDAD_P
											,CO.NOM_COMUNA NOM_COMUNA_P
											,DI.EMAIL EMAIL_P
											,DI.TELEFONO TELEFONO_P
											,DI.CELULAR CELULAR_P  
									FROM 	DECLARACION DE, DIRECCION DI LEFT OUTER JOIN COMUNA CO ON DI.COD_COMUNA = CO.COD_COMUNA
                                                    LEFT OUTER JOIN CIUDAD CI LEFT OUTER JOIN REGION RE ON CI.COD_REGION = RE.COD_REGION ON DI.COD_CIUDAD = CI.COD_CIUDAD 
                                                    LEFT OUTER JOIN PAIS PA ON DI.COD_PAIS = PA.COD_PAIS
									WHERE 	DE.COD_DECLARACION = {PARAM1}
					                AND 	DI.COD_DIRECCION = F_GET_DIRECCION_DEUDOR (DE.COD_DEUDOR, 'P')";
							
						}else if (K_TIPO_BD=="mssql"){
							$sql = "SELECT  DI.DIRECCION DIRECCION_P
											,convert(varchar(10),DI.FECHA_DIRECCION, 103) FECHA_DIRECCION_P 
											,PA.NOM_PAIS NOM_PAIS_P
											,RE.NOM_REGION NOM_REGION_P
											,CI.NOM_CIUDAD NOM_CIUDAD_P
											,CO.NOM_COMUNA NOM_COMUNA_P
											,DI.EMAIL EMAIL_P
											,DI.TELEFONO TELEFONO_P
											,DI.CELULAR CELULAR_P  
									FROM 	DECLARACION DE, DIRECCION DI LEFT OUTER JOIN COMUNA CO ON DI.COD_COMUNA = CO.COD_COMUNA
                                                    LEFT OUTER JOIN CIUDAD CI LEFT OUTER JOIN REGION RE ON CI.COD_REGION = RE.COD_REGION ON DI.COD_CIUDAD = CI.COD_CIUDAD 
                                                    LEFT OUTER JOIN PAIS PA ON DI.COD_PAIS = PA.COD_PAIS
									WHERE 	DE.COD_DECLARACION = {PARAM1}
					                AND 	DI.COD_DIRECCION = dbo.F_GET_DIRECCION_DEUDOR (DE.COD_DEUDOR, 'P')";	
						}
						break;
						
					//////////////// PRINT_DIRECCION_COMERCIAL ////////////////
					case 'PRINT_DIRECCION_COMERCIAL' :
						if (K_TIPO_BD=="oci"){
							$sql = "SELECT 	DI.DIRECCION DIRECCION_C
											,To_Char(DI.FECHA_DIRECCION, 'dd/mm/yyyy') FECHA_DIRECCION_C
											,PA.NOM_PAIS NOM_PAIS_C
											,RE.NOM_REGION NOM_REGION_C
											,CI.NOM_CIUDAD NOM_CIUDAD_C
											,CO.NOM_COMUNA NOM_COMUNA_C
											,DI.EMAIL EMAIL_C
											,DI.TELEFONO TELEFONO_C
											,DI.CELULAR CELULAR_C
									FROM 	DECLARACION DE, DIRECCION DI LEFT OUTER JOIN COMUNA CO ON DI.COD_COMUNA = CO.COD_COMUNA
                                                    LEFT OUTER JOIN CIUDAD CI LEFT OUTER JOIN REGION RE ON CI.COD_REGION = RE.COD_REGION ON DI.COD_CIUDAD = CI.COD_CIUDAD 
                                                    LEFT OUTER JOIN PAIS PA ON DI.COD_PAIS = PA.COD_PAIS
									WHERE 	DE.COD_DECLARACION = {PARAM1}
					                AND 	DI.COD_DIRECCION = F_GET_DIRECCION_DEUDOR (DE.COD_DEUDOR, 'C')";
						}else if (K_TIPO_BD=="mssql"){
							$sql = "SELECT 	DI.DIRECCION DIRECCION_C
											,convert(varchar(10), DI.FECHA_DIRECCION, 103) FECHA_DIRECCION_C 
											,PA.NOM_PAIS NOM_PAIS_C
											,RE.NOM_REGION NOM_REGION_C
											,CI.NOM_CIUDAD NOM_CIUDAD_C
											,CO.NOM_COMUNA NOM_COMUNA_C
											,DI.EMAIL EMAIL_C
											,DI.TELEFONO TELEFONO_C
											,DI.CELULAR CELULAR_C
									FROM 	DECLARACION DE, DIRECCION DI LEFT OUTER JOIN COMUNA CO ON DI.COD_COMUNA = CO.COD_COMUNA
                                                    LEFT OUTER JOIN CIUDAD CI LEFT OUTER JOIN REGION RE ON CI.COD_REGION = RE.COD_REGION ON DI.COD_CIUDAD = CI.COD_CIUDAD 
                                                    LEFT OUTER JOIN PAIS PA ON DI.COD_PAIS = PA.COD_PAIS
									WHERE 	DE.COD_DECLARACION = {PARAM1}
					                AND 	DI.COD_DIRECCION = dbo.F_GET_DIRECCION_DEUDOR (DE.COD_DEUDOR, 'C')";	
						}
						break;
					//////////////// PRINT_DECLARACION_DEUDOR ////////////////
					case 'PRINT_DECLARACION_DEUDOR' :
						if (K_TIPO_BD=="oci"){
							$sql = "SELECT 	F_GET_NOM_MES(DM.MES) MES
											,DM.MONTO_DEUDOR_PESOS
											,DM.MONTO_DEUDOR_MONEDA
										    ,CASE MES 
										    	WHEN 1 THEN f_mon_get_valor_moneda(3, To_Date(To_Char('01/01/')||To_Char(DE.ANO),'dd/mm/yyyy'))
										      	WHEN 2 THEN f_mon_get_valor_moneda(3, To_Date(To_Char('01/02/')||To_Char(DE.ANO),'dd/mm/yyyy'))
										      	WHEN 3 THEN f_mon_get_valor_moneda(3, To_Date(To_Char('01/03/')||To_Char(DE.ANO),'dd/mm/yyyy'))
										      	WHEN 4 THEN f_mon_get_valor_moneda(3, To_Date(To_Char('01/04/')||To_Char(DE.ANO),'dd/mm/yyyy'))
										      	WHEN 5 THEN f_mon_get_valor_moneda(3, To_Date(To_Char('01/05/')||To_Char(DE.ANO),'dd/mm/yyyy'))
										      	WHEN 6 THEN f_mon_get_valor_moneda(3, To_Date(To_Char('01/06/')||To_Char(DE.ANO),'dd/mm/yyyy'))
										      	WHEN 7 THEN f_mon_get_valor_moneda(3, To_Date(To_Char('01/07/')||To_Char(DE.ANO),'dd/mm/yyyy'))
										      	WHEN 8 THEN f_mon_get_valor_moneda(3, To_Date(To_Char('01/08/')||To_Char(DE.ANO),'dd/mm/yyyy'))
										      	WHEN 9 THEN f_mon_get_valor_moneda(3, To_Date(To_Char('01/09/')||To_Char(DE.ANO),'dd/mm/yyyy'))
										      	WHEN 10 THEN f_mon_get_valor_moneda(3, To_Date(To_Char('01/10/')||To_Char(DE.ANO),'dd/mm/yyyy'))       
										      	WHEN 11 THEN f_mon_get_valor_moneda(3, To_Date(To_Char('01/11/')||To_Char(DE.ANO),'dd/mm/yyyy'))
										      	WHEN 12 THEN f_mon_get_valor_moneda(3, To_Date(To_Char('01/12/')||To_Char(DE.ANO),'dd/mm/yyyy'))
										    END VALOR_UTM_DEUDOR   
									FROM 	DECLARACION DE, DECLARACION_MES DM
									WHERE 	DE.COD_DECLARACION = DM.COD_DECLARACION
											AND DE.COD_DECLARACION = {PARAM1}
									ORDER BY DM.MES";
							
						}else if (K_TIPO_BD=="mssql"){
							$sql = "SELECT 	DBO.F_GET_NOM_MES(DM.MES) MES
											,DM.MONTO_DEUDOR_PESOS
											,DM.MONTO_DEUDOR_MONEDA
											,case mes
												when 1 then dbo.f_mon_get_valor_moneda(3, dbo.to_date('01/01/'+convert(varchar(4), DE.ANO)))
												when 2 then dbo.f_mon_get_valor_moneda(3, dbo.to_date('01/02/'+convert(varchar(4), DE.ANO)))
												when 3 then dbo.f_mon_get_valor_moneda(3, dbo.to_date('01/03/'+convert(varchar(4), DE.ANO)))
												when 4 then dbo.f_mon_get_valor_moneda(3, dbo.to_date('01/04/'+convert(varchar(4), DE.ANO)))
												when 5 then dbo.f_mon_get_valor_moneda(3, dbo.to_date('01/05/'+convert(varchar(4), DE.ANO)))
												when 6 then dbo.f_mon_get_valor_moneda(3, dbo.to_date('01/06/'+convert(varchar(4), DE.ANO)))
												when 7 then dbo.f_mon_get_valor_moneda(3, dbo.to_date('01/07/'+convert(varchar(4), DE.ANO)))
												when 8 then dbo.f_mon_get_valor_moneda(3, dbo.to_date('01/08/'+convert(varchar(4), DE.ANO)))
												when 9 then dbo.f_mon_get_valor_moneda(3, dbo.to_date('01/09/'+convert(varchar(4), DE.ANO)))
												when 10 then dbo.f_mon_get_valor_moneda(3, dbo.to_date('01/10/'+convert(varchar(4), DE.ANO)))
												when 11 then dbo.f_mon_get_valor_moneda(3, dbo.to_date('01/11/'+convert(varchar(4), DE.ANO)))
												when 12 then dbo.f_mon_get_valor_moneda(3, dbo.to_date('01/12/'+convert(varchar(4), DE.ANO)))
											end VALOR_UTM_DEUDOR
									FROM 	DECLARACION DE, DECLARACION_MES DM
									WHERE 	DE.COD_DECLARACION = DM.COD_DECLARACION
											AND DE.COD_DECLARACION = {PARAM1}
									ORDER BY DM.MES";	
						}
						break;
					//////////////// PRINT_DECLARACION_CONYUGE ////////////////
					case 'PRINT_DECLARACION_CONYUGE' :
						if (K_TIPO_BD=="oci"){
							$sql = "SELECT 	F_GET_NOM_MES(DM.MES) MES
											,DM.MONTO_CONYUGE_PESOS
											,DM.MONTO_CONYUGE_MONEDA
										    ,CASE MES 
										    	WHEN 1 THEN f_mon_get_valor_moneda(3, To_Date(To_Char('01/01/')||To_Char(DE.ANO),'dd/mm/yyyy'))
										      	WHEN 2 THEN f_mon_get_valor_moneda(3, To_Date(To_Char('01/02/')||To_Char(DE.ANO),'dd/mm/yyyy'))
										      	WHEN 3 THEN f_mon_get_valor_moneda(3, To_Date(To_Char('01/03/')||To_Char(DE.ANO),'dd/mm/yyyy'))
										      	WHEN 4 THEN f_mon_get_valor_moneda(3, To_Date(To_Char('01/04/')||To_Char(DE.ANO),'dd/mm/yyyy'))
										      	WHEN 5 THEN f_mon_get_valor_moneda(3, To_Date(To_Char('01/05/')||To_Char(DE.ANO),'dd/mm/yyyy'))
										      	WHEN 6 THEN f_mon_get_valor_moneda(3, To_Date(To_Char('01/06/')||To_Char(DE.ANO),'dd/mm/yyyy'))
										      	WHEN 7 THEN f_mon_get_valor_moneda(3, To_Date(To_Char('01/07/')||To_Char(DE.ANO),'dd/mm/yyyy'))
										      	WHEN 8 THEN f_mon_get_valor_moneda(3, To_Date(To_Char('01/08/')||To_Char(DE.ANO),'dd/mm/yyyy'))
										      	WHEN 9 THEN f_mon_get_valor_moneda(3, To_Date(To_Char('01/09/')||To_Char(DE.ANO),'dd/mm/yyyy'))
										      	WHEN 10 THEN f_mon_get_valor_moneda(3, To_Date(To_Char('01/10/')||To_Char(DE.ANO),'dd/mm/yyyy'))       
										      	WHEN 11 THEN f_mon_get_valor_moneda(3, To_Date(To_Char('01/11/')||To_Char(DE.ANO),'dd/mm/yyyy'))
										      	WHEN 12 THEN f_mon_get_valor_moneda(3, To_Date(To_Char('01/12/')||To_Char(DE.ANO),'dd/mm/yyyy'))
										    END VALOR_UTM_CONYUGE
									FROM 	DECLARACION DE, DECLARACION_MES DM
									WHERE 	DE.COD_DECLARACION = DM.COD_DECLARACION
									AND 	DE.COD_DECLARACION = {PARAM1}
									ORDER BY DM.MES";
							
						}else if (K_TIPO_BD=="mssql"){
							$sql = "SELECT 	DBO.F_GET_NOM_MES(DM.MES) MES
											,DM.MONTO_CONYUGE_PESOS
											,DM.MONTO_CONYUGE_MONEDA
											,case mes
												when 1 then dbo.f_mon_get_valor_moneda(3, dbo.to_date('01/01/'+convert(varchar(4), DE.ANO)))
												when 2 then dbo.f_mon_get_valor_moneda(3, dbo.to_date('01/02/'+convert(varchar(4), DE.ANO)))
												when 3 then dbo.f_mon_get_valor_moneda(3, dbo.to_date('01/03/'+convert(varchar(4), DE.ANO)))
												when 4 then dbo.f_mon_get_valor_moneda(3, dbo.to_date('01/04/'+convert(varchar(4), DE.ANO)))
												when 5 then dbo.f_mon_get_valor_moneda(3, dbo.to_date('01/05/'+convert(varchar(4), DE.ANO)))
												when 6 then dbo.f_mon_get_valor_moneda(3, dbo.to_date('01/06/'+convert(varchar(4), DE.ANO)))
												when 7 then dbo.f_mon_get_valor_moneda(3, dbo.to_date('01/07/'+convert(varchar(4), DE.ANO)))
												when 8 then dbo.f_mon_get_valor_moneda(3, dbo.to_date('01/08/'+convert(varchar(4), DE.ANO)))
												when 9 then dbo.f_mon_get_valor_moneda(3, dbo.to_date('01/09/'+convert(varchar(4), DE.ANO)))
												when 10 then dbo.f_mon_get_valor_moneda(3, dbo.to_date('01/10/'+convert(varchar(4), DE.ANO)))
												when 11 then dbo.f_mon_get_valor_moneda(3, dbo.to_date('01/11/'+convert(varchar(4), DE.ANO)))
												when 12 then dbo.f_mon_get_valor_moneda(3, dbo.to_date('01/12/'+convert(varchar(4), DE.ANO)))
											end VALOR_UTM_CONYUGE
									FROM	DECLARACION DE, DECLARACION_MES DM
									WHERE 	DE.COD_DECLARACION = DM.COD_DECLARACION
											AND DE.COD_DECLARACION = {PARAM1}
									ORDER BY DM.MES";	
						}
						break;
					//////////////// PRINT_PARAM ////////////////
					case 'PRINT_PARAM' :
						if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
							$sql = "SELECT 	VALOR NOM_UNIVERSIDAD
									FROM 	PARAMETRO
									WHERE	COD_PARAMETRO = {PARAM1}";
						}
						break;
					////////////// AJAX_VALOR_UTM ////////////////
					case 'AJAX_VALOR_UTM' :
						if (K_TIPO_BD=="oci"){
							$sql = "SELECT 	f_mon_get_valor_moneda({PARAM1}, to_date('01/01/'||TO_CHAR(PARAM2)||'', 'dd/mm/yyyy')) VALOR_UTM1
											,f_mon_get_valor_moneda({PARAM1}, to_date('01/02/'||TO_CHAR(PARAM2)||'', 'dd/mm/yyyy')) VALOR_UTM2
											,f_mon_get_valor_moneda({PARAM1}, to_date('01/03/'||TO_CHAR(PARAM2)||'', 'dd/mm/yyyy')) VALOR_UTM3
											,f_mon_get_valor_moneda({PARAM1}, to_date('01/04/'||TO_CHAR(PARAM2)||'', 'dd/mm/yyyy')) VALOR_UTM4
											,f_mon_get_valor_moneda({PARAM1}, to_date('01/05/'||TO_CHAR(PARAM2)||'', 'dd/mm/yyyy')) VALOR_UTM5
											,f_mon_get_valor_moneda({PARAM1}, to_date('01/06/'||TO_CHAR(PARAM2)||'', 'dd/mm/yyyy')) VALOR_UTM6
											,f_mon_get_valor_moneda({PARAM1}, to_date('01/07/'||TO_CHAR(PARAM2)||'', 'dd/mm/yyyy')) VALOR_UTM7
											,f_mon_get_valor_moneda({PARAM1}, to_date('01/08/'||TO_CHAR(PARAM2)||'', 'dd/mm/yyyy')) VALOR_UTM8
											,f_mon_get_valor_moneda({PARAM1}, to_date('01/09/'||TO_CHAR(PARAM2)||'', 'dd/mm/yyyy')) VALOR_UTM9
											,f_mon_get_valor_moneda({PARAM1}, to_date('01/10/'||TO_CHAR(PARAM2)||'', 'dd/mm/yyyy')) VALOR_UTM10
											,f_mon_get_valor_moneda({PARAM1}, to_date('01/11/'||TO_CHAR(PARAM2)||'', 'dd/mm/yyyy')) VALOR_UTM11
											,f_mon_get_valor_moneda({PARAM1}, to_date('01/12/'||TO_CHAR(PARAM2)||'', 'dd/mm/yyyy')) VALOR_UTM12 FROM DUAL
							";
						}else if (K_TIPO_BD=="mssql"){
							$sql = "SELECT 	dbo.f_mon_get_valor_moneda({PARAM1}, dbo.to_date('01/01/{PARAM2}')) VALOR_UTM1
											,dbo.f_mon_get_valor_moneda({PARAM1}, dbo.to_date('01/02/{PARAM2}')) VALOR_UTM2
											,dbo.f_mon_get_valor_moneda({PARAM1}, dbo.to_date('01/03/{PARAM2}')) VALOR_UTM3
											,dbo.f_mon_get_valor_moneda({PARAM1}, dbo.to_date('01/04/{PARAM2}')) VALOR_UTM4
											,dbo.f_mon_get_valor_moneda({PARAM1}, dbo.to_date('01/05/{PARAM2}')) VALOR_UTM5
											,dbo.f_mon_get_valor_moneda({PARAM1}, dbo.to_date('01/06/{PARAM2}')) VALOR_UTM6
											,dbo.f_mon_get_valor_moneda({PARAM1}, dbo.to_date('01/07/{PARAM2}')) VALOR_UTM7
											,dbo.f_mon_get_valor_moneda({PARAM1}, dbo.to_date('01/08/{PARAM2}')) VALOR_UTM8
											,dbo.f_mon_get_valor_moneda({PARAM1}, dbo.to_date('01/09/{PARAM2}')) VALOR_UTM9
											,dbo.f_mon_get_valor_moneda({PARAM1}, dbo.to_date('01/10/{PARAM2}')) VALOR_UTM10
											,dbo.f_mon_get_valor_moneda({PARAM1}, dbo.to_date('01/11/{PARAM2}')) VALOR_UTM11
											,dbo.f_mon_get_valor_moneda({PARAM1}, dbo.to_date('01/12/{PARAM2}')) VALOR_UTM12";
						}
						break;
					////////////// AJAX_AVISO_DEUDA_CRUZADA ///////////////
					case 'AJAX_AVISO_DEUDA_CRUZADA' :
						if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
							$sql = "SELECT  COUNT(*) COUNT 
									FROM    DEUDA DA, CRUZADO C, DEUDOR DR
									WHERE   C.COD_DEUDA = DA.COD_DEUDA
									AND     DA.COD_DEUDOR = DR.COD_DEUDOR
									AND     RUT = {PARAM1}";
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
						
					//////////////////////////		DEUDA_CARTERA_DIFERENTE		//////////////////////
					case 'DEUDA_CARTERA_DIFERENTE' :
						if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
							$sql = 	"SELECT COUNT(*) CANT
										FROM DEUDOR DR, DEUDA DA 
										WHERE DR.COD_DEUDOR = DA.COD_DEUDOR 
										AND DA.COD_CARTERA  <> {PARAM1}
										AND DR.RUT = {PARAM2}
										AND COD_TIPO_DEUDA IN ('CS', 'CSR','CR12')
										AND COD_ESTADO_DEUDA = ".self::K_ESTADO_DEUDA_EXIGIBLE;
						}
					break;
					
					//////////////////////////		DEUDA_CARTERA_DIFERENTE_USACH		//////////////////////
					case 'DEUDA_CARTERA_DIFERENTE_USACH' :
						if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
							$sql = 	"SELECT COUNT(*) CANT
										FROM DEUDOR DR, DEUDA DA 
										WHERE DR.COD_DEUDOR = DA.COD_DEUDOR 
										AND DA.COD_CARTERA  NOT IN({PARAM1},{PARAM3})
										AND DR.RUT = {PARAM2}
										AND COD_TIPO_DEUDA IN ('CS', 'CSR','CR12')";
						}
					break;
					
					//////////////////////////		AJAX_TIPO_DEUDA		//////////////////////
					case 'AJAX_TIPO_DEUDA' :
						if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
							$sql = "SELECT 	COD_TIPO_DEUDA
									FROM 	DEUDA DA, DEUDOR DR
									WHERE 	DA.COD_DEUDOR = DR.COD_DEUDOR
									AND 	RUT = {PARAM1}";
						}
					break;
					
					//////////////////////////		AJAX_CANT_CUOTAS		//////////////////////
					case 'AJAX_CANT_CUOTAS' :
						if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
							$sql = "SELECT 	TD.CANT_CUOTA
											,DA.CANT_CUOTA CANT_CUOTA_DEUDA 
									FROM 	DEUDA DA, DEUDOR DR, TIPO_DEUDA TD
									WHERE 	DA.COD_DEUDOR = DR.COD_DEUDOR
									AND 	TD.COD_TIPO_DEUDA = DA.COD_TIPO_DEUDA
									AND 	RUT =  {PARAM1}";
						}
					break;
					
					//////////////////////////		AJAX_DEUDA_TIPO_CS_CSR		//////////////////////
					case 'AJAX_DEUDA_TIPO_CS_CSR' :
						if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
							$sql = "SELECT  COUNT(*) CUANTO
									FROM    DEUDA DA, DEUDOR DR
									WHERE   DR.COD_DEUDOR = DA.COD_DEUDOR
									AND     RUT = {PARAM1}
									AND     COD_TIPO_DEUDA IN ('CS','CSR','CR12')";
						}
					break;
					
						
			/////////////////  ERROR ///////////////
			default:
				base::error("sql_declaracion::get_sql, selector: '$selector_sql' no definido");
					
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>
