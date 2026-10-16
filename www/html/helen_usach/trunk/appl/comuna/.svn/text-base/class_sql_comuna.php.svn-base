<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_comuna extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			/////////////////////		INPUT		/////////////////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 		COD_COMUNA
										,COD_CIUDAD
										,NOM_COMUNA																				 						
							FROM 		COMUNA
							WHERE		COD_COMUNA = {KEY1}";
				}
			break;
			////////////////////		OUTPUT		//////////////////////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 		COD_COMUNA
										,NOM_COMUNA			   			   
        					FROM 		COMUNA
        					ORDER BY	COD_COMUNA";
				}
			break;
			////////////////////////		DD_COD_CIUDAD		///////////////////////
			case 'DD_COD_CIUDAD' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 		COD_CIUDAD
										,NOM_CIUDAD																				 						
							FROM 		CIUDAD";
				}
			break;
			///////////////		AJAX_COD_COMUNA	///////////////////
			case 'AJAX_COD_COMUNA' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_COMUNA
							FROM	COMUNA
							WHERE	COD_COMUNA = '{PARAM1}'";
				}
				break;
			///////////////		AJAX_NOM_COMUNA	///////////////////
			case 'AJAX_NOM_COMUNA' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	NOM_COMUNA
							FROM	COMUNA
							WHERE	NOM_COMUNA = '{PARAM1}'";
				}
				break;
			////////////////////		ERROR		/////////////////////////////
			default:
			base::error("sql_comuna::get_sql, selector: '$selector_sql' no definido");	
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>