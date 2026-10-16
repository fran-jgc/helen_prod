<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_inf_deudor_dia extends sql_tabla {
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "";
				}
				break;
			
			case 'TIPO_DEUDA' :
				if (K_TIPO_BD=="oci"){
					$sql = "select  '' COD_TIPO_DEUDA
							from 	DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select '' COD_TIPO_DEUDA";
				}
				break;				
				
			case 'DD_COD_TIPO_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select COD_TIPO_DEUDA
									, COD_TIPO_DEUDA COD_TIPO_DEUDA_COD
							from 	 TIPO_DEUDA
							union
							select '0' COD_TIPO_DEUDA
      								,'TODOS' COD_TIPO_DEUDA_COD
							order by COD_TIPO_DEUDA";
				}
				break;
				////////////////////			ERROR		//////////////////////////////////
				default:
				base::error("sql_inf_deudor_dia::get_sql, selector: '$selector_sql' no definido");
				
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
		}
}
?>