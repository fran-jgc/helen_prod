<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_inf_documentos_cartera extends sql_tabla {
	const K_EST_SIMULACION = 7;
	const K_EST_ANULADO = 8;
	
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT RUT
							,FECHA_DOC_PAGO
							,NOM_BANCO
							,NOM_PLAZA
							,MONTO
							FROM TEMP_DOCUMENTOS_CATERA
							ORDER BY RUT";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "";
				}
				break;
			
			case 'FECHA' :
				if (K_TIPO_BD=="oci"){
					$sql = "select  to_char(sysdate, 'dd/mm/yyyy') FECHA
									,null RUT
									,1 COD_ESTADO_DOCUMENTO_PAGO
									from dual";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select convert(varchar(10), getdate(), 103) FECHA
									,null RUT
									,1 COD_ESTADO_DOCUMENTO_PAGO";
				}
				break;				
				
			case 'DD_ESTADO_DOCUMENTO_PAGO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select 	COD_ESTADO_DOCUMENTO_PAGO
									,NOM_ESTADO_DOCUMENTO_PAGO
							from 	ESTADO_DOCUMENTO_PAGO
							WHERE COD_ESTADO_DOCUMENTO_PAGO <> ".self::K_EST_SIMULACION."
							  AND COD_ESTADO_DOCUMENTO_PAGO <> ".self::K_EST_ANULADO."
							order by COD_ESTADO_DOCUMENTO_PAGO";
				}
				break;
				////////////////////			ERROR		//////////////////////////////////
				default:
				base::error("sql_inf_documentos_cartera::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
		}
}
?>