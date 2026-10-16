<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_condonacion_cs extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			/////////////////		INPUT		///////////////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT 	CCS.COD_CONDONACION_CS
									,TO_CHAR(CCS.FECHA_CONDONACION_CS,'dd/mm/yyyy') FECHA_CONDONACION_CS
									,CCS.COD_DEUDA
									,DR.RUT
									,DR.DIG_VERIF
									,DR.NOMBRE||' '||DR.APELLIDO_PATERNO||' '||DR.APELLIDO_MATERNO NOMBRE
									,CCS.COD_ESTADO_CONDONACION_CS
									,f_condcs_monto_condonacion(CCS.COD_DEUDA) MONTO
									,CCS.OBS
									,f_last_mod('NOM_USUARIO', 'CONDONACION_CS', 'COD_ESTADO_CONDONACION_CS', CCS.COD_CONDONACION_CS) NOM_USUARIO_CAMBIO
							        ,f_last_mod('FECHA_CAMBIO', 'CONDONACION_CS', 'COD_ESTADO_CONDONACION_CS', CCS.COD_CONDONACION_CS) FECHA_CAMBIO
							FROM 	CONDONACION_CS CCS, DEUDA DA, DEUDOR DR
							WHERE 	CCS.COD_CONDONACION_CS = {KEY1} 
							  AND 	CCS.COD_DEUDA = DA.COD_DEUDA
							  AND 	DA.COD_DEUDOR = DR.COD_DEUDOR";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT 	CCS.COD_CONDONACION_CS
									,convert(varchar(20), CCS.FECHA_CONDONACION_CS, 103) FECHA_CONDONACION_CS
									,CCS.COD_DEUDA
									,DR.RUT
									,DR.DIG_VERIF
									,DR.NOMBRE + ' ' + DR.APELLIDO_PATERNO + ' ' + DR.APELLIDO_MATERNO NOMBRE
									,CCS.COD_ESTADO_CONDONACION_CS
									,ISNULL(ROUND(dbo.f_condcs_monto_condonacion(CCS.COD_DEUDA),4),0) MONTO
									,CCS.OBS
									,dbo.f_last_mod('NOM_USUARIO', 'CONDONACION_CS', 'COD_ESTADO_CONDONACION_CS', CCS.COD_CONDONACION_CS) NOM_USUARIO_CAMBIO
							        ,dbo.f_last_mod('FECHA_CAMBIO', 'CONDONACION_CS', 'COD_ESTADO_CONDONACION_CS', CCS.COD_CONDONACION_CS) FECHA_CAMBIO
							FROM 	CONDONACION_CS CCS, DEUDA DA, DEUDOR DR
							WHERE 	CCS.COD_CONDONACION_CS = {KEY1} 
							  AND 	CCS.COD_DEUDA = DA.COD_DEUDA
							  AND 	DA.COD_DEUDOR = DR.COD_DEUDOR";
				}
			break;
				
			////////////////////		OUTPUT		//////////////////////	
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT  C.COD_CONDONACION_CS
							        ,TO_CHAR(C.FECHA_CONDONACION_CS,'dd/mm/yyyy') FECHA_CONDONACION_CS
							        ,C.COD_DEUDA
							        ,f_condcs_monto_condonacion(C.COD_DEUDA) MONTO
							        ,DR.RUT
							        ,DR.DIG_VERIF
							      	,DR.NOMBRE||' '||DR.APELLIDO_PATERNO||' '||DR.APELLIDO_MATERNO NOMBRE
							        ,ECCS.NOM_ESTADO_CONDONACION_CS
							FROM    CONDONACION_CS C, ESTADO_CONDONACION_CS ECCS, DEUDA DA, DEUDOR DR
							WHERE   C.COD_ESTADO_CONDONACION_CS = ECCS.COD_ESTADO_CONDONACION_CS
							  AND   C.COD_DEUDA = DA.COD_DEUDA
							  AND   DA.COD_DEUDOR = DR.COD_DEUDOR
							ORDER BY  COD_CONDONACION_CS DESC";
				}
				else if(K_TIPO_BD=="mssql"){
					$sql= "SELECT  C.COD_CONDONACION_CS
							        ,convert(varchar(20), C.FECHA_CONDONACION_CS, 103) FECHA_CONDONACION_CS
							        ,C.COD_DEUDA
							        ,ISNULL(ROUND(dbo.f_condcs_monto_condonacion(C.COD_DEUDA),4),0) MONTO
							        ,DR.RUT
							        ,DR.DIG_VERIF
							      	,DR.NOMBRE + ' ' + DR.APELLIDO_PATERNO + ' ' + DR.APELLIDO_MATERNO NOMBRE
							        ,ECCS.NOM_ESTADO_CONDONACION_CS
							FROM    CONDONACION_CS C, ESTADO_CONDONACION_CS ECCS, DEUDA DA, DEUDOR DR
							WHERE   C.COD_ESTADO_CONDONACION_CS = ECCS.COD_ESTADO_CONDONACION_CS
							  AND   C.COD_DEUDA = DA.COD_DEUDA
							  AND   DA.COD_DEUDOR = DR.COD_DEUDOR
							ORDER BY  COD_CONDONACION_CS DESC";
				}
			break;
				
			///////////////// DD_COD_ESTADO_CONDONACION_CS /////////////////	
			case 'DD_COD_ESTADO_CONDONACION_CS' :
					if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= 	"SELECT 	COD_ESTADO_CONDONACION_CS
										,NOM_ESTADO_CONDONACION_CS
							FROM 		ESTADO_CONDONACION_CS 
							ORDER BY 	COD_ESTADO_CONDONACION_CS";
				}
			break;

			///////////////		ERROR		/////////////////////
			default:
			base::error("sql_condonacion_cs::get_sql, selector: '$selector_sql' no definido");
				
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>