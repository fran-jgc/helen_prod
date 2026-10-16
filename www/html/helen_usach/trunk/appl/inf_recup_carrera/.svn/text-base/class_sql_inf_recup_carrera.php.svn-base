<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_inf_recup_carrera extends sql_tabla {
	static function get_sql($selector_sql) {
		
		$cod_usuario = session::get("COD_USUARIO");

		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT		 COD_CARRERA
						        		,NOM_CARRERA
						        		,CANT_DEUDOR
						        		,MONTO_DEUDA_VIGENTE
						        		,MONTO_ABONO_VIGENTE
						        		,MONTO_ABONO_VENCIDA
						        		,MONTO_ABONO_ANTICIPADO
						        		,MONTO_ABONO_OTRO
						        		,MONTO_TOTAL
							FROM  		TEMP_RECUP_CARRERA
					   		ORDER BY 	COD_CARRERA";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "spi_recup_carrera";
				}
				break;
				//por el parametro  $ano
			case 'YEAR' :
				if (K_TIPO_BD=="oci"){
					$sql = "select  1 COD_FAMILIA_DEUDA
									,to_number(To_Char(SYSDATE,'yyyy'))-1 ANO
									,'RESUMEN' RESUMEN
									,'RESUMEN' DETALLE
									,( SELECT To_Char(MAX(FECHA_REGISTRO), 'dd/mm/yyyy HH24:MI' )      
																	FROM   TEMP_FLUJO_SVP																																
																	where COD_USUARIO=$cod_usuario) FECHA_FLUJO_SVP
									,( SELECT To_Char(MAX(FECHA_REGISTRO), 'dd/mm/yyyy HH24:MI' )     
																	FROM   TEMP_FLUJO_EXIGIBLE																																
																	where COD_USUARIO=$cod_usuario) FECHA_FLUJO_EXIGIBLE
							from 	DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql =  "select  1 COD_FAMILIA_DEUDA
									,YEAR(GETDATE()) ANO
									,'RESUMEN' RESUMEN
									,'RESUMEN' DETALLE";
				}
				break;
				
			case 'INPUT_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql =  "select 		COD_FAMILIA_DEUDA, 	
										NOM_FAMILIA_DEUDA 
							from 		FAMILIA_DEUDA 
							order by 	COD_FAMILIA_DEUDA";
				}
				break;	
				////////////////////			ERROR		//////////////////////////////////
				default:
				base::error("sql_inf_recup_carrera::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>