<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_inf_morosidad_ano_base extends sql_tabla {
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	 ANO            
									,CANT_DEUDOR              
									,MONTO_MORA
									,MONTO_MORA_PESOS
									,MONTO_MULTA
									,MONTO_MULTA_PESOS
									,MONTO_TOTAL
									,MONTO_TOTAL_PESOS
							FROM	TEMP_MOROSIDAD_ANO
							ORDER BY	ANO";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "spi_morosidad_ano";
				}
				break;
				
			case 'INPUT_ANO' :
				if (K_TIPO_BD=="oci"){
					$sql = "select  '' COD_TIPO_DEUDA
									,To_Char(SYSDATE,'yyyy') ANO_INICIAL
									,To_Char(SYSDATE,'yyyy') ANO_FINAL
							from 	DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select  '' COD_TIPO_DEUDA
									,year(getdate()) ANO_INICIAL
									,year(getdate()) ANO_FINAL";
				}
				break;				
				
			case 'INPUT_DD_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select COD_TIPO_DEUDA
									, COD_TIPO_DEUDA COD_TIPO_DEUDA_COD
							from 	 TIPO_DEUDA 
							order by COD_TIPO_DEUDA";
				}
				break;
				////////////////////			ERROR		//////////////////////////////////
				default:
				base::error("sql_inf_morosidad_ano::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
		}
}
/////////////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_sql_inf_morosidad_ano.php";
if (file_exists($file_name))
    require_once($file_name);
else {
    class sql_inf_morosidad_ano extends sql_inf_morosidad_ano_base {
    }
}
?>