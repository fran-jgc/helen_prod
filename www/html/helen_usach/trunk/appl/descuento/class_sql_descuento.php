<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");

class sql_descuento extends sql_tabla {
	static function get_sql($selector_sql) {
		switch ($selector_sql) {
			////////////////////// INPUT ////////////////////////////	
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	DSCTO.COD_DESCUENTO
							        , TO_CHAR(DSCTO.FECHA_DESCUENTO,'dd/mm/yyyy') FECHA_DESCUENTO
							        , TO_CHAR(DSCTO.FECHA_REGISTRO,'DD/MM/YYYY') FECHA_REGISTRO
							        , DSCTO.COD_COMPROBANTE
							        , DSCTO.COD_DEUDA
							        , DSCTO.MONTO_CAPITAL
							        , DSCTO.MONTO_INTERES
							        , DSCTO.MONTO_MULTA
							        , DSCTO.MONTO_MONEDA
							        , DSCTO.MONTO_PESOS
							       	, OP.NOM_ORIGEN_PAGO
							        , CD.NOM_COMPONENTE_DEUDA
							        , PP.ANO_COBRO
							        , PP.NRO_CUOTA
							        , EPP.NOM_ESTADO_PLAN_PAGO
							        , U.NOM_USUARIO
							        , TD.NOM_TIPO_DEUDA
							        , TD.COD_TIPO_DEUDA        
							        , M.SIMBOLO
							        , ESTADO_PAGO.COD_ESTADO_PAGO
							        , DEUDOR.RUT
							        , DEUDOR.DIG_VERIF 
							        , DEUDOR.NOMBRE||' '||DEUDOR.APELLIDO_PATERNO||' '||DEUDOR.APELLIDO_MATERNO NOMBRE
							FROM    DESCUENTO DSCTO left outer join PLAN_PAGO PP left outer join ESTADO_PLAN_PAGO EPP on PP.COD_ESTADO_PLAN_PAGO = EPP.COD_ESTADO_PLAN_PAGO on DSCTO.COD_PLAN_PAGO = PP.COD_PLAN_PAGO
									, ORIGEN_PAGO OP, COMPONENTE_DEUDA CD 
							        ,USUARIO U, DEUDA DEUDA, TIPO_DEUDA TD, MONEDA M, DEUDOR DEUDOR, ESTADO_PAGO
							WHERE   DSCTO.COD_DESCUENTO = {KEY1} AND
									DSCTO.COD_ORIGEN_PAGO = OP.COD_ORIGEN_PAGO AND
							        DSCTO.COD_COMPONENTE_DEUDA = CD.COD_COMPONENTE_DEUDA AND
							        DSCTO.COD_USUARIO = U.COD_USUARIO AND
							        DSCTO.COD_DEUDA = DEUDA.COD_DEUDA AND
							        DEUDA.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA AND
							        TD.COD_MONEDA = M.COD_MONEDA AND
							        DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR and ESTADO_PAGO.COD_ESTADO_PAGO = DSCTO.COD_ESTADO_PAGO";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 	DSCTO.COD_DESCUENTO
							        , convert(varchar(20),DSCTO.FECHA_DESCUENTO,103) FECHA_DESCUENTO
							        , convert(varchar(20),DSCTO.FECHA_REGISTRO,103) FECHA_REGISTRO
							        , DSCTO.COD_COMPROBANTE
							        , DSCTO.COD_DEUDA
							        , DSCTO.MONTO_CAPITAL
							        , DSCTO.MONTO_INTERES
							        , DSCTO.MONTO_MULTA
							        , DSCTO.MONTO_MONEDA
							        , DSCTO.MONTO_PESOS
							       	, OP.NOM_ORIGEN_PAGO
							        , CD.NOM_COMPONENTE_DEUDA
							        , PP.ANO_COBRO
							        , PP.NRO_CUOTA
							        , EPP.NOM_ESTADO_PLAN_PAGO
							        , U.NOM_USUARIO
							        , TD.NOM_TIPO_DEUDA
							        , TD.COD_TIPO_DEUDA        
							        , M.SIMBOLO
							        , DEUDOR.RUT
							        , DEUDOR.DIG_VERIF 
							        , DEUDOR.NOMBRE + ' ' + DEUDOR.APELLIDO_PATERNO + ' ' + DEUDOR.APELLIDO_MATERNO NOMBRE
							FROM    DESCUENTO DSCTO left outer join PLAN_PAGO PP left outer join ESTADO_PLAN_PAGO EPP on PP.COD_ESTADO_PLAN_PAGO = EPP.COD_ESTADO_PLAN_PAGO on DSCTO.COD_PLAN_PAGO = PP.COD_PLAN_PAGO
									, ORIGEN_PAGO OP, COMPONENTE_DEUDA CD 
							        ,USUARIO U, DEUDA DEUDA, TIPO_DEUDA TD, MONEDA M, DEUDOR DEUDOR
							WHERE   DSCTO.COD_DESCUENTO = {KEY1} AND
									DSCTO.COD_ORIGEN_PAGO = OP.COD_ORIGEN_PAGO AND
							        DSCTO.COD_COMPONENTE_DEUDA = CD.COD_COMPONENTE_DEUDA AND
							        DSCTO.COD_USUARIO = U.COD_USUARIO AND
							        DSCTO.COD_DEUDA = DEUDA.COD_DEUDA AND
							        DEUDA.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA AND
							        TD.COD_MONEDA = M.COD_MONEDA AND
							        DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR";
				}
				break;
			
			/////////////////// OUTPUT ////////////////////	
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci"){
					$sql = "SELECT 	                               DEUDOR.RUT
						             , DEUDOR.DIG_VERIF
						             , DSCTO.COD_DESCUENTO
						             , DSCTO.COD_DEUDA
						             , TO_CHAR(DSCTO.FECHA_DESCUENTO,'dd/mm/yyyy') FECHA_DESCUENTO
						             , DSCTO.MONTO_MONEDA
						             , DSCTO.MONTO_PESOS
						             , ESTADO_PAGO.NOM_ESTADO_PAGO
						             , TD.NOM_TIPO_DEUDA
						             , TD.COD_TIPO_DEUDA
						             , PP.ANO_COBRO
						             , EPP.NOM_ESTADO_PLAN_PAGO
						      FROM 	DESCUENTO DSCTO, DEUDA DEUDA, DEUDOR DEUDOR, TIPO_DEUDA TD, PLAN_PAGO PP, ESTADO_PLAN_PAGO EPP, ESTADO_PAGO
						      WHERE DSCTO.COD_DEUDA = DEUDA.COD_DEUDA AND
						            DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR AND                
						            DSCTO.COD_PLAN_PAGO = PP.COD_PLAN_PAGO (+) AND
						            PP.COD_ESTADO_PLAN_PAGO = EPP.COD_ESTADO_PLAN_PAGO (+) AND
						            DEUDA.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA and
						            ESTADO_PAGO.COD_ESTADO_PAGO = DSCTO.COD_ESTADO_PAGO and deudor.rut <> 111 
						            ORDER BY COD_DESCUENTO DESC";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql = "SELECT 	DSCTO.COD_DESCUENTO
						             , DSCTO.COD_DEUDA
						             , CONVERT(VARCHAR(20), DSCTO.FECHA_DESCUENTO,103) FECHA_DESCUENTO
						             , DSCTO.MONTO_MONEDA
						             , DSCTO.MONTO_PESOS
						             , DEUDOR.RUT
						             , DEUDOR.DIG_VERIF
						             , TD.NOM_TIPO_DEUDA
						             , TD.COD_TIPO_DEUDA
						             , PP.ANO_COBRO
						             , EPP.NOM_ESTADO_PLAN_PAGO
						      FROM 	DESCUENTO DSCTO left outer join (PLAN_PAGO PP left outer join ESTADO_PLAN_PAGO EPP on PP.COD_ESTADO_PLAN_PAGO = EPP.COD_ESTADO_PLAN_PAGO)on DSCTO.COD_PLAN_PAGO = PP.COD_PLAN_PAGO,
									DEUDA DEUDA,
									DEUDOR DEUDOR, 
									TIPO_DEUDA TD
						      WHERE DSCTO.COD_DEUDA = DEUDA.COD_DEUDA AND
						            DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR AND      
						            DEUDA.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
						            ORDER BY COD_DESCUENTO DESC";
				}
				break;
				
			//////////////////// DD_COD_TIPO_DEUDA //////////////////	
			case 'DD_COD_TIPO_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_TIPO_DEUDA 
									,NOM_TIPO_DEUDA 
							FROM  	TIPO_DEUDA 
							ORDER BY COD_TIPO_DEUDA";
				}
				break;


				//////////////////// DD_COD_ESTADO_PAGO //////////////////	
			case 'DD_COD_ESTADO_PAGO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_ESTADO_PAGO
									,NOM_ESTADO_PAGO 
							FROM  	ESTADO_PAGO 
							ORDER BY COD_ESTADO_PAGO";
				}
				break;
				
			///////////////// DD_COD_ESTADO_PLAN_PAGO ///////////////	
			case 'DD_COD_ESTADO_PLAN_PAGO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql = "SELECT 	COD_ESTADO_PLAN_PAGO
									, NOM_ESTADO_PLAN_PAGO 
							FROM 	ESTADO_PLAN_PAGO 
							ORDER BY COD_ESTADO_PLAN_PAGO";
				}
				break;

			/////////////////  ERROR ///////////////
			default: 
				base::error("sql_descuento::get_sql, selector: '$selector_sql' no definido");	
			
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>