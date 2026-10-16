<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_estado_deuda extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			/////////////////		INPUT		////////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 			COD_ESTADO_DEUDA 
											,NOM_ESTADO_DEUDA
							FROM 			ESTADO_DEUDA
							WHERE 			COD_ESTADO_DEUDA = {KEY1}
							ORDER BY 		COD_ESTADO_DEUDA";
				}
			break;
			/////////////////		OUTPUT		/////////////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT			COD_ESTADO_DEUDA
        									,NOM_ESTADO_DEUDA
								FROM 		ESTADO_DEUDA
								ORDER BY 	COD_ESTADO_DEUDA";
				}
			break;
			//////////////////		ERROR		///////////////////////
			default:
			base::error("sql_estado_deuda::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>