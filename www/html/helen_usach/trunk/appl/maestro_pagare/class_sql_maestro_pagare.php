<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_maestro_pagare extends sql_tabla {
	CONST K_RUT_UNIVERSIDAD 	= 4;
	CONST K_NOM_UNIVERSIDAD 	= 5;	
	CONST K_NOM_COORDINADOR 	= 6;
	CONST K_FONO_COORDINADOR 	= 7;
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
			/////////////////   OUTPUT   ///////////////
			case 'OUTPUT' :
				/////////////////   ORACLE   ///////////////
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT          DEUDOR.COD_DEUDOR,
											DEUDOR.RUT,
					                		DEUDOR.DIG_VERIF,
					                		DEUDOR.APELLIDO_PATERNO,
					                		DEUDOR.APELLIDO_MATERNO,
					                		DEUDOR.NOMBRE,
					                		PAGARE.FOLIO_PAGARE,
					                		PAGARE.MONTO_MONEDA,
					                		to_char(PAGARE.FECHA_PAGARE,'YYYYmmdd') FECHA_PAGARE,
					                		Nvl (f_deudor_carrera(DEUDOR.COD_DEUDOR), 0) COD_CARRERA,
					                		DIR.DIRECCION, 
											COM.NOM_COMUNA,
											CIU.NOM_CIUDAD,
											DIR.TELEFONO
							FROM            PAGARE, DEUDA, DEUDOR left outer join DIRECCION DIR ON DIR.COD_DIRECCION = f_persona_direccion(DEUDOR.COD_DEUDOR, 'P')
																  left outer join  COMUNA COM ON COM.COD_COMUNA = DIR.COD_COMUNA
																  left outer join CIUDAD CIU ON CIU.COD_CIUDAD = DIR.COD_CIUDAD
							WHERE           DEUDA.COD_TIPO_DEUDA = 'CS' AND                
					                		PAGARE.COD_DEUDA = DEUDA.COD_DEUDA AND
					                		DEUDOR.COD_DEUDOR = DEUDA.COD_DEUDOR AND
					                		to_char(PAGARE.FECHA_PAGARE,'YYYY') = {PARAM1}";
				}
				/////////////////   SQL-SERVER   ///////////////
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT			DEUDOR.COD_DEUDOR,
											DEUDOR.RUT,
							        		DEUDOR.DIG_VERIF,
							        		DEUDOR.APELLIDO_PATERNO,
							        		DEUDOR.APELLIDO_MATERNO,
							        		DEUDOR.NOMBRE,
							        		PAGARE.FOLIO_PAGARE,
							        		PAGARE.MONTO_MONEDA,
							        		CONVERT(VARCHAR(20),PAGARE.FECHA_PAGARE,112) FECHA_PAGARE,
							        		dbo.f_deudor_carrera(deudor.cod_deudor)COD_CARRERA,
							        		DIR.DIRECCION, 
											COM.NOM_COMUNA,
											CIU.NOM_CIUDAD,
											DIR.TELEFONO
							FROM            PAGARE, DEUDA, DEUDOR left outer join DIRECCION DIR ON DIR.COD_DIRECCION = dbo.f_persona_direccion(DEUDOR.COD_DEUDOR, 'P')
																  left outer join  COMUNA COM ON COM.COD_COMUNA = DIR.COD_COMUNA
																  left outer join CIUDAD CIU ON CIU.COD_CIUDAD = DIR.COD_CIUDAD
							WHERE           DEUDA.COD_TIPO_DEUDA = 'CS' AND                
							        		PAGARE.COD_DEUDA = DEUDA.COD_DEUDA AND
							        		DEUDOR.COD_DEUDOR = DEUDA.COD_DEUDOR and
							        		year(PAGARE.FECHA_PAGARE) = {PARAM1}";
				}
				break;
			
			/////////////////   EXISTE   ///////////////
			case 'EXISTE' :
				/////////////////   ORACLE   ///////////////
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT          DEUDOR.RUT,
		                					DEUDOR.DIG_VERIF,
		                					DEUDOR.APELLIDO_PATERNO,
		                					DEUDOR.APELLIDO_MATERNO,
		                					DEUDOR.NOMBRE,
		                					PAGARE.FOLIO_PAGARE,
		                					PAGARE.MONTO_MONEDA,
		                					PAGARE.FECHA_PAGARE,
		                					NVL(f_deudor_carrera(DEUDOR.COD_DEUDOR), 0) COD_CARRERA
							FROM			PAGARE, DEUDA, DEUDOR
							WHERE           DEUDA.COD_TIPO_DEUDA = 'CS' and
		                					PAGARE.COD_DEUDA = DEUDA.COD_DEUDA and
		                					to_char(PAGARE.FECHA_PAGARE,'YYYY') = {PARAM1} and
		                					DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR";
				}
				/////////////////   SQL-SERVER   ///////////////
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT          DEUDOR.RUT,
		                					DEUDOR.DIG_VERIF,
		                					DEUDOR.APELLIDO_PATERNO,
		                					DEUDOR.APELLIDO_MATERNO,
		                					DEUDOR.NOMBRE,
		                					PAGARE.FOLIO_PAGARE,
		                					PAGARE.MONTO_MONEDA,
		                					PAGARE.FECHA_PAGARE,
		                					isnull(dbo.f_deudor_carrera(DEUDOR.COD_DEUDOR), 0) COD_CARRERA
							FROM			PAGARE, DEUDA, DEUDOR
							WHERE           DEUDA.COD_TIPO_DEUDA = 'CS' and
		                					PAGARE.COD_DEUDA = DEUDA.COD_DEUDA and
		                					year(PAGARE.FECHA_PAGARE) = {PARAM1} and
		                					DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR";
				}
				break;
				/////////////////////	RUT_UNIVERSIDAD		////////////////
			case 'RUT_UNIVERSIDAD' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" ){
					$sql = "SELECT 	f_get_parametro(".self::K_RUT_UNIVERSIDAD.")RUT_UNIVERSIDAD 
							FROM 	Dual";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 	dbo.f_get_parametro(".self::K_RUT_UNIVERSIDAD.")RUT_UNIVERSIDAD";
				}
			break;
			
			//////////////////////	NOM_UNIVERSIDAD		/////////////////////
			case 'NOM_UNIVERSIDAD' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" ){
					$sql = "SELECT	 f_get_parametro(".self::K_NOM_UNIVERSIDAD.") NOM_UNIVERSIDAD 
							FROM 	Dual";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	 dbo.f_get_parametro(".self::K_NOM_UNIVERSIDAD.") NOM_UNIVERSIDAD";
				}
			break;
			//////////////////////	NOM_COORDINADOR		/////////////////////
			case 'NOM_COORDINADOR' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" ){
					$sql = "SELECT f_get_parametro(".self::K_NOM_COORDINADOR.") NOM_COORDINADOR 
							FROM Dual";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT dbo.f_get_parametro(".self::K_NOM_COORDINADOR.") NOM_COORDINADOR";
				}
			break;
			//////////////////////	FONO_COORDINADOR		/////////////////////
			case 'FONO_COORDINADOR' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" ){
					$sql = "SELECT 	f_get_parametro(".self::K_FONO_COORDINADOR.") FONO_COORDINADOR 
							FROM 	Dual";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 	dbo.f_get_parametro(".self::K_FONO_COORDINADOR.") FONO_COORDINADOR";
				}
			break;
				
			/////////////////  ERROR ///////////////
			default:
			base::error("sql_maestro_pagare::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>