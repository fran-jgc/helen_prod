<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_empresa_cobranza extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			
			/////////////   INPUT   //////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= 	"SELECT 	COD_EMPRESA_COBRANZA, 
										NOM_EMPRESA_COBRANZA
							FROM 		EMPRESA_COBRANZA
							WHERE 		COD_EMPRESA_COBRANZA = {KEY1}
							ORDER BY 	COD_EMPRESA_COBRANZA";
				}
			break;
			
			////////////   OUTPUT /////////////////////
			case 'OUTPUT' :
					if (K_TIPO_BD=="oci"  || K_TIPO_BD=="mssql"){
					$sql= 	"SELECT 	COD_EMPRESA_COBRANZA
              							,NOM_EMPRESA_COBRANZA
        					FROM 		EMPRESA_COBRANZA
							ORDER BY 	COD_EMPRESA_COBRANZA";
				}
			break;
			
			///////////////		AJAX_COD_EMPRESA_COBRANZA	///////////////////
			case 'AJAX_COD_EMPRESA_COBRANZA' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_EMPRESA_COBRANZA
							FROM	EMPRESA_COBRANZA
							WHERE	COD_EMPRESA_COBRANZA = '{PARAM1}'";
				}
				break;
			///////////////		AJAX_NOM_EMPRESA_COBRANZA	///////////////////
			case 'AJAX_NOM_EMPRESA_COBRANZA' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	NOM_EMPRESA_COBRANZA
							FROM	EMPRESA_COBRANZA
							WHERE	NOM_EMPRESA_COBRANZA = Upper('{PARAM1}')";
				}
				break;
			
			
			/////////////////    ERROR    ///////////
			default:
				base::error("sql_empresa_cobranza::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;	
	}
}
?>