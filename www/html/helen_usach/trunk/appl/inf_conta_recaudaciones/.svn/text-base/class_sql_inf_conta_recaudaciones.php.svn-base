<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_inf_conta_recaudaciones extends sql_tabla {
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
			//////////////////FECHA		//////////////////
			case 'FECHA' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	To_Char(SYSDATE,'YYYY') ANO
									,'' COD_TIPO_DEUDA
							FROM 	DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	DATEPART(YYYY, getdate()) ANO
									,'' COD_TIPO_DEUDA";
				}
			break;
			//////////////////////		DD_TIPO_DEUDA	///////////////////
			case 'DD_TIPO_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT		COD_TIPO_DEUDA 
							, COD_TIPO_DEUDA TIPO_DEUDA
							, NOM_TIPO_DEUDA
				FROM 		TIPO_DEUDA
				ORDER BY	NOM_TIPO_DEUDA";
				}
			break;
			////////////////////			ERROR		//////////////////////////////////
			default:
			base::error("wi_inf_conta_recaudacione::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>