<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_cartera extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			
			/////////////		INPUT		/////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 		COD_CARTERA
										,NOM_CARTERA
										,ENCARGADO													 						
							FROM 		CARTERA
							WHERE 		COD_CARTERA = {KEY1}";
				}
			break;
			
			/////////////////	OUTPUT		////////////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 		COD_CARTERA
	               						,NOM_CARTERA
	               						,ENCARGADO			   			   
	       					FROM 		CARTERA
							ORDER BY 	COD_CARTERA";
				}
			break;
				
			///////////////		AJAX_NOM_CARTERA	///////////////////
			case 'AJAX_NOM_CARTERA' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	NOM_CARTERA
							FROM	CARTERA
							WHERE	NOM_CARTERA = '{PARAM1}'";
				}
			break;
			/////////////////	 ERROR		//////////////////////
			default:
				base::error("sql_cartera::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>