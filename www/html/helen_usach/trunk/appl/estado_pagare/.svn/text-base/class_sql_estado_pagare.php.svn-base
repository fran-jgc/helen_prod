<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_estado_pagare extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			///////////////		INPUT		///////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 		COD_ESTADO_PAGARE 
										,NOM_ESTADO_PAGARE
							FROM 		ESTADO_PAGARE
							WHERE 		COD_ESTADO_PAGARE = {KEY1}
							ORDER BY 	COD_ESTADO_PAGARE";
				}
				break;

			///////////////		OUTPUT		///////////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT		COD_ESTADO_PAGARE
        								,NOM_ESTADO_PAGARE
							FROM 		ESTADO_PAGARE
							ORDER BY 	COD_ESTADO_PAGARE";
				}
				break;
				
			///////////////		AJAX_NOM_ESTADO		///////////////////
			case 'AJAX_NOM_ESTADO' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	NOM_ESTADO_PAGARE
							FROM	ESTADO_PAGARE
							WHERE	NOM_ESTADO_PAGARE = '{PARAM1}'";
				}
				break;

			////////////////		ERROR		////////////////////
			default:
			base::error("sql_estado_pagare::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql; 
	}
}
?>