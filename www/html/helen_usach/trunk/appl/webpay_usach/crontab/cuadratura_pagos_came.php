<?php
ini_set('display_errors', 'On');
ini_set('max_execution_time', 900);
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

$sql = "SELECT COD_WP_TRANSACCION
				,TOKEN
				,RUT
		FROM CUADRATURA_CAME 
        WHERE TOKEN IS NOT NULL
        AND REVISADO IS NULL
        ORDER BY COD_WP_TRANSACCION ASC";

$result = $db->build_results($sql);
if(count($result)>0){
	for ($i = 0; $i < 72; $i++) {
        $numTransaction = $result[$i]['TOKEN'];
        $COD_WP_TRANSACCION = $result[$i]['COD_WP_TRANSACCION'];
        $MONTO_BD = $result[$i]['MONTO'];
        $EXITO = $result[$i]['EXITO'];
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
		
        if (array_key_exists('error', $json_decode)) {
        	$resp_pago_segic = 1;
		}
		else if (array_key_exists('success', $json_decode)) {
        	//el monto viene solo cuando succes = true
        	$monto = $json_decode['monto'];
        	$voucher = $json_decode['voucher'];
        	$numTarjeta = $json_decode['numTarjeta'];
        	$codigoAuth = $json_decode['codigoAuth'];
        	$numCota = $json_decode['numCota'];
        	$estado = $json_decode['estado'];
        	$succes = $json_decode['success'];
        	
        	if($succes){
	        	if($estado == 2){ //aprobado
		        	$resp_pago_segic = 2;
		        	
	        	}
        	}
        	else if($succes == FALSE){
        		/*echo "ERROR SUCCES FALSE";
		        echo '	 -> ';
		        echo '<br>';*/
		        
		        $resp_pago_segic = 3;
        	}
        	else{
        		/*echo "ERROR SIN ESTADO EN SUCCES";
		        echo '	 -> ';
		        echo '<br>';*/
		        
		        $resp_pago_segic = 4;
        	}
        }
        else{
        	/*echo "ERROR SIN SUCCES";
		    echo '	 -> ';
		    echo '<br>';*/
		    
		    $resp_pago_segic = 5;
        }
        

	    $K_TIPO_BD = "oci";
		$K_SERVER = "(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=bdprdlegacy.usach.cl)(PORT = 1521)))(CONNECT_DATA=(SERVICE_NAME=FINA)))";
		$K_BD = "FINA";
		$K_USER ="CONS_INTERNET_EX";
		$K_PASS = "consulta2019";
		
		$db_segic = new database($K_TIPO_BD, $K_SERVER, $K_BD, $K_USER, $K_PASS);
		
		
		//se consulta a Legacy el pago
        $sql = "Paq_FondoCredito.pro_ConsPagoWebFC('$RUT','$numTransaction',{RESULT})";
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
			
			$resp_legacy = 'S';
			
		}
		else if(count($result_consulta) == 0){
			$resp_legacy = 'N';
			
			$CAPITAL_CONS = 'NULL';
			$INTERES_CONS = 'NULL';
			$MULTA_CONS = 'NULL';
		}
	        
        $sp_bitacora = 'sp_cuadratura_came';
		$param = "'UPDATE',$COD_WP_TRANSACCION, '$resp_pago_segic', '$resp_legacy', $CAPITAL_CONS, $INTERES_CONS, $MULTA_CONS, '$resp_json'";
		$db->EXECUTE_SP($sp_bitacora,$param);
	        
	    
    
    //echo 'Pagados: '.$cant_pagados;
    //echo '<br>';
    //echo '<br>';
    //echo 'Abonos Totales: '.$sum_monto_total;
    //echo '<br>';
	}
}
else{
	echo 'nada encontrado';
}
echo "fin";
?>