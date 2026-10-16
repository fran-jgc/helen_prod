<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_tipo_rebaja_pagare extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			
			////////////////////    INPUT     //////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 	COD_TIPO_REBAJA_PAGARE,
									NOM_TIPO_REBAJA_PAGARE
							FROM 	TIPO_REBAJA_PAGARE
							WHERE 	COD_TIPO_REBAJA_PAGARE = {KEY1}";
				}				
			break;
			///////////////////   OUTPUT      ////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 		COD_TIPO_REBAJA_PAGARE,
              							NOM_TIPO_REBAJA_PAGARE			   			   
        					FROM 		TIPO_REBAJA_PAGARE
							ORDER BY 	COD_TIPO_REBAJA_PAGARE";
				}
			break;
			
			///////////////		AJAX_NOM_TIPO_REBAJA_PAGARE	///////////////////
			case 'AJAX_NOM_TIPO_REBAJA_PAGARE' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	NOM_TIPO_REBAJA_PAGARE
							FROM	TIPO_REBAJA_PAGARE
							WHERE	NOM_TIPO_REBAJA_PAGARE = '{PARAM1}'";
				}
				break;
			///////////////   ERROR    ////////////////////
			default:
				base::error("sql_tipo_rebaja_pagare::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>