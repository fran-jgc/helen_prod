<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_plaza extends sql_tabla {
	
	static function get_sql($selector_sql) {
			switch ($selector_sql) {
				/////////////////   INPUT   ///////////////
				case 'INPUT' :
					/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
					if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
							$sql = "select 		COD_PLAZA, 
												NOM_PLAZA														 						
									from 		PLAZA
									where 		COD_PLAZA = {KEY1}";
					}
				break;
				
				/////////////////   OUTPUT   ///////////////
				case 'OUTPUT' :
					/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
					if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
							$sql = "select 		COD_PLAZA,
												NOM_PLAZA			   			   
									from 		PLAZA
									order by 	COD_PLAZA";
					}
				break;
				///////////////		AJAX_COD_PLAZA	///////////////////
				case 'AJAX_COD_PLAZA' :
					/////////////////  ORACLE - O - SQL-SERVER  ///////////////
					if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
						$sql = "SELECT	COD_PLAZA
								FROM	PLAZA
								WHERE	COD_PLAZA = '{PARAM1}'";
					}
					break;
				///////////////		AJAX_NOM_PLAZA	///////////////////
				case 'AJAX_NOM_PLAZA' :
					/////////////////  ORACLE - O - SQL-SERVER  ///////////////
					if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
						$sql = "SELECT	NOM_PLAZA
								FROM	PLAZA
								WHERE	NOM_PLAZA = '{PARAM1}'";
					}
					break;
					/////////////////  ERROR ///////////////
					default: 
					base::error("sql_plaza::get_sql, selector: '$selector_sql' no definido");
			}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>