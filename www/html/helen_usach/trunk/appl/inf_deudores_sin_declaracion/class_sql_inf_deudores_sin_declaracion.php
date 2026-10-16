<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_inf_deudores_sin_declaracion extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT RUT						
							  	,NOMBRE                 
							    ,APELLIDO_PATERNO       
							  	,APELLIDO_MATERNO 
							  	,DIRECCION
							  	,COMUNA
							  	,CIUDAD      
							  	,TELEFONO               
							  	,EMAIL                  
							  	,CELULAR
							  	,NOM_CARTERA                    
					    		FROM TEMP_DEUDORES_SIN_DECLARACION
					    ORDER BY RUT";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "";
				}
			break;
			case 'FECHA' :
				if (K_TIPO_BD=="oci"){
					$sql = "select To_Char(to_number(To_Char(SYSDATE, 'yyyy')-1)) ANO
								 ,1 COD_FAMILIA_DEUDA
				  		          from   DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select   YEAR(GETDATE())-1 ANO
									,1 COD_FAMILIA_DEUDA";
				}
			break;
			case 'INPUT_DD_FAMILIA_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select 		COD_FAMILIA_DEUDA, 
										NOM_FAMILIA_DEUDA 
							from 		FAMILIA_DEUDA 
							order by 	COD_FAMILIA_DEUDA";			
				}
				break;
			///////////////////		ERROR		///////////////////////////////
			default:
			base::error("sql_inf_deudores_sin_declaracion::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>