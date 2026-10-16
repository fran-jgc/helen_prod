<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_inf_deudores_con_excedentes extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT   COD_EXCEDENTE 
								 ,To_Char(FECHA_EXCEDENTE,'dd/mm/yyyy') FECHA_EXCEDENTE        
								 ,RUT						
								 ,NOMBRE                 
								 ,APELLIDO_PATERNO       
								 ,APELLIDO_MATERNO
								-- ,DIRECCION
								-- ,NOM_COMUNA
								--,NOM_CIUDAD
								-- ,TELEFONO
								-- ,EMAIL       
								 ,MONTO_EXCEDENTE                         
					    FROM 	 TEMP_DEUDORES_CON_EXCEDENTES
					    ORDER BY COD_EXCEDENTE";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "";
				}
			break;
			case 'TIPO_DEUDA' :
				if (K_TIPO_BD=="oci"){
					$sql = "select  '01/01/{PARAM1}' FECHA_INICIO
								,'31/12/{PARAM1}' FECHA_TERMINO
								,1 COD_ESTADO_EXCEDENTE
				  		from  	DUAL";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select  '01/01/{PARAM1}' FECHA_INICIO
								,'31/12/{PARAM1}' FECHA_TERMINO
								,1 COD_ESTADO_EXCEDENTE";
				}
			break;
			case 'DD_COD_ESTADO_EXCEDENTE' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "select COD_ESTADO_EXCEDENTE
								, NOM_ESTADO_EXCEDENTE 
					from 		ESTADO_EXCEDENTE 
					order by 	COD_ESTADO_EXCEDENTE";
				}
			break;
			///////////////////		ERROR		///////////////////////////////
			default:
			base::error("sql_inf_deudores_con_excedentes::get_sql, selector: '$selector_sql' no definido");		
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>