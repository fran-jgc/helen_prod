<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_carrera extends sql_tabla {
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
				
			///////////////////		INPUT		//////////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT 		C.COD_CARRERA,
										C.NOM_CARRERA,
										F.COD_FACULTAD														 						
							FROM 		CARRERA C, FACULTAD F
							WHERE 		C.COD_CARRERA = {KEY1}
							AND			F.COD_FACULTAD (+) = C.COD_FACULTAD
							ORDER BY 	C.COD_CARRERA";
				}
				else if(K_TIPO_BD=="mssql"){
					$sql= "SELECT 		C.COD_CARRERA,
										C.NOM_CARRERA,
										F.COD_FACULTAD														 						
							FROM 		CARRERA C LEFT OUTER JOIN FACULTAD F ON F.COD_FACULTAD = C.COD_FACULTAD
							WHERE 		C.COD_CARRERA = {KEY1}
							ORDER BY 	C.COD_CARRERA";
						
				}
				break;
				//////////////////		OUTPUT		////////////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT 		C.COD_CARRERA,
										C.NOM_CARRERA,
										F.NOM_FACULTAD
        					FROM 		CARRERA C, FACULTAD F
        					WHERE		C.COD_FACULTAD = F.COD_FACULTAD (+)
							ORDER BY 	COD_CARRERA";
				}
				else if(K_TIPO_BD=="mssql"){
					$sql =	"SELECT		C.COD_CARRERA,
										C.NOM_CARRERA,
										F.NOM_FACULTAD
							FROM 		CARRERA C LEFT OUTER JOIN FACULTAD F ON C.COD_FACULTAD = F.COD_FACULTAD
							ORDER BY	COD_CARRERA";
				}
				break;
					
				///////////////		DD_FACULTAD_OUTPUT	///////////////////
			case 'DD_FACULTAD_OUTPUT' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_FACULTAD
									,NOM_FACULTAD
							FROM	FACULTAD
							ORDER BY NOM_FACULTAD";
				}
				break;
					
			case 'DD_FACULTAD' :
				if (K_TIPO_BD=="oci"|| K_TIPO_BD=="mssql"){
					$sql= "SELECT   COD_FACULTAD
									,NOM_FACULTAD
						FROM 		FACULTAD
						ORDER BY 	COD_FACULTAD";
				}
				break;
					
				///////////////		AJAX_COD_CARRERA	///////////////////
			case 'AJAX_COD_CARRERA' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_CARRERA
							FROM	CARRERA
							WHERE	COD_CARRERA = '{PARAM1}'";
				}
				break;
				///////////////		AJAX_NOM_CARRERA	///////////////////
			case 'AJAX_NOM_CARRERA' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	NOM_CARRERA
							FROM	CARRERA
							WHERE	NOM_CARRERA = '{PARAM1}'";
				}
				break;
				///////////////////		ERROR		////////////////////////
			default:
				base::error("sql_carrera::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>