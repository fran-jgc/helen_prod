<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_componente_deuda extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			
			//////////////////		INPUT		//////////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 		COD_COMPONENTE_DEUDA 
										,NOM_COMPONENTE_DEUDA														 						
							FROM 		COMPONENTE_DEUDA
							WHERE 		COD_COMPONENTE_DEUDA = {KEY1}";
				}
			break;
			
			///////////////////		OUTPUT		////////////////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 		COD_COMPONENTE_DEUDA
              							,NOM_COMPONENTE_DEUDA		   			   
        					FROM 		COMPONENTE_DEUDA
							ORDER BY 	COD_COMPONENTE_DEUDA";
				}
			break;
			
			///////////////////		DD_NOM_COMPONENTE_DEUDA		////////////////////////
			case 'DD_NOM_COMPONENTE_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 		COD_COMPONENTE_DEUDA
              							,NOM_COMPONENTE_DEUDA		   			   
        					FROM 		COMPONENTE_DEUDA
							ORDER BY 	COD_COMPONENTE_DEUDA";
				}
			break;
			
			////////////////////		ERROR		////////////////////////
			default:
				base::error("sql_componente_deuda::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>