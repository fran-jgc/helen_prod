<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_inf_periodo_gracia extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT   RUT
									,ANO
									,FECHA_INICIO_COBRO
									,MONTO_INICIO_COBRO
									,MONTO_MORA
									,MONTO_MORA_PESOS
									,MONTO_MULTA
									,MONTO_MULTA_PESOS
							FROM 	TEMP_PERIODO_GRACIA
							ORDER BY RUT";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "spi_periodo_gracia";
				}
			break;
			case 'INPUT_CONTROL' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	 'CS' COD_TIPO_DEUDA 
									,To_Char(SYSDATE,'yyyy') ANO
							FROM	DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = " SELECT  'CS' COD_TIPO_DEUDA 
									,year(getdate()) ANO";
				}
			break;
			case 'DD_COD_TIPO_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select COD_TIPO_DEUDA
								 , COD_TIPO_DEUDA NOM_TIPO_DEUDA 
						from 	 TIPO_DEUDA 
						order by COD_TIPO_DEUDA";
				}
			break;		
			////////////////////			ERROR		//////////////////////////////////
			default:
			base::error("sql_inf_periodo_gracia::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>