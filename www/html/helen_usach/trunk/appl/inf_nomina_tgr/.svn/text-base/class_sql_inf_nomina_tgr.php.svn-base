<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_inf_nomina_tgr extends sql_tabla {
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
			case 'INPUT_MONTO_MINIMO' :
				if(K_TIPO_BD == "oci"){
					$sql = "SELECT	null MONTO_MINIMO
							  FROM	DUAL";
				} 
				else if(K_TIPO_BD == "mssql"){
					$sql = "SELECT	null MONTO_MINIMO";
				}
				break;

			case 'SELECT_TABLA_TEMPORAL' :
				if(K_TIPO_BD == "oci"){
					$sql = "SELECT	 LINEA
							FROM	TEMP_NOMINA_TGR";
				}
				else if(K_TIPO_BD == "mssql"){
					$sql = "EXEC spi_nomina_tgr {PARAM1}";
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