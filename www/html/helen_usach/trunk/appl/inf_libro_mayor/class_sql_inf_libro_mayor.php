<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_inf_libro_mayor extends sql_tabla {
	const K_ESTADO_APROBADO	 = 2;
	const K_ESTADO_PENDIENTE = 1;
	
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "select COD_TEMP_LIBRO_MAYOR 
								,NRO_COMPROBANTE 
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
								,REPLACE(TO_CHAR(INICIAL_SALDO, '99,999,999'), ',', '.') INICIAL_SALDO 
						from TEMP_LIBRO_MAYOR
						order by FECHA_COMPROBANTE asc, NRO_COMPROBANTE asc";
					
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "exec spi_libro_mayor";
				}
				break;
				
			case 'INPUT_CONTABLE' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT		Min(COD_CUENTA_CONTABLE) COD_CUENTA_INICIO,
					   					Max(COD_CUENTA_CONTABLE) COD_CUENTA_TERMINO 
				  			FROM 		CUENTA_CONTABLE";
				}
				break;	
			case 'INPUT_PARAM' :
				if (K_TIPO_BD=="oci"){
					$sql = "select  '01/01/{PARAM1}' FECHA_INICIO
									,'31/12/{PARAM1}' FECHA_TERMINO
									,{PARAM2} COD_CUENTA_INICIO
									,{PARAM3} COD_CUENTA_TERMINO
									,1 COD_FAMILIA_DEUDA
									,'S' APROBADO
									,'N' PENDIENTE
									,'N' ANALITICO
							from 	DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select  '01/01/{PARAM1}' FECHA_INICIO
									,'31/12/{PARAM1}' FECHA_TERMINO
									,{PARAM2} COD_CUENTA_INICIO
									,{PARAM3} COD_CUENTA_TERMINO
									,1 COD_FAMILIA_DEUDA
									,'S' APROBADO
									,'N' PENDIENTE
									,'N' ANALITICO";
					
				}
				break;			

			case 'INPUT_DD_CUENTA' :
				if (K_TIPO_BD=="oci"){
					$sql = "select 		COD_CUENTA_CONTABLE, 
										To_Char(COD_CUENTA_CONTABLE || ' ' || NOM_CUENTA_CONTABLE) 
							from 		CUENTA_CONTABLE 
							order by 	COD_CUENTA_CONTABLE";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select 		COD_CUENTA_CONTABLE, 
										convert(varchar(10), COD_CUENTA_CONTABLE) + ' ' + NOM_CUENTA_CONTABLE 
							from 		CUENTA_CONTABLE 
							order by 	COD_CUENTA_CONTABLE";
				}
				break;	

			case 'INPUT_DD_CUENTA_TNO' :
				if (K_TIPO_BD=="oci"){
					$sql = "select 		COD_CUENTA_CONTABLE, 
										To_Char(COD_CUENTA_CONTABLE || ' ' || NOM_CUENTA_CONTABLE) 
							from 		CUENTA_CONTABLE 
							order by 	COD_CUENTA_CONTABLE";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select 		COD_CUENTA_CONTABLE, 
										convert(varchar(10), COD_CUENTA_CONTABLE) + ' ' + NOM_CUENTA_CONTABLE 
							from 		CUENTA_CONTABLE 
							order by 	COD_CUENTA_CONTABLE";
				}
				break;				

			case 'INPUT_DD_COD_FAN' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select 		COD_FAMILIA_DEUDA, 
										NOM_FAMILIA_DEUDA 
							from 		FAMILIA_DEUDA 
							order by 	COD_FAMILIA_DEUDA";
				}
				break;

			case 'INPUT_ESTADO_APROBADO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT NOM_ESTADO_COMPROBANTE
							FROM ESTADO_COMPROBANTE 
							WHERE COD_ESTADO_COMPROBANTE = ".self::K_ESTADO_APROBADO;
				}
				break;

			case 'INPUT_ESTADO_PENDIENTE' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT NOM_ESTADO_COMPROBANTE
							FROM ESTADO_COMPROBANTE 
							WHERE COD_ESTADO_COMPROBANTE = ".self::K_ESTADO_PENDIENTE;
				}
				break;
				////////////////////			ERROR		//////////////////////////////////
				default:
				base::error("sql_inf_libro_mayor::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>