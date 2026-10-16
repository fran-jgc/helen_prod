<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_inf_planilla_caja extends sql_tabla {
	const K_ESTADO_APROBADO	 = 2;
	const K_ESTADO_PENDIENTE = 1;
	
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			case 'SQL_REPORTE' :
				if (K_TIPO_BD=="oci"){
					$sql = "select COD_TEMP_LIBRO_MAYOR 
								,COD_COMPROBANTE 
								,to_char(FECHA_COMPROBANTE, 'dd/mm/yyyy') FECHA_COMPROBANTE
								,NOM_COMPROBANTE
								,COD_CUENTA_CONTABLE   
								,NOM_CUENTA_CONTABLE  
								,MONTO_DEBE               	
								,MONTO_HABER              	
								,NOM_ESTADO_COMPROBANTE   
								,DEBITO               	  	
								,CREDITO               		
								,SALDO               		
								,INICIAL_DEBITO         
								,INICIAL_CREDITO           
								,INICIAL_SALDO  
						from TEMP_LIBRO_MAYOR
						order by COD_CUENTA_CONTABLE, COD_COMPROBANTE, COD_TEMP_LIBRO_MAYOR";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "exec spi_planilla_caja";
				}
				break;
				
			
			case 'INPUT_PARAM' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT   TO_CHAR(SYSDATE, 'dd/mm/yyyy') FECHA_INICIO
									,TO_CHAR(SYSDATE, 'dd/mm/yyyy') FECHA_INICIO
							FROM	DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	 CONVERT(VARCHAR(20), GETDATE(), 103) FECHA_INICIO
									,CONVERT(VARCHAR(20), GETDATE(), 103) FECHA_TERMINO";
					
				}
				break;
				////////////////////			ERROR		//////////////////////////////////
				default:
				base::error("sql_inf_planilla_caja::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>