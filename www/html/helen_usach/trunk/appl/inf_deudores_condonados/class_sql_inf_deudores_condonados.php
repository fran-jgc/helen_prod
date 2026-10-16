<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_inf_deudores_condonados extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT   COD_CONDONACION     
								  ,To_Char(FECHA_CONDONACION,'dd/mm/yyyy') FECHA_CONDONACION 
								  ,RUT							      
								  ,NOMBRE                 
								  ,APELLIDO_PATERNO       
								  ,APELLIDO_MATERNO       
								  ,NOM_TIPO_CONDONACION
								  ,TIPO_DEUDA   
								  ,MONTO_CONDONACION
								  ,TOTAL_MONTO_CONDONACION                  
					    FROM 	 TEMP_DEUDORES_CONDONADOS
					    ORDER BY COD_CONDONACION";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "";
				}
			break;
			
			case 'PARAMETRO_CONDONACION':
				if (K_TIPO_BD=="oci"){
					$sql = "select  '01/01/{PARAM1}' FECHA_INICIO
									,'31/12/{PARAM1}' FECHA_TERMINO
									,'' R_CONDONACION
									,'' TODAS
									,'' TIPO_CONDONACION
				  		from  		DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select  '01/01/{PARAM1}' FECHA_INICIO
									,'31/12/{PARAM1}' FECHA_TERMINO
									,'' R_CONDONACION
									,'' TODAS
									,'' TIPO_CONDONACION";
				}
			break;
			
			case 'DD_TIPO_CONDONACION':
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT COD_TIPO_CONDONACION
					   			  ,NOM_TIPO_CONDONACION 
							FROM TIPO_CONDONACION
							UNION 
							SELECT 0 COD_TIPO_CONDONACION
					   			  ,'TODAS' NOM_TIPO_CONDONACION
					   		from dual
					   		order by COD_TIPO_CONDONACION
							";
				}
			break;
			
			///////////////////		ERROR		///////////////////////////////
			default:
			base::error("sql_inf_deudores_con_excedentes::get_sql, selector: '$selector_sql' no definido");						
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>