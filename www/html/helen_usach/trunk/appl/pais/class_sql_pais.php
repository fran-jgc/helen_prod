<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_pais extends sql_tabla {
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
			/////////////////  INPUT ///////////////
			case 'INPUT' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_PAIS, 
									NOM_PAIS														 						
							FROM	PAIS
							WHERE 	COD_PAIS = {KEY1}";
				}
			break;
				
			/////////////////  OUTPUT ///////////////
			case 'OUTPUT' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 		COD_PAIS,
               							NOM_PAIS			   			   
        					FROM 		PAIS
        					ORDER BY 	COD_PAIS";
				}
			break;
			
			///////////////		AJAX_COD_PAIS	///////////////////
			case 'AJAX_COD_PAIS' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_PAIS
							FROM	PAIS
							WHERE	COD_PAIS = '{PARAM1}'";
				}
				break;
			///////////////		AJAX_NOM_PAIS	///////////////////
			case 'AJAX_NOM_PAIS' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	NOM_PAIS
							FROM	PAIS
							WHERE	NOM_PAIS = '{PARAM1}'";
				}
				break;
				
			/////////////////  ERROR ///////////////
			default: 
			base::error("sql_pais::get_sql, selector: '$selector_sql' no definido");	
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>