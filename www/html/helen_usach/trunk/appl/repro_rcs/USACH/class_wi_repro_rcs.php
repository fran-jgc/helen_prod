<?php
	ini_set('memory_limit', '720M');
	ini_set('max_execution_time', 900); //900 seconds = 15 minutes
class wi_repro_rcs extends wi_repro_rcs_base {
	
    function __construct($cod_item_menu) {
        parent::__construct($cod_item_menu); 
		$this->dws['dw_repro_deuda'] = new dw_repro_deuda_base();
	}
	function new_record() {
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		
		$this->dws['dw_repro_rcs']->set_entrable('COD_ESTADO_REPRO_RCS', false);
		$this->dws['dw_repro_rcs']->set_entrable('RECIBIDO_DOCTO', false);
        $this->dws['dw_repro_rcs']->insert_row();
        
        /////////// SE SETEA FECHA Y USUARIO CUANDO ES UN NEW_RECORD //////////////////////
        $this->dws['dw_repro_rcs']->set_item(0, 'FECHA_REPRO_RCS', $this->current_date());
		$this->dws['dw_repro_rcs']->set_item(0, 'COD_USUARIO', $this->cod_usuario);
		
		$sql = "SELECT NOM_USUARIO FROM USUARIO WHERE COD_USUARIO = $this->cod_usuario";
		$result = $db->build_results($sql);
		$this->dws['dw_repro_rcs']->set_item(0, 'NOM_USUARIO',$result[0]['NOM_USUARIO']);
		///////////////////////////////////////////////////////////////////////////////////
		$this->dws['dw_repro_rcs']->set_item(0, 'COD_ESTADO_REPRO_RCS',1);
		
		$this->b_print_visible= false;
        
    }
	function load_record(){
		parent::load_record();
	}
	
    function save_record($db){
    	$cod_repro				= $this->get_key();
		$fecha_repro_rcs		= $this->dws['dw_repro_rcs']->get_item(0, 'FECHA_REPRO_RCS');
		$cod_usuario			= $this->dws['dw_repro_rcs']->get_item(0, 'COD_USUARIO');
		
		$cod_deudor				= $this->dws['dw_repro_rcs']->get_item(0, 'COD_DEUDOR');
		$porc_pago_inicial		= $this->dws['dw_repro_rcs']->get_item(0, 'PORC_PAGO_INICIAL');
		$monto_pago_inicial		= $this->dws['dw_repro_rcs']->get_item(0, 'MONTO_PAGO_INICIAL');
		$cant_cuota				= $this->dws['dw_repro_rcs']->get_item(0, 'CANT_CUOTA');
		$porc_dscto_multa		= $this->dws['dw_repro_rcs']->get_item(0, 'PORC_DSCTO_MULTA_H');
		$monto_dscto_multa		= $this->dws['dw_repro_rcs']->get_item(0, 'MONTO_DSCTO_MULTA_H');
		$monto_a_reprogramar	= $this->dws['dw_repro_rcs']->get_item(0, 'MONTO_A_REPROGRAMAR_H');
		$monto_cuota			= $this->dws['dw_repro_rcs']->get_item(0, 'MONTO_CUOTA_H');
		$obs					= $this->dws['dw_repro_rcs']->get_item(0, 'OBS');
		$recibido_docto			= $this->dws['dw_repro_rcs']->get_item(0, 'RECIBIDO_DOCTO');
			
		$cod_repro 			= ($cod_repro =='') ? "null" : $cod_repro;
		$obs 				= ($obs =='') ? "null" : "'".$obs."'";
		$fecha_repro_rcs = $this->str2date($fecha_repro_rcs);
		
		$porc_pago_inicial	= str_replace(",",".", $porc_pago_inicial);
		$monto_pago_inicial	= str_replace(",",".", $monto_pago_inicial);
		$porc_dscto_multa	= str_replace(",",".", $porc_dscto_multa);
		$monto_dscto_multa	= str_replace(",",".", $monto_dscto_multa);
		$monto_a_reprogramar= str_replace(",",".", $monto_a_reprogramar);
		$monto_cuota		= str_replace(",",".", $monto_cuota);
		
		/*
		$param = "'$operacion'
					,$cod_repro
					,$fecha_repro_rcs
					,$cod_usuario
					,$COD_ESTADO_REPRO_RCS
					,$cod_deudor			
					,$porc_pago_inicial
					,$monto_pago_inicial
					,$cant_cuota
					,$porc_dscto_multa
					,$monto_dscto_multa
					,$monto_a_reprogramar
					,$monto_cuota
					,$obs
					,'$recibido_docto'
					";
		
			//echo 	'2'.$param;	
		*/
    	if ($this->is_new_record()){
    		
			//$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	    	$sql_deudor =  "select 
	    				(SELECT f_get_parametro(3) FROM dual) COD_UNIVERSIDAD
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
				from DEUDOR DE left outer join DIRECCION DI on DE.COD_DEUDOR = DI.COD_DEUDOR
					and di.cod_direccion = f_get_direccion_deudor (de.COD_DEUDOR, 'P')
				where de.COD_DEUDOR = $cod_deudor
				ORDER BY FECHA_DIRECCION DESC";
			$result_deudor = $db->build_results($sql_deudor);
	    	
	        
			$sql_deudas = $this-> select_deuda($cod_deudor);
			$result_deudas = $db->build_results($sql_deudas);
			
			$contenido	=			"<?xml version='1.0' encoding='UTF-8'?>
									<Message>
									<REPROGRAMACION> 
									<DEUDOR>
									";
			$contenido	.=			"<COD_UNIVERSIDAD>"		.$result_deudor[0]['COD_UNIVERSIDAD'].		"</COD_UNIVERSIDAD>
									<RUT>"					.$result_deudor[0]['RUT']. 					"</RUT>
									<DIG_VERIF>"			.$result_deudor[0]['DIG_VERIF']. 			"</DIG_VERIF>
								    <NOMBRE>"				.$result_deudor[0]['NOMBRE']. 				"</NOMBRE>
									<APELLIDO_PATERNO>"		.$result_deudor[0]['APELLIDO_PATERNO']. 	"</APELLIDO_PATERNO>
									<APELLIDO_MATERNO>"		.$result_deudor[0]['APELLIDO_MATERNO']. 	"</APELLIDO_MATERNO>	
									<DIRECCION>"			.$result_deudor[0]['DIRECCION']. 			"</DIRECCION>
									<NOM_COMUNA>"			.$result_deudor[0]['NOM_COMUNA']. 			"</NOM_COMUNA>
									<NOM_CIUDAD>"			.$result_deudor[0]['NOM_CIUDAD']. 			"</NOM_CIUDAD>
									<EMAIL>"				.$result_deudor[0]['EMAIL']. 				"</EMAIL>
									<TELEFONO>"				.$result_deudor[0]['TELEFONO']. 			"</TELEFONO>
									<CELULAR>"				.$result_deudor[0]['CELULAR']. 				"</CELULAR>
									";
						
			$contenido	.=			"</DEUDOR>
									<DEUDAS_ORIGINALES>";
			
	    						for($i = 0;$i < count($result_deudas);$i++){
			$contenido	.=			"<DEUDA>
										<COD_DEUDA>"			.$result_deudas[$i]['COD_DEUDA'].		"</COD_DEUDA>
										<COD_TIPO_DEUDA>"		.$result_deudas[$i]['COD_TIPO_DEUDA']. 				"</COD_TIPO_DEUDA>
										<COD_UNIVERSIDAD>"		.$result_deudas[$i]['COD_UNIVERSIDAD']. 					"</COD_UNIVERSIDAD>
									    <SALDO_CAPITAL>"		.$result_deudas[$i]['SALDO_CAPITAL']. 				"</SALDO_CAPITAL>
										<SALDO_MULTA>"			.$result_deudas[$i]['SALDO_MULTA']. 	"</SALDO_MULTA>
									</DEUDA>
									";
								}
			$contenido	.=			"</DEUDAS_ORIGINALES>
									<REPRO_RCS>";
			
			$contenido	.=			"<PORC_PAGO_INICIAL>"	.$porc_pago_inicial.						"</PORC_PAGO_INICIAL>
									<MONTO_PAGO_INICIAL>"	.$monto_pago_inicial. 						"</MONTO_PAGO_INICIAL>
									<CANT_CUOTA>"			.$cant_cuota. 								"</CANT_CUOTA>
								    <PORC_DSCTO_MULTA>"		.$porc_dscto_multa. 						"</PORC_DSCTO_MULTA>
									<MONTO_DSCTO_MULTA>"	.$monto_dscto_multa. 						"</MONTO_DSCTO_MULTA>
									<MONTO_A_REPROGRAMAR>"	.$monto_a_reprogramar. 						"</MONTO_A_REPROGRAMAR>	
									<MONTO_CUOTA>"			.$monto_cuota. 								"</MONTO_CUOTA>
									";
			
			$contenido	.=		    "</REPRO_RCS>
									</REPROGRAMACION>
									</Message>";
			
			
			
			/*
			require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/nusoap-0.7.3/lib/nusoap.php");

			//$cliente = new nusoap_client('http://simulacion.helen.cl/wan/simulacion/simulacion_produccion/simulacion_RCS/trunk/appl/web_service/server/ws_server.wsdl',true);
			$cliente = new nusoap_client('http://192.168.2.104:8074/ecastillo/simulacion_rcs_seven/trunk/appl/web_service/server/ws_server.wsdl',true);
			
			$cliente->setCredentials(K_CLIENTE, "123", "basic");
			if ($cliente->getError()) {
				print_r($cliente->getError());
		        return false;
		    }
			
		    $result = $cliente->call("WS_obtiene_folio", array("ve_datos" =>base64_encode($contenido)));
		    $folio = base64_decode($result);
		    

		  
			*/

			$this->K_CLIENTE		= session::get("K_CLIENTE");
			$w = new llamada_api();
			
			$obtiene_folio[]=array("OBTIENE_FOLIO" => base64_encode($contenido));
			
			$datos[]=array('CLIENTE'=>base64_encode($this->K_CLIENTE),
				'USER'=>base64_encode('USACH'),
				'PASS'=>base64_encode(123));
			
			$respuesta=$w->envia_api($datos);
			if ($respuesta[0]['REST']=='ok') {
				
				$respuesta_folio=$w->envia_api($obtiene_folio);
				$folio = $respuesta_folio[0]['REST'];


			}else
				return false;


			session::set('folio_desde_save_record', $folio);
			session::set('OBS', $obs);
			session::set('NEW_USUARIO', $cod_usuario);
			require_once(dirname(__FILE__)."/../class_wakeup.php");
			//$a = new wakeUp();
			//$a->__wakeup();
			$sql_cod_repro  = "select MAX(COD_REPRO_RCS) COD_REPRO_RCS 
									from REPRO_RCS 
									where COD_DEUDOR = $cod_deudor";
			$res_cod_repro  = $db->build_results($sql_cod_repro);
			$cod_repro		= $res_cod_repro[0]['COD_REPRO_RCS'];

			if (!$db->EXECUTE_SP("spu_repro_rcs","'DESDE_HELEN',$cod_repro")){
				return false;
			}
			$this->dws['dw_repro_rcs']->set_item(0, 'COD_REPRO_RCS', $cod_repro);
		 	
			return true;
		}
    	else{

			$COD_ESTADO_REPRO_RCS	= $this->dws['dw_repro_rcs']->get_item(0, 'COD_ESTADO_REPRO_RCS');
			$sp = 'spu_repro_rcs';
			$operacion = 'UPDATE';
			$param = "'$operacion'
						,$cod_repro
						,$fecha_repro_rcs
						,$cod_usuario
						,$COD_ESTADO_REPRO_RCS
						,$cod_deudor			
						,$porc_pago_inicial
						,$monto_pago_inicial
						,$cant_cuota
						,$porc_dscto_multa
						,$monto_dscto_multa
						,$monto_a_reprogramar
						,$monto_cuota
						,$obs
						,'$recibido_docto'
						";
		
			if ($db->EXECUTE_SP($sp,$param)){
		
			//AL CONFIRMAR LA REPROGRAMACION QUE FUE CREADA POR WS SE TIENE QUE CONFIRMAR EN BD SIMULACION_RCS
			if($COD_ESTADO_REPRO_RCS == 2){
				///****************************** CONFIRMACION WS ********************************
				$sql_nro_repro_rcs = sql_repro_rcs::get_sql('NRO_REPRO_RCS',$cod_repro);
				$result_nro_repro_rcs = $db->build_results($sql_nro_repro_rcs);
				$nro_repro_rcs = $result_nro_repro_rcs[0]['NRO_REPRO_RCS'];

				if($nro_repro_rcs != ""){
					$this->confirma_ws($nro_repro_rcs);
				}
			}
				///**************************** FINCONFIRMACION WS********************************
			return true;
    		}
       	}
    	return false;

   }
   function select_deuda($cod_deudor)
   {
	   $sql = "";
	   $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	   $sql_es_cruzado ="SELECT COUNT(*) CANTIDAD
						   FROM DEUDA DA,CRUZADO C,TIPO_DEUDA t
						   WHERE COD_DEUDOR = $cod_deudor
						   AND DA.COD_DEUDA = C.COD_DEUDA
						   and t.COD_TIPO_DEUDA = da.COD_TIPO_DEUDA
						   and da.COD_ESTADO_DEUDA = 2 --EXIGIBLE
						   and da.COD_TIPO_DEUDA not in ('CSR', 'CRPI', 'CR12','CFR','CRI')
						   AND (SELECT  nvl(SUM (f_pp_saldo_mora(pp.cod_plan_pago, sysdate)), 0) SALDO_MORA
						   FROM plan_pago pp
						   WHERE cod_deuda = da.COD_DEUDA
						   and FECHA_VENCTO < add_months(sysdate,-18)) > 0.0000
						   and t.COD_FAMILIA_DEUDA = 1 --FONDO_SOLIDARIO";
	   $result_es_cruzado	= $db->build_results($sql_es_cruzado);
	   
	   if($result_es_cruzado[0]["CANTIDAD"] > 1)
	   {
		   $sql = "select da.COD_DEUDA
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
				   where da.COD_DEUDOR = $cod_deudor
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
				   where da.COD_DEUDOR = $cod_deudor
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
	   }
	   else
	   {
		   $sql = "select da.COD_DEUDA
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
				   where da.COD_DEUDOR = $cod_deudor
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
	   }
	   return $sql;
   }
}	
?>