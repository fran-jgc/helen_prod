<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_tipo_suspension extends sql_tabla {
	static function get_sql($selector_sql) {
		switch ($selector_sql) {
			/////////////////  INPUT ///////////////	
			case 'INPUT' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select COD_TIPO_SUSPENSION,
									NOM_TIPO_SUSPENSION,
									PERIODO_MAXIMO											 						
				   			from	TIPO_SUSPENSION
			   				where	COD_TIPO_SUSPENSION = {KEY1}";
				}
			break;
			/////////////////  OUTPUT ///////////////	
			case 'OUTPUT' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){			
					$sql = "select COD_TIPO_SUSPENSION,
	               					NOM_TIPO_SUSPENSION
	        	    		from TIPO_SUSPENSION
							order by COD_TIPO_SUSPENSION";
				}
			break;
			///////////////		AJAX_NOM_TIPO_SUSPENSION	///////////////////
			case 'AJAX_NOM_TIPO_SUSPENSION' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	NOM_TIPO_SUSPENSION
							FROM	TIPO_SUSPENSION
							WHERE	NOM_TIPO_SUSPENSION = '{PARAM1}'";
				}
			break;
			/////////////////  ERROR ///////////////
			default: 
			base::error("sql_tipo_suspension::get_sql, selector: '$selector_sql' no definido");		
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;		
	}
}

?>