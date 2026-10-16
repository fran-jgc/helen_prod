<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

	$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	$k_ruta_peticion = "https://webpay-server.usach.cl/api/common/default/get-transaction-detail";
	$k_store_id = 10; //parametro fijo que identifica a los pagos institucionales
	$cant_pagados = 0;
	$sum_monto_total = 0;
	
	$sp_bitacora = 'spw_bitacora_transaccion';
	
	$sql = "SELECT COD_WP_TRANSACCION
		FROM WP_TRANSACCION WP
        where cod_wp_transaccion = f_ultimo_pago(80100221)";
	
	$result = $db->build_results($sql);
	$COD_WP_TRANSACCION = $result[0]['COD_WP_TRANSACCION'];
	
	if($COD_WP_TRANSACCION != ''){
		$sql = "SELECT COD_WP_TRANSACCION
					,TOKEN
					,MONTO
					,TO_CHAR(DR.RUT)||DR.DIG_VERIF RUT
	                ,EXITO
			FROM WP_TRANSACCION WP, DEUDOR DR
	        WHERE TIPO_PAGO_WEB = 'PAGO_SEGIC'
	        AND WP.COD_DEUDOR = DR.COD_DEUDOR
	        AND WP.COD_WP_TRANSACCION =$COD_WP_TRANSACCION";
	
	    $result = $db->build_results($sql);
	    
	    echo $sql;
	    echo '<br>';
	    
	    print_r($result);
	    echo '<br>';
	    
		$numTransaction = $result[0]['TOKEN'];
		$COD_WP_TRANSACCION = $result[0]['COD_WP_TRANSACCION'];
		$MONTO_BD = $result[0]['MONTO'];
		$RUT = $result[0]['RUT'];
	
	        $ch = curl_init();
	        // definimos la URL a la que hacemos la petici�n
	        curl_setopt($ch, CURLOPT_URL,$k_ruta_peticion);
	        // indicamos el tipo de petici�n: POST
	        curl_setopt($ch, CURLOPT_POST, TRUE);
	        // definimos cada uno de los par�metros
	        curl_setopt($ch, CURLOPT_POSTFIELDS, "store_id=$k_store_id&numTransaction=$numTransaction");
	        // recibimos la respuesta y la guardamos en una variable
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        $remote_server_output = curl_exec ($ch);
	        /*posibles respuestas
	         * {"tipo":1,"success":false,"error":"El usuario cancela la transacci�n N� 307044","estado":"no"}
	         * {"tipo":1,"success":true,"estado":2,"monto":"10","voucher":"307048","numTarjeta":"2345","codigoAuth":"966087","numCota":0}
	         * */
	        // cerramos la sesi�n cURL
	        curl_close ($ch);
	        
	        //objeto $json_decode devuelve la transaccion ejemplo:
	        //Array ( [tipo] => 1 [success] => 1 [estado] => 2 [monto] => 45186 [voucher] => 925984 [numTarjeta] => 8910 [codigoAuth] => 008414 [numCota] => 0 )
	        //Array ( [tipo] => 1 [success] => [error] => El usuario cancela la transacción N° 925529 [estado] => no )
	        $json_decode = json_decode($remote_server_output,true);
	        
	        print_r($json_decode);
	        
	        
	        if (array_key_exists('error', $json_decode)) {
			    echo "hay un error en arreglo";
			    echo '<br>';
			    
	        	
			    $sp = "spw_web_pay_pago";
			    
	            $param = "$COD_WP_TRANSACCION,'0','S'";
	            
	            echo $param;
	            
	            /*$db->EXECUTE_SP($sp,$param);*/
	            
			}
			else if (array_key_exists('estado', $json_decode)) {
	        //el monto viene solo cuando succes = true
	        $monto = $json_decode['monto'];
	        $voucher = $json_decode['voucher'];
	        $numTarjeta = $json_decode['numTarjeta'];
	        $codigoAuth = $json_decode['codigoAuth'];
	        $numCota = $json_decode['numCota'];
	        $estado = $json_decode['estado'];
	        
	        if($estado == 2){ //aprobado
	        
		        if($MONTO_BD == $monto){
		        	echo "crea abono";
			    	echo '<br>';
		            /*$sp = "spw_web_pay_pago";
		            $param = "$COD_WP_TRANSACCION,'1','S'";
		            $db->EXECUTE_SP($sp,$param);*/
		            
		            
					
		        }
		        else {
		        	$sp_bitacora = 'spw_bitacora_transaccion';
					$param = "$COD_WP_TRANSACCION,'VALIDA PAGO PORTAL','Montos Diferentes' $MONTO_BD - $monto";
					$db->EXECUTE_SP($sp_bitacora,$param);
				
					//echo '<br>';
		            //echo "Montos Diferentes $MONTO_BD - $monto";
		            //echo '<br>';
		        }
	        }
			else {
	        	$sp_bitacora = 'spw_bitacora_transaccion';
				$param = "$COD_WP_TRANSACCION,'CRONTAB','ESTADO TRANSACCION DISTINTO DE 2 vl_estado= $estado'";
				$db->EXECUTE_SP($sp_bitacora,$param);	  
			}
	            
		}
		else{
	        $sp_bitacora = 'spw_bitacora_transaccion';
			$param = "$COD_WP_TRANSACCION,'VALIDA PAGO PORTAL','SIN ESTADO'";
			$db->EXECUTE_SP($sp_bitacora,$param);	
		}
	}

?>