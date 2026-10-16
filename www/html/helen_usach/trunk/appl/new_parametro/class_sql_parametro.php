<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_parametro extends sql_tabla {
	const K_PARAM_MAX_PRORROGA 			= 1;
	const K_PARAM_CARTERA_DEFECT		= 2;
	const K_PARAM_UNIV_DEFECTO			= 3;
	const K_PARAM_RUT_U 				= 4;
	const K_PARAM_NOM_U 				= 5;
	const K_PARAM_NOM_COORDINADOR 		= 6;
	const K_PARAM_TEL_COORDINADOR 		= 7;
	const K_PARAM_MAX_PERMITIDO_PAGO	= 8;
	const K_PARAM_DIRECCION 			= 9; 
	const K_PARAM_FONO		 			= 10; 
	const K_PARAM_COMUNA	 			= 11;
	const K_PARAM_CIUDAD	 			= 12; 
	const K_PARAM_EMAIL		 			= 13; 
	const K_PARAM_SITIO_WEB	 			= 14;
	const K_PARAM_FAX		 			= 15;
	const K_PARAM_PAIS	 				= 16;
	const K_PARAM_PROM_PERMANENCIA 		= 17;
	const K_PARAM_ADMINISTRADOR		 	= 19;
	const K_PARAM_CTA_UTILIDAD			= 20;
	const K_PARAM_CTA_CORREC_MONET		= 21;
	const K_PARAM_TAZA_INTERES_CHEQUE	= 22;
	const K_PARAM_MAX_NUMERO_CHEQUE     = 23;
	const K_PARAM_MAX_VALIDEZ_SIMULACION= 24;
	const K_PARAM_NRO_CUENTA_UNIVERSIDAD= 25;
	const K_PARAM_PORC_SUGERIDO_RENEGOCIACION= 28;
	const K_PARAM_PORC_INTERES_CONVENIO = 30;
	const K_PARAM_PORC_SUGERIDO_CONVENIO = 31;
	const K_PARAM_ANO_VIG_CONT = 32;
	const K_PARAM_PASSWORD_MINEDUC = 33;
	const K_PARAM_FONO_CENTRAL = 34;
	
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
			////////////////		DW_LOCK_TABLE		//////////////////////////
			case 'DW_LOCK_TABLE' :
				if (K_TIPO_BD=='mssql')
					$sql = "SELECT	NOM_MODULO + '/' + convert(varchar, NRO_MODULO) ELIMINAR,
									NRO_MODULO,
									UPPER(NOM_MODULO) NOM_MODULO,
									LK.COD_USUARIO,
									U.NOM_USUARIO,
									CONVERT(varchar, FECHA_REGISTRO, 103) + ' ' + CONVERT(varchar, FECHA_REGISTRO, 108) FECHA_REGISTRO
							FROM	LOCK_TABLE LK, USUARIO U
							WHERE	LK.COD_USUARIO = U.COD_USUARIO
							and NOM_MODULO <> 'PARAMETRO'";
				elseif (K_TIPO_BD=='mysql')
					$sql = "SELECT	CONCAT(NOM_MODULO, '/', NRO_MODULO) ELIMINAR,
									NRO_MODULO,
									UPPER(NOM_MODULO) NOM_MODULO,
									LK.COD_USUARIO,
									U.NOM_USUARIO,
									DATE_FORMAT(FECHA_REGISTRO, '%d/%m/%Y %T') FECHA_REGISTRO
							FROM	LOCK_TABLE LK, USUARIO U
							WHERE	LK.COD_USUARIO = U.COD_USUARIO
							and NOM_MODULO <> 'PARAMETRO";
				elseif (K_TIPO_BD=='oci')
					$sql = "SELECT	NOM_MODULO ||'/'|| To_Char(NRO_MODULO) ELIMINAR,
									NRO_MODULO,
									UPPER(NOM_MODULO) NOM_MODULO,
									LK.COD_USUARIO,
									U.NOM_USUARIO,
							    TO_CHAR(FECHA_REGISTRO, 'dd/mm/yyyy hh24:mi:ss') FECHA_REGISTRO
							FROM	LOCK_TABLE LK, USUARIO U
							WHERE	LK.COD_USUARIO = U.COD_USUARIO
							and Upper(LK.NOM_MODULO) <> 'PARAMETRO'";
			break;	
			//////////////////////		INPUT		////////////////////
			case 'INPUT' :
				/////////////////  ORACLE ///////////////
				if (K_TIPO_BD=="oci"){
					$sql = " SELECT	f_get_parametro(".self::K_PARAM_MAX_PRORROGA.") MAXIMO_PRORROGA,
						f_get_parametro(".self::K_PARAM_CARTERA_DEFECT.") CARTERA_DEFEC,
						f_get_parametro(".self::K_PARAM_UNIV_DEFECTO.") UNIV_DEFECTO,
						f_get_parametro(".self::K_PARAM_RUT_U.") RUT_U,
						f_get_parametro(".self::K_PARAM_NOM_U.") NOM_U,
						f_get_parametro(".self::K_PARAM_NOM_COORDINADOR.") NOM_COORDINADOR,
						f_get_parametro(".self::K_PARAM_TEL_COORDINADOR.") TEL_COORDINADOR,
						f_get_parametro(".self::K_PARAM_MAX_PERMITIDO_PAGO.") MAX_PERMITIDO_PAGO,
						f_get_parametro(".self::K_PARAM_DIRECCION.") DIRECCION,
						f_get_parametro(".self::K_PARAM_FONO.") FONO,
						f_get_parametro(".self::K_PARAM_COMUNA.") COMUNA,
						f_get_parametro(".self::K_PARAM_CIUDAD.") CIUDAD,
						f_get_parametro(".self::K_PARAM_EMAIL.") EMAIL,
						f_get_parametro(".self::K_PARAM_SITIO_WEB.") SITIO_WEB,
						f_get_parametro(".self::K_PARAM_FAX.") FAX,
						f_get_parametro(".self::K_PARAM_PAIS.") PAIS,
						f_get_parametro(".self::K_PARAM_PROM_PERMANENCIA.") PROM_PERMANENCIA,
						f_get_parametro(".self::K_PARAM_ADMINISTRADOR.") ADMINISTRADOR,
						f_get_parametro(".self::K_PARAM_CTA_UTILIDAD.") COD_CUENTA_CONTABLE_U,
						f_get_parametro(".self::K_PARAM_CTA_CORREC_MONET.") COD_CUENTA_CONTABLE_CM,
						f_get_parametro(".self::K_PARAM_TAZA_INTERES_CHEQUE.") TAZA_INTERES_CHEQUE,
						f_get_parametro(".self::K_PARAM_MAX_NUMERO_CHEQUE.") MAX_NUMERO_CHEQUE,
						f_get_parametro(".self::K_PARAM_MAX_VALIDEZ_SIMULACION.") MAX_VALIDEZ_SIMULACION,
						f_get_parametro(".self::K_PARAM_NRO_CUENTA_UNIVERSIDAD.") NRO_CUENTA_UNIVERSIDAD,
						f_get_parametro(".self::K_PARAM_PORC_SUGERIDO_RENEGOCIACION.") PORC_SUGERIDO_RENEGOCIACION,
						f_get_parametro(".self::K_PARAM_PORC_INTERES_CONVENIO.") PORC_INTERES_CONVENIO,
						f_get_parametro(".self::K_PARAM_PORC_SUGERIDO_CONVENIO.") PORC_SUGERIDO_CONVENIO,
						f_get_parametro(".self::K_PARAM_PASSWORD_MINEDUC.") PASSWORD_MINEDUC,
						f_get_parametro(".self::K_PARAM_FONO_CENTRAL.") FONO_CENTRAL,
						f_get_parametro(".self::K_PARAM_ANO_VIG_CONT.") ANO_VIG_CONT,
						'none' VISIBLE_TR
				FROM DUAL";
				}
				///////////////// SQL-SERVER  ///////////////
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	dbo.f_get_parametro(".self::K_PARAM_MAX_PRORROGA.") MAXIMO_PRORROGA,
						dbo.f_get_parametro(".self::K_PARAM_CARTERA_DEFECT.") CARTERA_DEFEC,
						dbo.f_get_parametro(".self::K_PARAM_UNIV_DEFECTO.") UNIV_DEFECTO,
						dbo.f_get_parametro(".self::K_PARAM_RUT_U.") RUT_U,
						dbo.f_get_parametro(".self::K_PARAM_NOM_U.") NOM_U,
						dbo.f_get_parametro(".self::K_PARAM_NOM_COORDINADOR.") NOM_COORDINADOR,
						dbo.f_get_parametro(".self::K_PARAM_TEL_COORDINADOR.") TEL_COORDINADOR,
						dbo.f_get_parametro(".self::K_PARAM_MAX_PERMITIDO_PAGO.") MAX_PERMITIDO_PAGO,
						dbo.f_get_parametro(".self::K_PARAM_DIRECCION.") DIRECCION,
						dbo.f_get_parametro(".self::K_PARAM_FONO.") FONO,
						dbo.f_get_parametro(".self::K_PARAM_COMUNA.") COMUNA,
						dbo.f_get_parametro(".self::K_PARAM_CIUDAD.") CIUDAD,
						dbo.f_get_parametro(".self::K_PARAM_EMAIL.") EMAIL,
						dbo.f_get_parametro(".self::K_PARAM_SITIO_WEB.") SITIO_WEB,
						dbo.f_get_parametro(".self::K_PARAM_FAX.") FAX,
						dbo.f_get_parametro(".self::K_PARAM_PAIS.") PAIS,
						dbo.f_get_parametro(".self::K_PARAM_PROM_PERMANENCIA.") PROM_PERMANENCIA,
						dbo.f_get_parametro(".self::K_PARAM_ADMINISTRADOR.") ADMINISTRADOR,
						dbo.f_get_parametro(".self::K_PARAM_CTA_UTILIDAD.") COD_CUENTA_CONTABLE_U,
						dbo.f_get_parametro(".self::K_PARAM_CTA_CORREC_MONET.") COD_CUENTA_CONTABLE_CM,
						dbo.f_get_parametro(".self::K_PARAM_TAZA_INTERES_CHEQUE.") TAZA_INTERES_CHEQUE,
						dbo.f_get_parametro(".self::K_PARAM_MAX_NUMERO_CHEQUE.") MAX_NUMERO_CHEQUE,
						dbo.f_get_parametro(".self::K_PARAM_MAX_VALIDEZ_SIMULACION.") MAX_VALIDEZ_SIMULACION,
						dbo.f_get_parametro(".self::K_PARAM_NRO_CUENTA_UNIVERSIDAD.") NRO_CUENTA_UNIVERSIDAD,
						dbo.f_get_parametro(".self::K_PARAM_PORC_SUGERIDO_RENEGOCIACION.") PORC_SUGERIDO_RENEGOCIACION,
						dbo.f_get_parametro(".self::K_PARAM_PORC_INTERES_CONVENIO.") PORC_INTERES_CONVENIO,
						dbo.f_get_parametro(".self::K_PARAM_PORC_SUGERIDO_CONVENIO.") PORC_SUGERIDO_CONVENIO,
						dbo.f_get_parametro(".self::K_PARAM_PASSWORD_MINEDUC.") PASSWORD_MINEDUC,
						dbo.f_get_parametro(".self::K_PARAM_FONO_CENTRAL.") FONO_CENTRAL,
						dbo.f_get_parametro(".self::K_PARAM_ANO_VIG_CONT.") ANO_VIG_CONT,
						'none' VISIBLE_TR";
				}
			break;
			//////////////////////DD_CUENTA_CONTABLE//////////////////////
			case 'DD_CUENTA_CONTABLE' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT		COD_CUENTA_CONTABLE 
                      					,NOM_CUENTA_CONTABLE
               				FROM   		CUENTA_CONTABLE
               				ORDER BY	NOM_CUENTA_CONTABLE";
				}
			break;
			//////////////////////DD_NRO_CUENTA_UNIVERSIDAD//////////////////////
			case 'DD_NRO_CUENTA_UNIVERSIDAD' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT COD_CUENTA_CORRIENTE
									,NOM_CUENTA_CORRIENTE																				 						
							FROM 	CUENTA_CORRIENTE			
							ORDER BY COD_CUENTA_CORRIENTE";
				}
			break;
			//////////////////////DD_CARTERA//////////////////////
			case 'DD_CARTERA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT		COD_CARTERA CARTERA_DEFEC
                      					,NOM_CARTERA
               				FROM   		CARTERA
               				ORDER BY	NOM_CARTERA";
				}
			break;

			//////////////////////DD_UNIVERSIDAD//////////////////////
			case 'DD_UNIVERSIDAD' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT		COD_UNIVERSIDAD UNIV_DEFECTO
                      					,NOM_UNIVERSIDAD
               				FROM   		UNIVERSIDAD
               				ORDER BY	NOM_UNIVERSIDAD";
				}
			break;
			///////////////////////////////		ERROR		////////////////////////////////////
			default:
			base::error("sql_parametro::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>