<?php
	require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
	require_once(dirname(__FILE__)."/../../appl.ini");
	require_once("class_llamada_api.php");
	session::set('K_ROOT_URL', K_ROOT_URL);
	session::set('K_ROOT_DIR', K_ROOT_DIR);
	session::set('K_CLIENTE', K_CLIENTE);
	session::set('K_APPL', K_APPL);
	
	 
	class wakeUp{
	    function __construct(){
			if(session::is_set('folio_desde_save_record')){
				$folio_api	= session::get('folio_desde_save_record');
				$folio[]=array("FOLIO" => $folio_api);
				$usuario_entrante='HELEN';
		    }else{
		    	$folio[]=array("FOLIO" => $_GET['folio']);
				session::set('COD_USUARIO',1);
				$usuario_entrante='WEB';
			}

			if (isset($_GET['folio']) || session::is_set('folio_desde_save_record')) {
			    
			    $this->K_CLIENTE		= session::get("K_CLIENTE");
			    $w = new llamada_api();
			    
			   // $folio[]=array("FOLIO" => $_GET['folio']);
			    
			    $datos[]=array('CLIENTE'=>base64_encode($this->K_CLIENTE),
			        'USER'=>base64_encode('USACH'),
			        'PASS'=>base64_encode(123));
			    
			    $respuesta=$w->envia_api($datos);
			    			    
			    if ($respuesta[0]['REST']=='ok') {
			        
			        $respuesta=$w->envia_api($folio);
			       
			        $DATA_XML= $respuesta[0]['DATA_XML'];
			         $xml = simplexml_load_string(base64_decode($DATA_XML));
			       
			    }else 
			        return;
    			//return;
    			
    		    $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    		    $obs=NULL;

				
					//SE OBTIENE EL COD_USUARIO WEB
					$sql_cod_usuario_ws = "	SELECT COD_USUARIO 
											FROM USUARIO
											WHERE NOM_USUARIO = 'USUARIO WEB'";
					$res_cod_usuario_ws = $db->build_results($sql_cod_usuario_ws);
					$usuario = $res_cod_usuario_ws[0]['COD_USUARIO'];
				
				if ($usuario_entrante=='WEB') {
					session::set('COD_USUARIO',$usuario);
				}else{
					$usuario=session::get('COD_USUARIO');
				}

    
    				
    			$sql_deudor = "SELECT COD_DEUDOR FROM DEUDOR WHERE RUT = ".$xml->REPROGRAMACION->SIMULACION->RUT;
    			$result_deudor = $db->build_results($sql_deudor);
    			
    			if ($db->EXECUTE_SP("sp_anula_repro_ws",$xml->REPROGRAMACION->SIMULACION->RUT)){
    			 
    				$cod_repro				= "NULL";
    				$fecha_repro			= $xml->REPROGRAMACION->SIMULACION->FECHA_SIMULACION;
    				$hora_repro				= $xml->REPROGRAMACION->SIMULACION->HORA_SIMULACION;
    				$cod_estado_repro		= 1; // estado emitido 
    				$cod_deudor				= $result_deudor[0]['COD_DEUDOR'];
    				$porc_pago_inicial		= $xml->REPROGRAMACION->SIMULACION->PORC_PAGO_INICIAL;
    				$monto_pago_inicial		= $xml->REPROGRAMACION->SIMULACION->MONTO_PAGO_INICIAL;
    				$cant_cuota				= $xml->REPROGRAMACION->SIMULACION->CANT_CUOTA;
    				$porc_dscto_multa		= $xml->REPROGRAMACION->SIMULACION->PORC_DSCTO_MULTA;
    				$monto_dscto_multa		= $xml->REPROGRAMACION->SIMULACION->MONTO_DSCTO_MULTA;
    				$monto_a_reprogramar	= $xml->REPROGRAMACION->SIMULACION->MONTO_A_REPROGRAMAR;
    				$monto_cuota			= $xml->REPROGRAMACION->SIMULACION->MONTO_CUOTA;
    				$cod_repro_rcs_ws		= $xml->REPROGRAMACION->SIMULACION->COD_SIMULACION;
    				
    				$fecha_repro			= $this->str2date($fecha_repro, $hora_repro);
    				
    				$sp = 'spu_repro_rcs_ws';
    				$param = "'INSERT'
    							,$cod_repro
    							,$fecha_repro
    							,$cod_estado_repro
    							,$cod_deudor			
    							,$porc_pago_inicial
    							,$monto_pago_inicial
    							,$cant_cuota
    							,$porc_dscto_multa
    							,$monto_dscto_multa
    							,$monto_a_reprogramar
    							,$monto_cuota
    							,$cod_repro_rcs_ws
    							,NULL
    							,$usuario
    							";

    				if ($db->EXECUTE_SP($sp,$param)){
    				   
    					if(K_TIPO_BD=="oci")
    					{
    						$sql_cod_repro = "select MAX(COD_REPRO_RCS) COD_REPRO 
    													,MAX(COD_DEUDA_RCS) COD_DEUDA_RCS
    													,TO_CHAR(SYSDATE,'dd/mm/yyyy') FECHA_ACTUAL
    											from REPRO_RCS 
    											where COD_DEUDOR = $cod_deudor
    											and COD_ESTADO_REPRO_RCS = 1";
    					}
    					else if (K_TIPO_BD=="mssql")
    					{
    						$sql_cod_repro  = 	"select MAX(COD_REPRO_RCS) COD_REPRO 
    													,MAX(COD_DEUDA_RCS) COD_DEUDA_RCS
    													,CONVERT(varchar, GETDATE(), 103) FECHA_ACTUAL
    											from REPRO_RCS 
    											where COD_DEUDOR = $cod_deudor
    											and COD_ESTADO_REPRO_RCS = 1";
    					}
    
    					$res_cod_repro  = $db->build_results($sql_cod_repro);
    					
    					$cod_repro			= $res_cod_repro [0]['COD_REPRO'];
    					$cod_deuda_repro	= $res_cod_repro [0]['COD_DEUDA_RCS'];
    					$fecha_actual		= $res_cod_repro [0]['FECHA_ACTUAL'];
    					$fecha_actual		= $this->str2date($fecha_actual);
    
    					//SE CREAN LOS PP ENVIADOS POR WS 
    
    					for ($i = 0; $i < count($xml->REPROGRAMACION->LISTA_PLAN_PAGO->PLAN_PAGO); $i++) {
    
    						$fecha_vencto	= $xml->REPROGRAMACION->LISTA_PLAN_PAGO->PLAN_PAGO[$i]->FECHA_VENCTO;
    						$fecha_vencto	= $this->str2date($fecha_vencto);
    						
    						$param_PP = "$cod_deuda_repro
    								,".$xml->REPROGRAMACION->LISTA_PLAN_PAGO->PLAN_PAGO[$i]->NRO_CUOTA."
    								,".$xml->REPROGRAMACION->LISTA_PLAN_PAGO->PLAN_PAGO[$i]->ANO_COBRO."
    								,".$xml->REPROGRAMACION->LISTA_PLAN_PAGO->PLAN_PAGO[$i]->MONTO_CAPITAL."
    								,".$xml->REPROGRAMACION->LISTA_PLAN_PAGO->PLAN_PAGO[$i]->MONTO_INTERES."
    								,".$fecha_vencto."
    								";
    
    						if (!$db->EXECUTE_SP("sp_genera_plan_repro_ws",$param_PP)){
    							return false;
    						}
    					}
    					$sp = 'spu_repro_rcs_ws';
    					$param = "'BOLETA'
    								,$cod_repro
    								";
    
    					if (!$db->EXECUTE_SP($sp,$param)){
    						return false;
    					}
    
    					
    					//SE INSERTAN LAS DEUDAS A TABLA REPRO_DEUDA_RCS
    			    	for ($i = 0; $i < count($xml->REPROGRAMACION->LISTA_DEUDAS->DEUDA); $i++) {
    						
    			        	$cod_deuda				= $xml->REPROGRAMACION->LISTA_DEUDAS->DEUDA[$i]->COD_DEUDA;
    			    		$saldo_capital			= $xml->REPROGRAMACION->LISTA_DEUDAS->DEUDA[$i]->MONTO_CAPITAL;
    			    		$saldo_multa			= $xml->REPROGRAMACION->LISTA_DEUDAS->DEUDA[$i]->MONTO_MULTA;
    			    		$cod_universidad		= $xml->REPROGRAMACION->LISTA_DEUDAS->DEUDA[$i]->COD_UNIVERSIDAD;
    			        	
    			        	$param = "$cod_repro
    				        			,$cod_deuda
    				        			,$cod_universidad
    				        			,$saldo_capital
    				        			,$saldo_multa
    				        			";
    						//ejecucion procedimiento
    			        	if (!$db->EXECUTE_SP('spu_repro_rcs_deuda',$param)){
    							return false;
    						}	
    			        }
    					
    					//SE CREAN LOS PAGARE ENVIADOS POR WS
    					for ($i = 0; $i < count($xml->REPROGRAMACION->LISTA_PAGARES->PAGARE); $i++) {
    						
    						
    						if(K_TIPO_BD=="oci")
    						{
    							$sql ="SELECT f_get_constante('K_EST_PAGARE_VIG') CTE FROM DUAL";
    						}
    						else if (K_TIPO_BD=="mssql")
    						{
    							$sql ="SELECT dbo.f_get_constante('K_EST_PAGARE_VIG') CTE";
    						}
    						$cod_estad_pag = $db->build_results($sql);
    						
    						$estado_pagare = $cod_estad_pag[0]['CTE'];
    						
    						$monto_capital			= str_replace(",",".", $xml->REPROGRAMACION->LISTA_PAGARES->PAGARE[$i]->MONTO_CAPITAL);
    						$monto_interes_penal	= str_replace(",",".", $xml->REPROGRAMACION->LISTA_PAGARES->PAGARE[$i]->MONTO_INTERES_PENAL);
    										  
    						$monto_capital_pesos		= $xml->REPROGRAMACION->LISTA_PAGARES->PAGARE[$i]->MONTO_CAPITAL_PESOS;
    						$monto_interes_penal_pesos	= $xml->REPROGRAMACION->LISTA_PAGARES->PAGARE[$i]->MONTO_INTERES_PENAL_PESOS;
    						
    	            		$monto_moneda = $monto_capital+$monto_interes_penal; 
    	            		$monto_pesos = $monto_capital_pesos+$monto_interes_penal_pesos;
    						
    						$param_pagare = "'INSERT'
    										, NULL
    										, $fecha_actual
    										, '".$xml->REPROGRAMACION->LISTA_PAGARES->PAGARE[$i]->FOLIO_PAGARE."'
    										, $monto_moneda
    										, $monto_pesos
    										, $estado_pagare
    										, $cod_deuda_repro
    										, $usuario
    										, NULL
    										, NULL
    										, NULL
    										, NULL
    										, NULL
    										";
    						
    						if (!$db->EXECUTE_SP("spu_pagare",$param_pagare)){
    							return false;
    						}else{
    							if(K_TIPO_BD=="oci")
    								$db->COMMIT_TRANSACTION();
    						}
    					}
    					if (!$db->EXECUTE_SP("sp_cruzado_repro_ws",$cod_repro))
    						return false;
    					if(K_TIPO_BD=="oci")
    						$db->COMMIT_TRANSACTION();
    						
    			        return true;
    			        
    				}
    			}
    			else
    				return false;
 
			}
		}

		static function str2date($fecha_str, $hora_str='00:00:00') {
			if ($fecha_str=='')
				return 'null';
			// Entra la fecha en formato dd/mm/yyyy		
			if (K_TIPO_BD=='mssql') {
				$res = explode('/', $fecha_str);
				if (strlen($res[2])==2)
					$res[2] = '20'.$res[2];
				return sprintf("{ts '$res[2]-$res[1]-$res[0] $hora_str.000'}");
			}
			else if (K_TIPO_BD=='oci')
				return "to_date('$fecha_str $hora_str', 'dd/mm/yyyy hh24:mi:ss')";
			else
				base::error("base.str2date, no soportado para ".K_TIPO_BD);
		}
		public function __wakeup()
	    {
	        $this->wakeup();
	    }
	}
	$a = new wakeUp();
?>