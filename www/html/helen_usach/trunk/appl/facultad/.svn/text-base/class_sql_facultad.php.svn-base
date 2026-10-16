<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_facultad extends sql_tabla {
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
			/////////////////  INPUT ///////////////
			case 'INPUT' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select  F.COD_FACULTAD,
									F.NOM_FACULTAD
							 from 	FACULTAD F
							where 	F.COD_FACULTAD = {KEY1}";
				}
			break;
			/////////////////  OUTPUT ///////////////
			case 'OUTPUT' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 		F.COD_FACULTAD,
	               						F.NOM_FACULTAD		   			   
	        				FROM 		FACULTAD F
                  			ORDER BY 	 COD_FACULTAD";
				}
			break;
			/////////////////  AJAX_NOM_FACULTAD	///////////////			
			case 'AJAX_NOM_FACULTAD' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT	COD_FACULTAD
									,NOM_FACULTAD 
						   FROM		FACULTAD
						   WHERE 	COD_FACULTAD = {PARAM1}
						   ORDER BY COD_FACULTAD";
				}
			break;
			
			/////////////////  ERROR ///////////////
			default: 
			base::error("sql_facultad::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>