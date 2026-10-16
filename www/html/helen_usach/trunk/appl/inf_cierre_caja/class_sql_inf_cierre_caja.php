<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_inf_cierre_caja extends sql_tabla {
	static function get_sql($selector_sql) {
		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "";
				}
				break;
			
			case 'ORIGEN' :
				if (K_TIPO_BD=="oci"){
					$sql = "";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "	 SELECT  '' COD_ORIGEN_PAGO
										,CONVERT(VARCHAR(2),'01')+SUBSTRING(CONVERT(varchar(10), getdate(),103),3,8) FECHA_INICIAL
										,CONVERT(VARCHAR(2),dbo.f_ultimo_dia(MONTH(GETDATE()), YEAR(GETDATE())))+SUBSTRING(CONVERT(varchar(10), getdate(),103),3,8) FECHA_FINAL";
				}
				break;				
				
			case 'DD_COD_ORIGEN_PAGO':
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT COD_ORIGEN_PAGO
									, NOM_ORIGEN_PAGO
							FROM 	 ORIGEN_PAGO 
							WHERE	COD_ORIGEN_PAGO IN(2,6)
							ORDER BY COD_ORIGEN_PAGO";
				}
				break;
				////////////////////			ERROR		//////////////////////////////////
				default:
				base::error("sql_inf_credito_asig::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
		}
}
?>