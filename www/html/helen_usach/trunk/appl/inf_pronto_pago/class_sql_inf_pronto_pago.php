<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_inf_pronto_pago_base extends sql_tabla {
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT   RUT      
									,To_Char(FECHA_PAGO,'dd/mm/yyyy') FECHA_PAGO           
									,COD_TIPO_DEUDA          
									,MONTO_ABONO_NO_EXIGIBLE
									,PESOS_ABONO_NO_EXIGIBLE
									,MONTO_ABONO_FUTURO
									,PESOS_ABONO_FUTURO
									,MONTO_DSCTO_NO_EXIGIBLE
									,PESOS_DSCTO_NO_EXIGIBLE
									,MONTO_DSCTO_FUTURO
									,PESOS_DSCTO_FUTURO
									,MONTO_TOTAL
									,MONTO_TOTAL_PESOS               
							FROM 	TEMP_PRONTO_PAGO
							ORDER BY	RUT";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "spi_pronto_pago";
				}
				break;
			
			case 'INPUT_FECHA' :
				if (K_TIPO_BD=="oci"){
					$sql = "select '' COD_TIPO_DEUDA
									,'01/01/{PARAM1}' FECHA_INICIAL
									,'31/12/{PARAM1}' FECHA_FINAL
							from DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select '' COD_TIPO_DEUDA
									,'01/01/{PARAM1}' FECHA_INICIAL
									,'31/12/{PARAM1}' FECHA_FINAL";
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
				base::error("sql_inf_pronto_pago::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
/////////////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_sql_inf_pronto_pago.php";
if (file_exists($file_name))
    require_once($file_name);
else {
    class sql_inf_pronto_pago extends sql_inf_pronto_pago_base {
    }
}
?>