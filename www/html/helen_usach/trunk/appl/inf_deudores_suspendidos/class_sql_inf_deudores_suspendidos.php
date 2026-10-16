<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_inf_deudores_suspendidos extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			///////////////////		INPUT		/////////////////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT   COD_SUSPENSION   
									,To_Char(FECHA_SUSPENSION,'dd/mm/yyyy') FECHA_SUSPENSION       
									,RUT							      
									,NOMBRE                 
									,APELLIDO_PATERNO       
									,APELLIDO_MATERNO       
									,NOM_TIPO_SUSPENSION    
									,COD_DEUDA              
									,COD_TIPO_DEUDA         
									,ANO_SUSPENSION                    
					    FROM 		 TEMP_DEUDORES_SUSPENDIDOS
					    ORDER BY 	COD_SUSPENSION";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "";
				}
			break;
			//////////////////////		FECHA		///////////////////////////////////////
			case 'FECHA' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT  '01/01/{PARAM1}' FECHA_INICIO
									,'31/12/{PARAM1}' FECHA_TERMINO
						FROM  DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT  '01/01/{PARAM1}' FECHA_INICIO
									,'31/12/{PARAM1}' FECHA_TERMINO";
				}
			break;
			default:	
			base::error("sql_inf_deudores_suspendidos::get_sql, selector: '$selector_sql' no definido");								
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>