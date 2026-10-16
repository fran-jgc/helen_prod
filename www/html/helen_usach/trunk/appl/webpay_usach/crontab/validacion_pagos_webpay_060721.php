<?php
ini_set('display_errors', 'On');
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/clases/class_PHPMailer.php");
require_once ("../../../appl.ini");

session::set('K_ROOT_URL', K_ROOT_URL);
session::set('K_ROOT_DIR', K_ROOT_DIR);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$k_ruta_peticion = "https://webpay-server.usach.cl/api/common/default/get-transaction-detail";
$k_store_id = 10; //parametro fijo que identifica a los pagos institucionales
$cant_pagados = 0;
$sum_monto_total = 0;

$sp_bitacora = 'spw_bitacora_transaccion';
$param = "NULL,'CRONTAB','LLAMADO'";
$db->EXECUTE_SP($sp_bitacora,$param);

$sql = "SELECT COD_WP_TRANSACCION
				,TOKEN
				,MONTO
				,TO_CHAR(DR.RUT)||DR.DIG_VERIF RUT
                ,EXITO
		FROM WP_TRANSACCION WP, DEUDOR DR
        WHERE TIPO_PAGO_WEB = 'PAGO_SEGIC'
        AND WP.COD_DEUDOR = DR.COD_DEUDOR
        AND EXITO is null
        AND TOKEN IS NOT NULL
        ORDER BY COD_WP_TRANSACCION ASC";

$result = $db->build_results($sql);
if(count($result)>0){
    for ($i = 0; $i < count($result); $i++) {
        $numTransaction = $result[$i]['TOKEN'];
        $COD_WP_TRANSACCION = $result[$i]['COD_WP_TRANSACCION'];
        $MONTO_BD = $result[$i]['MONTO'];
        $RUT = $result[$i]['RUT'];
        
        // abrimos la sesi�n cURL
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
        
        echo $COD_WP_TRANSACCION;
        echo '<br>';
        
        //print_r($json_decode);
        
        echo '<br>';
        echo '<br>';
        echo '<br>';
        echo '<br>';
        echo '<br>';
        
        $succes = $json_decode['success'];
        
        if($succes){
        	//el monto viene solo cuando succes = true
        	$monto = $json_decode['monto'];
        	$voucher = $json_decode['voucher'];
        	$numTarjeta = $json_decode['numTarjeta'];
        	$codigoAuth = $json_decode['codigoAuth'];
        	$numCota = $json_decode['numCota'];
        	$estado = $json_decode['estado'];
        	
        	if($MONTO_BD == $monto){
	            $sp = "spw_web_pay_pago";
	            $param = "$COD_WP_TRANSACCION,'1','S'";
	            $db->EXECUTE_SP($sp,$param);
	            
	            $sql_count = "SELECT count(*) COUNT
						FROM WP_TRANSACCION_WS
				        WHERE COD_WP_TRANSACCION =$COD_WP_TRANSACCION";
				
				$result_count = $db->build_results($sql_count);
	            $count = $result_count[0]['COUNT'];
	            
	            if($count == 0){
		            $sp = 'sp_wp_pago_transaccion_ws';
					$param = "'INSERT',$COD_WP_TRANSACCION,'null','$numTarjeta',null,null,null,null,null,'$codigoAuth',null,'$estado',$monto,null,$numCota,'CREDITO INSTITUCIONAL','$voucher'";
					
	        		if(!$db->EXECUTE_SP($sp,$param)){ 
						$sp_bitacora = 'spw_bitacora_transaccion';
						$param = "$COD_WP_TRANSACCION,'CRONTAB','Error sp_wp_pago_transaccion_ws' $param";
						$db->EXECUTE_SP($sp_bitacora,$param);
					}
	            }
	            else{
	            	echo '<br>';
		            echo 'Ya existe en WP_TRANSACCION_WS';
		            echo '<br>';
	            }
	            
	            $sql = "SELECT f_get_matricula(D.COD_DEUDOR, 3000) COD_CARRERA
						,SB.MONTO_CAPITAL * f_mon_get_valor_moneda(TD.COD_MONEDA,WP.FECHA) MONTO_CAPITAL
						,SB.MONTO_INTERES * f_mon_get_valor_moneda(TD.COD_MONEDA,WP.FECHA) MONTO_INTERES
						,SB.MONTO_MULTA * f_mon_get_valor_moneda(TD.COD_MONEDA,WP.FECHA) MONTO_MULTA
						,To_Char(WP.FECHA,'DD/MM/YYYY') FECHA_TRANSACCION
            			,To_Char(WP.FECHA,'HH24:MI:SS') HORA_TRANSACCION
						FROM WP_TRANSACCION  WP
						,BOLETA B
						,WP_SUB_BOLETA WPS
						,SUB_BOLETA SB 
				        ,PLAN_PAGO PP
				        ,DEUDOR D
				        ,DEUDA DEU
				        ,TIPO_DEUDA TD
						WHERE WP.COD_WP_TRANSACCION =$COD_WP_TRANSACCION
						AND WPS.COD_WP_TRANSACCION = WP.COD_WP_TRANSACCION
						AND SB.COD_SUB_BOLETA = WPS.COD_SUB_BOLETA
						AND B.COD_BOLETA = SB.COD_BOLETA
				        AND PP.COD_PLAN_PAGO = B.COD_PLAN_PAGO
				        AND DEU.COD_DEUDA = B.COD_DEUDA
				        AND D.COD_DEUDOR = DEU.COD_DEUDOR
				        AND TD.COD_TIPO_DEUDA = DEU.COD_TIPO_DEUDA";
					$result_monto = $db->build_results($sql);
					
				
				$COD_CARRERA = $result_monto[0]['COD_CARRERA'];
				$MONTO_CAPITAL = $result_monto[0]['MONTO_CAPITAL'];
				$MONTO_INTERES = $result_monto[0]['MONTO_INTERES'];
				$MONTO_MULTA = $result_monto[0]['MONTO_MULTA'];
				$FECHA_TRANSACCION = $result_monto[0]['FECHA_TRANSACCION'];
				$HORA_TRANSACCION = $result_monto[0]['HORA_TRANSACCION'];
	            
				$K_TIPO_BD = "oci";
				$K_SERVER = "(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=bdprdlegacy.usach.cl)(PORT = 1521)))(CONNECT_DATA=(SERVICE_NAME=FINA)))";
				$K_BD = "FINA";
				$K_USER ="CONS_INTERNET_EX";
				$K_PASS = "consulta2019";
				
				$db_segic = new database($K_TIPO_BD, $K_SERVER, $K_BD, $K_USER, $K_PASS);
				
				$cont_intentos = 0;
				$respuesta_segic = false;
				$error_segic = false;
				
				while(!$respuesta_segic){
					
					//se consulta a Legacy el pago
	        		$sql = "Paq_FondoCredito.pro_ConsPagoWebFC('$RUT','$voucher',{RESULT})";
	        		echo $sql;
	        		echo '<br>';
					$result_consulta = $db_segic->build_results2($sql);
					
					
					if(count($result_consulta)>0){
						echo 'vienen registros de legacy';
						echo '<br>';
						$NUM_TRANS_CONS = $result_consulta[0]['NUM_TRANS'];
						$FECPAGO_CONS = $result_consulta[0]['FEC_PAGO'];
						$RUT_CONS = $result_consulta[0]['RUT_ALUMNO'];
						$CARR_CONS = $result_consulta[0]['CARRERA'];
						$COD_COMERCIO_CONS = $result_consulta[0]['COD_COMERCIO'];
						$CAPITAL_CONS = $result_consulta[0]['CAPITAL'];
						$INTERES_CONS = $result_consulta[0]['INTERES'];
						$MULTA_CONS = $result_consulta[0]['MULTA'];
						$ESTADO_CONS = $result_consulta[0]['ESTADO'];
						
						$respuesta_segic = true;
						
					}
					else{
						echo 'NO vienen, se debe registrar';
						echo '<br>';
						$COD_COMERCIO = 597032905501;
						$fecha_pago = base::str2date($FECHA_TRANSACCION, $HORA_TRANSACCION);
						$sql = "Paq_FondoCredito.Pro_RegPagoWebFC($voucher, $fecha_pago, '$RUT',$COD_CARRERA,$COD_COMERCIO,$MONTO_CAPITAL,$MONTO_INTERES,$MONTO_MULTA,{RESULT})";
						
						echo $sql;
						echo '<br>';
						$result_registro = $db_segic->build_results2($sql);
						$respuesta_registro = $result_registro[0][0];
						
						print_r($result_registro);
						if($respuesta_registro == 'OK'){
							echo 'Registro insertado en Legacy';
							echo '<br>';
							$respuesta_segic = true; 
						}
					}
					
					$cont_intentos++;
					
					if(($cont_intentos > 3)&&(!$respuesta_segic)){
						//no se pudo registrar el pago despues de 3 intentos, mandar correo, salir del buckle.
						echo 'No se pudo obtener respuesta de legacy, informar a SEGIC';
						echo '<br>';
						$error_segic = true;
						$respuesta_segic = true;
					}
					
					
					
				}
				
        		if($error_segic == true){
					//correo
					$mail_from = 'validacion.segic@integrasystem.cl';
					$mail_fromname = 'validacion segic';
					$mail_to = "evergara.lda@gmail.com";
					$mail_cc = '';
					$mail_bcc = '';
					$mail_subject = 'Validacion Pagos Webpay';
					$mail_body = 'Cuerpo del Correo';
						
					//ENVIO MAIL
					// smpt
		            $email                = new phpmailer();
		            //$email->PluginDir = dirname(__FILE__)."/";
		            $mail->PluginDir = dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/clases/";
		            $email->Mailer        = "smtp";
		            $email->SMTPAuth        = true;
		            $email->SMTPSecure    = "ssl";
		            $email->IsHTML(True);
		            //pagina oficial del smtp: http://www.serversmtp.com/es
		            $email->Host            = "pro.turbo-smtp.com";
		            $email->Username        = "vmelo@integrasystem.cl";
		            $email->Password        = "KsVjKzdY";
		
		            $email->Port            = 465;
		            $email->Timeout        = 30;
		
		            $email->From            = $mail_from;
		            $email->FromName        = $mail_fromname;
		
		
		            $email->Subject = $mail_subject;
		
		            $email->ClearAddresses();
		            
		            $email->AddAddress($mail_to);
		            
		            //$email->AddBCC("evergara.lda@gmail.com");
		            //$email->AddBCC("vmelo@integrasystem.cl");
		
		            $email->Body = "Pago Webpay: $COD_WP_TRANSACCION, Orden compra cegic: $voucher";
		            $res = $email->Send(); 	
				}
        	}
        	else {
        		$sp_bitacora = 'spw_bitacora_transaccion';
				$param = "$COD_WP_TRANSACCION,'CRONTAB','Montos Diferentes' $MONTO_BD - $monto";
				$db->EXECUTE_SP($sp_bitacora,$param);
				
				echo '<br>';
	            echo "Montos Diferentes $MONTO_BD - $monto";
	            echo '<br>';
        	}
        	
        	
            echo '<br>';
            echo 'Pago OK en SEGIC';
            echo '<br>';
            
            $cant_pagados++; 
            $sum_monto_total = $sum_monto_total + $monto;
	            
        }
        else{
        	$sp = "spw_web_pay_pago";
            $param = "$COD_WP_TRANSACCION,'0','S'";
            $db->EXECUTE_SP($sp,$param);
            
            echo '<br>';
            echo 'No pagado';
            echo '<br>';
        }
        
    }
    echo '<br>';
    echo 'Pagados: '.$cant_pagados;
    echo '<br>';
    echo '<br>';
    echo 'Abonos Totales: '.$sum_monto_total;
    echo '<br>';
}
else{
	echo 'nada encontrado';
}

$sp_bitacora = 'spw_bitacora_transaccion';
$param = "NULL,'CRONTAB','FINALIZADO'";
$db->EXECUTE_SP($sp_bitacora,$param);

?>