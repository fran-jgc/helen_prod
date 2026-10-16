<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_carga_consolidado_project extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			
			///////////////////////// OUTPUT  ///////////////////////////////			
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= 	"SELECT  COD_CARGA_CONSOLIDADO_PROJECT
							        , RUT
							        , DIG_VERIF
							        , APELLIDO_PATERNO
							        , APELLIDO_MATERNO
							        , NOMBRE
							        , CCP.DIRECCION
							        , COMUNA
							        , CIUDAD
							        , TELEFONO
							        , CCP.COD_UNIVERSIDAD
							        , NOM_UNIVERSIDAD
							        , TIPO_DEUDOR
							        , DEUDA_TOTAL
							        , ANO_CONSOLIDACION
							        , SITUACION_DETERMINADA
							        , COD_UNIVERSIDAD_COBRA1
							        , COD_UNIVERSIDAD_COBRA2
							        , ANO_INICIO_COBRO
							        , ANO_ULTIMO_CREDITO
							        , ANO_ULTIMA_MATRICULA
							        , COD_UNIVERSIDAD_DEUDA1
							        , MONTO_DEUDA_CONSOLIDADA1
							        , PORC_RECAUDA1
							        , COD_ERROR1
							        , COD_UNIVERSIDAD_DEUDA2
							        , MONTO_DEUDA_CONSOLIDADA2
							        , PORC_RECAUDA2
							        , COD_ERROR2
							        , COD_UNIVERSIDAD_DEUDA3
							        , MONTO_DEUDA_CONSOLIDADA3
							        , PORC_RECAUDA3
							        , COD_ERROR3
							        , COD_UNIVERSIDAD_DEUDA4
							        , MONTO_DEUDA_CONSOLIDADA4
							        , PORC_RECAUDA4
							        , COD_ERROR4
							        , COD_UNIVERSIDAD_DEUDA5
							        , MONTO_DEUDA_CONSOLIDADA5
							        , PORC_RECAUDA5
							        , COD_ERROR5
							        , INDICADOR_ERROR
							        , TIPO_CUENTA
							FROM    CARGA_CONSOLIDADO_PROJECT CCP
									,UNIVERSIDAD U
							WHERE	CCP.COD_UNIVERSIDAD = U.COD_UNIVERSIDAD
							ORDER BY COD_CARGA_CONSOLIDADO_PROJECT";
				}
			break;
			////////////////////////		DD_UNIVERSIDAD		///////////////////////
			case 'DD_UNIVERSIDAD' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= 	"SELECT 	COD_UNIVERSIDAD,
										NOM_UNIVERSIDAD
							FROM 		UNIVERSIDAD 
							ORDER BY 	NOM_UNIVERSIDAD";
				}
			break;
			////////////////////////   	ERROR   //////////////////////////////////////
			default:
				base::error("sql_carga_consolidado_project::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>