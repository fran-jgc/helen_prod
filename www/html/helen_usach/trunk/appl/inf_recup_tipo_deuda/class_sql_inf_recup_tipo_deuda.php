<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_inf_recup_tipo_deuda_base extends sql_tabla {
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
			case 'INPUT_INF' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	 COD_TIPO_DEUDA
									,MONTO_MORA
									,MONTO_MULTA
									,MONTO_MULTA_TOTAL_PESOS
									,MONTO_CAPITAL_VIGENTE
									,MONTO_INTERES_VIGENTE
									,MONTO_CI_TOTAL_VIGENTE_PESOS
									,MONTO_CAPITAL_FUTURO
									,MONTO_INTERES_FUTURO
									,MONTO_CI_TOTAL_FUTURO_PESOS
									,MONTO_CAPITAL_NO_EXIGIBLE
									,MONTO_INTERES_NO_EXGIGIBLE
									,MONTO_CI_TOTAL_NO_EXGIGIBLE_P
									,MONTO_TOTAL
									,MONTO_TOTAL_PESOS
							FROM 	TEMP_RECUP_TIPO_DEUDA
							ORDER BY COD_TIPO_DEUDA";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "spi_recup_tipo_deuda";
				}
				break;
				
			case 'INPUT_FAMILIA_DEUDA' :
				if (K_TIPO_BD=="oci"){
					$sql = "select  	'01/01/{PARAM1}' FECHA_INICIO
										,'31/12/{PARAM1}' FECHA_TERMINO
										,1 COD_FAMILIA_DEUDA
							from 		DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select  	'01/01/{PARAM1}' FECHA_INICIO
										,'31/12/{PARAM1}' FECHA_TERMINO
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
				////////////////////			ERROR		//////////////////////////////////
				default:
				base::error("sql_inf_recup_tipo_deuda::get_sql, selector: '$selector_sql' no definido");			
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}

/////////////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_sql_inf_recup_tipo_deuda.php";
if (file_exists($file_name))
    require_once($file_name);
else {
    class sql_inf_recup_tipo_deuda extends sql_inf_recup_tipo_deuda_base {
    }
}
?>