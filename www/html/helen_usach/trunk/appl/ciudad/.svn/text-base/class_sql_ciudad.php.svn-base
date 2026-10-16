<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_ciudad extends sql_tabla {
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
				
			////////////////		INPUT		/////////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 		COD_CIUDAD
										,COD_REGION
										,COD_PAIS 
										,NOM_CIUDAD																				 						
							FROM 		CIUDAD
							WHERE 		COD_CIUDAD = {KEY1}";
				}
				break;
					
				////////////////		OUTPUT		//////////////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT		COD_CIUDAD
        								,NOM_CIUDAD
							FROM 		CIUDAD
							ORDER BY 	COD_CIUDAD";
				}
				break;
					
				/////////////////////		DD_COD_REGION		///////////////
			case 'DD_COD_REGION' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 		COD_REGION
										,NOM_REGION																				 						
							FROM 		REGION
							ORDER BY 	COD_REGION";
				}
				break;
					
				//////////////////////		DD_COD_PAIS		////////////////////
			case 'DD_COD_PAIS' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 		COD_PAIS
										,NOM_PAIS																				 						
							FROM 		PAIS
							ORDER BY 	NOM_PAIS";
				}
				break;
				///////////////		AJAX_COD_CIUDAD	///////////////////
			case 'AJAX_COD_CIUDAD' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_REGION
							FROM	REGION
							WHERE	COD_REGION = '{PARAM1}'";
				}
				break;
				///////////////		AJAX_NOM_CIUDAD	///////////////////
			case 'AJAX_NOM_CIUDAD' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	NOM_REGION
							FROM	REGION
							WHERE	NOM_REGION = '{PARAM1}'";
				}
				break;
					
				//////////////////////		DD_COD_CIUDAD_REGION		////////////////////
			case 'DD_COD_CIUDAD_REGION' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 		COD_REGION
										,NOM_REGION																				 						
							FROM 		REGION
							WHERE		COD_PAIS = {KEY1}
							ORDER BY 	NOM_REGION";
				}
				break;
				///////////////		AJAX_COD_CIUDAD	///////////////////
			case 'AJAX_COD_CIUDAD' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_CIUDAD
							FROM	CIUDAD
							WHERE	COD_CIUDAD = '{PARAM1}'";
				}
				break;
				///////////////		AJAX_NOM_REGION	///////////////////
			case 'AJAX_NOM_CIUDAD' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	NOM_CIUDAD
							FROM	CIUDAD
							WHERE	NOM_CIUDAD = '{PARAM1}'";
				}
				break;
					
				////////////////////////		ERROR		//////////////////////
			default:
				base::error("sql_ciudad::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>