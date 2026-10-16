<?php
ini_set('display_errors', 'OFF');
require '../vendor/autoload.php';
require 'class_database.php';
require_once("../../appl.ini");
$parametros ="";
$rut ="";
$monto_abono_pesos ="";
$codigo_trans ="";

//$URL_integracion = 'https://webpay.helen.cl/webpay_rest_utem_institucional/webpay-plus/index.php?action=result';
$URL_integracion = 'https://sishelen.usach.cl/helen_usach/trunk/webpay_rest_usach/webpay-plus/index.php?action=result';
//$URL_integracion = 'https://fondocredito.usach.cl/trunk/webpay_rest_usach/webpay-plus/index.php?action=result';

//$URL_comercio = 'https://web.helen.cl/helen_utem/helen_utem_7/trunk/appl/portal_utem/w_pagar_cuota/webpay_cupon.php';

//$URL_comercio = 'https://sishelen.usach.cl/helen_usach/trunk/appl/webpay_usach/webpay_cupon.php';
$URL_comercio = 'https://fondocredito.usach.cl/helen_usach/trunk/appl/webpay_usach/webpay_cupon.php';


if(isset($_REQUEST['TBK_MONTO'])){
    $monto_abono_pesos = $_REQUEST['TBK_MONTO'];
    
    /** Orden de compra de la tienda */
    $id_transbank = $_REQUEST['TBK_ORDEN_COMPRA'];
}

/*
|--------------------------------------------------------------------------
| Inicializamos el objeto Transaction
|--------------------------------------------------------------------------
*/

$transaction = new \Transbank\Webpay\WebpayPlus\MallTransaction();

// Por simplicidad de este ejemplo, este es nuestro "controlador" que define que vamos a hacer dependiendo del parametro ?action= de la URL.
$action = $_GET['action'] ?? null;
if (!$action) {
    exit('Debe indicar la acción a realizar');
}

/*
|--------------------------------------------------------------------------
| Crear transacción
|--------------------------------------------------------------------------
/ Apenas entramos esta página, con fines demostrativos,
*/
if ($_GET['action'] === 'create') {
    $dbc   = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    $sp_bitacora = 'spw_bitacora_transaccion';
    $param = "'$id_transbank','create','Inicio Metodo Create'";
   
    $dbc->EXECUTE_SP($sp_bitacora,$param);

    $commerceCode='597032896480';
    $apiKeySecret = 'b6a7dda52a8d0c5c77211418dc89da1b';
    
    //esta funcion pasa a produccion el ambiente, debe hacerse antes del crate y antes del commit
    $transaction = (new Transbank\Webpay\WebpayPlus\MallTransaction)->configureForProduction($commerceCode, $apiKeySecret);
        
    $transaction_details = [
        [
            "amount" => $monto_abono_pesos,
            "commerce_code" => 597032909078,
            "buy_order" => "$id_transbank"
        ],
    ];
    
    $createResponse = $transaction->create($id_transbank, uniqid(), $URL_integracion, $transaction_details);
    
    // Acá guardar el token recibido ($createResponse->getToken()) en tu base de datos asociado a la orden o
    // lo que se esté pagando en tu sistema
    $token = $createResponse->getToken();
    $sp = 'spw_wp_init_transaccion';
    $param = "'$id_transbank','$token'";
  
    $dbc->EXECUTE_SP($sp,$param);

   // return;

    //Redirigimos al formulario de Webpay por GET, enviando a la URL recibida con el token recibido.
    $redirectUrl = $createResponse->getUrl().'?token_ws='.$createResponse->getToken();
    header('Location: '.$redirectUrl, true, 302);
    exit;
}
/*
|--------------------------------------------------------------------------
| Confirmar transacción
|--------------------------------------------------------------------------
/ Esto se debería ejecutar cuando el usario finaliza el proceso de pago en el formulario de webpay.
*/
if ($_GET['action'] === 'result') {
    
    $dbc   = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    if (userAbortedOnWebpayForm()) {
        cancelOrder();
        exit('Has cancelado la transacción en el formulario de pago. Intenta nuevamente');
    }
    if (anErrorOcurredOnWebpayForm()) {
        cancelOrder();
        exit('Al parecer ocurrió un error en el formulario de pago. Intenta nuevamente');
    }
    if (theUserWasRedirectedBecauseWasIdleFor10MinutesOnWebapayForm()) {
        cancelOrder();
        exit('Superaste el tiempo máximo que puedes estar en el formulario de pago (10 minutos). La transacción fue cancelada por Webpay. ');
    }
    //Por último, verificamos que solo tengamos un token_ws. Si no es así, es porque algo extraño ocurre.
    if (!isANormalPaymentFlow()) { // Notar que dice ! al principio.
        cancelOrder();
        exit('En este punto, si NO es un flujo de pago normal es porque hay algo extraño y es mejor abortar. Quizás alguien intenta llamar a esta URL directamente o algo así...');
    }
    // Acá ya estamos seguros de que tenemos un flujo de pago normal. Si no, habría "muerto" en los checks anteriores.
    $token = $_GET['token_ws'] ?? $_POST['token_ws'] ?? null; // Obtener el token de un flujo normal
    
    $commerceCode ='597032896480';
    $apiKeySecret = 'b6a7dda52a8d0c5c77211418dc89da1b';
    
    //esta funcion pasa a produccion el ambiente, debe hacerse antes del crate y antes del commit
    $transaction = (new Transbank\Webpay\WebpayPlus\MallTransaction)->configureForProduction($commerceCode, $apiKeySecret);
    
    $response = $transaction->commit($token);
    $fase = 'result';
    $id_transbank = $response->getBuyOrder();//orden compra
    //$monto_pago = $response->getAmount();
    $details = $response->getDetails();
    
    $monto_pago = '';
    $codigo_respuesta = '';
    $estado = '';
    
    foreach($details as $detail){ // En SDk 2.x cada $detail es un Objeto TransactionDetails
        $monto_pago = $detail->getAmount();
        $codigo_respuesta = $detail->getResponseCode();
        $estado = $detail->getStatus();
    }
    
    if ($response->isApproved()) {
        //Si el pago está aprobado (responseCode == 0 && status === 'AUTHORIZED') entonces aprobamos nuestra compra
        
        //- validar orden de compra
        $sql = "SELECT COUNT(*) CANT
				FROM WP_TRANSACCION
				WHERE COD_WP_TRANSACCION ='$id_transbank'
				AND EXITO = 'S'";
        $result = $dbc->build_results($sql);
        $cant = $result[0]["CANT"];
        
        // validar monto
        $sql = "SELECT MONTO
					  ,TOKEN
				FROM WP_TRANSACCION
				WHERE COD_WP_TRANSACCION ='$id_transbank'";
        $result = $dbc->build_results($sql);
        $monto_guardado = $result[0]['MONTO'];
        $token_guardado = $result[0]['TOKEN'];
        $token_guardado = trim($token_guardado);
        $token = trim($token);
        
        if($cant > 0){//COMPROBAR QUE NO ESTE DUPLICADA
            $sp_bitacora = 'spw_bitacora_transaccion';
            $param = "'$id_transbank','result','Transaccion Duplicada'";
            $dbc->EXECUTE_SP($sp_bitacora,$param);
            
            //enviar a una URL de fracaso.
            $url  = $URL_comercio."?RESULTADO=".encriptar_url($id_transbank,'Encript_Code_Compra');
            header ('Location: '.$url);
        }else if($monto_guardado != $monto_pago){
            $sp_bitacora = 'spw_bitacora_transaccion';
            $param = "'$id_transbank','result','MONTOS DIFERENTES $monto_guardado VS $monto_pago'";
            $dbc->EXECUTE_SP($sp_bitacora,$param);
            
            //enviar a una URL de fracaso.
            $url  = $URL_comercio."?RESULTADO=".encriptar_url($id_transbank,'Encript_Code_Compra');
            header ('Location: '.$url);
        }else if($token_guardado != $token){
            $sp_bitacora = 'spw_bitacora_transaccion';
            $param = "'$id_transbank','result','TOKENS DIFERENTES'";
            $dbc->EXECUTE_SP($sp_bitacora,$param);
            
            //enviar a una URL de fracaso.
            $url  = $URL_comercio."?RESULTADO=".encriptar_url($id_transbank,'Encript_Code_Compra');
            header ('Location: '.$url);
        }else{
        
            if($codigo_respuesta != 0){
                $sp_bitacora = 'spw_bitacora_transaccion';
                $param = "'$id_transbank','result','RECHAZADO RESPUESTA responseCode NO ES 0'";
                $dbc->EXECUTE_SP($sp_bitacora,$param);
                
                //enviar a una URL de fracaso.
                $url  = $URL_comercio."?RESULTADO=".encriptar_url($id_transbank,'Encript_Code_Compra');
                header ('Location: '.$url);
            }
            else if($estado != 'AUTHORIZED'){
                $sp_bitacora = 'spw_bitacora_transaccion';
                $param = "'$id_transbank','result','RECHAZADO RESPUESTA status NO ES AUTHORIZED'";
                $dbc->EXECUTE_SP($sp_bitacora,$param);
                
                //enviar a una URL de fracaso.
                $url  = $URL_comercio."?RESULTADO=".encriptar_url($id_transbank,'Encript_Code_Compra');
                header ('Location: '.$url);
            }
            else if($codigo_respuesta == 0 && $estado === 'AUTHORIZED'){
                // Código para aprobar compra acá
                $sp_bitacora = 'spw_bitacora_transaccion';
                $param = "'$id_transbank','result','TRANSACCION APROBADA'";
                $dbc->EXECUTE_SP($sp_bitacora,$param);
                
                approveOrder($response);
            }
            else{
                //error inesperado, enviar a una URL de fracaso
                $url  = $URL_comercio."?RESULTADO=".encriptar_url($id_transbank,'Encript_Code_Compra');
                header ('Location: '.$url);
            }
        }
        
    } else {
        $sp_bitacora = 'spw_bitacora_transaccion';
        $param = "'$id_transbank','result','TRANSICION ERROR INESPERADO'";
        $dbc->EXECUTE_SP($sp_bitacora,$param);
        
        cancelOrder($response);
    }

    return;
}

function cancelOrder($response=null)
{
    //$URL_comercio = 'https://sishelen.usach.cl/helen_usach/trunk/appl/webpay_usach/webpay_cupon.php';
      $URL_comercio = 'https://fondocredito.usach.cl/helen_usach/trunk/appl/webpay_usach/webpay_cupon.php';
    //
    //$URL_comercio = 'https://web.helen.cl/helen_utem/helen_utem_7/trunk/appl/portal_utem/w_pagar_cuota/webpay_cupon.php';
    // Acá has lo que tangas que hacer para marcar la orden como fallida o cancelada
    // El primer if solo es llamado desde el result cuando la transaccion no fue aprobada 
    // al else caería cuando se cancela la transaccion antes, ya sea porque el usuario canceló manualmente
    if ($response) {
        $id_transbank = $response->getBuyOrder();
        echo '<pre>'.print_r($response, true).'</pre>';
    }
    else if(isset($_REQUEST['TBK_ORDEN_COMPRA'])){
        $id_transbank = $_REQUEST['TBK_ORDEN_COMPRA'];
    }
    
    echo 'La orden ha sido RECHAZADA';
    
    $url  = $URL_comercio."?RESULTADO=".encriptar_url($id_transbank,'Encript_Code_Compra');
    header ('Location: '.$url);
}

function approveOrder($response)
{
    //$URL_comercio = 'http://192.168.2.92/desarrolladores/evergara/helen_utem_7/trunk/appl/portal_utem/w_pagar_cuota/webpay_cupon.php';
    $URL_comercio = 'https://fondocredito.usach.cl/helen_usach/trunk/appl/webpay_usach/webpay_cupon.php';
    
    $orden_compra_tienda = $response->getBuyOrder();//cod_wp_transaccion
    $id_transbank = $orden_compra_tienda;
    $resultado_autenticacion = $response->getVci();
    //$monto      = $response->getAmount();
    //$estado     = $response->getStatus();
    $session_id = $response->getSessionId();
    $numero_tarjeta = $response->getCardNumber();
    $fecha_autorizacion     = $response->getAccountingDate();
    $fecha_hora_autorizacion= $response->getTransactionDate();
    //$codigo_autorizacion    = $response->getAuthorizationCode();
    //$tipo_pago         = $response->getPaymentTypeCode();
    //$codigo_respuesta  = $response->getResponseCode();
    //$valor_cuota       = $response->getInstallmentsAmount();
    //$cantidad_cuota    = $response->getInstallmentsNumber();
    //$monto_restante    = $response->getBalance();//Monto restante para un detalle anulado
    
    $monto = '';
    $codigo_autorizacion = '';
    $orden_compra_tienda = '';
    $codigo_comercio_tienda = '';
    $cantidad_cuota = '';
    $valor_cuota = '';
    $tipo_pago = '';
    $codigo_respuesta = '';
    $estado = '';
    
    $details = $response->getDetails();
    foreach($details as $detail){ // En SDk 2.x cada $detail es un Objeto TransactionDetails
        $monto = $detail->getAmount();
        $codigo_autorizacion = $detail->getAuthorizationCode();
        $orden_compra_tienda = $detail->getBuyOrder();
        $codigo_comercio_tienda = $detail->getCommerceCode();
        $cantidad_cuota = $detail->getInstallmentsNumber();
        $valor_cuota = $detail->getInstallmentsAmount();
        $tipo_pago = $detail->getPaymentTypeCode();
        $codigo_respuesta = $detail->getResponseCode();
        $estado = $detail->getStatus();
        
        
    }
    
    
    
    if(!is_numeric($monto))
        $monto = "null";
    if(!is_numeric($valor_cuota))
        $valor_cuota = "null";
    if(!is_numeric($cantidad_cuota))
        $cantidad_cuota = "null";
    
        $dbc   = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
    
    $param = "'INSERT'
				,'$id_transbank'
				,'$session_id'
				,'$numero_tarjeta'
				,null
				,'$fecha_autorizacion'
				,'$fecha_hora_autorizacion'
				,'$resultado_autenticacion'
				,null
				,'$codigo_autorizacion'
				,'$tipo_pago'
				,'$codigo_respuesta'
				,$monto
				,$valor_cuota
				,$cantidad_cuota
				,'$codigo_comercio_tienda'
				,'$orden_compra_tienda'";
    
    $sp = 'sp_wp_pago_transaccion_ws';

    if(!$dbc->EXECUTE_SP($sp,$param)){
        
        $urlRedirection = $url_fin_transaccion;
        $data = "sp_wp_pago_transaccion ERROR";
        
        $sp_bitacora = 'spw_bitacora_transaccion';
        $param = "'$id_transbank','result','Error sp_wp_pago_transaccion_ws'";
        $dbc->EXECUTE_SP($sp_bitacora,$param);
    }else{
        
        
        /*CURL*/
       // JSON->
        
        
        
        $sp = "spw_web_pay_pago";
        $param = "'$id_transbank', '1'";
        if(!$dbc->EXECUTE_SP($sp,$param)){
            $sp_bitacora = 'spw_bitacora_transaccion';
            $param = "'$id_transbank','result','ERROR spw_web_pay_pago'";
            $dbc->EXECUTE_SP($sp_bitacora,$param);
        }else{
            $sp_bitacora = 'spw_bitacora_transaccion';
            $param = "'$id_transbank','result','TRANSICION EXITOSA'";
            $dbc->EXECUTE_SP($sp_bitacora,$param);
        }
    }
    
    // Acá has lo que tangas que hacer para marcar la orden como aprobada o finalizada o lo que necesites en tu negocio.,
    echo 'La orden ha sido APROBADA';
    echo '<pre>'.print_r($response, true).'</pre>';
    $url  = $URL_comercio."?RESULTADO=".encriptar_url($id_transbank,'Encript_Code_Compra');
    header ('Location: '.$url);
}

function encriptar_url($txt_input, $key){
    $result = '';
    for($i=0; $i<strlen($txt_input); $i++) {
        $char = substr($txt_input, $i, 1);
        $keychar = substr($key, ($i % strlen($key))-1, 1);
        $char = chr(ord($char)+ord($keychar));
        $result.=$char;
    }
    $txt_ouput = base64_encode($result);
    
    return $txt_ouput;
}

function userAbortedOnWebpayForm()
{
    $tokenWs = $_GET['token_ws'] ?? $_POST['token_ws'] ?? null;
    $tbkToken = $_GET['TBK_TOKEN'] ?? $_POST['TBK_TOKEN'] ?? null;
    $ordenCompra = $_GET['TBK_ORDEN_COMPRA'] ?? $_POST['TBK_ORDEN_COMPRA'] ?? null;
    $idSesion = $_GET['TBK_ID_SESION'] ?? $_POST['TBK_ID_SESION'] ?? null;

    // Si viene TBK_TOKEN, TBK_ORDEN_COMPRA y TBK_ID_SESION es porque el usuario abortó el pago
    return $tbkToken && $ordenCompra && $idSesion && !$tokenWs;
}

function anErrorOcurredOnWebpayForm()
{
    $tokenWs = $_GET['token_ws'] ?? $_POST['token_ws'] ?? null;
    $tbkToken = $_GET['TBK_TOKEN'] ?? $_POST['TBK_TOKEN'] ?? null;
    $ordenCompra = $_GET['TBK_ORDEN_COMPRA'] ?? $_POST['TBK_ORDEN_COMPRA'] ?? null;
    $idSesion = $_GET['TBK_ID_SESION'] ?? $_POST['TBK_ID_SESION'] ?? null;

    // Si viene token_ws, TBK_TOKEN, TBK_ORDEN_COMPRA y TBK_ID_SESION es porque ocurrió un error en el formulario de pago
    return $tokenWs && $ordenCompra && $idSesion && $tbkToken;
}

function theUserWasRedirectedBecauseWasIdleFor10MinutesOnWebapayForm()
{
    $tokenWs = $_GET['token_ws'] ?? $_POST['token_ws'] ?? null;
    $tbkToken = $_GET['TBK_TOKEN'] ?? $_POST['TBK_TOKEN'] ?? null;
    $ordenCompra = $_GET['TBK_ORDEN_COMPRA'] ?? $_POST['TBK_ORDEN_COMPRA'] ?? null;
    $idSesion = $_GET['TBK_ID_SESION'] ?? $_POST['TBK_ID_SESION'] ?? null;

    // Si viene solo TBK_ORDEN_COMPRA y TBK_ID_SESION es porque el usuario estuvo 10 minutos sin hacer nada en el
    // formulario de pago y se canceló la transacción automáticamente (por timeout)
    return $ordenCompra && $idSesion && !$tokenWs && !$tbkToken;
}

function isANormalPaymentFlow()
{
    $tokenWs = $_GET['token_ws'] ?? $_POST['token_ws'] ?? null;
    $tbkToken = $_GET['TBK_TOKEN'] ?? $_POST['TBK_TOKEN'] ?? null;
    $ordenCompra = $_GET['TBK_ORDEN_COMPRA'] ?? $_POST['TBK_ORDEN_COMPRA'] ?? null;
    $idSesion = $_GET['TBK_ID_SESION'] ?? $_POST['TBK_ID_SESION'] ?? null;

    // Si viene solo token_ws es porque es un flujo de pago normal
    return $tokenWs && !$ordenCompra && !$idSesion && !$tbkToken;
}
