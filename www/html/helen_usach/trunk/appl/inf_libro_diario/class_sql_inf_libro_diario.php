<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_inf_libro_diario extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "select NRO_COMPROBANTE
							  ,to_char(FECHA_COMPROBANTE, 'dd/mm/yyyy') FECHA_COMPROBANTE
							  ,NOM_TIPO_COMPROBANTE
							  ,COD_CUENTA_CONTABLE
							  ,NOM_CUENTA_CONTABLE
							  ,MONTO_DEBE
							  ,MONTO_HABER
							  ,NOM_ESTADO_COMPROBANTE
							  ,NOM_COMPROBANTE
						from TEMP_LIBRO_DIARIO
						order by FECHA_COMPROBANTE asc, NRO_COMPROBANTE asc";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "exec spi_libro_diario";
				}
				break;
				case 'FECHA' :
				if (K_TIPO_BD=="oci"){
					$sql = "select  '01/01/{PARAM1}' FECHA_INICIO
									,'31/12/{PARAM1}' FECHA_TERMINO
									,1 COD_FAMILIA_DEUDA
									,'S' APROBADO
									,'N' PENDIENTE
							from DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select  '01/01/{PARAM1}' FECHA_INICIO
									,'31/12/{PARAM1}' FECHA_TERMINO
									,1 COD_FAMILIA_DEUDA
									,'S' APROBADO
									,'N' PENDIENTE";
				}
				break;
				case 'DD_COD_FAMILIA_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select COD_FAMILIA_DEUDA
							 , NOM_FAMILIA_DEUDA 
					from 	 FAMILIA_DEUDA 
					order by COD_FAMILIA_DEUDA";
				}
				break;		
				////////////////////			ERROR		//////////////////////////////////
				default:
				base::error("sql_inf_libro_diario::get_sql, selector: '$selector_sql' no definido");			
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>