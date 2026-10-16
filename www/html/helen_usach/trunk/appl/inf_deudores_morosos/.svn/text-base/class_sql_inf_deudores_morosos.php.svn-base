<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_inf_deudores_morosos_base extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT RUT		  
							   ,NOMBRE                
							   ,APELLIDO_PATERNO      
							   ,APELLIDO_MATERNO       
							   ,MONTO_MORA             
							   ,MONTO_MULTA            
							   ,MONTO_TOTAL
							   ,NOM_CARTERA
						FROM  	TEMP_DEUDORES_MOROSOS
					   ORDER BY RUT";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "";
				}
			break;
			case 'FECHA' :
				if (K_TIPO_BD=="oci"){
					$sql = "select  '' COD_TIPO_DEUDA
									,to_char(sysdate, 'dd/mm/yyyy') FECHA
					  		from 	DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select  '' COD_TIPO_DEUDA
									,CONVERT(VARCHAR(20),GETDATE(),103) FECHA";
				}
			break;
			case 'DD_COD_TIPO_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select COD_TIPO_DEUDA
									, COD_TIPO_DEUDA COD_TIPO_DEUDA_COD
							from 	 TIPO_DEUDA 
							order by COD_TIPO_DEUDA";
				}
			break;
			///////////////////		ERROR		///////////////////////////////
			default:
			base::error("sql_inf_deudores_morosos::get_sql, selector: '$selector_sql' no definido");			
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
/////////////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_sql_inf_deudores_morosos.php";
if (file_exists($file_name))
    require_once($file_name);
else {
    class sql_inf_deudores_morosos extends sql_inf_deudores_morosos_base {
    }
}
?>