<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_tipo_descuento extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			
			/////////////   INPUT   //////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= 	"SELECT 	COD_TIPO_DESCUENTO 
										,NOM_TIPO_DESCUENTO
										,PORC_DESCUENTO
							FROM 		TIPO_DESCUENTO
							WHERE 		COD_TIPO_DESCUENTO = {KEY1}
							ORDER BY 	COD_TIPO_DESCUENTO";
				}
			break;
			
			////////////   OUTPUT /////////////////////
			case 'OUTPUT' :
					if (K_TIPO_BD=="oci"  || K_TIPO_BD=="mssql"){
					$sql= 	"SELECT 	COD_TIPO_DESCUENTO
              							,NOM_TIPO_DESCUENTO
              							,PORC_DESCUENTO
        					FROM 		TIPO_DESCUENTO
							ORDER BY 	COD_TIPO_DESCUENTO";
				}
			break;
			
			///////////////		AJAX_COD_TIPO_DESCUENTO	///////////////////
			case 'AJAX_COD_TIPO_DESCUENTO' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_TIPO_DESCUENTO
							FROM	TIPO_DESCUENTO
							WHERE	COD_TIPO_DESCUENTO = '{PARAM1}'";
				}
				break;

			
			
			/////////////////    ERROR    ///////////
			default:
				base::error("sql_tipo_descuento::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;	
	}
}
?>