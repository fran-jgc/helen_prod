<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_banco extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			
			/////////////   INPUT   //////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= 	"SELECT 	COD_BANCO, 
										NOM_BANCO														 						
							FROM 		BANCO
							WHERE 		COD_BANCO = {KEY1}
							ORDER BY 	COD_BANCO";
				}
			break;
			
			////////////   OUTPUT /////////////////////
			case 'OUTPUT' :
					if (K_TIPO_BD=="oci"  || K_TIPO_BD=="mssql"){
					$sql= 	"SELECT 	COD_BANCO
              							,NOM_BANCO			   			   
        					FROM 		BANCO
							ORDER BY 	COD_BANCO";
				}
			break;
			///////////////		AJAX_COD_BANCO	///////////////////
			case 'AJAX_COD_BANCO' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_BANCO
							FROM	BANCO
							WHERE	COD_BANCO = '{PARAM1}'";
				}
				break;
			///////////////		AJAX_NOM_BANCO	///////////////////
			case 'AJAX_NOM_BANCO' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	NOM_BANCO
							FROM	BANCO
							WHERE	NOM_BANCO = upper('{PARAM1}')";
				}
				break;
			
			/////////////////    ERROR    ///////////
			default:
				base::error("sql_banco::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;	
	}
}
?>