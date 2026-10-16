<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_maestro_matricula extends sql_tabla {
	CONST K_RUT_UNIVERSIDAD 	= 4;
	CONST K_NOM_UNIVERSIDAD 	= 5;	
	CONST K_NOM_COORDINADOR 	= 6;
	CONST K_FONO_COORDINADOR 	= 7;
	const K_PARAM_UNIVERSIDAD = 3;
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
			/////////////////   OUTPUT   ///////////////
			case 'OUTPUT' :
				/////////////////  ORACLE    ///////////////
				if (K_TIPO_BD=="oci"){
					$sql = "como se ejecutan de distinta manera el codigo debe ser separado en wo_maestro_matricula";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "exec spi_project_matricula {PARAM1}";
				}
			break;
			
			/////////////////   EXISTE  ///////////////
			case 'EXISTE' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 		DISTINCT   D.RUT,
				                  		D.DIG_VERIF,
				                  		D.APELLIDO_PATERNO,
				                  		D.APELLIDO_MATERNO,
				                  		D.NOMBRE
							FROM    	MATRICULA M, DEUDOR D
							WHERE   	M.COD_UNIVERSIDAD = f_get_parametro(".self::K_PARAM_UNIVERSIDAD.")
							AND     	M.ANO_MATRICULA = {PARAM1}
							AND     	D.COD_DEUDOR = M.COD_DEUDOR";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 		DISTINCT   D.RUT,
				                  		D.DIG_VERIF,
				                  		D.APELLIDO_PATERNO,
				                  		D.APELLIDO_MATERNO,
				                  		D.NOMBRE
							FROM    	MATRICULA M, DEUDOR D
							WHERE   	M.COD_UNIVERSIDAD = dbo.f_get_parametro(".self::K_PARAM_UNIVERSIDAD.")
							AND     	M.ANO_MATRICULA = {PARAM1}
							AND     	D.COD_DEUDOR = M.COD_DEUDOR";
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
					$sql = "SELECT 	dbo.f_get_parametro(".self::K_RUT_UNIVERSIDAD.")RUT_UNIVERSIDAD ";
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
			base::error("sql_maestro_matricula::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;		
	}
}
?>