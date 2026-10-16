<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_tipo_condonacion extends sql_tabla {
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
			/////////////////  INPUT ///////////////
			case 'INPUT' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_TIPO_CONDONACION,
									NOM_TIPO_CONDONACION												 						
							FROM 	TIPO_CONDONACION
							WHERE 	COD_TIPO_CONDONACION = {KEY1}";
				}
			break;
			
			/////////////////  OUTPUT ///////////////
			case 'OUTPUT' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 		COD_TIPO_CONDONACION,
	               						NOM_TIPO_CONDONACION		   			   
	      					FROM 		TIPO_CONDONACION
							ORDER BY 	COD_TIPO_CONDONACION";
				}
			break;
			///////////////		AJAX_NOM_TIPO_CONDONACION	///////////////////
			case 'AJAX_NOM_TIPO_CONDONACION' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	NOM_TIPO_CONDONACION
							FROM	TIPO_CONDONACION
							WHERE	NOM_TIPO_CONDONACION = '{PARAM1}'";
				}
			break;
			/////////////////  ERROR ///////////////
			default: 
				base::error("sql_novacion::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>