<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_cuenta_corriente extends sql_tabla {
	static function get_sql($selector_sql) {
		switch ($selector_sql) {
			/////////////		INPUT			//////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 	COD_CUENTA_CORRIENTE
									,NOM_CUENTA_CORRIENTE
									,COD_FAMILIA_DEUDA
									,COD_CUENTA_CONTABLE
									,COD_BANCO
									,NRO_CUENTA_CORRIENTE														 						
							FROM	CUENTA_CORRIENTE
							WHERE 	COD_CUENTA_CORRIENTE = {KEY1}";
				}
				break;
					
				//////////////////		OUTPUT		/////////////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT COD_CUENTA_CORRIENTE
								,NOM_CUENTA_CORRIENTE
								,NRO_CUENTA_CORRIENTE																				 						
					FROM 		CUENTA_CORRIENTE			
					ORDER BY 	COD_CUENTA_CORRIENTE";
				}
				break;
					
				////////////////		DD_COD_FAMILIA_DEUDA		////////////////
			case 'DD_COD_FAMILIA_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT COD_FAMILIA_DEUDA
								,NOM_FAMILIA_DEUDA
					FROM	 	FAMILIA_DEUDA
					ORDER BY 	COD_FAMILIA_DEUDA";
				}
				break;
					
				//////////////////		DD_COD_CUENTA_CONTABLE			////////////////////
			case 'DD_COD_CUENTA_CONTABLE' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT   COD_CUENTA_CONTABLE
									, To_Char(COD_CUENTA_CONTABLE) || '  ' || NOM_CUENTA_CONTABLE 
						FROM 		CUENTA_CONTABLE 
						ORDER BY 	COD_CUENTA_CONTABLE";
				}
				else if(K_TIPO_BD=="mssql"){
					$sql=	"SELECT		COD_CUENTA_CONTABLE
										,CAST(COD_CUENTA_CONTABLE AS VARCHAR)+'  '+NOM_CUENTA_CONTABLE 
							FROM 		CUENTA_CONTABLE 
							ORDER BY	COD_CUENTA_CONTABLE";
				}
				break;
					
				//////////////////////		DD_COD_BANCO		////////////////////////
			case 'DD_COD_BANCO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT COD_BANCO,
								NOM_BANCO
					FROM 	 	BANCO
					ORDER BY	NOM_BANCO";
				}

				break;
				///////////////		AJAX_NOM_CTA_CORRIENTE	///////////////////
			case 'AJAX_NOM_CTA_CORRIENTE' :
				/////////////////  ORACLE - O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT	NOM_CUENTA_CORRIENTE
							FROM	CUENTA_CORRIENTE
							WHERE	NOM_CUENTA_CORRIENTE = '{PARAM1}'";
				}
				break;
				/////////////////////		ERROR		////////////////////////
			default:
				base::error("sql_cuenta_corriente::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>