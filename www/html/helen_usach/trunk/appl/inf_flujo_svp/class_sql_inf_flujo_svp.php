<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_inf_flujo_svp_base extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT  RUT
								,DIG_VERIF
								,SALDO_DEUDA
								,MONTO_ANO1
								,MONTO_ANO2
								,MONTO_ANO3
								,MONTO_ANO4
								,MONTO_ANO5
								,MONTO_ANO6
								,MONTO_ANO7
								,MONTO_ANO8
								,MONTO_ANO9
								,MONTO_ANO10	
								,MONTO_ANO11	
								,MONTO_ANO12	
								,MONTO_ANO13	
							FROM TEMP_FLUJO_SVP
							ORDER BY RUT";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "";
				}
			break;
			case 'FECHA' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	To_Char(SYSDATE,'YYYY') ANO
							FROM 	DUAL";						
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	datepart(YYYY, getdate()) ANO
									,'' COD_TIPO_DEUDA
									,null COD_CARTERA";
				}
			break;
			case 'DD_COD_TIPO_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT		COD_TIPO_DEUDA 
										, COD_TIPO_DEUDA TIPO_DEUDA
							FROM 		TIPO_DEUDA
							ORDER BY	NOM_TIPO_DEUDA";
				}
			break;		
			//////////////////////		DD_COD_CARTERA	///////////////////
			case 'DD_COD_CARTERA' :
				if (K_TIPO_BD=="oci")
					$sql = "SELECT	0 			COD_CARTERA
									,'Todas' 	NOM_CARTERA
							from DUAL
							union
							SELECT		COD_CARTERA
										, NOM_CARTERA
							FROM 		CARTERA
							ORDER BY	COD_CARTERA";
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	0 			COD_CARTERA
									,'Todas' 	NOM_CARTERA
							union
							SELECT		COD_CARTERA
										, NOM_CARTERA
							FROM 		CARTERA
							ORDER BY	COD_CARTERA";
				}
			break;
			////////////////////			ERROR		//////////////////////////////////
			default:
			base::error("sql_inf_flujo_svp::get_sql, selector: '$selector_sql' no definido");		
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
/////////////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_sql_inf_flujo_svp.php";
if (file_exists($file_name))
    require_once($file_name);
else {
    class sql_inf_flujo_svp extends sql_inf_flujo_svp_base {
    }
}
?>