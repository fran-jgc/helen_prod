<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_inf_pagares_protestados extends sql_tabla {
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
			case 'INPUT_INF' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	 RUT
									,APELLIDO_PATERNO
									,APELLIDO_MATERNO
									,NOMBRE
									,DIRECCION
									,NOM_COMUNA
									,NOM_CIUDAD
									,NRO_DEUDA
									,FOLIO_PAGARE
									,MONEDA
									,MONTO
									,MONTO_PESOS
							FROM	TEMP_PAGARES_PROTESTADOS";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "spi_pagares_protestados";
				}
				break;
				
			case 'INPUT_FECHA_BUSQUEDA' :
				if (K_TIPO_BD=="oci"){
					$sql = "select  	'01/01/{PARAM1}' FECHA_INICIO
										,TO_CHAR(SYSDATE,'dd/mm/yyyy') FECHA_TERMINO
							from 		DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select  	'01/01/{PARAM1}' FECHA_INICIO
										,CONVERT(VARCHAR(10),GETDATE(),103) FECHA_TERMINO";
				}
				break;
				////////////////////			ERROR		//////////////////////////////////
				default:
				base::error("sql_inf_pagares_protestados::get_sql, selector: '$selector_sql' no definido");			
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>