<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_inf_por_tipo_docto extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "No implementado";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "exec spi_tipo_documento_pago";
				}
				break;
				////////////////////// FECHA //////////////////////
				case 'FECHA' :
				if (K_TIPO_BD=="oci"){
					$sql = "No implementado";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select  convert(varchar(20), getdate(), 103) FECHA_INICIO
									,convert(varchar(20), getdate(), 103) FECHA_TERMINO
									,'' COD_ESTADO_DOCUMENTO_PAGO
									,'' COD_TIPO_DOCUMENTO_PAGO";
				}
				break;
				////////////////////// DD_COD_ESTADO_DOCUMENTO_PAGO //////////////////////
				case 'DD_COD_ESTADO_DOCUMENTO_PAGO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select 	COD_ESTADO_DOCUMENTO_PAGO
							 		, NOM_ESTADO_DOCUMENTO_PAGO 
							from 	 ESTADO_DOCUMENTO_PAGO
							order by COD_ESTADO_DOCUMENTO_PAGO";
				}
				break;
				////////////////////// DD_COD_TIPO_DOCUMENTO_PAGO //////////////////////
				case 'DD_COD_TIPO_DOCUMENTO_PAGO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select 	COD_TIPO_DOCUMENTO_PAGO
							 		, NOM_TIPO_DOCUMENTO_PAGO
							from 	 TIPO_DOCUMENTO_PAGO
							order by COD_TIPO_DOCUMENTO_PAGO";
				}
				break;		
				////////////////////			ERROR		//////////////////////////////////
				default:
				base::error("sql_inf_por_tipo_docto::get_sql, selector: '$selector_sql' no definido");			
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>