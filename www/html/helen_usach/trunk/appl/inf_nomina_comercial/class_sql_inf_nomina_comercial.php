<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_inf_nomina_tgr extends sql_tabla {
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
			case 'INPUT' :
				if(K_TIPO_BD == "oci"){
					$sql = "SELECT '' COD_TIPO_DEUDA
								, '' ANIO
							FROM DUAL";
				}
				else if(K_TIPO_BD == "mssql"){
					$sql = "SELECT '' COD_TIPO_DEUDA
								, '' ANIO";
				}
				break;

			case 'DD_COD_TIPO_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select COD_TIPO_DEUDA
								 , NOM_TIPO_DEUDA 
						from 	 TIPO_DEUDA 
						order by COD_TIPO_DEUDA";
				}
			break;	
				////////////////////			ERROR		//////////////////////////////////
				default:
				base::error("sql_inf_nomina_tgr::get_sql, selector: '$selector_sql' no definido");			
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>


