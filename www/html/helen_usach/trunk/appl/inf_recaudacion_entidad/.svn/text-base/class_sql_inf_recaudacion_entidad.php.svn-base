<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_inf_recaudacion_entidad_base extends sql_tabla {
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
			////////////////		INPUT_RECAUDACION		//////////////////////////
			case 'INPUT_RECAUDACION' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT  	 	RUT	
								  		,To_Char(FECHA_ABONO, 'dd/mm/yyyy') FECHA_ABONO       
								  		,MONTO_ABONO
					   		FROM 	 	TEMP_RECAUDACION_ENTIDAD
					    	ORDER BY  	RUT";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "spi_recaudacion_entidad";
				}
			break;
			///////////////////			INPUT_TIPO_DEUDA		//////////////////////
			
			//////////////////////		INPUT_FECHA			////////////////////////////		
			case 'INPUT_FECHA' :
				if (K_TIPO_BD=="oci"){
					$sql= "select  	'	01/01/{PARAM1}' FECHA_INICIO
										,'31/12/{PARAM1}' FECHA_TERMINO
										,1 COD_ORIGEN_PAGO
										,'' COD_TIPO_DEUDA
							from 		DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "select  	'	01/01/{PARAM1}' FECHA_INICIO
										,'31/12/{PARAM1}' FECHA_TERMINO
										,1 COD_ORIGEN_PAGO
										,'' COD_TIPO_DEUDA";
				}
			break;		
			////////////////////////		INPUT_DD_PAGO		/////////////////////////
			case 'INPUT_DD_PAGO' :
				if (K_TIPO_BD=="oci"){
					$sql= "select 		COD_ORIGEN_PAGO, 
										NOM_ORIGEN_PAGO 
							from 		ORIGEN_PAGO 
							order by 	COD_ORIGEN_PAGO";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "select 		COD_ORIGEN_PAGO, 
										NOM_ORIGEN_PAGO 
							from 		ORIGEN_PAGO 
							order by 	COD_ORIGEN_PAGO";
				}
			break;		
			////////////////////////////		INPUT_DD_DEUDA		//////////////////////

				case 'TIPO_DEUDA' :
				if (K_TIPO_BD=="oci"){
					$sql = "select  'TODAS' COD_TIPO_DEUDA
						,To_Char(SYSDATE,'yyyy') ANO
						from DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = " select  'TODAS' COD_TIPO_DEUDA
									,year(getdate()) ANO";
				}
			break;
			
			
			case 'INPUT_DD_DEUDA' :
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
			///////////////////////////////		ERROR		////////////////////////////////////
			default:
			base::error("sql_inf_recaudacion_entidad::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
/////////////////////////////////////////////////////////////
// Se separa por UNIVERSIDAD
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_sql_inf_recaudacion_entidad.php";
if (file_exists($file_name))
    require_once($file_name);
else {
    class sql_inf_recaudacion_entidad extends sql_inf_recaudacion_entidad_base {
    }
}
?>