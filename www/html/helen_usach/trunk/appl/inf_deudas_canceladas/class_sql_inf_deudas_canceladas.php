<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_inf_deudas_canceladas_base extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	RUT
								,COD_DEUDA
  								,MONTO_ABONO
  								,MONTO_DESCTO
  								,MONTO_TOTAL
								,TIPO_DEUDA
  						FROM 	TEMP_DEUDAS_CANCELADAS";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "";
				}
			break;
			
			case 'TIPO_DEUDA_1' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT  '' COD_TIPO_DEUDA
								,'01/01/{PARAM1}' FECHA_INICIO
								,'31/12/{PARAM1}' FECHA_TERMINO
						from 	DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT  '' COD_TIPO_DEUDA
								,'01/01/{PARAM1}' FECHA_INICIO
								,'31/12/{PARAM1}' FECHA_TERMINO";
				}
			break;
			case 'DD_COD_TIPO_DEUDA' :
				if (K_TIPO_BD=="oci"){
					$sql= "select 	'TODAS'	COD_TIPO_DEUDA
										,'TODAS' NOM_TIPO_DEUDA
							
							FROM DUAL UNION
							select 		COD_TIPO_DEUDA
										,NOM_TIPO_DEUDA
							from 		TIPO_DEUDA 
							order by 	COD_TIPO_DEUDA";
				}
				else if( K_TIPO_BD=="mssql"){
					$sql= "select 	'TODAS'	 COD_TIPO_DEUDA
									,'TODAS' NOM_TIPO_DEUDA
								
								UNION 
								select 		COD_TIPO_DEUDA
											,NOM_TIPO_DEUDA
								from 		TIPO_DEUDA 
								order by 	COD_TIPO_DEUDA";			
					
				}
			break;
			////////////////////			ERROR		//////////////////////////////////
			default:
			base::error("sql_inf_deudas_canceladas::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
/////////////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_sql_inf_deudas_canceladas.php";
if (file_exists($file_name))
    require_once($file_name);
else {
    class sql_inf_deudas_canceladas extends sql_inf_deudas_canceladas_base {
    }
}
?>