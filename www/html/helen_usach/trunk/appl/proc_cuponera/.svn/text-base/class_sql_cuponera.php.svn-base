<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_cuponera extends sql_tabla {
	static function get_sql($selector_sql) {
		switch ($selector_sql) {
			///////////////////////////////////////////////////////////////////////////////
			////////////////////////SELECT PARA CREACION DE CARTAS/////////////////////////
			case 'SELECT_CARTAS' :
				if (K_TIPO_BD=="oci"){
					$sql = "select	 DO.COD_DEUDOR
									,DO.APELLIDO_PATERNO 
									,DO.APELLIDO_MATERNO 
									,DO.NOMBRE
									,DI.DIRECCION
									,DO.RUT
									,DI.EMAIL 
									,CO.NOM_COMUNA 
									,CI.NOM_CIUDAD 
									,PP.MONTO_CAPITAL 
									,f_carta_get_cant_sub_boleta(PP.COD_PLAN_PAGO) CANT_SUB_BOLETA
									,DE.COD_DEUDA  CUOTAS_MOROSAS
							FROM	BOLETA BO
									,DEUDA DE
									,DEUDOR DO	LEFT OUTER JOIN DIRECCION DI ON DI.COD_DIRECCION = f_get_direccion_deudor (DO.COD_DEUDOR, 'P') 
												LEFT OUTER JOIN COMUNA CO ON CO.COD_COMUNA = DI.COD_COMUNA 
												LEFT OUTER JOIN CIUDAD CI ON CI.COD_CIUDAD = DI.COD_CIUDAD 
									,PLAN_PAGO PP 
							WHERE	BO.COD_BOLETA = {PARAM1}
							  AND   BO.COD_EJECUCION_PROCESO = {PARAM2}
							  AND	BO.COD_DEUDA = DE.COD_DEUDA
							  AND	DE.COD_DEUDOR = DO.COD_DEUDOR
							  AND	DE.COD_TIPO_DEUDA = '{PARAM3}'
							  AND	BO.COD_PLAN_PAGO = PP.COD_PLAN_PAGO";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select	 DO.COD_DEUDOR
									,DO.APELLIDO_PATERNO 
									,DO.APELLIDO_MATERNO 
									,DO.NOMBRE
									,DI.DIRECCION
									,DO.RUT
									,DI.EMAIL 
									,CO.NOM_COMUNA 
									,CI.NOM_CIUDAD 
									,PP.MONTO_CAPITAL 
									,dbo.f_carta_get_cant_sub_boleta(PP.COD_PLAN_PAGO) CANT_SUB_BOLETA
									,DE.COD_DEUDA  CUOTAS_MOROSAS
							FROM	BOLETA BO
									,DEUDA DE
									,DEUDOR DO	LEFT OUTER JOIN DIRECCION DI ON DI.COD_DIRECCION = dbo.f_get_direccion_deudor (DO.COD_DEUDOR, 'P') 
												LEFT OUTER JOIN COMUNA CO ON CO.COD_COMUNA = DI.COD_COMUNA 
												LEFT OUTER JOIN CIUDAD CI ON CI.COD_CIUDAD = DI.COD_CIUDAD 
									,PLAN_PAGO PP 
							WHERE	BO.COD_BOLETA = {PARAM1}
							  AND   BO.COD_EJECUCION_PROCESO = {PARAM2}
							  AND	BO.COD_DEUDA = DE.COD_DEUDA
							  AND	DE.COD_DEUDOR = DO.COD_DEUDOR
							  AND	DE.COD_TIPO_DEUDA = '{PARAM3}'
							  AND	BO.COD_PLAN_PAGO = PP.COD_PLAN_PAGO";
				}
				break;
			
			////////////////////////SELECT_MAIL/////////////////////////				
			case 'SELECT_MAIL' :
				if (K_TIPO_BD=="oci"){
					$sql = "select	 DO.COD_DEUDOR 
									,DO.APELLIDO_PATERNO 
									,DO.APELLIDO_MATERNO 
									,DO.NOMBRE 
									,DO.RUT 
									,DI.EMAIL
							FROM	BOLETA BO
									,DEUDA DE
									,DEUDOR DO	LEFT OUTER JOIN DIRECCION DI ON DI.COD_DIRECCION = f_get_direccion_deudor (DO.COD_DEUDOR, 'P') 
												LEFT OUTER JOIN COMUNA CO ON CO.COD_COMUNA = DI.COD_COMUNA 
												LEFT OUTER JOIN CIUDAD CI ON CI.COD_CIUDAD = DI.COD_CIUDAD 
									,PLAN_PAGO PP 
							WHERE	BO.COD_BOLETA = {PARAM1}
							  AND   BO.COD_EJECUCION_PROCESO = {PARAM2}
							  AND	BO.COD_DEUDA = DE.COD_DEUDA
							  AND	DE.COD_TIPO_DEUDA = '{PARAM3}'
							  AND	DE.COD_DEUDOR = DO.COD_DEUDOR
							  AND	BO.COD_PLAN_PAGO = PP.COD_PLAN_PAGO";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "select	 DO.COD_DEUDOR 
									,DO.APELLIDO_PATERNO 
									,DO.APELLIDO_MATERNO 
									,DO.NOMBRE 
									,DO.RUT 
									,DI.EMAIL
							FROM	BOLETA BO
									,DEUDA DE
									,DEUDOR DO	LEFT OUTER JOIN DIRECCION DI ON DI.COD_DIRECCION = dbo.f_get_direccion_deudor (DO.COD_DEUDOR, 'P') 
												LEFT OUTER JOIN COMUNA CO ON CO.COD_COMUNA = DI.COD_COMUNA 
												LEFT OUTER JOIN CIUDAD CI ON CI.COD_CIUDAD = DI.COD_CIUDAD 
									,PLAN_PAGO PP 
							WHERE	BO.COD_BOLETA = {PARAM1}
							  AND   BO.COD_EJECUCION_PROCESO = {PARAM2}
							  AND	BO.COD_DEUDA = DE.COD_DEUDA
							  AND	DE.COD_TIPO_DEUDA = '{PARAM3}'
							  AND	DE.COD_DEUDOR = DO.COD_DEUDOR
							  AND	BO.COD_PLAN_PAGO = PP.COD_PLAN_PAGO";
				}
				break;
			///////////////////////////////////////////////////////////////////////////////
			///////////////////////////////////////////////////////////////////////////////
			
			/////////////////  ERROR ///////////////
			default: 
				base::error("sql_cuponera::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>