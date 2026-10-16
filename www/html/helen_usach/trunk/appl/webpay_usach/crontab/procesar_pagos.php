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


    for ($i = 0; $i < 1; $i++) {
        $numTransaction = 1574213;
        //$COD_WP_TRANSACCION = $result[$i]['COD_WP_TRANSACCION'];
        //$MONTO_BD = $result[$i]['MONTO'];
        //$RUT = $result[$i]['RUT'];
        
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
        
        //echo $COD_WP_TRANSACCION;
        //echo '<br>';
        
        //print_r($json_decode);
        
        //echo '<br>';
        //echo '<br>';
        //echo '<br>';
        //echo '<br>';
        //echo '<br>';
        /*$resp_json = '';
	    while (list($key,$val) = each($json_decode))
		{
			$resp_json.= "$key=$val"."->";
		}*/
		
		print_r($json_decode);

        
        
    }
    //echo '<br>';
    //echo 'Pagados: '.$cant_pagados;
    //echo '<br>';
    //echo '<br>';
    //echo 'Abonos Totales: '.$sum_monto_total;
    //echo '<br>';




?>