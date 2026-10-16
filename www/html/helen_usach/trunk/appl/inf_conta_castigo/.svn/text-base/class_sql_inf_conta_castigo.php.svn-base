<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_inf_conta_castigo extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "";
					//aqui se ejecuta el spi
				}
			break;
			//////////////////FECHA		//////////////////
			case 'FECHA' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	To_Char(SYSDATE,'YYYY') ANO
									,1 COD_FAMILIA_DEUDA
									,'TODAS' COD_TIPO_DEUDA
									,'N' CASTIGO_AGOSTO
							FROM 	DUAL";
				}
			break;
			//////////////////////		DD_FAMILIA_DEUDA	///////////////////
			case 'DD_FAMILIA_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	COD_FAMILIA_DEUDA
									,NOM_FAMILIA_DEUDA
							FROM 	FAMILIA_DEUDA
							where cod_familia_deuda = 1
							ORDER BY COD_FAMILIA_DEUDA";
				}
			break;
			//////////////////////		DD_TIPO_DEUDA	///////////////////
			case 'DD_TIPO_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	'TODAS' COD_TIPO_DEUDA
								   ,'TODAS' NOM_TIPO_DEUDA
							from dual
							union
							SELECT	COD_TIPO_DEUDA
								   ,COD_TIPO_DEUDA NOM_TIPO_DEUDA
							FROM 	TIPO_DEUDA
							where cod_familia_deuda = 1
							ORDER BY COD_TIPO_DEUDA";
				}
			break;
			////////////////////			ERROR		//////////////////////////////////
			default:
			base::error("sql_inf_conta_castigo::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>