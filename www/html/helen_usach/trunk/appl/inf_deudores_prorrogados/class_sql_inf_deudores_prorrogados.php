<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_inf_deudores_prorrogados extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT   COD_PRORROGA        
								  ,To_Char(FECHA_PRORROGA,'dd/mm/yyyy') FECHA_PRORROGA         
								  ,RUT						
								  ,NOMBRE                 
								  ,APELLIDO_PATERNO       
								  ,APELLIDO_MATERNO       
								  ,COD_DEUDA               
								  ,COD_TIPO_DEUDA            
					    FROM 	 TEMP_DEUDORES_PRORROGADOS
					    ORDER BY COD_PRORROGA";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "";
				}
			break;
			case 'FECHA' :
				if (K_TIPO_BD=="oci"){
					$sql = "select '01/01/{PARAM1}' FECHA_INICIO
									,'31/12/{PARAM1}' FECHA_TERMINO
				 			from  	DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select '01/01/{PARAM1}' FECHA_INICIO
									,'31/12/{PARAM1}' FECHA_TERMINO";
				}
			break;
			///////////////////		ERROR		///////////////////////////////
			default:
			base::error("sql_inf_deudores_prorrogados::get_sql, selector: '$selector_sql' no definido");					
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>