<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_afp extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			
			////////////////////    INPUT     //////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 	COD_AFP,
									NOM_AFP
							FROM 	AFP
							WHERE 	COD_AFP = {KEY1}";
				}				
			break;
			///////////////////   OUTPUT      ////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 		COD_AFP,
              							NOM_AFP			   			   
        					FROM 		AFP
							ORDER BY 	COD_AFP";
				}
			break;
			
			///////////////		AJAX_NOM_AFP	///////////////////
			case 'AJAX_NOM_AFP' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	NOM_AFP
							FROM	AFP
							WHERE	NOM_AFP = '{PARAM1}'";
				}
				break;
			///////////////   ERROR    ////////////////////
			default:
				base::error("sql_afp::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>