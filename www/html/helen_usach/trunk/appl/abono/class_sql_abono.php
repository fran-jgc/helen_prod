<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	


class sql_abono extends sql_tabla {
	static function get_sql($selector_sql) {
		
		switch ($selector_sql) {
			
			//////////////////     INPUT       /////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT   	A.COD_ABONO
							        	, TO_CHAR(A.FECHA_ABONO,'dd/mm/yyyy') FECHA_ABONO
							        	, TO_CHAR(A.FECHA_REGISTRO,'dd/mm/yyyy') FECHA_REGISTRO
							        	, A.COD_COMPROBANTE
							        	, A.COD_DEUDA
							        	, A.MONTO_CAPITAL
							        	, A.MONTO_INTERES
							        	, A.MONTO_MULTA
							        	, A.MONTO_MONEDA
							        	, A.MONTO_PESOS
							        	, A.DIFERENCIA_PESOS
							        	, OP.NOM_ORIGEN_PAGO
							        	, CD.NOM_COMPONENTE_DEUDA
							        	, EP.NOM_ESTADO_PAGO
							        	, PP.ANO_COBRO
							       	 	, PP.NRO_CUOTA
							        	, U.NOM_USUARIO
							        	, DEUDOR.RUT
							        	, DEUDOR.DIG_VERIF 
							        	, DEUDOR.NOMBRE||' '||DEUDOR.APELLIDO_PATERNO||' '||DEUDOR.APELLIDO_MATERNO NOMBRE
							        	, TD.NOM_TIPO_DEUDA
							        	, TD.COD_TIPO_DEUDA
							        	, M.SIMBOLO
							        	, TO_CHAR(A.FECHA_DEPOSITO,'dd/mm/yyyy') FECHA_DEPOSITO
							        	, FOLIO_CARGA
							        	,(SELECT B.COD_BOLETA FROM BOLETA B, SUB_BOLETA SB WHERE B.COD_BOLETA = SB.COD_BOLETA AND SB.COD_SUB_BOLETA = A.COD_SUB_BOLETA) COD_BOLETA
							        	, SB.COD_BOLETA
							        	,decode(SB.COD_SUB_BOLETA, NULL, NULL, ' / ' || TO_CHAR(SB.COD_SUB_BOLETA))COD_SUB_BOLETA
							FROM 		ABONO A left outer join PLAN_PAGO PP on A.COD_PLAN_PAGO = PP.COD_PLAN_PAGO
												LEFT OUTER JOIN SUB_BOLETA SB ON A.COD_SUB_BOLETA = SB.COD_SUB_BOLETA,
										ORIGEN_PAGO OP, COMPONENTE_DEUDA CD
							      		,USUARIO U, DEUDA DEUDA, DEUDOR DEUDOR, TIPO_DEUDA TD, MONEDA M, ESTADO_PAGO EP
							WHERE 		A.COD_ABONO = {KEY1} AND
							      		A.COD_ORIGEN_PAGO = OP.COD_ORIGEN_PAGO AND
							      		A.COD_COMPONENTE_DEUDA = CD.COD_COMPONENTE_DEUDA AND
							      		A.COD_ESTADO_PAGO = EP.COD_ESTADO_PAGO AND
							      		U.COD_USUARIO = A.COD_USUARIO AND
							      		A.COD_DEUDA = DEUDA.COD_DEUDA AND
							      		DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR  AND
							      		DEUDA.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA AND
							      		TD.COD_MONEDA = M.COD_MONEDA";
				}			
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT   	A.COD_ABONO
							        	, convert(varchar(20),A.FECHA_ABONO,103) FECHA_ABONO
							        	, convert(varchar(20),A.FECHA_REGISTRO,103) FECHA_REGISTRO
							        	, A.COD_COMPROBANTE
							        	, A.COD_DEUDA
							        	, A.MONTO_CAPITAL
							        	, A.MONTO_INTERES
							        	, A.MONTO_MULTA
							        	, A.MONTO_MONEDA
							        	, A.MONTO_PESOS
							        	, A.DIFERENCIA_PESOS
							        	, OP.NOM_ORIGEN_PAGO
							        	, CD.NOM_COMPONENTE_DEUDA
							        	, EP.NOM_ESTADO_PAGO
							        	, PP.ANO_COBRO
							       	 	, PP.NRO_CUOTA
							        	, U.NOM_USUARIO
							        	, DEUDOR.RUT
							        	, DEUDOR.DIG_VERIF 
							        	, DEUDOR.NOMBRE + ' ' + DEUDOR.APELLIDO_PATERNO + ' ' + DEUDOR.APELLIDO_MATERNO NOMBRE
							        	, TD.NOM_TIPO_DEUDA
							        	, TD.COD_TIPO_DEUDA
							        	, M.SIMBOLO
							        	, convert(varchar(20),A.FECHA_DEPOSITO,103) FECHA_DEPOSITO
							        	, FOLIO_CARGA
							        	,(SELECT B.COD_BOLETA FROM BOLETA B, SUB_BOLETA SB WHERE B.COD_BOLETA = SB.COD_BOLETA AND SB.COD_SUB_BOLETA = A.COD_SUB_BOLETA) COD_BOLETA
							        	,SB.COD_BOLETA
							        	,CASE ISNULL(SB.COD_SUB_BOLETA,0) 
				                         WHEN       0
				                         THEN       NULL
				                         ELSE       ' / ' + convert(varchar,SB.COD_SUB_BOLETA)
				                         END COD_SUB_BOLETA
							FROM 		ABONO A left outer join PLAN_PAGO PP on A.COD_PLAN_PAGO = PP.COD_PLAN_PAGO
												LEFT OUTER JOIN SUB_BOLETA SB ON A.COD_SUB_BOLETA = SB.COD_SUB_BOLETA,
										ORIGEN_PAGO OP, COMPONENTE_DEUDA CD
							      		,USUARIO U, DEUDA DEUDA, DEUDOR DEUDOR, TIPO_DEUDA TD, MONEDA M, ESTADO_PAGO EP
							WHERE 		A.COD_ABONO = {KEY1} AND
							      		A.COD_ORIGEN_PAGO = OP.COD_ORIGEN_PAGO AND
							      		A.COD_COMPONENTE_DEUDA = CD.COD_COMPONENTE_DEUDA AND
							      		A.COD_ESTADO_PAGO = EP.COD_ESTADO_PAGO AND
							      		U.COD_USUARIO = A.COD_USUARIO AND
							      		A.COD_DEUDA = DEUDA.COD_DEUDA AND
							      		DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR  AND
							      		DEUDA.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA AND
							      		TD.COD_MONEDA = M.COD_MONEDA ";
				}
			break;
			
			////////////////////////  OUTPUT   ///////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci"){
					/*$sql= 	"SELECT  	ai.cod_ingreso
										,A.COD_ABONO
						           		, TO_CHAR(FECHA_ABONO,'dd/mm/yyyy') FECHA_ABONO
						           		, TO_CHAR(FECHA_REGISTRO,'dd/mm/yyyy') FECHA_REGISTRO
						           		, A.MONTO_CAPITAL
										, A.MONTO_INTERES
										, A.MONTO_MULTA
						           		, A.MONTO_MONEDA
						           		, TD.COD_MONEDA
						           		, 0 MONTO_CAPITAL_PESOS
							       		, 0 MONTO_INTERES_PESOS
							       		, 0 MONTO_MULTA_PESOS						           		
						           		, A.MONTO_PESOS
						           		, A.COD_DEUDA
						           		, TD.NOM_TIPO_DEUDA
						           		, TD.COD_TIPO_DEUDA
						           		, DEU.RUT
						           		, DEU.DIG_VERIF
						           		, EP.NOM_ESTADO_PAGO
						           		, ANO_COBRO
						           		, OP.NOM_ORIGEN_PAGO
						           		, A.COD_COMPROBANTE
						    FROM  		ABONO A, DEUDA D, TIPO_DEUDA TD, DEUDOR DEU, PLAN_PAGO PP,
						          		ESTADO_PAGO EP, ORIGEN_PAGO OP,abono_ingreso AI
						    WHERE   	A.COD_DEUDA = D.COD_DEUDA AND
						            	D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA AND
						            	A.COD_PLAN_PAGO = PP.COD_PLAN_PAGO (+) AND
						            	A.COD_ORIGEN_PAGO = OP.COD_ORIGEN_PAGO (+) AND
						            	A.COD_ESTADO_PAGO = EP.COD_ESTADO_PAGO AND
						            	A.cod_abono=AI.cod_abono(+) and
						            	DEU.COD_DEUDOR = D.COD_DEUDOR 
						            	ORDER BY COD_ABONO DESC";*/
				    
				    $sql = "SELECT  	ai.cod_ingreso
										,A.COD_ABONO
						           		, TO_CHAR(FECHA_ABONO,'dd/mm/yyyy') FECHA_ABONO
						           		, TO_CHAR(FECHA_REGISTRO,'dd/mm/yyyy') FECHA_REGISTRO
						           		, A.MONTO_CAPITAL
										, A.MONTO_INTERES
										, A.MONTO_MULTA
						           		, A.MONTO_MONEDA
						           		, TD.COD_MONEDA
						           		, 0 MONTO_CAPITAL_PESOS
							       		, 0 MONTO_INTERES_PESOS
							       		, 0 MONTO_MULTA_PESOS						           		
						           		, A.MONTO_PESOS
						           		, A.COD_DEUDA
						           		, TD.NOM_TIPO_DEUDA
						           		, TD.COD_TIPO_DEUDA
						           		, DEU.RUT
						           		, DEU.DIG_VERIF
						           		, EP.NOM_ESTADO_PAGO
						           		, ANO_COBRO
						           		, OP.NOM_ORIGEN_PAGO
						           		, A.COD_COMPROBANTE
						    FROM  		ABONO A LEFT OUTER JOIN PLAN_PAGO PP on A.COD_PLAN_PAGO = PP.COD_PLAN_PAGO
                                                LEFT OUTER JOIN ORIGEN_PAGO OP on A.COD_ORIGEN_PAGO = OP.COD_ORIGEN_PAGO
                                                LEFT OUTER JOIN abono_ingreso AI on A.cod_abono=AI.cod_abono
                                        , DEUDA D
                                        , TIPO_DEUDA TD
                                        , DEUDOR DEU
                                        
                                        , ESTADO_PAGO EP
                                     
						    WHERE   	A.COD_DEUDA = D.COD_DEUDA AND
						            	D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA AND
						            	A.COD_ESTADO_PAGO = EP.COD_ESTADO_PAGO AND
						            	DEU.COD_DEUDOR = D.COD_DEUDOR 
						            	ORDER BY COD_ABONO DESC";
				}				
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT  		ai.COD_INGRESO
										,A.COD_ABONO
										, CONVERT(VARCHAR(20), FECHA_ABONO, 103) FECHA_ABONO
										, CONVERT(VARCHAR(20), FECHA_REGISTRO, 103) FECHA_REGISTRO
										, A.MONTO_CAPITAL
										, A.MONTO_INTERES
										, A.MONTO_MULTA
						           		, A.MONTO_MONEDA
						           		, TD.COD_MONEDA
						           		, Round(dbo.f_mon_get_valor_moneda (TD.COD_MONEDA, A.FECHA_ABONO)* A.MONTO_CAPITAL, 0) MONTO_CAPITAL_PESOS
							       		, Round(dbo.f_mon_get_valor_moneda (TD.COD_MONEDA, A.FECHA_ABONO)* A.MONTO_INTERES, 0) MONTO_INTERES_PESOS
							       		, Round(dbo.f_mon_get_valor_moneda (TD.COD_MONEDA, A.FECHA_ABONO)* A.MONTO_MULTA, 0) MONTO_MULTA_PESOS						           		
						           		, A.MONTO_PESOS
						           		, A.COD_DEUDA
						           		, TD.NOM_TIPO_DEUDA
						           		, TD.COD_TIPO_DEUDA
						           		, DEU.RUT
						           		, DEU.DIG_VERIF
						           		, EP.NOM_ESTADO_PAGO
						           		, ANO_COBRO
						           		, OP.NOM_ORIGEN_PAGO
						           		, A.COD_COMPROBANTE
						    FROM  		ABONO A left outer join PLAN_PAGO PP on A.COD_PLAN_PAGO = PP.COD_PLAN_PAGO
						    					left outer join ORIGEN_PAGO OP on A.COD_ORIGEN_PAGO = OP.COD_ORIGEN_PAGO
						    					left outer join abono_ingreso ai on a.cod_abono=ai.cod_abono
						    			, DEUDA D, TIPO_DEUDA TD, DEUDOR DEU, 
						          		ESTADO_PAGO EP
						    WHERE   	A.COD_DEUDA = D.COD_DEUDA AND
						            	D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA AND
						            	A.COD_ESTADO_PAGO = EP.COD_ESTADO_PAGO AND
			            	        	DEU.COD_DEUDOR = D.COD_DEUDOR			            
						            	ORDER BY COD_ABONO DESC";
				}
			break;
				
				//////////////////  DD_COD_TIPO_DEUDA   //////////
			case 'DD_COD_TIPO_DEUDA' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= 	"SELECT		COD_TIPO_DEUDA
										,NOM_TIPO_DEUDA
							FROM 		TIPO_DEUDA
							ORDER BY	COD_TIPO_DEUDA";
				}
			break;
				
				//////////////////////  DD_COD_ESTADO_PAGO   /////////////
			case 'DD_COD_ESTADO_PAGO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 		COD_ESTADO_PAGO
										,NOM_ESTADO_PAGO	
							FROM		ESTADO_PAGO
							ORDER BY	COD_ESTADO_PAGO";
				}
			break;
				
				//////////////////   ERRROR ////////////////////
			default:
				base::error("sql_abono::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>