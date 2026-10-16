<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_simulacion extends sql_tabla {
	const K_ESTADO_SIMULACION		= 1000;
	
	static function get_sql($selector_sql) {
		switch ($selector_sql) {
			/////////////////  OUTPUT ///////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	COD_INGRESO 
									,COD_INGRESO COD_SIMULACION
								    ,to_char(FECHA_INGRESO, 'dd/mm/yyyy') FECHA_INGRESO
								    ,DE.RUT
								    ,DE.DIG_VERIF
								    ,DE.NOMBRE
								    ,DE.APELLIDO_PATERNO
								    ,DE.APELLIDO_MATERNO
							FROM 	INGRESO I
								  	,DEUDOR DE
								  	,ESTADO_INGRESO E
							WHERE 	DE.COD_DEUDOR = I.COD_DEUDOR
									and E.COD_ESTADO_INGRESO = I.COD_ESTADO_INGRESO
									and I.COD_ESTADO_INGRESO = ".self::K_ESTADO_SIMULACION."
							ORDER BY COD_INGRESO DESC";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_INGRESO 
									,COD_INGRESO COD_SIMULACION
								    ,CONVERT(VARCHAR(20), FECHA_INGRESO, 103) FECHA_INGRESO
								    ,DE.RUT
								    ,DE.DIG_VERIF
								    ,DE.NOMBRE
								    ,DE.APELLIDO_PATERNO
								    ,DE.APELLIDO_MATERNO
							FROM 	INGRESO I
								  	,DEUDOR DE
								  	,ESTADO_INGRESO E
							WHERE 	DE.COD_DEUDOR = I.COD_DEUDOR
									and E.COD_ESTADO_INGRESO = I.COD_ESTADO_INGRESO
									and I.COD_ESTADO_INGRESO = ".self::K_ESTADO_SIMULACION."
							ORDER BY COD_INGRESO DESC";
					
				}
				break;
				///////////////// OUTPUT_DD_COD_ESTADO ///////////////
			case 'DD_TIPO_DOCUMENTO_PAGO_SIMULACION' :
				/////////////////  ORACLE  -  O - SQL-SERVER  ///////////////
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT		COD_TIPO_DOCUMENTO_PAGO
										, NOM_TIPO_DOCUMENTO_PAGO
							FROM 		TIPO_DOCUMENTO_PAGO
							WHERE		COD_TIPO_DOCUMENTO_PAGO IN (1,2,4,6,99)
							ORDER BY 	COD_TIPO_DOCUMENTO_PAGO";
				}
				break;
				
			/////////////////  ERROR ///////////////
			default: 
				base::error("sql_simulacion::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>