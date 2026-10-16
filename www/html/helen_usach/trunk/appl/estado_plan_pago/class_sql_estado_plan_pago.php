<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_estado_plan_pago extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			////////////////		INPUT		//////////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 		COD_ESTADO_PLAN_PAGO 
										,NOM_ESTADO_PLAN_PAGO
							FROM 		ESTADO_PLAN_PAGO
							WHERE 		COD_ESTADO_PLAN_PAGO = {KEY1}";
				}
				break;
				
			///////////////////		OUTPUT		///////////////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT		COD_ESTADO_PLAN_PAGO
        								,NOM_ESTADO_PLAN_PAGO
							FROM 		ESTADO_PLAN_PAGO
							ORDER BY 	COD_ESTADO_PLAN_PAGO";
				}
				break;
				
			////////////////////		ERROR		//////////////////////
			default:
			base::error("sql_estado_plan_pago::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>