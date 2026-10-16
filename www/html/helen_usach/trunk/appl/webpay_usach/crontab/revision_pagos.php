<?php
ini_set('display_errors', 'On');
ini_set('max_execution_time', 0);
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../../../../commonlib_seven/trunk/php/clases/class_PHPMailer.php");
require_once ("../../../../../helen_usach/trunk/appl.ini");

session::set('K_ROOT_URL', K_ROOT_URL);
session::set('K_ROOT_DIR', K_ROOT_DIR);

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$k_ruta_peticion = "https://webpay-server.usach.cl/api/default/get-transaction-detail";
$k_store_id = 12; //parametro fijo que identifica a los pagos institucionales
$cant_pagados = 0;
$sum_monto_total = 0;

$sql = "SELECT COD_WP_TRANSACCION
				,TOKEN
				,MONTO
				,TO_CHAR(DR.RUT)||DR.DIG_VERIF RUT
                ,EXITO
                ,INGRESO_LEGACY
		FROM WP_TRANSACCION WP, DEUDOR DR
        WHERE TIPO_PAGO_WEB = 'PAGO_SEGIC'
        AND WP.COD_DEUDOR = DR.COD_DEUDOR
        and exito = 'S'
        and rownum <= 10000
        order by cod_wp_transaccion desc  ";

$result = $db->build_results($sql);
if(count($result)>0){
    for ($i = 0; $i < count($result); $i++) {
        $numTransaction = $result[$i]['TOKEN'];
        $COD_WP_TRANSACCION = $result[$i]['COD_WP_TRANSACCION'];
        $MONTO_BD = $result[$i]['MONTO'];
        $RUT = $result[$i]['RUT'];
        $EXITO = $result[$i]['EXITO'];
        $INGRESO_LEGACY = $result[$i]['INGRESO_LEGACY'];
        
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
        
        //echo '<br>';
        //echo '<br>';
        //echo '<br>';
        //echo '<br>';
        //echo '<br>';
        $resp_json = '';
	    while (list($key,$val) = each($json_decode))
		{
			$resp_json.= "$key=$val"."->";
		}
		
		
		
        /*$sp_bitacora = 'spw_bitacora_transaccion';
		$param = "$COD_WP_TRANSACCION,'CRONTAB','RESP_JSON: $resp_json'";
		$db->EXECUTE_SP($sp_bitacora,$param);
		*/
		
        if (array_key_exists('success', $json_decode)) {
        	//el monto viene solo cuando succes = true
        	$monto = $json_decode['monto'];
        	$voucher = $json_decode['detail']['details'][0]['buyOrder'];
        	$numTarjeta = $json_decode['numTarjeta'];
        	$codigoAuth = $json_decode['codigoAuth'];
        	$numCota = $json_decode['numCota'];
        	$estado = $json_decode['estado'];
        	$succes = $json_decode['success'];
        	
        	if($succes){
	        	if($estado == 2){ //aprobado
		        	$sp_bitacora = 'SP_REVISION_PAGOS';
                    $param = "$COD_WP_TRANSACCION, 'PAGADO OK'";
                    $db->EXECUTE_SP($sp_bitacora,$param);
		        	
		        	if($EXITO != 'S'){ //aprobado
		        	    $sp_bitacora = 'SP_REVISION_PAGOS';
                        $param = "$COD_WP_TRANSACCION, 'FALTA ABONO'";
                        $db->EXECUTE_SP($sp_bitacora,$param);
		        	}
		        	
		        	if($INGRESO_LEGACY != 'S'){ //aprobado
		        	    $sp_bitacora = 'SP_REVISION_PAGOS';
                        $param = "$COD_WP_TRANSACCION, 'FALTA LEGACY'";
                        $db->EXECUTE_SP($sp_bitacora,$param);
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
                                ,DEUDOR D
                                ,DEUDA DEU
                                ,TIPO_DEUDA TD
                                WHERE WP.COD_WP_TRANSACCION =$COD_WP_TRANSACCION
                                AND WPS.COD_WP_TRANSACCION = WP.COD_WP_TRANSACCION
                                AND SB.COD_SUB_BOLETA = WPS.COD_SUB_BOLETA
                                AND B.COD_BOLETA = SB.COD_BOLETA
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
                        

                        //while(!$respuesta_segic){
                            
                            //se consulta a Legacy el pago
                            $sql = "Paq_FondoCredito.pro_ConsPagoWebFC('$RUT','$voucher',{RESULT})";
                            //echo $sql;
                            //echo '<br>';
                            $result_consulta = $db_segic->build_results2($sql);
                            
                            
                            if(count($result_consulta)>0){
                                //echo 'vienen registros de legacy';
                                //echo '<br>';
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

                                $sp_bitacora = 'SP_REVISION_PAGOS';
                                $param = "$COD_WP_TRANSACCION, 'Registro ya exite legacy'";
                                $db->EXECUTE_SP($sp_bitacora,$param);
                                
                            }
                            else{
                                //echo 'NO vienen, se debe registrar';
                                //echo '<br>';

                                $sp_bitacora = 'SP_REVISION_PAGOS';
                                $param = "$COD_WP_TRANSACCION, 'PENDIENTE LEGACY'";
                                $db->EXECUTE_SP($sp_bitacora,$param);

                                $sql = "SELECT sum(SB.MONTO_CAPITAL * f_mon_get_valor_moneda(TD.COD_MONEDA,WP.FECHA))  MONTO_CAPITAL
                                            ,sum(SB.MONTO_INTERES * f_mon_get_valor_moneda(TD.COD_MONEDA,WP.FECHA))  MONTO_INTERES
                                            ,Sum(SB.MONTO_MULTA * f_mon_get_valor_moneda(TD.COD_MONEDA,WP.FECHA))  MONTO_MULTA
                                        FROM WP_TRANSACCION  WP
                                        ,BOLETA B
                                        ,WP_SUB_BOLETA WPS
                                        ,SUB_BOLETA SB 
                                        ,DEUDOR D
                                        ,DEUDA DEU
                                        ,TIPO_DEUDA TD
                                        WHERE WP.COD_WP_TRANSACCION =$COD_WP_TRANSACCION
                                        AND WPS.COD_WP_TRANSACCION = WP.COD_WP_TRANSACCION
                                        AND SB.COD_SUB_BOLETA = WPS.COD_SUB_BOLETA
                                        AND B.COD_BOLETA = SB.COD_BOLETA
                                        AND DEU.COD_DEUDA = B.COD_DEUDA
                                        AND D.COD_DEUDOR = DEU.COD_DEUDOR
                                        AND TD.COD_TIPO_DEUDA = DEU.COD_TIPO_DEUDA";
                                    $result_monto = $db->build_results($sql);
                                    
                                
                                $MONTO_CAPITAL = $result_monto[0]['MONTO_CAPITAL'];
                                $MONTO_INTERES = $result_monto[0]['MONTO_INTERES'];
                                $MONTO_MULTA = $result_monto[0]['MONTO_MULTA'];

                                $COD_COMERCIO = 597032905501;
                                $fecha_pago = base::str2date($FECHA_TRANSACCION, $HORA_TRANSACCION);
                                

                                $respuesta_segic = true;
                            }
                            
                            $cont_intentos++;
                            
                           
                            
                            
                            
                        //}

                        





	        	}
        	}
        	else if($succes == FALSE){
        		$sp_bitacora = 'SP_REVISION_PAGOS';
                $param = "$COD_WP_TRANSACCION, 'ERROR SUCCES FALSE'";
                $db->EXECUTE_SP($sp_bitacora,$param);
        	}
        	else{
        		$sp_bitacora = 'SP_REVISION_PAGOS';
                $param = "$COD_WP_TRANSACCION, 'ERROR SIN ESTADO EN SUCCES'";
                $db->EXECUTE_SP($sp_bitacora,$param);
        	}
        }
        else{
        	$sp_bitacora = 'SP_REVISION_PAGOS';
            $param = "$COD_WP_TRANSACCION, 'ERROR SIN SUCCES'";
            $db->EXECUTE_SP($sp_bitacora,$param);
        }
        
        
    }
    
    //echo 'Pagados: '.$cant_pagados;
    //echo '<br>';
    //echo '<br>';
    //echo 'Abonos Totales: '.$sum_monto_total;
    //echo '<br>';
}
else{
	echo 'nada encontrado';
}
echo "fin";
?>