<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_inf_balance_ocho extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT  COD_CUENTA_CONTABLE			
								,NOM_CUENTA_CONTABLE					
								,MONTO_DEBE               	
								,MONTO_HABER              	
								,SALDO_DEBITO				
								,SALDO_CREDITO				
								,ACTIVO						
								,PASIVO						
								,PERDIDA					
								,UTILIDAD
					 	FROM TEMP_BALANCE_OCHO
						ORDER BY COD_CUENTA_CONTABLE";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "exec spi_balance_ocho";
				}
			break;
			case 'FECHA' :
				if (K_TIPO_BD=="oci"){
					$sql = "select  To_Char(SYSDATE,'DD/MM/YYYY') FECHA
								,1 COD_FAMILIA_DEUDA
								,'S' APROBADO
								,'N' PENDIENTE
						from DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select  convert(varchar(10), getdate(), 103) FECHA
								,1 COD_FAMILIA_DEUDA
								,'S' APROBADO
								,'N' PENDIENTE";
				}
			break;
			case 'DD_COD_FAMILIA_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select COD_FAMILIA_DEUDA
								  ,NOM_FAMILIA_DEUDA 
							from   FAMILIA_DEUDA 
							order by COD_FAMILIA_DEUDA";
				}
			break;
			////////////////////			ERROR		//////////////////////////////////
			default:
			base::error("sql_inf_balance_ocho::get_sql, selector: '$selector_sql' no definido");			
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>