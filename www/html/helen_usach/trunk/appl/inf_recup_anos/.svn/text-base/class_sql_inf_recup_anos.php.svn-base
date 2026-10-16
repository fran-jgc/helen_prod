<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_inf_recup_anos_base extends sql_tabla {
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
		/////////////////		INPUT		//////////////////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci" ){
					$sql= "SELECT  ANO
							      ,MONTO_MORA
							      ,MONTO_MULTA
							      ,MONTO_MULTA_TOTAL_PESOS
							      ,MONTO_CAPITAL_VIGENTE
							      ,MONTO_INTERES_VIGENTE
							      ,MONTO_CI_VIGENTE_PESOS
							      ,MONTO_CAPITAL_FUTURO
							      ,MONTO_INTERES_FUTURO
							      ,MONTO_CI_FUTURO_PESOS
							      ,MONTO_CAPITAL_NO_EXIGIBLE
							      ,MONTO_INTERES_NO_EXGIGIBLE
							      ,MONTO_CI_NO_EXGIGIBLE_PESOS
							      ,MONTO_TOTAL
							      ,MONTO_TOTAL_PESOS
							FROM  TEMP_RECUP_ANOS
							ORDER BY ANO";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "";
				}
			break;
			
			case 'INPUT_TIPO_DEUDA' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT 		COD_TIPO_DEUDA 
							FROM 		TIPO_DEUDA 
							WHERE 		ROWNUM = 1";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT COD_TIPO_DEUDA FROM TIPO_DEUDA WHERE COD_TIPO_DEUDA='CFR'";
				}
				break;
				
			////////////////		OUTPUT		/////////////////////////////////	
			case 'INPUT_ANO' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT  		'' COD_TIPO_DEUDA
										,To_Char(SYSDATE,'yyyy') ANO_INICIAL
										,To_Char(SYSDATE,'yyyy') ANO_FINAL
							FROM DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT  		'' COD_TIPO_DEUDA
										,YEAR(GETDATE()) ANO_INICIAL
										,YEAR(GETDATE()) ANO_FINAL";
				}
				break;			

			case 'INPUT_DD_TIPO_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 	COD_TIPO_DEUDA, NOM_TIPO_DEUDA
							FROM 		TIPO_DEUDA";
				}
				break;
				////////////////////			ERROR		//////////////////////////////////
				default:
				base::error("sql_inf_recup_anos::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}

/////////////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_sql_inf_recup_anos.php";
if (file_exists($file_name))
    require_once($file_name);
else {
    class sql_inf_recup_anos extends sql_inf_recup_anos_base {
    }
}
?>