<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_concepto_cobro extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			
			//////////////////			INPUT		///////////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 		COD_CONCEPTO_COBRO
										,NOM_CONCEPTO_COBRO														 						
							FROM 		CONCEPTO_COBRO
							WHERE 		COD_CONCEPTO_COBRO = {KEY1}";
				}
			break;
			
			///////////////////////		OUTPUT		/////////////////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 		COD_CONCEPTO_COBRO
										,NOM_CONCEPTO_COBRO
        					FROM 		CONCEPTO_COBRO
							ORDER BY 	COD_CONCEPTO_COBRO";
				}
			break;
			
			//////////////////////			ERROR		/////////////////////////
			default:
				base::error("sql_concepto_cobro::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>