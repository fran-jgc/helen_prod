<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class sql_anula_abono extends sql_tabla {
	
	const K_PAGO_VIGENTE= 1;
	static function get_sql($selector_sql) {
		switch ($selector_sql) {
			///////////////  INPUT    ////////////////////
			case 'INPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT    AA.COD_ANULA_ABONO
						  		, DO.COD_DEUDOR COD_DEUDOR_H
						  		, DO.RUT
						  		, DO.RUT RUT_H
						  		, DO.DIG_VERIF
					      		, DO.NOMBRE ||' '|| DO.APELLIDO_PATERNO ||' '|| DO.APELLIDO_MATERNO NOMBRE
					      		, DA.COD_DEUDA
					      		, DA.COD_DEUDA COD_DEUDA_DD
					      		, M.SIMBOLO
					      		, PP.COD_PLAN_PAGO COD_PLAN_PAGO_PP
					      		, PP.ANO_COBRO
		                  		, PP.NRO_CUOTA
		                  		, Decode(Nvl(PP.COD_PLAN_PAGO, 0), 0, 0,
		                           f_pp_saldo_plan_pago(PP.COD_PLAN_PAGO, AA.FECHA_ANULA_ABONO)) SALDO_CUOTA
		                  		, AA.COD_ABONO         
		                  		, AA.OBS
		                  		, AA.COD_USUARIO  
		                  		, U.NOM_USUARIO
		                  		, TO_CHAR(AA.FECHA_ANULA_ABONO,'dd/mm/yyyy') FECHA_ANULA_ABONO
		                  		, AA.COD_COMPROBANTE
		                  		, AA.ENVIAR_A_EXCEDENTE
		                  		, E.COD_EXCEDENTE	     
										
							FROM      DEUDOR DO, ANULA_ABONO AA, ABONO A, DEUDA DA, TIPO_DEUDA TD, 
						  		MONEDA M, PLAN_PAGO PP, USUARIO U, EXCEDENTE E
				
							WHERE     AA.COD_ANULA_ABONO  = {KEY1} AND
				          		A.COD_ABONO 		  = AA.COD_ABONO (+) AND
				          		DA.COD_DEUDA 		  = A.COD_DEUDA AND 
					      		DO.COD_DEUDOR 	  = DA.COD_DEUDOR AND
				          		TD.COD_TIPO_DEUDA   = DA.COD_TIPO_DEUDA AND 
				          		M.COD_MONEDA		  = TD.COD_MONEDA AND
				          		PP.COD_PLAN_PAGO(+) = A.COD_PLAN_PAGO AND
				          		U.COD_USUARIO 	  = AA.COD_USUARIO AND
            		      		E.COD_DEUDOR(+)	  = DO.COD_DEUDOR";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT    AA.COD_ANULA_ABONO
						  			, DO.COD_DEUDOR COD_DEUDOR_H
						  			, DO.RUT
						  			, DO.RUT RUT_H
						  			, DO.DIG_VERIF
					      			, DO.NOMBRE + ' ' + DO.APELLIDO_PATERNO + ' ' + DO.APELLIDO_MATERNO NOMBRE
					      			, DA.COD_DEUDA
					      			, DA.COD_DEUDA COD_DEUDA_DD
					      			, M.SIMBOLO
					      			, PP.COD_PLAN_PAGO COD_PLAN_PAGO_PP
					      			, PP.ANO_COBRO
		                  			, PP.NRO_CUOTA
		                  			,case isnull(PP.COD_PLAN_PAGO, 0)
										when 0 then 0
										else dbo.f_pp_saldo_plan_pago(PP.COD_PLAN_PAGO, AA.FECHA_ANULA_ABONO)
									end SALDO_CUOTA	
		                  			, AA.COD_ABONO         
		                  			, AA.OBS
		                  			, AA.COD_USUARIO  
		                  			, U.NOM_USUARIO
		                  			, convert(varchar(20), AA.FECHA_ANULA_ABONO, 103) FECHA_ANULA_ABONO
		                  			, AA.COD_COMPROBANTE
		                  			, AA.ENVIAR_A_EXCEDENTE
		                  			, E.COD_EXCEDENTE	     
										
							FROM    DEUDOR DO left outer join EXCEDENTE E on DO.COD_DEUDOR = E.COD_DEUDOR, 
									ABONO A left outer join ANULA_ABONO AA on A.COD_ABONO = AA.COD_ABONO
										left outer join PLAN_PAGO PP on A.COD_PLAN_PAGO = PP.COD_PLAN_PAGO
									,DEUDA DA, TIPO_DEUDA TD, MONEDA M, USUARIO U
				
							WHERE   AA.COD_ANULA_ABONO  = {KEY1} AND
				          			DA.COD_DEUDA 		= A.COD_DEUDA AND 
					      			DO.COD_DEUDOR 		= DA.COD_DEUDOR AND
				          			TD.COD_TIPO_DEUDA   = DA.COD_TIPO_DEUDA AND 
				          			M.COD_MONEDA		= TD.COD_MONEDA AND
				          			U.COD_USUARIO 		= AA.COD_USUARIO ";
				}
			break;
			//////////////////// OUTPUT   ////////////////////////
			case 'OUTPUT' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT   ANULA_ABONO.COD_ANULA_ABONO
				       			, TO_CHAR(ANULA_ABONO.FECHA_ANULA_ABONO,'dd/mm/yyyy') FECHA_ANULA_ABONO
				       			, DEUDOR.RUT
				       			, DEUDOR.DIG_VERIF
				       			, DEUDOR.NOMBRE
				       			, DEUDOR.APELLIDO_PATERNO
				       			, DEUDOR.APELLIDO_MATERNO
				       			, ABONO.COD_DEUDA
				       			, (SELECT ANO_COBRO FROM PLAN_PAGO WHERE PLAN_PAGO.COD_DEUDA = DEUDA.COD_DEUDA AND PLAN_PAGO.COD_PLAN_PAGO = ABONO.COD_PLAN_PAGO) ANO_COBRO
				       			, ABONO.MONTO_PESOS
				    		FROM 	ANULA_ABONO 
				       			, DEUDOR 
				       			, ABONO 
				       			, DEUDA 
				       			 
				   			WHERE 	ABONO.COD_ABONO = ANULA_ABONO.COD_ABONO
				     			AND DEUDA.COD_DEUDA = ABONO.COD_DEUDA
				     			AND DEUDA.COD_DEUDOR = DEUDOR.COD_DEUDOR
				     			--AND ANULA_ABONO.COD_ANULA_ABONO  = 7044
								ORDER BY ANULA_ABONO.COD_ANULA_ABONO DESC";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT   AA.COD_ANULA_ABONO
				       			,CONVERT(VARCHAR(20), FECHA_ANULA_ABONO, 103) FECHA_ANULA_ABONO
				       			, DO.RUT
				       			, DO.DIG_VERIF
				       			, DO.NOMBRE
				       			, DO.APELLIDO_PATERNO
				       			, DO.APELLIDO_MATERNO
				       			, A.COD_DEUDA
				       			, ANO_COBRO
				       			, A.MONTO_PESOS
				    		FROM 	ANULA_ABONO AA
				       			, DEUDOR DO
				       			, ABONO A
				       			, DEUDA DA left outer join PLAN_PAGO PP on DA.COD_DEUDA = PP.COD_DEUDA
				   			WHERE 	A.COD_ABONO = AA.COD_ABONO
				     			AND DA.COD_DEUDA = A.COD_DEUDA
								AND A.COD_PLAN_PAGO = PP.COD_PLAN_PAGO
				     			AND DA.COD_DEUDOR = DO.COD_DEUDOR
								ORDER BY AA.COD_ANULA_ABONO DESC";
				}
			break;
			///////////////////  DW_ABONO    ///////////////////
			case 'DW_ABONO' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT    A.COD_ABONO
								, OP.NOM_ORIGEN_PAGO
				        		, TO_CHAR(A.FECHA_ABONO,'dd/mm/yyyy') FECHA_ABONO
				        		, A.MONTO_CAPITAL
				        		, A.MONTO_INTERES                            
				        		, A.MONTO_MULTA
				        		, A.MONTO_MONEDA
				        		, A.MONTO_PESOS
				        		, AA.ENVIAR_A_EXCEDENTE
				        		, 'S' ANULA_ABONO
							FROM    ANULA_ABONO AA, ABONO A, ORIGEN_PAGO OP
							WHERE   AA.COD_ANULA_ABONO = {KEY1} AND
								A.COD_ABONO = AA.COD_ABONO AND
								OP.COD_ORIGEN_PAGO = A.COD_ORIGEN_PAGO";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT    A.COD_ABONO
									, OP.NOM_ORIGEN_PAGO
				        			, convert(varchar(20), A.FECHA_ABONO, 103) FECHA_ABONO
				        			, A.MONTO_CAPITAL
				        			, A.MONTO_INTERES                            
				        			, A.MONTO_MULTA
				        			, A.MONTO_MONEDA
				        			, A.MONTO_PESOS
				        			, AA.ENVIAR_A_EXCEDENTE
				        			, 'S' ANULA_ABONO
							FROM    ANULA_ABONO AA, ABONO A, ORIGEN_PAGO OP
							WHERE   AA.COD_ANULA_ABONO = {KEY1} AND
									A.COD_ABONO = AA.COD_ABONO AND
									OP.COD_ORIGEN_PAGO = A.COD_ORIGEN_PAGO";
				}
				break;
				/////////////////////	DD_COD_DEUDA		////////////////////////
				case 'DD_COD_ABONO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT  D.COD_DEUDA, D.COD_TIPO_DEUDA V, M.SIMBOLO
		        FROM    DEUDA D, DEUDOR DR, TIPO_DEUDA TD, MONEDA M
		        WHERE   DR.RUT = {KEY1}
		            AND D.COD_DEUDOR = DR.COD_DEUDOR
		            AND TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA
		            AND M.COD_MONEDA = TD.COD_MONEDA";
				}
				break;
				////////////////		DD_PLAN_PAGO			///////////////////////////
		case 'DD_PLAN_PAGO':
	if (K_TIPO_BD == 'oci') {
		$sql = "SELECT
					PP.COD_PLAN_PAGO,
					PP.ANO_COBRO V,
					PP.NRO_CUOTA || '|0' NRO_SALDO_CUOTA
				FROM PLAN_PAGO PP
				WHERE PP.COD_DEUDA = {KEY1}
				ORDER BY PP.ANO_COBRO";
	}
	else if (K_TIPO_BD == 'mssql') {
		$sql = "SELECT
					PP.COD_PLAN_PAGO,
					PP.ANO_COBRO V,
					CONVERT(VARCHAR(20), PP.NRO_CUOTA)
						+ '|0' NRO_SALDO_CUOTA
				FROM PLAN_PAGO PP
				WHERE PP.COD_DEUDA = {KEY1}
				ORDER BY PP.ANO_COBRO";
	}
	break;
				////////////////////	PAGO_VIGENTE			/////////////////////////////
				case 'PAGO_VIGENTE' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT    A.COD_ABONO
									, OP.NOM_ORIGEN_PAGO
									, TO_CHAR(A.FECHA_ABONO,'dd/mm/yyyy') FECHA_ABONO
									, A.MONTO_CAPITAL
									, A.MONTO_INTERES                            
									, A.MONTO_MULTA
									, A.MONTO_MONEDA
									, A.MONTO_PESOS
					       		 	, A.COD_DEUDA
									, '' ENVIAR_A_EXCEDENTE
									, 'N' ANULA_ABONO
							FROM  	ABONO A, ORIGEN_PAGO OP,DEUDA DA, DEUDOR DR
							WHERE   OP.COD_ORIGEN_PAGO = A.COD_ORIGEN_PAGO AND
					       	 		DA.COD_DEUDA = A.COD_DEUDA AND
					        		DR.COD_DEUDOR = DA.COD_DEUDOR AND
					        		DR.RUT = {PARAM1} AND
					        		A.COD_ESTADO_PAGO = ".self::K_PAGO_VIGENTE." AND
					        		A.COD_DEUDA = {PARAM2} AND
					        		A.COD_PLAN_PAGO = {PARAM3}";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT    A.COD_ABONO
									, OP.NOM_ORIGEN_PAGO
									, convert(varchar(20), A.FECHA_ABONO, 103) FECHA_ABONO
									, A.MONTO_CAPITAL
									, A.MONTO_INTERES                            
									, A.MONTO_MULTA
									, A.MONTO_MONEDA
									, A.MONTO_PESOS
					       		 	, A.COD_DEUDA
									, '' ENVIAR_A_EXCEDENTE
									, 'N' ANULA_ABONO
							FROM  	ABONO A, ORIGEN_PAGO OP,DEUDA DA, DEUDOR DR
							WHERE   OP.COD_ORIGEN_PAGO = A.COD_ORIGEN_PAGO AND
					       	 		DA.COD_DEUDA = A.COD_DEUDA AND
					        		DR.COD_DEUDOR = DA.COD_DEUDOR AND
					        		DR.RUT = {PARAM1} AND
					        		A.COD_ESTADO_PAGO = ".self::K_PAGO_VIGENTE." AND
					        		A.COD_DEUDA = {PARAM2} AND
					        		A.COD_PLAN_PAGO = {PARAM3}";
				}
				break;
				
				////////////////////	PAGO_DEUDA_NO_EXIGIBLE			/////////////////////////////
				case 'PAGO_DEUDA_NO_EXIGIBLE' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT    A.COD_ABONO
									, OP.NOM_ORIGEN_PAGO
									, TO_CHAR(A.FECHA_ABONO,'dd/mm/yyyy') FECHA_ABONO
									, A.MONTO_CAPITAL
									, A.MONTO_INTERES                            
									, A.MONTO_MULTA
									, A.MONTO_MONEDA
									, A.MONTO_PESOS
					       		 	, A.COD_DEUDA
									, '' ENVIAR_A_EXCEDENTE
									, 'N' ANULA_ABONO
							FROM  	ABONO A, ORIGEN_PAGO OP,DEUDA DA, DEUDOR DR
							WHERE   OP.COD_ORIGEN_PAGO = A.COD_ORIGEN_PAGO AND
					       	 		DA.COD_DEUDA = A.COD_DEUDA AND
					        		DR.COD_DEUDOR = DA.COD_DEUDOR AND
					        		DR.RUT = {PARAM1} AND
					        		A.COD_ESTADO_PAGO = ".self::K_PAGO_VIGENTE." AND
					        		A.COD_DEUDA = {PARAM2}";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT    A.COD_ABONO
									, OP.NOM_ORIGEN_PAGO
									, convert(varchar(20), A.FECHA_ABONO, 103) FECHA_ABONO
									, A.MONTO_CAPITAL
									, A.MONTO_INTERES                            
									, A.MONTO_MULTA
									, A.MONTO_MONEDA
									, A.MONTO_PESOS
					       		 	, A.COD_DEUDA
									, '' ENVIAR_A_EXCEDENTE
									, 'N' ANULA_ABONO
							FROM  	ABONO A, ORIGEN_PAGO OP,DEUDA DA, DEUDOR DR
							WHERE   OP.COD_ORIGEN_PAGO = A.COD_ORIGEN_PAGO AND
					       	 		DA.COD_DEUDA = A.COD_DEUDA AND
					        		DR.COD_DEUDOR = DA.COD_DEUDOR AND
					        		DR.RUT = {PARAM1} AND
					        		A.COD_ESTADO_PAGO = ".self::K_PAGO_VIGENTE." AND
					        		A.COD_DEUDA = {PARAM2}";
				}
				break;				
				
				////////////////////	OBTIENE_NOMBRE		///////////////////////////
				case 'OBTIENE_NOMBRE' :
				if (K_TIPO_BD=="oci"){
					$sql= "SELECT 	COD_DEUDOR, 
									NOMBRE || ' ' || APELLIDO_PATERNO || ' ' ||APELLIDO_MATERNO  NOMBRE
									, DIG_VERIF
							FROM 	DEUDOR
							WHERE 	RUT={PARAM1}
									AND ROWNUM = 1";
				}
				else if (K_TIPO_BD=="mssql"){
					$sql= "SELECT 	COD_DEUDOR, 
									NOMBRE + ' ' + APELLIDO_PATERNO + ' ' + APELLIDO_MATERNO  NOMBRE
									, DIG_VERIF
							FROM 	DEUDOR
							WHERE 	RUT={PARAM1}";
				}
				break;
				
				////////////////////	COMP_APROBADO		///////////////////////////
				case 'COMP_APROBADO' :
				if (K_TIPO_BD=="oci" || K_TIPO_BD=="mssql"){
					$sql= "SELECT 	A.COD_COMPROBANTE
									,C.COD_ESTADO_COMPROBANTE
							FROM 	ABONO A left outer join COMPROBANTE C on  C.COD_COMPROBANTE = A.COD_COMPROBANTE
							WHERE 	A.COD_ABONO = {PARAM1}";
				}
				break;

				
				default:
				base::error("sql_anula_bono::get_sql, selector: '$selector_sql' no definido");
		}
		$keys = func_get_args();
		$sql = sql_tabla::replace_KEYS($sql, $keys);
		return $sql;
	}
}
?>
