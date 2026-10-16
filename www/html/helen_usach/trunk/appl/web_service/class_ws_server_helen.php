<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
ini_set('display_errors', 'On');
	//desarrollo
	//$_SESSION["K_ROOT_DIR"] = "/var/www/html/estudianteswebpay.uct.cl/portal_webpay_uct/web_pay_uct/trunk/";
	//Pre-producccion
	//$_SESSION["K_ROOT_DIR"] = "/var/www/pre-pagos.uct.cl/docs/portal_webpay_uct/web_pay_uct/trunk/";
	//Producccion
	$_SESSION['K_ROOT_DIR'] = "/var/www/new_helen_pro/helen/trunk/";
	require_once("../../appl.ini");

	$a = new session();
	$a->set('K_ROOT_DIR', K_ROOT_DIR);
	require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/clases/class_database.php");
	
	function ws_valida_deudor($rut,$dig_verif){
		
		$cant_deudor = "";
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    	$sql_deuda = "select 	count(*) CANT
    						,PASSWORD
    						,COD_DEUDOR
    						,NOMBRE || ' ' || APELLIDO_PATERNO NOM_DEUDOR
					from 	deudor
					where 	rut='$rut'
					GROUP BY PASSWORD, COD_DEUDOR, NOMBRE, APELLIDO_PATERNO";

		$result_dd = $db->build_results($sql_deuda);
		
		$cant_deudor = $result_dd[0]['CANT'];
		
		if($cant_deudor === ''){
			$codigo_respuesta = 101;
			$mensaje_respuesta = "ERROR BASE DATOS";
			$password = '';
			$nombre = '';
			$cod_deudor = '';
		}
		else if($cant_deudor == 0){
			$codigo_respuesta = 101;
			$mensaje_respuesta = "NO EXISTE DEUDOR EN BASE DATOS";
			$password = '';
			$nombre = '';
			$cod_deudor = '';
		}
		else if($cant_deudor > 0){
			$codigo_respuesta = 100;
			$mensaje_respuesta = "DATOS DEUDOR ENCONTRADOS";
			$password = $result_dd[0]['PASSWORD'];
			$nombre = $result_dd[0]['NOM_DEUDOR'];
			$cod_deudor = $result_dd[0]['COD_DEUDOR'];
		}
		else {
			$codigo_respuesta = 101;
			$mensaje_respuesta = "ERROR DESCONOCIDO";
			$password = '';
			$nombre = '';
			$cod_deudor = '';
		}
		
    	$param_resp = base64_encode(serialize(Array("COD_RESPUESTA"=>$codigo_respuesta, "MENSAJE_RESPUESTA"=>$mensaje_respuesta, "PASS"=>$password, "COD_DEUDOR"=>$cod_deudor, "NOM_DEUDOR"=>$nombre)));
        
		return $param_resp;
	}
	
	function ws_valida_deuda($rut){
		
		$cant_deuda = "";
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    	$sql_deuda = "SELECT   count(*) CANT
					  FROM 		DEUDA DA, DEUDOR DD 
				   	  WHERE DA.COD_DEUDOR = DD.COD_DEUDOR 
				   	  AND DA.COD_ESTADO_DEUDA = 2 --cobranza normal		
				   	  AND DD.RUT = $rut";
									
		$result_dd = $db->build_results($sql_deuda);
		$cant_deuda = $result_dd[0]['CANT'];
		
		if($cant_deuda == ""){
			$codigo_respuesta = 101;
			$mensaje_respuesta = "ERROR BASE DATOS";
		}
		else if($cant_deuda == 0){
			$codigo_respuesta = 101;
			$mensaje_respuesta = "NO EXISTE DEUDA EN BASE DATOS";
		}
		else if($cant_deuda > 0){
			$codigo_respuesta = 100;
			$mensaje_respuesta = "DATOS DEUDA ENCONTRADOS";
		}
		else {
			$codigo_respuesta = 101;
			$mensaje_respuesta = "ERROR DESCONOCIDO";
		}
		
    	$param_resp = base64_encode(serialize(Array("COD_RESPUESTA"=>$codigo_respuesta, "MENSAJE_RESPUESTA"=>$mensaje_respuesta)));
        
		return $param_resp;
	}
	
	function ws_inicio_repro($cod_deudor){
		
		$cant_deuda = "";
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    	$sql_deuda = "select RUT STATIC_RUT
						,(SELECT f_get_parametro(3) from dual) COD_UNIVERSIDAD_DEUDOR
						,RUT
						,DIG_VERIF
						,NOMBRE
						,APELLIDO_PATERNO
						,APELLIDO_MATERNO
						,DIRECCION
						,(SELECT NOM_COMUNA FROM COMUNA CO WHERE CO.COD_COMUNA = DI.COD_COMUNA) NOM_COMUNA
						,(SELECT NOM_CIUDAD FROM CIUDAD CI WHERE CI.COD_CIUDAD = DI.COD_CIUDAD) NOM_CIUDAD
						,EMAIL
						,TELEFONO
						,CELULAR
				from DEUDOR DE,DIRECCION DI
				where DE.COD_DEUDOR = DI.COD_DEUDOR
				and de.COD_DEUDOR =$cod_deudor
				and di.cod_direccion = f_get_direccion_deudor (de.COD_DEUDOR, 'P')
				ORDER BY FECHA_DIRECCION DESC";
									
		$result_deuda = $db->build_results($sql_deuda);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_inicio_repro_2($cod_deudor){
		
		$cant_deuda = "";
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    	$sql_deuda = "SELECT COUNT(*) CANTIDAD
							FROM DEUDA DA,CRUZADO C,TIPO_DEUDA t
							WHERE COD_DEUDOR =$cod_deudor
							AND DA.COD_DEUDA = C.COD_DEUDA
							and t.COD_TIPO_DEUDA = da.COD_TIPO_DEUDA
							and da.COD_ESTADO_DEUDA = 2 --EXIGIBLE
							and da.COD_TIPO_DEUDA not in ('CSR', 'CRPI', 'CR12','CFR','CRI')
							AND (SELECT  nvl(SUM (f_pp_saldo_mora(pp.cod_plan_pago, sysdate)), 0) SALDO_MORA
				        	FROM plan_pago pp
						    WHERE cod_deuda = da.COD_DEUDA
						    and FECHA_VENCTO < add_months(sysdate,-18)) > 0.0000
							and t.COD_FAMILIA_DEUDA = 1 --FONDO_SOLIDARIO";
									
		$result_deuda = $db->build_results($sql_deuda);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_inicio_repro_3($cod_deudor){
		
		$cant_deuda = "";
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    	$sql_deuda = "select da.COD_DEUDA
							 ,DA.COD_TIPO_DEUDA
							 ,CR.COD_UNIVERSIDAD
							 ,(SELECT U.NOM_CORTO_UNIVERSIDAD FROM UNIVERSIDAD U WHERE U.COD_UNIVERSIDAD = CR.COD_UNIVERSIDAD) NOM_UNIVERSIDAD
							 ,ROUND((CR.PORC*sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)))/100,4) SALDO_CAPITAL
							 ,ROUND((f_mon_get_valor_moneda(3, sysdate) * ROUND((CR.PORC*sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)))/100,4)),0) SALDO_CAPITAL_P
							 ,ROUND((CR.PORC*sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate)))/100,4) SALDO_MULTA
							 ,ROUND(f_mon_get_valor_moneda(3, sysdate) *ROUND((CR.PORC*sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate)))/100,4) ,0) SALDO_MULTA_P
							 ,ROUND((CR.PORC*sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)))/100,4)+
							  ROUND((CR.PORC*sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate)))/100,4) SALDO_TOTAL
							 ,ROUND(f_mon_get_valor_moneda(3, sysdate) *
							  (ROUND((CR.PORC*sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)))/100,4)+
							  ROUND((CR.PORC*sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate)))/100,4)),0) SALDO_TOTAL_P
							  ,CR.PORC
					from TIPO_DEUDA t, PLAN_PAGO p,
					DEUDA da LEFT OUTER JOIN CRUZADO CR ON CR.COD_DEUDA = DA.COD_DEUDA
					where da.COD_DEUDOR =$cod_deudor
					and da.COD_ESTADO_DEUDA = 2 --EXIGIBLE
					and da.COD_TIPO_DEUDA not in ('CSR', 'CRPI', 'CR12','CFR','CRI') 
					and t.COD_TIPO_DEUDA = da.COD_TIPO_DEUDA
					and t.COD_FAMILIA_DEUDA = 1 --FONDO_SOLIDARIO
					and p.COD_DEUDA = da.COD_DEUDA
					AND (SELECT  nvl(SUM (f_pp_saldo_mora(pp.cod_plan_pago, sysdate)), 0) SALDO_MORA
				        FROM plan_pago pp
					    WHERE cod_deuda = da.COD_DEUDA
					    and FECHA_VENCTO < add_months(sysdate,-18)) > 0.0000
					group by da.COD_DEUDA,DA.COD_TIPO_DEUDA,CR.PORC, CR.COD_UNIVERSIDAD
					UNION
					select da.COD_DEUDA
							 ,DA.COD_TIPO_DEUDA
							 ,U.COD_UNIVERSIDAD
							 ,U.NOM_CORTO_UNIVERSIDAD NOM_UNIVERSIDAD
							 ,sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)) SALDO_CAPITAL
							 ,ROUND(f_mon_get_valor_moneda(3, sysdate) * 
									(sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end))),0) SALDO_CAPITAL_P
							 ,sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate)) SALDO_MULTA 
							 ,ROUND(f_mon_get_valor_moneda(3, sysdate) *(sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate))) ,0) SALDO_MULTA_P
							 ,sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)) +
							  sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate)) SALDO_TOTAL
							 ,ROUND(f_mon_get_valor_moneda(3, sysdate) *
							  (sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)) +
							  sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate))),0) SALDO_TOTAL_P
							  ,NULL
					from DEUDA da, TIPO_DEUDA t, PLAN_PAGO p,UNIVERSIDAD U
					where da.COD_DEUDOR =$cod_deudor
					and da.COD_ESTADO_DEUDA = 2 --EXIGIBLE
					and da.COD_TIPO_DEUDA not in ('CSR', 'CRPI', 'CR12','CFR','CRI') 
					and t.COD_TIPO_DEUDA = da.COD_TIPO_DEUDA
					AND DA.COD_UNIVERSIDAD = U.COD_UNIVERSIDAD
					and t.COD_FAMILIA_DEUDA = 1 --FONDO_SOLIDARIO
					and p.COD_DEUDA = da.COD_DEUDA
					AND DA.COD_DEUDA NOT IN (SELECT COD_DEUDA FROM CRUZADO)
					AND (SELECT  nvl(SUM (f_pp_saldo_mora(pp.cod_plan_pago, sysdate)), 0) SALDO_MORA
				        FROM plan_pago pp
					    WHERE cod_deuda = da.COD_DEUDA
					    and FECHA_VENCTO < add_months(sysdate,-18)) > 0.0000
					group by da.COD_DEUDA,DA.COD_TIPO_DEUDA,U.NOM_CORTO_UNIVERSIDAD,U.COD_UNIVERSIDAD";
									
		$result_deuda = $db->build_results($sql_deuda);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_inicio_repro_4($cod_deudor){
		
		$cant_deuda = "";
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    	$sql_deuda = "select da.COD_DEUDA
							 ,DA.COD_TIPO_DEUDA
							 ,U.COD_UNIVERSIDAD
							 ,U.NOM_CORTO_UNIVERSIDAD NOM_UNIVERSIDAD
							 ,sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)) SALDO_CAPITAL
							 ,ROUND(f_mon_get_valor_moneda(3, sysdate) * 
									(sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end))),0) SALDO_CAPITAL_P
							 ,sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate)) SALDO_MULTA 
							 ,ROUND(f_mon_get_valor_moneda(3, sysdate) *(sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate))) ,0) SALDO_MULTA_P
							 ,sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)) +
							  sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate)) SALDO_TOTAL
							 ,ROUND(f_mon_get_valor_moneda(3, sysdate) *
							  (sum(f_pp_saldo_capital(p.cod_plan_pago, sysdate) + (case WHEN p.COD_CONCEPTO_COBRO = 11 AND p.ANO_COBRO > To_Char(SYSDATE,'yyyy')
                                                                                                then 0
                                                                                                else 
                                                                                                    f_pp_saldo_interes_normal(p.cod_plan_pago, sysdate)
                                                                                        end)) +
							  sum(f_pp_saldo_multa(p.cod_plan_pago, sysdate))),0) SALDO_TOTAL_P
					from DEUDA da, TIPO_DEUDA t, PLAN_PAGO p,UNIVERSIDAD U
					where da.COD_DEUDOR =$cod_deudor
					and da.COD_ESTADO_DEUDA = 2 --EXIGIBLE
					and da.COD_TIPO_DEUDA not in ('CSR', 'CRPI', 'CR12','CFR','CRI') 
					and t.COD_TIPO_DEUDA = da.COD_TIPO_DEUDA
					AND DA.COD_UNIVERSIDAD = U.COD_UNIVERSIDAD
					and t.COD_FAMILIA_DEUDA = 1 --FONDO_SOLIDARIO
					and p.COD_DEUDA = da.COD_DEUDA
					AND (SELECT  nvl(SUM (f_pp_saldo_mora(pp.cod_plan_pago, sysdate)), 0) SALDO_MORA
				        FROM plan_pago pp
					    WHERE cod_deuda = da.COD_DEUDA
					    and FECHA_VENCTO < add_months(sysdate,-18)) > 0.0000
					group by da.COD_DEUDA,DA.COD_TIPO_DEUDA,U.NOM_CORTO_UNIVERSIDAD,U.COD_UNIVERSIDAD";
									
		$result_deuda = $db->build_results($sql_deuda);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_datos_deuda($cod_deudor){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    	$sql_deuda = "SELECT	D.RUT
					    ,D.DIG_VERIF
					    ,D.NOMBRE||' '||D.APELLIDO_PATERNO||' '||D.APELLIDO_MATERNO NOMBRE
					    ,DIP.DIRECCION  DIRECCION
					    ,DIP.TELEFONO FONO_PARTICULAR
					    ,DIP.EMAIL EMAIL_PARTICULAR
					    ,DIC.DIRECCION DIRECCION_COMERCIAL
					    ,DIC.TELEFONO FONO_COMERCIAL
					    ,DIC.EMAIL EMAIL_COMERCIAL
					    ,CASE
							WHEN CANDIDATO_17_BIS = 'N' THEN 'none'
							WHEN CANDIDATO_17_BIS = 'S' THEN
								case
									WHEN f_repro_rcs_es_candidato(D.COD_DEUDOR,'WEBPAY') = 'N' THEN 'none'
									WHEN f_repro_rcs_es_candidato(D.COD_DEUDOR,'WEBPAY') = 'S' THEN ''
								END
							WHEN CANDIDATO_17_BIS IS null THEN
								case 
									WHEN f_repro_rcs_es_candidato(D.COD_DEUDOR,'WEBPAY') = 'N' THEN 'none'
									WHEN f_repro_rcs_es_candidato(D.COD_DEUDOR,'WEBPAY') = 'S' THEN ''
								END
							END DIV_REPRO
						,CASE
							WHEN CANDIDATO_17_BIS = 'N' THEN ''
							WHEN CANDIDATO_17_BIS = 'S' THEN
								case
									WHEN f_repro_rcs_es_candidato(D.COD_DEUDOR,'WEBPAY') = 'N' THEN ''
									WHEN f_repro_rcs_es_candidato(D.COD_DEUDOR,'WEBPAY') = 'S' THEN 'href=\"simulacion_RCS/inicio.php?cod_deudor='|| To_Char(D.COD_DEUDOR) ||'\"'
								END
							WHEN CANDIDATO_17_BIS IS null THEN
								case
									WHEN f_repro_rcs_es_candidato(D.COD_DEUDOR,'WEBPAY') = 'N' THEN ''
									WHEN f_repro_rcs_es_candidato(D.COD_DEUDOR,'WEBPAY') = 'S' THEN 'href=\"simulacion_RCS/inicio.php?cod_deudor='|| To_Char(D.COD_DEUDOR) ||'\"'
								END
						END URL
				FROM 	DEUDOR D
					    ,DIRECCION DIP
					    ,DIRECCION DIC
				WHERE 	D.COD_DEUDOR=$cod_deudor
					    AND D.COD_DEUDOR = DIP.COD_DEUDOR(+)
					    AND D.COD_DEUDOR = DIC.COD_DEUDOR(+)
					    AND DIP.TIPO_DIRECCION(+) = 'P'
					    AND DIC.TIPO_DIRECCION(+)= 'C'
				        AND DIP.COD_DIRECCION(+) = f_get_direccion_deudor(D.COD_DEUDOR,'P')
				        AND DIC.COD_DIRECCION(+) = f_get_direccion_deudor(D.COD_DEUDOR,'C')";
									
		$result_deuda = $db->build_results($sql_deuda);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_datos_deudor_exito($rut){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    	$sql_pago	=  "SELECT	NOMBRE ||' '|| APELLIDO_PATERNO || ' '|| APELLIDO_MATERNO NOMBRE
							  FROM	deudor
							 WHERE	to_char(RUT) || DIG_VERIF ='$rut'";
									
		$result_deuda = $db->build_results($sql_pago);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_datos_deudor($rut){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    	$sql_deuda = "SELECT RUT
						    ,DIG_VERIF
							,NOMBRE
							,APELLIDO_PATERNO 
							,APELLIDO_MATERNO
                            ,COD_DEUDOR
					  FROM DEUDOR
					  WHERE RUT = $rut";
									
		$result_deuda = $db->build_results($sql_deuda);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_datos_ciudad($cod_region){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    	$sql = "SELECT 		COD_CIUDAD,
										NOM_CIUDAD
							FROM 		CIUDAD
							WHERE 		COD_REGION =$cod_region
							AND      COD_CIUDAD <> 99999
							ORDER BY	NOM_CIUDAD";
									
		$result_deuda = $db->build_results($sql);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_datos_comuna($cod_ciudad){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    	$sql = "select 		COD_COMUNA,
										NOM_COMUNA
							from 		COMUNA
							where 		COD_CIUDAD =$cod_ciudad
							order by	NOM_COMUNA";
									
		$result_deuda = $db->build_results($sql);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_datos_region($cod_pais){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    	$sql = "SELECT 		COD_REGION,
										NOM_REGION
							FROM 		REGION
							WHERE 		COD_PAIS =$cod_pais
							AND      COD_REGION <> 99999
							ORDER BY	COD_REGION";
									
		$result_deuda = $db->build_results($sql);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_actualiza_direccion($ve_cod_direccion, $cod_deudor, $tipo_direccion, $direccion, $cod_comuna, $cod_ciudad, $cod_pais, $email, $telefono, $celular){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    	$sp = 'spu_direccion';
		
    	$operacion = 'INSERT';
		$param	= "'$operacion'
				,'$cod_direccion'
				,$cod_deudor
				,'$tipo_direccion'
				,'$direccion'
				,$cod_comuna
				,$cod_ciudad
				,$cod_pais
				,'$email'
				,'$telefono'
				,'$celular'
				,'S'
				,1
				,null
				,null
				,null";
		
		$db->BEGIN_TRANSACTION();
		if ($db->EXECUTE_SP($sp, $param)){
			$db->COMMIT_TRANSACTION();	
			$codigo_respuesta = 100;
			$mensaje_respuesta = "OK";
		}else{
			$db->ROLLBACK_TRANSACTION();
			$codigo_respuesta = 101;
			$mensaje_respuesta = "ERROR BASE DATOS";
		}
		
		$param_resp = base64_encode(serialize(Array("COD_RESPUESTA"=>$codigo_respuesta, "MENSAJE_RESPUESTA"=>$mensaje_respuesta)));
		return $param_resp;
	}
	
	function ws_datos_direccion_p($cod_deudor){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    	$sql_deuda = "SELECT	COD_DIRECCION 			COD_DIRECCION_PARTICULAR
									, DIRECCION 			DIRECCION_PARTICULAR
									, TO_CHAR(FECHA_DIRECCION,'DD/MM/YYYY')	FECHA_DIRECCION_PARTICULAR
									, COD_COMUNA 			COD_COMUNA_PARTICULAR
	     							, DIR.COD_CIUDAD 		COD_CIUDAD_PARTICULAR
	     							, COD_REGION			COD_REGION_PARTICULAR
									, DIR.COD_PAIS	  		COD_PAIS_PARTICULAR
									, EMAIL	  				EMAIL_PARTICULAR
									, EMAIL	  				EMAIL_PARTICULAR_H
					     			, TELEFONO	  			TELEFONO_PARTICULAR
					     			, CELULAR	  			CELULAR_PARTICULAR
					     			, DIRECCION 			DIRECCION_PARTICULAR_H
	     							, COD_COMUNA			COD_COMUNA_PARTICULAR_H
	     							, DIR.COD_CIUDAD		COD_CIUDAD_PARTICULAR_H
	     							, COD_REGION			COD_REGION_PARTICULAR_H
	     							, DIR.COD_PAIS	  		COD_PAIS_PARTICULAR_H
	     							, TELEFONO	  			TELEFONO_PARTICULAR_H
	     							, CELULAR 				CELULAR_PARTICULAR_H
					     			, ES_VALIDA				ES_VALIDA_PARTICULAR
					     			, ''		 			EXT_COMUNA_PARTICULAR
	     							, ''					EXT_CIUDAD_PARTICULAR
	     							, ''					EXT_REGION_PARTICULAR
	     							,Decode(DIR.COD_PAIS,f_get_constante('K_CHILE'),'','none')  DISPLAY_PAIS_CHILE_PARTICULAR
									,Decode(DIR.COD_PAIS,f_get_constante('K_CHILE'),'none','')  DISPLAY_EXTRANJERO_PARTICULAR
	  						FROM 	DIRECCION   DIR
        							,CIUDAD         C
	 						WHERE 	COD_DEUDOR =$cod_deudor
	 								AND DIR.COD_CIUDAD= C.COD_CIUDAD (+)
	   								AND COD_DIRECCION = F_GET_DIRECCION_DEUDOR (COD_DEUDOR, 'P')";
									
		$result_deuda = $db->build_results($sql_deuda);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_datos_inicio($cod){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    	$sql_deuda = "SELECT	TO_CHAR(SYSDATE,'DD/MM/YYYY')FECHA_ACTUAL
        			,(SELECT	MONTO_VALOR_MONEDA FROM VALOR_MONEDA WHERE COD_MONEDA = 3 AND TO_CHAR(FECHA_VALOR_MONEDA,'DD/MM/YYYY') = TO_CHAR(SYSDATE,'DD/MM/YYYY')) MONTO_VALOR_MONEDA FROM DUAL";
									
		$result_deuda = $db->build_results($sql_deuda);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_datos_direccion_c($cod_deudor){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    	$sql_deuda = "SELECT	COD_DIRECCION 			COD_DIRECCION_COMERCIAL
									, DIRECCION 			DIRECCION_COMERCIAL
									, TO_CHAR(FECHA_DIRECCION,'DD/MM/YYYY')	FECHA_DIRECCION_COMERCIAL
									, COD_COMUNA 			COD_COMUNA_COMERCIAL
	     							, DIR.COD_CIUDAD 		COD_CIUDAD_COMERCIAL
	     							, COD_REGION			COD_REGION_COMERCIAL
									, DIR.COD_PAIS	  		COD_PAIS_COMERCIAL
									, EMAIL	  				EMAIL_COMERCIAL
									, EMAIL	  				EMAIL_COMERCIAL_H
					     			, TELEFONO	  			TELEFONO_COMERCIAL
					     			, CELULAR	  			CELULAR_COMERCIAL
					     			, DIRECCION 			DIRECCION_COMERCIAL_H
	     							, COD_COMUNA			COD_COMUNA_COMERCIAL_H
	     							, DIR.COD_CIUDAD		COD_CIUDAD_COMERCIAL_H
	     							, COD_REGION			COD_REGION_COMERCIAL_H
	     							, DIR.COD_PAIS	  		COD_PAIS_COMERCIAL_H
	     							, TELEFONO	  			TELEFONO_COMERCIAL_H
	     							, CELULAR 				CELULAR_COMERCIAL_H
					     			, ES_VALIDA				ES_VALIDA_COMERCIAL
					     			, ''		 			EXT_COMUNA_COMERCIAL
	     							, ''					EXT_CIUDAD_COMERCIAL
	     							, ''					EXT_REGION_COMERCIAL
	     							,Decode(DIR.COD_PAIS,f_get_constante('K_CHILE'),'','none')  DISPLAY_PAIS_CHILE_COMERCIAL
									,Decode(DIR.COD_PAIS,f_get_constante('K_CHILE'),'none','')  DISPLAY_EXTRANJERO_COMERCIAL
	  						FROM 	DIRECCION   DIR
        							,CIUDAD         C
	 						WHERE 	COD_DEUDOR =$cod_deudor
	 								AND DIR.COD_CIUDAD= C.COD_CIUDAD (+)
	   								AND COD_DIRECCION = F_GET_DIRECCION_DEUDOR (COD_DEUDOR, 'C')";
									
		$result_deuda = $db->build_results($sql_deuda);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_valida_plan_pago($rut, $cod_deuda){
		
		$cant_pp = "";
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		//se puede consultar por rut o c�digo deuda los palnes de pagos vigentes en estado emitido, abonado y moroso
    	$sql_deuda = "SELECT   count(*) CANT
					  FROM 		DEUDA DA, DEUDOR DD , PLAN_PAGO PP
				   	  WHERE DA.COD_DEUDOR = DD.COD_DEUDOR 
				   	  AND PP.COD_DEUDA = DA.COD_DEUDA	
				   	  AND DA.COD_DEUDA =$cod_deuda
				   	  AND PP.COD_ESTADO_PLAN_PAGO in (1, 2 , 4)";
									
		$result_dd = $db->build_results($sql_deuda);
		$cant_pp = $result_dd[0]['CANT'];
		
		if($cant_pp == ""){
			$codigo_respuesta = 101;
			$mensaje_respuesta = "ERROR BASE DATOS";
		}
		else if($cant_pp == 0){
			$codigo_respuesta = 101;
			$mensaje_respuesta = "NO EXISTE PLAN PAGO EN BASE DATOS";
		}
		else if($cant_pp > 0){
			$codigo_respuesta = 100;
			$mensaje_respuesta = "DATOS DEUDA ENCONTRADOS";
		}
		else {
			$codigo_respuesta = 101;
			$mensaje_respuesta = "ERROR DESCONOCIDO";
		}
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_detalle_deuda($cod_deuda){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql_detalle_deuda = "SELECT RUT, DIG_VERIF
					        ,NOMBRE||' '||APELLIDO_PATERNO||' '||APELLIDO_MATERNO NOMBRE
					        ,COD_DEUDA
					        ,TD.COD_TIPO_DEUDA
					        ,SIMBOLO
					FROM    DEUDA D
					        ,DEUDOR DR
					        ,MONEDA M
					        ,TIPO_DEUDA TD
					WHERE   COD_DEUDA =".$cod_deuda."
					        AND DR.COD_DEUDOR = d.COD_DEUDOR
					        AND M.COD_MONEDA = TD.COD_MONEDA
					        AND TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA";
									
		$result_deuda = $db->build_results($sql_detalle_deuda);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_ajax_plan_pago_1($cod_deuda){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql_detalle_deuda = "SELECT CASE Count(*) when 0 then 'N' else 'S' end EXISTE_CUOTA_ACTUAL 
								FROM plan_pago
								WHERE cod_deuda =$cod_deuda
								AND ano_cobro = To_Number(To_Char(SYSDATE, 'yyyy'))";
									
		$result_deuda = $db->build_results($sql_detalle_deuda);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_ajax_plan_pago_2($cod_deuda){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql_detalle_deuda = "SELECT COD_PLAN_PAGO 
								FROM plan_pago
								WHERE cod_deuda =$cod_deuda
								AND NRO_CUOTA = 99";
									
		$result_deuda = $db->build_results($sql_detalle_deuda);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_ajax_plan_pago_3($cod_deuda){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql_detalle_deuda = "SELECT	 pp.COD_PLAN_PAGO
									,pp.NRO_CUOTA
									,pp.ANO_COBRO
									,to_char(pp.FECHA_VENCTO, 'dd/mm/yyyy') FECHA_VENCTO
									,f_pp_saldo_plan_pago(pp.COD_PLAN_PAGO, sysdate) SALDO
									,round(f_pp_saldo_plan_pago(pp.COD_PLAN_PAGO, sysdate) * f_mon_get_valor_moneda(td.COD_MONEDA, sysdate),0) SALDO_PESOS
									,'S' ES_RADIO_BUTTON
							FROM	plan_pago pp, deuda de, tipo_deuda td
							WHERE	pp.COD_DEUDA =$cod_deuda		
							AND		pp.COD_DEUDA = de.COD_DEUDA
							AND		de.COD_TIPO_DEUDA = td.COD_TIPO_DEUDA
							AND		pp.COD_ESTADO_PLAN_PAGO NOT IN (3, 4, 8, 6)	-- PAGADA, MOROSA, EXCENTO, SUSPENSION
							ORDER BY pp.NRO_CUOTA ASC";
									
		$result_deuda = $db->build_results($sql_detalle_deuda);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_ajax_plan_pago_4($cod_deuda,$cod_pp_99){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql_detalle_deuda = "SELECT	 pp.COD_PLAN_PAGO
									,pp.NRO_CUOTA
									,pp.ANO_COBRO
									,to_char(pp.FECHA_VENCTO, 'dd/mm/yyyy') FECHA_VENCTO
									,f_pp_saldo_plan_pago(pp.COD_PLAN_PAGO, sysdate) SALDO
									,round(f_pp_saldo_plan_pago(pp.COD_PLAN_PAGO, sysdate) * f_mon_get_valor_moneda(td.COD_MONEDA, sysdate),0) SALDO_PESOS
									,'S' ES_RADIO_BUTTON
							FROM	plan_pago pp, deuda de, tipo_deuda td
							WHERE	pp.COD_DEUDA = $cod_deuda
							AND		pp.COD_PLAN_PAGO <> $cod_pp_99
							AND		pp.COD_DEUDA = de.COD_DEUDA
							AND		de.COD_TIPO_DEUDA = td.COD_TIPO_DEUDA
							AND		pp.COD_ESTADO_PLAN_PAGO NOT IN (3, 4, 8, 6)	--  PAGADA, MOROSA, EXCENTO, SUSPENSION
							ORDER BY pp.NRO_CUOTA ASC";
									
		$result_deuda = $db->build_results($sql_detalle_deuda);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_pago_online_1($cod_plan_pago){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql_detalle_deuda = "SELECT	 COD_DEUDA
									,NRO_CUOTA
									,ANO_COBRO
							FROM PLAN_PAGO
							WHERE COD_PLAN_PAGO =$cod_plan_pago";
									
		$result_deuda = $db->build_results($sql_detalle_deuda);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_validationResult($wp_transaccion){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT COUNT(BT.COD_WP_TRANSACCION)CONTADOR
			FROM BITACORA_TRANSACCION BT
			WHERE BT.COD_WP_TRANSACCION =$wp_transaccion
			AND BT.FASE = 'INICIO TRANSACCION'
			AND BT.MENSAJE = 'Inicio Transaccion Exitoso'";
									
		$result_deuda = $db->build_results($sql);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_pago_confirmacion($token){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql="select EXITO from WP_TRANSACCION where TOKEN ='$token'";
									
		$result_deuda = $db->build_results($sql);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_datos_exito($cod_wp_transaccion){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql	=  "SELECT DE.RUT||DE.DIG_VERIF RUT
				        ,B.MONTO_PESOS
				        ,WT.COD_WP_TRANSACCION
				        ,TO_CHAR(WT.FECHA,'dd/mm/yyyy hh24:mi:ss') FECHA
				        ,WP.CODIGO_AUTORIZACION TBK_CODIGO_AUTORIZACION
				        ,WP.NUMERO_TARJETA TBK_FINAL_NUMERO_TARJETA
				        ,WP.CANTIDAD_CUOTA TBK_NUMERO_CUOTAS
				        ,WP.TIPO_PAGO TBK_TIPO_PAGO
				        ,WP.ORDEN_COMPRA_TIENDA TBK_ORDEN_COMPRA
				FROM WP_TRANSACCION WT, WP_PAGO_TRANSACCION_WS WP, WP_SUB_BOLETA WS, SUB_BOLETA S, BOLETA B, DEUDOR DE
				WHERE WT.COD_WP_TRANSACCION =$cod_wp_transaccion
				AND WS.COD_WP_TRANSACCION = WT.COD_WP_TRANSACCION
				AND S.COD_SUB_BOLETA = WS.COD_SUB_BOLETA
				AND B.COD_BOLETA = S.COD_BOLETA
				AND WT.COD_WP_TRANSACCION = WP.COD_WP_TRANSACCION
				AND WT.COD_DEUDOR = DE.COD_DEUDOR";
									
		$result_deuda = $db->build_results($sql);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_bitacora_transaccion($wp_transaccion, $fase, $msg){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		
		$sp = 'spw_bitacora_transaccion';
		$param	= "$wp_transaccion, '$fase', '$msg'"; 

		if ($db->EXECUTE_SP($sp, $param)){
			$codigo_respuesta = 100;
			$mensaje_respuesta = "OK";
		}else{
			$codigo_respuesta = 101;
			$mensaje_respuesta = "ERROR BASE DATOS";
		}
		
		$param_resp = base64_encode(serialize(Array("COD_RESPUESTA"=>$codigo_respuesta, "MENSAJE_RESPUESTA"=>$mensaje_respuesta)));
		return $param_resp;
	}
	
	function ws_cambio_clave($cod_deudor, $clave_nueva){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		
		$sp = 'spu_deudor';
		$param = "'CHANGE_PASSWORD'
					,$cod_deudor
					,null	
					,null	
					,null	
					,null	
					,null	
					,null	
					,null	
					,null	
					,null	
					,'$clave_nueva'
					,null";

		if ($db->EXECUTE_SP($sp, $param)){
			$codigo_respuesta = 100;
			$mensaje_respuesta = "OK";
		}else{
			$codigo_respuesta = 101;
			$mensaje_respuesta = "ERROR BASE DATOS";
		}
		
		$param_resp = base64_encode(serialize(Array("COD_RESPUESTA"=>$codigo_respuesta, "MENSAJE_RESPUESTA"=>$mensaje_respuesta)));
		return $param_resp;
	}
	
	function ws_init_transaccion($wp_transaccion, $token){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		
		$sp = 'spw_wp_init_transaccion';
		$param	= "$wp_transaccion, '$token'"; 

		if ($db->EXECUTE_SP($sp, $param)){
			$codigo_respuesta = 100;
			$mensaje_respuesta = "OK";
		}else{
			$codigo_respuesta = 101;
			$mensaje_respuesta = "ERROR BASE DATOS";
		}
		
		$param_resp = base64_encode(serialize(Array("COD_RESPUESTA"=>$codigo_respuesta, "MENSAJE_RESPUESTA"=>$mensaje_respuesta)));
		return $param_resp;
	}
	
	function ws_pago_transaccion($operacion
							,$orden_compra
							,$sessionId
							,$numeroTarjeta
							,$expiracion_tarjeta
							,$fecha_autorizacion
							,$fecha_hora_autorizacion
							,$resultado_autenticacion
							,$urlRedirection
							,$codigo_autorizacion
							,$tipo_transaccion
							,$respuesta_pago
							,$monto_pago
							,$null
							,$cantidad_cuota
							,$codigo_comercio
							,$orden_compra_tienda
							,$token){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		
		$param = "'$operacion'
			,$orden_compra
			,'$sessionId'
			,'$numeroTarjeta'
			,'$expiracion_tarjeta'
			,'$fecha_autorizacion'
			,'$fecha_hora_autorizacion'
			,'$resultado_autenticacion'
			,'$urlRedirection'
			,'$codigo_autorizacion'
			,'$tipo_transaccion'
			,'$respuesta_pago'
			,$monto_pago
			,$null
			,$cantidad_cuota
			,'$codigo_comercio'
			,'$orden_compra_tienda'";
	
		$sp = 'sp_wp_pago_transaccion_ws';

		$fase = "TRANSICION";
		
		if(!$db->EXECUTE_SP($sp,$param)){ 
			$codigo_respuesta = 101;
			$data = "RECHAZADO";
			$sp_bitacora = 'spw_bitacora_transaccion';
			$param = "$orden_compra,'$fase','Error sp_wp_pago_transaccion_ws' $param";
			$db->EXECUTE_SP($sp_bitacora,$param);
			
		}else{ 
			//- validar orden de compra
			$sql = "SELECT COUNT(*) CANT
					FROM WP_TRANSACCION
					WHERE COD_WP_TRANSACCION = $orden_compra
					AND EXITO = 'S'";
			$db->query($sql);
			$result = $db->get_row();
			$cant = $result['CANT'];
			
			// validar monto
			$sql = "SELECT B.MONTO_PESOS
					,WP.TOKEN
					FROM WP_TRANSACCION  WP
					,BOLETA B
					,WP_SUB_BOLETA WPS
					,SUB_BOLETA SB 
					WHERE WP.COD_WP_TRANSACCION = $orden_compra
					AND WPS.COD_WP_TRANSACCION = WP.COD_WP_TRANSACCION
					AND SB.COD_SUB_BOLETA = WPS.COD_SUB_BOLETA
					AND B.COD_BOLETA = SB.COD_BOLETA ";
			$db->query($sql);
			$result = $db->get_row();
			$monto_guardado = $result['MONTO_PESOS'];
			$token_guardado = $result['TOKEN'];
			$token_guardado = trim($token_guardado);
			$token = trim($token);
			if($cant > 0){//COMPROBAR QUE NO ESTE DUPLICADA
				$codigo_respuesta = 101;
				$urlRedirection = $url_fracaso; 
				$data = "DUPLICADA";
				$sp_bitacora = 'spw_bitacora_transaccion';
				$param = "$orden_compra,'$fase','DUPLICADA'";
				$db->EXECUTE_SP($sp_bitacora,$param);
				
			}else if($monto_guardado != $monto_pago){ 
				$codigo_respuesta = 101;
			 	$urlRedirection = $url_fracaso; 
			 	$data = "RECHAZADO";
			 	
			 	$sp_bitacora = 'spw_bitacora_transaccion';
				$param = "$orden_compra,'$fase','MONTOS DIFERENTES $monto_guardado VS $monto_pago'";
				$db->EXECUTE_SP($sp_bitacora,$param);
				
			}else if($token_guardado != $token){
				$codigo_respuesta = 101;
				$data = "RECHAZADO";
				$urlRedirection = $url_fracaso; 
				
				$sp_bitacora = 'spw_bitacora_transaccion';
				$param = "$orden_compra,'$fase','TOKENS DIFERENTES $token_guardado - $token'";
				$db->EXECUTE_SP($sp_bitacora,$param);
				
			}else{
				//SE INFORMA A TRANSBANK QUE SE HIZO UNA TRANSACCION
				
				
				if($respuesta_pago != '0'){	
					$codigo_respuesta = 101;
					$data = "RECHAZADO RESPUESTA NO ES 0";
					$urlRedirection = $url_fracaso; 
					$sp_bitacora = 'spw_bitacora_transaccion';
					$param = "$orden_compra,'$fase','RESPUESTA DE PAGO NO FUE 0'";
					$db->EXECUTE_SP($sp_bitacora,$param);
					
				}else if($respuesta_pago == '0'){
					//si todas las validaciones OK
					
					$sp = "spw_web_pay_pago";
					$param = "$orden_compra, '1'";
					if(!$db->EXECUTE_SP($sp,$param)){ 
						$codigo_respuesta = 101;
						$urlRedirection = $url_fracaso;
						$data = "spw_web_pay_pago error";
						$sp_bitacora = 'spw_bitacora_transaccion';
						$param = "$orden_compra,'$fase','ERROR spw_web_pay_pago'";
						$db->EXECUTE_SP($sp_bitacora,$param);
						
					}else{
						$codigo_respuesta = 100;
						$data = "ACEPTADO";
						$sp_bitacora = 'spw_bitacora_transaccion';
						$param = "$orden_compra,'$fase','TRANSICION EXITOSA'";
						$db->EXECUTE_SP($sp_bitacora,$param);
						$action = "CONFIRMACION";
						
					}	
				} 
			}
		}
		
		
		$param_resp = base64_encode(serialize(Array("COD_RESPUESTA"=>$codigo_respuesta, "MENSAJE_RESPUESTA"=>$data)));
		return $param_resp;
	}
	
      		
	function ws_crea_sub_boleta($tipo_pago,$cod_deudor,$cod_sub_boletas,$cod_plan_pago,$monto_capital,$monto_interes,$monto_multa,$monto_moneda,$monto_pesos,$cod_tipo_descuento,$dscto_utm,$dscto_pesos,$tipo_pago_web){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		
		$sp = 'spw_wp_transaccion';
		$param = "'$tipo_pago'
				 ,$cod_deudor
				 ,'$cod_sub_boletas'
				 ,$cod_plan_pago
				 ,$monto_capital
				 ,$monto_interes
				 ,$monto_multa
				 ,$monto_moneda
				 ,$monto_pesos
				 ,$cod_tipo_descuento
				 ,$dscto_utm
				 ,$dscto_pesos
				 ,'$tipo_pago_web'";
				 
		if ($db->EXECUTE_SP($sp, $param)){
			$codigo_respuesta = 100;
			$mensaje_respuesta = "OK";
			$cod_transaccion = $db->GET_IDENTITY("WP_TRANSACCION");
			
			$db2 = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$sql_deudor = "SELECT to_char(RUT) || DIG_VERIF RUT
							FROM	DEUDOR DR, WP_TRANSACCION WP
							WHERE DR.COD_DEUDOR = WP.COD_DEUDOR
							AND COD_WP_TRANSACCION =$cod_transaccion";
			$result_deudor = $db2->build_results($sql_deudor);
			$rut = $result_deudor[0]['RUT'];
		
		}else{
			$codigo_respuesta = 101;
			$mensaje_respuesta = "ERROR BASE DATOS";
			$cod_transaccion = '';
			$rut = '';
		}
		
		$param_resp = base64_encode(serialize(Array("COD_RESPUESTA"=>$codigo_respuesta, "MENSAJE_RESPUESTA"=>$mensaje_respuesta, "COD_TRANSACCION"=>$cod_transaccion, "RUT"=>$rut)));
		return $param_resp;
	}
	
	function ws_pago_online_2($cod_deuda, $cod_plan_pago, $cod_tipo_descuento, $monto_abono_pesos){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		
		$sp = 'sp_boleta_calculo_web';
		$param	= "$cod_deuda, $cod_plan_pago, $cod_tipo_descuento, $monto_abono_pesos"; 

		$db->EXECUTE_SP($sp, $param);
		
		$sql_detalle_deuda = "SELECT	 D_MONTO_CAPITAL	
									,D_MONTO_INTERES
									,D_MONTO_MULTA
									,D_MONTO_PESOS
									,D_DSCTO_CAPITAL
									,D_DSCTO_INTERES
									,D_DSCTO_MULTA
									,D_DSCTO_PESOS
							FROM	TEMP_TOTAL_BOLETA";
									
		$result_deuda = $db->build_results($sql_detalle_deuda);
		
		$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_pago_online_3($cod_deuda){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql_detalle_deuda = "SELECT	 D_MONTO_CAPITAL	
									,D_MONTO_INTERES
									,D_MONTO_MULTA
									,D_MONTO_PESOS
									,D_DSCTO_CAPITAL
									,D_DSCTO_INTERES
									,D_DSCTO_MULTA
									,D_DSCTO_PESOS
							FROM	TEMP_TOTAL_BOLETA";
									
		$result_deuda = $db->build_results($sql_detalle_deuda);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_pago_online_4($cod_deudor){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql_detalle_deuda = "SELECT	 D.NOMBRE TBK_NOMBRE 
								    ,D.APELLIDO_PATERNO || ' ' || D.APELLIDO_MATERNO TBK_APELLIDO
								    ,DIP.DIRECCION TBK_DIRECCION
								    ,DIP.TELEFONO TBK_FONO
								    ,DIP.EMAIL TBK_EMAIL
									,CI.NOM_CIUDAD TBK_CIUDAD
									,PS.NOM_PAIS TBK_PAIS
							FROM 	DEUDOR D left outer	join DIRECCION DIP on DIP.COD_DIRECCION = f_get_direccion_deudor(D.COD_DEUDOR,'P')
											 left outer join COMUNA CO on DIP.COD_COMUNA = CO.COD_COMUNA
											 left outer join CIUDAD CI on DIP.COD_CIUDAD = CI.COD_CIUDAD
											 left outer join PAIS PS on DIP.COD_PAIS = PS.COD_PAIS
							WHERE 	D.COD_DEUDOR =$cod_deudor";
									
		$result_deuda = $db->build_results($sql_detalle_deuda);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_datos_webpay_cupon($tbk_rut){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql_pago	=  "SELECT	NOMBRE ||' '|| APELLIDO_PATERNO || ' '|| APELLIDO_MATERNO NOMBRE
							  FROM	deudor
							 WHERE	to_char(RUT) || DIG_VERIF ='$tbk_rut'";
									
		$result_deuda = $db->build_results($sql_pago);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_obtiene_deuda($cod_deudor){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql_deuda = "SELECT	COD_DEUDA
								,'' COD_DEUDA_DECODE
						        ,NOM_ESTADO_DEUDA
						        ,D.COD_TIPO_DEUDA
						        ,SIMBOLO
						        ,To_Char(FECHA_INICIO_COBRO,'dd/mm/yyyy') FECHA_INICIO_COBRO
						        ,f_deuda_saldo_total(COD_DEUDA, sysdate)   SALDO
						        ,f_deuda_saldo_total(COD_DEUDA, sysdate) * f_mon_get_valor_moneda(td.cod_moneda) SALDO_PESOS
						        ,D.COD_ESTADO_DEUDA
						        ,D.COD_CARTERA
						        ,TD.COD_FAMILIA_DEUDA
						        ,To_Number(To_Char(FECHA_INICIO_COBRO, 'yyyy')) ANO_INICIO_COBRO
					   FROM    	DEUDA D
						        ,ESTADO_DEUDA ED
						        ,MONEDA M
						        ,TIPO_DEUDA TD
					   WHERE   	D.COD_ESTADO_DEUDA = ED.COD_ESTADO_DEUDA
						        AND M.COD_MONEDA = TD.COD_MONEDA
						        AND TD.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA 
						               						AND D.COD_DEUDA NOT IN (SELECT COD_DEUDA 
			                                            FROM REPRO_RCS_DEUDA 
			                                            WHERE COD_REPRO_RCS IN (SELECT MAX (COD_REPRO_RCS) 
			                                                                  FROM REPRO_RCS 
			                                                                  WHERE COD_ESTADO_REPRO_RCS<>3
			                                                                  AND COD_DEUDOR =$cod_deudor))
						        AND COD_DEUDOR =  ".$cod_deudor;
									
		$result_deuda = $db->build_results($sql_deuda);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_datos_web_pay($cod_deudor){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT	 D.RUT
				    ,D.DIG_VERIF
				    ,D.NOMBRE || ' ' || D.APELLIDO_PATERNO || ' ' || D.APELLIDO_MATERNO NOMBRE
				    ,DIP.DIRECCION  DIRECCION
				    ,DIP.TELEFONO FONO_PARTICULAR
				    ,DIP.EMAIL EMAIL_PARTICULAR
				    ,'' ABONO_SUB_BOLETA
				    ,'' CB_COD_PLAN_PAGO_H
				    ,'' CB_MONTO_PESOS_BOLETA_H
			FROM 	DEUDOR D left outer join DIRECCION DIP 
					 on DIP.COD_DIRECCION = f_get_direccion_deudor(D.COD_DEUDOR,'P') 
			WHERE 	D.COD_DEUDOR=$cod_deudor";
									
		$result_deuda = $db->build_results($sql);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_cupon_pago_1($cod_deudor){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT  RUT
					,DIG_VERIF
					,NOMBRE||' '||APELLIDO_PATERNO||' '||APELLIDO_MATERNO NOMBRE
				FROM    DEUDOR 
				WHERE	COD_DEUDOR =$cod_deudor";
									
		$result_deuda = $db->build_results($sql);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_cupon_pago_cuotas($cod_deudor){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT  D.COD_DEUDA
					,D.COD_TIPO_DEUDA
					,NRO_CUOTA
					,ANO_COBRO
					,to_char(FECHA_VENCTO,'dd/mm/yyyy') FECHA_VENCTO
					,f_pp_saldo_plan_pago(COD_PLAN_PAGO, sysdate) SALDO
					,round(f_pp_saldo_plan_pago(COD_PLAN_PAGO, sysdate) * f_mon_get_valor_moneda(cod_moneda, sysdate),0) SALDO_PESOS
					,NOM_ESTADO_PLAN_PAGO
					,'' IMPRIMIR
					,To_Number(To_Char(FECHA_INICIO_COBRO, 'yyyy')) ANO_INICIO_COBRO
					,PP.COD_PLAN_PAGO
					,TD.COD_FAMILIA_DEUDA
					,D.COD_TIPO_DEUDA
					,D.COD_ESTADO_DEUDA
					,D.COD_CARTERA
					,PP.COD_ESTADO_PLAN_PAGO
				
				FROM DEUDOR DE
					,PLAN_PAGO PP
		        	,DEUDA D
		      		,TIPO_DEUDA TD
		      		,ESTADO_PLAN_PAGO EPP
				
				WHERE DE.COD_DEUDOR =$cod_deudor
				  AND D.COD_DEUDOR = DE.COD_DEUDOR 
 		          AND PP.COD_DEUDA = D.COD_DEUDA
				  AND PP.COD_ESTADO_PLAN_PAGO NOT IN (3,4)
		          AND D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
		          AND EPP.COD_ESTADO_PLAN_PAGO = PP.COD_ESTADO_PLAN_PAGO
				  AND D.COD_ESTADO_DEUDA = 2
				UNION
				
				SELECT  D.COD_DEUDA
					,D.COD_TIPO_DEUDA
					,NRO_CUOTA
					,ANO_COBRO
					,to_char(FECHA_VENCTO,'dd/mm/yyyy') FECHA_VENCTO
					,f_pp_saldo_plan_pago(COD_PLAN_PAGO, sysdate) SALDO
					,round(f_pp_saldo_plan_pago(COD_PLAN_PAGO, sysdate) * f_mon_get_valor_moneda(cod_moneda, sysdate),0) SALDO_PESOS
					,NOM_ESTADO_PLAN_PAGO
					,'' IMPRIMIR
					,To_Number(To_Char(FECHA_INICIO_COBRO, 'yyyy')) ANO_INICIO_COBRO
					,PP.COD_PLAN_PAGO
					,TD.COD_FAMILIA_DEUDA
					,D.COD_TIPO_DEUDA
					,D.COD_ESTADO_DEUDA
					,D.COD_CARTERA
					,PP.COD_ESTADO_PLAN_PAGO
				FROM DEUDOR DE
					,PLAN_PAGO PP
		        	,DEUDA D
		      		,TIPO_DEUDA TD
		      		,ESTADO_PLAN_PAGO EPP
				
				WHERE DE.COD_DEUDOR =$cod_deudor
				  AND D.COD_DEUDOR = DE.COD_DEUDOR 
		          AND PP.COD_DEUDA = D.COD_DEUDA
				  AND PP.COD_ESTADO_PLAN_PAGO= 4 -- MOROSA 
		          AND D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
		          AND EPP.COD_ESTADO_PLAN_PAGO = PP.COD_ESTADO_PLAN_PAGO
				  AND D.COD_ESTADO_DEUDA = 2
				ORDER BY 3 ASC";
									
		$result_deuda = $db->build_results($sql);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_cupon_pago_boleta($cod_plan_pago){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "select max(B.COD_BOLETA) COD_BOLETA
								from  BOLETA B
								where B.COD_PLAN_PAGO =$cod_plan_pago
								  and B.CARTERA_CONOCIDA = 'S'
								  AND To_Char(B.FECHA_BOLETA,'yyyy') = To_Char(SYSDATE,'yyyy')";
									
		$result_deuda = $db->build_results($sql);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_cant_cruzado($cod_deuda){
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT	count(*) CANTIDAD
					FROM	CRUZADO
					where	COD_DEUDA =$cod_deuda";
									
		$result_deuda = $db->build_results($sql);
		
    	$param_resp = base64_encode(serialize($result_deuda));
		return $param_resp;
	}
	
	function ws_obtiene_plan_pago($cod_deuda){                    
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql_plan_pago = "SELECT NRO_CUOTA
					,ANO_COBRO
					,to_char(FECHA_VENCTO,'dd/mm/yyyy') FECHA_VENCTO
					,f_pp_saldo_plan_pago(COD_PLAN_PAGO, sysdate) SALDO
					,round(f_pp_saldo_plan_pago(COD_PLAN_PAGO, sysdate) * f_mon_get_valor_moneda(cod_moneda, sysdate),0) SALDO_PESOS
					,NOM_ESTADO_PLAN_PAGO
					,'' IMPRIMIR
					,PP.COD_PLAN_PAGO
					,TD.COD_FAMILIA_DEUDA
					,D.COD_TIPO_DEUDA
					,D.COD_ESTADO_DEUDA
					,D.COD_CARTERA
					,PP.COD_ESTADO_PLAN_PAGO
				FROM    PLAN_PAGO PP
		        		,DEUDA    D
		      			,TIPO_DEUDA TD
		      			,ESTADO_PLAN_PAGO EPP
				WHERE   PP.COD_DEUDA =$cod_deuda
	        		AND PP.COD_DEUDA = D.COD_DEUDA
	        		AND D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
	        		AND EPP.COD_ESTADO_PLAN_PAGO = PP.COD_ESTADO_PLAN_PAGO
			        order by NRO_CUOTA";
									
		$result_pp = $db->build_results($sql_plan_pago);
		
    	$param_resp = base64_encode(serialize($result_pp));
		return $param_resp;
	}
	
	function ws_obtiene_ult_boleta($cod_plan_pago){                    
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "select max(B.COD_BOLETA) COD_BOLETA
				from  BOLETA B
				where B.COD_PLAN_PAGO =$cod_plan_pago
				  and B.CARTERA_CONOCIDA = 'S'
				  AND To_Char(B.FECHA_BOLETA,'yyyy') = To_Char(SYSDATE,'yyyy')";
									
		$result_pp = $db->build_results($sql);
		
    	$param_resp = base64_encode(serialize($result_pp));
		return $param_resp;
	}
	function ws_obtiene_boleta($cod_plan_pago){                    
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "select max(B.COD_BOLETA) COD_BOLETA
				from  BOLETA B
				where B.COD_PLAN_PAGO =$cod_plan_pago
				  and B.CARTERA_CONOCIDA = 'S'";
									
		$result_pp = $db->build_results($sql);
		
    	$param_resp = base64_encode(serialize($result_pp));
		return $param_resp;
	}
	
	function ws_pago_abono($orden_compra){
	    $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	    
	    $sp = "spw_web_pay_pago";
	    $param = "'$orden_compra', '1','S'";
	    if(!$db->EXECUTE_SP($sp,$param)){ 
	        $codigo_respuesta = 1;// INCORRECTO
	        $mensaje_respuesta = "ERROR BASE DATOS";
	    }else{ 
	        $codigo_respuesta = 0;// CORRECTO
	        $mensaje_respuesta = "OK";
	    }
	    
        $param_resp = base64_encode(serialize(Array("COD_RESPUESTA"=>$codigo_respuesta, "MENSAJE_RESPUESTA"=>$mensaje_respuesta)));
        
		return $param_resp;
	}
	
	function ws_sub_boleta($cod_plan_pago,$monto_abono_pesos){
	
		$cod_tipo_descuento = "NULL";
		$tipo_pago_web		= 'PAGO_ABONO';
	
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT	 COD_DEUDA
				FROM PLAN_PAGO
				WHERE COD_PLAN_PAGO = $cod_plan_pago";
		$result = $db->build_results($sql);
		$cod_deuda = $result[0]['COD_DEUDA'];
		
		$db_b = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sp = 'sp_boleta_calculo_web';
		$param	= "$cod_deuda, $cod_plan_pago, $cod_tipo_descuento, $monto_abono_pesos"; 
		$db_b->EXECUTE_SP($sp, $param);
		
		$sql = "SELECT	 D_MONTO_CAPITAL	
					,D_MONTO_INTERES
					,D_MONTO_MULTA
					,D_MONTO_PESOS
					,D_DSCTO_CAPITAL
					,D_DSCTO_INTERES
					,D_DSCTO_MULTA
					,D_DSCTO_PESOS
			FROM	TEMP_TOTAL_BOLETA";
		$db_c = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result_c = $db_c->build_results($sql);
		//$result_c->ROLLBACK_TRANSACTION();
		
		if (count($result_c) != 0) {
			$tipo_pago	= 'PAGO_ABONO';
			$cod_deudor	= session::get('COD_DEUDOR');
			$cod_sub_boletas	= 'NULL';
			///$cod_plan_pago	= lo trae por request 
			$monto_capital	= $result_c[0]['D_MONTO_CAPITAL'];
			$monto_interes	= $result_c[0]['D_MONTO_INTERES'];
			$monto_multa	= $result_c[0]['D_MONTO_MULTA'];
			$monto_moneda	= $monto_capital+$monto_interes+$monto_multa;
			$monto_pesos	= $result_c[0]['D_MONTO_PESOS'];
			$cod_tipo_descuento = 'NULL';
			$dscto_utm		= 0;
			$dscto_pesos	= 0;
			
			$ds = new datos_server();
			
			$ip						= $ds->getRealIP();
			$navegador				= $ds->browser();
			$sistema_operativo		= $ds->so();
		
			$param_wp = "'$tipo_pago'
						 ,$cod_deudor
						 ,'$cod_sub_boletas'
						 ,$cod_plan_pago
						 ,$monto_capital
						 ,$monto_interes
						 ,$monto_multa
						 ,$monto_moneda
						 ,$monto_pesos
						 ,$cod_tipo_descuento
						 ,$dscto_utm
						 ,$dscto_pesos
						 ,'$tipo_pago_web'
						 ,'$ip'
						 ,'$navegador'
						 ,'$sistema_operativo'";
		
			$db_sp = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$sql_sp = 'spw_wp_transaccion';
		
			if ($db_sp->EXECUTE_SP($sql_sp, $param_wp)){
				//identity de WP_SUB_BOLETA => sub_boleta
				$cod_wp_sub_boleta = $db_sp->GET_IDENTITY("WP_SUB_BOLETA");
		
				$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
				$sql_deudor = "SELECT to_char(RUT) || DIG_VERIF RUT
								FROM	DEUDOR
								WHERE	COD_DEUDOR = $cod_deudor";
				$result_deudor = $db->build_results($sql_deudor);
				$rut = $result_deudor[0]['RUT'];
		
				$sql_trans = 	"select COD_WP_TRANSACCION
								 from	WP_SUB_BOLETA
								 where	COD_WP_SUB_BOLETA = $cod_wp_sub_boleta";
				$result_trans = $db->build_results($sql_trans);
				$cod_wp_transaccion = $result_trans[0]['COD_WP_TRANSACCION'];
				
				///SE ENCRIPTA LOS DATOS Y SE ENVIA AL FORMULARIO
				$usuario = "fondoCreditoWebPay";  ///usuario de conexion
				$password = "WebPay";	///password de conexion
				$rut_deudor = $rut;
				$monto_abono_pesos = number_format($monto_pesos, 0, '', '');
				$codigo_trans = $cod_wp_transaccion;
				
				//estas llaves se van ocupar tanto para servipag y transbank
				//es de uso entre servidor usach y cliente-web
				$fp=fopen("../webpay_usach/web_pay/publica.pem","r"); 
				//$fp=fopen("/var/www/cgi-helen/maestros/publica.pem","r");  //se descomenta para produccion
				$pub_key=fread($fp,8192);
				fclose($fp);
				
				$datos = $usuario.'&'.$password.'&'.$rut_deudor.'&'.$monto_abono_pesos.'&'.$codigo_trans;
				$error = 3; //sin errores
				if(!openssl_get_publickey($pub_key)) {
					$error = 1;	//echo"Error abriendo clave";
					$datosEncriptados = "ERROR_CLAVE";
				}else if(!openssl_public_encrypt($datos, &$datosEncriptados, openssl_get_publickey($pub_key))) {
					$error = 2; //echo "Error encriptando datos";
					$datosEncriptados = "ERROR_ENCRIPTADO";
				}
				
				if($error == 3){
					//SE ENCRIPTA LOS DATOS Y SE ENVIA AL FORMULARIO		
					$param_resp = base64_encode(serialize($datosEncriptados));
        
					return $param_resp;
				}else{
					$param_resp = base64_encode(serialize($datosEncriptados));
        
					return $param_resp;
				}
				
			}else{
				$res = "SIN_EXITO";
				$param_resp = base64_encode(serialize($res));
        
				return $param_resp;
			}	
			
		}
	}
?>