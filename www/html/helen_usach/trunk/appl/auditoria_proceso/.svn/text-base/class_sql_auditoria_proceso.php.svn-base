<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_auditoria_proceso extends sql_tabla {
	
	static function get_sql($selector_sql) {

		switch ($selector_sql) {
			///////////////// INPUT ///////////////
			case 'INPUT' :
				///////////////// ORACLE ///////////////
				if (K_TIPO_BD=="oci"){
					$sql =	"SELECT	  EP.COD_EJECUCION_PROCESO
									, TO_CHAR(EP.FECHA_EJECUCION_PROCESO,'DD/MM/YYYY HH24:MI:ss') FECHA_EJECUCION_PROCESO 
									, TO_CHAR(EP.FECHA_TERMINO,'DD/MM/YYYY hh:mm:ss') FECHA_TERMINO 
									, trim(To_Char(trunc(mod((FECHA_TERMINO - FECHA_EJECUCION_PROCESO)*24, 24)), '9900')) || ':' ||
                         			  trim(To_Char(trunc( mod( (FECHA_TERMINO - FECHA_EJECUCION_PROCESO)*24*60, 60)), '00')) || ':'  ||
                         			  trim(To_Char(trunc( mod( (FECHA_TERMINO - FECHA_EJECUCION_PROCESO)*24*60*60, 60)), '00')) DURACION
									, P.NOM_PROCESO
									, U.NOM_USUARIO
									, EP.ACTUAL_REGISTRO
									, F_EP_CANT_ERROR(COD_EJECUCION_PROCESO,'E') CANT_ERROR
			               			, F_EP_CANT_ERROR(COD_EJECUCION_PROCESO,'W') CANT_WARNING
									,'' DISPLAY_DETALLE_ERROR
									,'none' DISPLAY_MENSAJE_DETALLE_ERROR
            				FROM	EJECUCION_PROCESO EP
            						, USUARIO U
            						, PROCESO P
							
      				        WHERE	EP.COD_USUARIO = U.COD_USUARIO  
                    				AND EP.COD_PROCESO = P.COD_PROCESO
                    				AND EP.COD_EJECUCION_PROCESO = {KEY1}
                    				
							
						 	
              				ORDER BY	EP.COD_EJECUCION_PROCESO";
				}
				///////////////// SQL-SERVER ///////////////
				else if (K_TIPO_BD=="mssql"){
						$sql = "SELECT	  EP.COD_EJECUCION_PROCESO
										, convert(varchar(10), EP.FECHA_EJECUCION_PROCESO, 103) FECHA_EJECUCION_PROCESO 
										, convert(varchar(10), EP.FECHA_TERMINO, 103) FECHA_TERMINO 
										, dbo.f_aud_proceso(FECHA_EJECUCION_PROCESO, FECHA_TERMINO) DURACION
										, P.NOM_PROCESO
										, U.NOM_USUARIO
										, EP.ACTUAL_REGISTRO
										, dbo.F_EP_CANT_ERROR(COD_EJECUCION_PROCESO,'E') CANT_ERROR
										, dbo.F_EP_CANT_ERROR(COD_EJECUCION_PROCESO,'W') CANT_WARNING
										,'' DISPLAY_DETALLE_ERROR
										,'none' DISPLAY_MENSAJE_DETALLE_ERROR
								FROM	EJECUCION_PROCESO EP
										, USUARIO U
										, PROCESO P
								WHERE	EP.COD_USUARIO = U.COD_USUARIO  
										AND EP.COD_PROCESO = P.COD_PROCESO
										AND EP.COD_EJECUCION_PROCESO = {KEY1}
								ORDER BY	EP.COD_EJECUCION_PROCESO";
				}
			break;
			
			///////////////// OUTPUT///////////////
			case 'OUTPUT' :
				///////////////// ORACLE ///////////////
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT	  EP.COD_EJECUCION_PROCESO
									, P.COD_PROCESO
									, TO_CHAR(EP.FECHA_EJECUCION_PROCESO,'DD/MM/YYYY HH24:MI:ss') FECHA_EJECUCION_PROCESO 
									, P.NOM_PROCESO
									, U.NOM_USUARIO
									, EP.ACTUAL_REGISTRO
									, F_EP_CANT_ERROR(COD_EJECUCION_PROCESO,'E') CANT_ERROR
			               			, F_EP_CANT_ERROR(COD_EJECUCION_PROCESO,'W') CANT_WARNING
						
            				FROM	EJECUCION_PROCESO EP
            						, USUARIO U
            						, PROCESO P
							
      				        WHERE	EP.COD_USUARIO = U.COD_USUARIO  
                    				AND EP.COD_PROCESO = P.COD_PROCESO
							
              				ORDER BY	EP.COD_EJECUCION_PROCESO DESC
					";
				}
				///////////////// SQL-SERVER  ///////////////
				else if (K_TIPO_BD=="mssql"){
						$sql = "SELECT	  EP.COD_EJECUCION_PROCESO
										, P.COD_PROCESO
										, convert(varchar(20), EP.FECHA_EJECUCION_PROCESO, 103) +'  '+ convert(varchar(20), EP.FECHA_EJECUCION_PROCESO, 8) FECHA_EJECUCION_PROCESO
										, P.NOM_PROCESO
										, U.NOM_USUARIO
										, EP.ACTUAL_REGISTRO
										, DBO.F_EP_CANT_ERROR(COD_EJECUCION_PROCESO,'E') CANT_ERROR
										, DBO.F_EP_CANT_ERROR(COD_EJECUCION_PROCESO,'W') CANT_WARNING
								FROM	EJECUCION_PROCESO EP
										, USUARIO U
										, PROCESO P
								WHERE	EP.COD_USUARIO = U.COD_USUARIO  
										AND EP.COD_PROCESO = P.COD_PROCESO
								ORDER BY	EP.COD_EJECUCION_PROCESO DESC";
				}
			break;

			case 'DW_DETALLE_ERROR_PROCESO' :
				///////////////// ORACLE ///////////////
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT   	ER.COD_ERROR_PROCESO 
										,EP.COD_EJECUCION_PROCESO COD_EJECUCION_PROCESO_DW
										,ER.TIPO_ERROR
										,ER.ERROR_CODE_BD
										,ER.ERROR_MESSAGE_BD
										,ER.ERROR_MESSAGE
							FROM 		ERROR_PROCESO ER, EJECUCION_PROCESO EP
              				WHERE  		ER.COD_EJECUCION_PROCESO = EP.COD_EJECUCION_PROCESO
              							AND ER.COD_EJECUCION_PROCESO  = {KEY1}";
				}
				///////////////// SQL-SERVER  ///////////////
				else if (K_TIPO_BD=="mssql"){
						$sql = "SELECT   	ER.COD_ERROR_PROCESO 
											,EP.COD_EJECUCION_PROCESO COD_EJECUCION_PROCESO_DW
											,ER.TIPO_ERROR
											,ER.ERROR_CODE_BD
											,ER.ERROR_MESSAGE_BD
											,ER.ERROR_MESSAGE
								FROM 		ERROR_PROCESO ER
											,EJECUCION_PROCESO EP
								WHERE  		ER.COD_EJECUCION_PROCESO = EP.COD_EJECUCION_PROCESO
								  AND		ER.COD_EJECUCION_PROCESO  = {KEY1}
								ORDER BY	EP.COD_EJECUCION_PROCESO DESC";
				}
			break;
			
			case 'DD_NOM_PROCESO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_PROCESO 
									,NOM_PROCESO
							FROM	PROCESO  
							ORDER BY COD_PROCESO";
				}
			break;
			
			case 'CANT_DETALLE_ERROR' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COUNT(*) AS CANT_DETALLE_ERROR 
							FROM 	ERROR_PROCESO 
							WHERE 	COD_EJECUCION_PROCESO = {PARAM1}";
				}
			break;
			/////////////////  ERROR ///////////////
			default: 
			base::error("sql_auditoria_proceso::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>