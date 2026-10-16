<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_tipo_bitacora extends sql_tabla {
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
			/////////////////  INPUT ///////////////
			case 'INPUT' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_TIPO_BITACORA,
									NOM_TIPO_BITACORA,
									PERMITE_MANUAL										 						
				   			FROM 	TIPO_BITACORA
			   				WHERE 	COD_TIPO_BITACORA = {KEY1}";
				}
			break;
			
			/////////////////  OUTPUT ///////////////
			case 'OUTPUT' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 		COD_TIPO_BITACORA,
	               						NOM_TIPO_BITACORA
	        				FROM 		TIPO_BITACORA
							ORDER BY 	COD_TIPO_BITACORA";
				}
			break;
			///////////////		AJAX_NOM_TIPO_BITACORA	///////////////////
			case 'AJAX_NOM_TIPO_BITACORA' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	NOM_TIPO_BITACORA
							FROM	TIPO_BITACORA
							WHERE	NOM_TIPO_BITACORA = '{PARAM1}'";
				}
			break;
			
			/////////////////  ERROR ///////////////
			default: 
			base::error("sql_tipo_bitacora::get_sql, selector: '$selector_sql' no definido");		
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>