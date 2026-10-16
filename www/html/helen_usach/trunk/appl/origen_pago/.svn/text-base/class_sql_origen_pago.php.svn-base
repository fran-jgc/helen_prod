<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_origen_pago extends sql_tabla {
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
				
			///////////////////		INPUT		//////////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT 		COD_ORIGEN_PAGO,
										NOM_ORIGEN_PAGO														 						
							FROM 		ORIGEN_PAGO
							WHERE 		COD_ORIGEN_PAGO = {KEY1}
							ORDER BY 	COD_ORIGEN_PAGO";
				}
				else if(K_TIPO_BD=="mssql"){
					$sql= "SELECT 		COD_ORIGEN_PAGO,
										NOM_ORIGEN_PAGO														 						
							FROM 		ORIGEN_PAGO
							WHERE 		COD_ORIGEN_PAGO = {KEY1}
							ORDER BY 	COD_ORIGEN_PAGO";
						
				}
				break;
				//////////////////		OUTPUT		////////////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT 	COD_ORIGEN_PAGO,
									NOM_ORIGEN_PAGO
        					FROM 		ORIGEN_PAGO
							ORDER BY 	COD_ORIGEN_PAGO";
				}
				else if(K_TIPO_BD=="mssql"){
					$sql =	"SELECT 	COD_ORIGEN_PAGO,
									NOM_ORIGEN_PAGO
        					FROM 		ORIGEN_PAGO
							ORDER BY 	COD_ORIGEN_PAGO";
				}
				break;
			
			 ///////////////		AJAX_COD_ORIGEN_PAGO	///////////////////
			case 'AJAX_COD_ORIGEN_PAGO' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_ORIGEN_PAGO
							FROM	ORIGEN_PAGO
							WHERE	COD_ORIGEN_PAGO = '{PARAM1}'";
				}
				break;
				///////////////		AJAX_NOM_ORIGEN_PAGO	///////////////////
			case 'AJAX_NOM_ORIGEN_PAGO' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	NOM_ORIGEN_PAGO
							FROM	ORIGEN_PAGO
							WHERE	NOM_ORIGEN_PAGO = '{PARAM1}'";
				}
				break;
				///////////////////		ERROR		////////////////////////
			default:
				base::error("sql_origen_pago::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>