<?php
////////////////////////////////////////
/////////////// USACH //////////////////
////////////////////////////////////////

class sql_inf_flujo_svp extends sql_inf_flujo_svp_base {
	static function get_sql($selector_sql) {
		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	 RUT
									,DIG_VERIF
									,COD_DEUDA
									,ANO_INICIO_COBRO
									,ANO_FIN_COBRO
									,ANOS_PACTADOS
									,SALDO_DEUDA
									,MONTO_ANO1
									,MONTO_ANO2
									,MONTO_ANO3
									,MONTO_ANO4
									,MONTO_ANO5
									,MONTO_ANO6
									,MONTO_ANO7
									,MONTO_ANO8
									,MONTO_ANO9
									,MONTO_ANO10	
									,MONTO_ANO11	
									,MONTO_ANO12	
									,MONTO_ANO13	
							FROM	TEMP_FLUJO_SVP
							ORDER BY RUT";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "";
				}
				break;
			default: 
				$sql = sql_inf_flujo_svp_base::get_sql($selector_sql);
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>