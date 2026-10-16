<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_cuenta_contable extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			
			////////////////////		INPUT	////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 		COD_CUENTA_CONTABLE
				      					, COD_TIPO_CUENTA_CONTABLE
				      					, NOM_CUENTA_CONTABLE										 						
							FROM 		CUENTA_CONTABLE
							WHERE 		COD_CUENTA_CONTABLE = {KEY1}";
				}
			break;
			
			////////////////////		OUTPUT		///////////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= 	"SELECT 	COD_CUENTA_CONTABLE, 
      				 					NOM_CUENTA_CONTABLE  
      	    	  			FROM 		CUENTA_CONTABLE
   				 			ORDER BY 	COD_CUENTA_CONTABLE ASC";
				}
			break;
			
			/////////////////////		DD_COD_TIPO_CUENTA_CONTABLE		//////
			case 'DD_COD_TIPO_CUENTA_CONTABLE' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= 	"SELECT 	COD_TIPO_CUENTA_CONTABLE
										, NOM_TIPO_CUENTA_CONTABLE 
							FROM 		TIPO_CUENTA_CONTABLE";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT COD_TIPO_CUENTA_CONTABLE
									, NOM_TIPO_CUENTA_CONTABLE 
						FROM 		TIPO_CUENTA_CONTABLE";
				}
			break;
			///////////////		AJAX_COD_CTA_CONTABLE	///////////////////
			case 'AJAX_COD_CTA_CONTABLE' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_CUENTA_CONTABLE
							FROM	CUENTA_CONTABLE
							WHERE	COD_CUENTA_CONTABLE = '{PARAM1}'";
				}
				break;
			///////////////		AJAX_NOM_CTA_CONTABLE	///////////////////
			case 'AJAX_NOM_CTA_CONTABLE' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	NOM_CUENTA_CONTABLE
							FROM	CUENTA_CONTABLE
							WHERE	NOM_CUENTA_CONTABLE = '{PARAM1}'";
				}
				break;
			
			default:
				base::error("sql_cuenta_contable::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>
