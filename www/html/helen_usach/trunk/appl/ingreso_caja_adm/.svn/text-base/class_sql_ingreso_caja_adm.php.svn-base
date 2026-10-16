<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_ingreso_caja_adm extends sql_tabla {
	static function get_sql($selector_sql) {
		switch ($selector_sql) {
			/////////////////  INPUT ///////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "no usado";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT  IC.COD_INGRESO_CAJA_ADM
							        ,convert(varchar(20), FECHA_INGRESO_CAJA_ADM, 103) FECHA_INGRESO_CAJA_ADM
								  	,IC.COD_USUARIO
								  	,U.NOM_USUARIO
								  	,SB.COD_BOLETA
								  	,IC.COD_SUB_BOLETA
								  	,IC.MONTO
								  	,DR.RUT
								  	,DR.DIG_VERIF
								  	,DR.NOMBRE + ' ' + DR.APELLIDO_PATERNO + ' ' + DR.APELLIDO_MATERNO NOMBRE
								  	,D.COD_DEUDA
								  	,D.COD_DEUDA COD_DEUDA_H
								  	,D.COD_TIPO_DEUDA
								  	,TD.NOM_TIPO_DEUDA
								  	,PP.ANO_COBRO
							        ,M.SIMBOLO
							FROM    INGRESO_CAJA_ADM IC
									,SUB_BOLETA SB
									,BOLETA B left outer join PLAN_PAGO PP on B.COD_PLAN_PAGO = PP.COD_PLAN_PAGO 
									,DEUDA D
									,DEUDOR DR
									,USUARIO U
									,TIPO_DEUDA TD
							        ,MONEDA M
							WHERE   IC.COD_INGRESO_CAJA_ADM = {KEY1}
									AND IC.COD_SUB_BOLETA = SB.COD_SUB_BOLETA  
									AND IC.COD_USUARIO = U.COD_USUARIO
									AND SB.COD_BOLETA = B.COD_BOLETA
									AND B.COD_DEUDA = D.COD_DEUDA
									AND D.COD_DEUDOR = DR.COD_DEUDOR
									AND TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA
							        AND TD.COD_MONEDA = M.COD_MONEDA";
				}
				break;
				
			/////////////////  OUTPUT ///////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "no usado";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT   COD_INGRESO_CAJA_ADM,
							         CONVERT(VARCHAR(20),FECHA_INGRESO_CAJA_ADM, 103) FECHA_INGRESO_CAJA_ADM,
							         DEUDOR.RUT,
							         DEUDOR.DIG_VERIF,
							         DEUDA.COD_DEUDA,
							         TD.COD_TIPO_DEUDA,
							         TD.NOM_TIPO_DEUDA,
							         PP.ANO_COBRO ANO_COBRO,							         
							         B.COD_BOLETA,
							         NRO_SUB_BOLETA,
							         B.MONTO_MONEDA,
							         B.MONTO_PESOS
							    FROM INGRESO_CAJA_ADM IC,
							         SUB_BOLETA SB,
							         BOLETA B left outer join PLAN_PAGO PP on B.COD_PLAN_PAGO = PP.COD_PLAN_PAGO,
							         DEUDA,
							         DEUDOR,
							         TIPO_DEUDA TD
							   WHERE IC.COD_SUB_BOLETA = SB.COD_SUB_BOLETA
							     AND SB.COD_BOLETA = B.COD_BOLETA
							     AND B.COD_DEUDA = DEUDA.COD_DEUDA
							     AND DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR
							     AND DEUDA.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
							ORDER BY COD_INGRESO_CAJA_ADM DESC";
				}
				break;
				
			/////////////////  DD_SUB_BOLETA ///////////////
			case 'DD_SUB_BOLETA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 		COD_SUB_BOLETA
										,NRO_SUB_BOLETA 
						  	FROM 		SUB_BOLETA
						 	WHERE		FECHA_PAGO IS NULL
						 				AND COD_BOLETA = {KEY1}
						 	ORDER BY	NRO_SUB_BOLETA";
				}
			break;

			/////////////////  DD_SUB_BOLETA_CON_FECHA_PAGO ///////////////
			case 'DD_SUB_BOLETA_CON_FECHA_PAGO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 		COD_SUB_BOLETA
										,NRO_SUB_BOLETA 
						  	FROM 		SUB_BOLETA
						 	WHERE		COD_BOLETA = {KEY1}
						 	ORDER BY	NRO_SUB_BOLETA";
				}
			break;	

			/////////////// AJAX_INFO_BOLETA ///////////////	
			case 'AJAX_INFO_BOLETA' :
				if (K_TIPO_BD=="oci"){
					$sql = "no usado";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT	DR.RUT
									,DR.DIG_VERIF
									,DR.NOMBRE + ' ' + DR.APELLIDO_PATERNO + ' ' + DR.APELLIDO_MATERNO NOMBRE
									,D.COD_DEUDA
									,D.COD_TIPO_DEUDA
									,TD.NOM_TIPO_DEUDA
									,isnull(PP.ANO_COBRO, 9999) ANO_COBRO
						        	,SIMBOLO
						        	,SIMBOLO SIMBOLO_CH_FECHA 
							FROM    BOLETA B left outer join PLAN_PAGO PP on B.COD_PLAN_PAGO = PP.COD_PLAN_PAGO
									,DEUDA D
									,DEUDOR DR
									,TIPO_DEUDA TD
									,MONEDA M
							WHERE   COD_BOLETA = {PARAM1}
								    AND B.COD_DEUDA = D.COD_DEUDA
								    AND D.COD_DEUDOR = DR.COD_DEUDOR
								    AND TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA
									AND TD.COD_MONEDA = M.COD_MONEDA";
				}
				break;		

			/////////////////  DD_TIPO_DEUDA ///////////////
			case 'DD_TIPO_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT COD_TIPO_DEUDA
								   ,NOM_TIPO_DEUDA
							FROM 	TIPO_DEUDA 
							ORDER BY	COD_TIPO_DEUDA";
				}
				break;	
				
			/////////////////  AJAX_EXISTE_BOLETA ///////////////
			case 'AJAX_EXISTE_BOLETA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT COD_BOLETA
							  FROM BOLETA B
							 WHERE COD_BOLETA = {PARAM1}";
				}
				break;
				
				
			///////////////  AJAX_EXISTE_SUB_BOLETA ///////////////	
			case 'AJAX_EXISTE_SUB_BOLETA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	SB.COD_SUB_BOLETA
						    FROM   	SUB_BOLETA SB
							WHERE  	SB.COD_BOLETA = {PARAM1}
               						AND SB.FECHA_PAGO IS NULL";
				}
				break;
				
			/////////////////  ERROR ///////////////
			default: 
				base::error("sql_ingreso_caja_adm::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>