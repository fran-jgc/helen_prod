<?php
ini_set('display_errors', 0);
ini_set('max_execution_time', 300);
require_once('wss/soap-wsse.php');
require_once('wss/soap-validation.php');
require_once('WebServiceMall.php');
require_once("class_database.php");
require_once("../appl.ini");

function base64url_decode($data) { 
  return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT)); 
} 

//define('SERVER_CERT', 'certificados_mall/tbk_server_mall.pem'); //local
define('SERVER_CERT', 'webpay.crt'); //produccion

$data ="RECHAZADO";
$recarga_automatica = "window.document.forms['imput'].submit();";
$urlRedirection = "";
$url_fracaso = "https://sishelen.usach.cl/helen_usach/trunk/appl/webpay_usach/web_pay/fracaso.php";


$wp_transaccion = isset($_REQUEST["TBK_ORDEN_COMPRA"]) ? $_REQUEST["TBK_ORDEN_COMPRA"] : '';
$TBK_ORDEN_COMPRA = "";

$action = isset($_REQUEST["action"]) ? $_REQUEST["action"] : 'init';
$token ='';
$nombre_archivo = "logs.txt";

switch ($action) {
	default: //INIT TRANSACCION
		$fase = "INICIO TRANSACCION";
		$TBK_BD = $_REQUEST["TBK_BD"] ;
		
		/*
		session_start();

		$_SESSION["TBK_HELEN_USACH"] = $TBK_BD; 
		
		 
		$error = 1;
		$datos_bd = base64url_decode($TBK_BD);
		
		
		$privatekey = openssl_get_privatekey('file://clave.pem');
		if(!openssl_private_decrypt($datos_bd, $param_descripter, $privatekey)) {
			$error = 2; //echo "Error DESENcriptando datos <br>"; 
			$param_descripter = "ERROR_DESENCRIPTADO "; 
			$data = $param_descripter;
			$action = "CONFIRMACION";
			break;
		}
		
		$bd_array = explode('&', $param_descripter); 
		$bd_type = $bd_array[0];
		$bd_host = $bd_array[1];
		$bd_name = $bd_array[2];
		$bd_user = $bd_array[3];
		$bd_pass = $bd_array[4];
		
		$dbc   = new database($bd_type,$bd_host,$bd_name,$bd_user,$bd_pass);
		*/
		$dbc   = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		
		$TBK_MONTO = $_REQUEST["TBK_MONTO"]; 
		$TBK_CODIGO_TIENDA = $_REQUEST["TBK_CODIGO_TIENDA"];
		
		$wsInitTransactionInput = new wsInitTransactionInput(); 
		$wsTransactionDetail = new wsTransactionDetail();
		$wsInitTransactionInput->wSTransactionType = 'TR_MALL_WS'; 
		$wsInitTransactionInput->commerceId = '597032896480';
		$wsInitTransactionInput->buyOrder = $wp_transaccion;
		$wsInitTransactionInput->sessionId = '';
		$wsInitTransactionInput->returnURL ='https://sishelen.usach.cl/helen_usach/trunk/webpay_ws_usach/transaccion_completa_mall.php?action=TRANSICION';
		$wsInitTransactionInput->finalURL = "https://sishelen.usach.cl/helen_usach/trunk/webpay_ws_usach/transaccion_completa_mall.php?action=CONFIRMACION&TBK_ORDEN_COMPRA=$wp_transaccion";
		//PRIMERA TIENDA
		$wsTransactionDetail->commerceCode = $TBK_CODIGO_TIENDA;
		$wsTransactionDetail->buyOrder =  $wp_transaccion;
		$wsTransactionDetail->amount = $TBK_MONTO;
		/*SE AGREGA ARREGLO DE LA TIENDA*/
		$detailArray[0] = $wsTransactionDetail;
		
		$wsInitTransactionInput->transactionDetails = $detailArray;
		
		$webpayService = new WebService();  
		$initTransactionResponse = $webpayService->initTransaction(array("wsInitTransactionInput" => $wsInitTransactionInput));
		$xmlResponse = $webpayService->soapClient->__getLastResponse(); 
		$soapValidation = new SoapValidation($xmlResponse, SERVER_CERT);
		$validationResult = $soapValidation->getValidationResult();
		if($validationResult === true){// se valida que el certificado sea de transbank
		
			//echo 'inicia';
			/*Invocar s�lo s� $validationResult es TRUE*/ 	
			$sql = "SELECT COUNT(BT.COD_WP_TRANSACCION)CONTADOR
			FROM BITACORA_TRANSACCION BT
			WHERE BT.COD_WP_TRANSACCION = $wp_transaccion
			AND BT.FASE = 'INICIO TRANSACCION'
			AND BT.MENSAJE = 'Inicio Transaccion Exitoso'";

			$dbc->query($sql);
			$result = $dbc->get_row();
			
			//print_r($result);
			$CONTADOR = $result['CONTADOR'];
			
			if($CONTADOR > 0){
				$url = $url_fracaso;
				$sp_bitacora = 'spw_bitacora_transaccion';  
				$param = "'$wp_transaccion','$fase','INIT TRANSACCTION EJECUTADO 2 VECES'";
				$dbc->EXECUTE_SP($sp_bitacora,$param);
				break;
			}else{
				$wsInitTransactionOutput = $initTransactionResponse->return;
				$token= $wsInitTransactionOutput->token;
				$urlRedirection = $wsInitTransactionOutput->url;
				$data = $token;
				$action = "TRANSICION";
				$TBK_ORDEN_COMPRA = $wsTransactionDetail->buyOrder;
				
				//$_SESSION["TBK_TOKEN"] = $token; 
				
				if($archivo = fopen($nombre_archivo, "a"))
				{
					fwrite($archivo, $token."&".$urlRedirection."&".$data."&".$action."&".$TBK_ORDEN_COMPRA."&". "\n");
					
			 
					fclose($archivo);
				}
				
				
				$sp = 'spw_wp_init_transaccion';
				$param = "$wp_transaccion,'$token'";
				
				if(!$dbc->EXECUTE_SP($sp,$param)){
					$data = "spw_wp_init_transaccion error";
					$urlRedirection = $url_fracaso;
					$sp_bitacora = 'spw_bitacora_transaccion';
					$param = "$wp_transaccion,'$fase','Error con spw_wp_init_transaccion'";
					$dbc->EXECUTE_SP($sp_bitacora,$param);
					break;
				}else{
					$sp_bitacora = 'spw_bitacora_transaccion';
					$param = "$wp_transaccion,'$fase','Inicio Transaccion Exitoso'";
					$dbc->EXECUTE_SP($sp_bitacora,$param);
					break;	
				}
			}
		}else{ 
			//echo 'fracasa';
			$urlRedirection = $url_fracaso;
			$data = "validacion false "; 
			$sp_bitacora = 'spw_bitacora_transaccion';
			$param = "$wp_transaccion,'$fase','Error con validacion de certificado'";
			$dbc->EXECUTE_SP($sp_bitacora,$param);
		}
	break;
	case "TRANSICION":
		$fase = "TRANSICION";
		if (!isset($_POST["token_ws"]))
            break;
        
        /*
        session_start();
        $TBK_BD = $_SESSION["TBK_HELEN_USACH"];
		
		$datos_bd = base64url_decode($TBK_BD);
		
		$privatekey = openssl_get_privatekey('file://clave.pem');
		if(!openssl_private_decrypt($datos_bd, $param_descripter, $privatekey)) {
			$error = 2; //echo "Error DESENcriptando datos <br>";
			$param_descripter = "ERROR_DESENCRIPTADO ";
			$action = "CONFIRMACION";
			break;
		}
		
		$bd_array = explode('&', $param_descripter);
		$bd_type = $bd_array[0];
		$bd_host = $bd_array[1];
		$bd_name = $bd_array[2];
		$bd_user = $bd_array[3];
		$bd_pass = $bd_array[4];
		
		$dbc   = new database($bd_type,$bd_host,$bd_name,$bd_user,$bd_pass);	
		
		*/
        $dbc   = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
        
		$token = $_POST['token_ws'];
		//$token = $_SESSION["TBK_TOKEN"];
		
        $webpayService = new WebService();
		$getTransactionResult = new getTransactionResult();
		$getTransactionResult->tokenInput = $token;
		$getTransactionResultResponse = $webpayService->getTransactionResult($getTransactionResult);
		$transactionResultOutput = $getTransactionResultResponse->return;
		
		$orden_compra = $transactionResultOutput->buyOrder;
		$sessionId = $transactionResultOutput->sessionId;
		$fecha_autorizacion = $transactionResultOutput->accountingDate;
		$fecha_hora_autorizacion = $transactionResultOutput->transactionDate;
		$resultado_autenticacion = $transactionResultOutput->VCI;
		$urlRedirection = $transactionResultOutput->urlRedirection;
		$detailsOutput = $transactionResultOutput->detailOutput;
		//detalle tarjeta
		$numeroTarjeta = $transactionResultOutput->cardDetail->cardNumber;
		$expiracion_tarjeta = $transactionResultOutput->cardDetail->cardExpirationDate;
		
		//detalle compra
		$codigo_autorizacion = $transactionResultOutput->detailOutput->authorizationCode;
		$tipo_transaccion = $transactionResultOutput->detailOutput->paymentTypeCode;
		$respuesta_pago = $transactionResultOutput->detailOutput->responseCode;
		$cantidad_cuota = $transactionResultOutput->detailOutput->sharesNumber;
		
		$monto_pago = $transactionResultOutput->detailOutput->amount;
		$codigo_comercio = $transactionResultOutput->detailOutput->commerceCode;
		$orden_compra_tienda = $transactionResultOutput->detailOutput->buyOrder;
		
		$TBK_ORDEN_COMPRA = $orden_compra;
		
		$param = "'INSERT',$orden_compra,'$sessionId','$numeroTarjeta','$expiracion_tarjeta','$fecha_autorizacion','$fecha_hora_autorizacion','$resultado_autenticacion','$urlRedirection','$codigo_autorizacion','$tipo_transaccion','$respuesta_pago',$monto_pago,null,$cantidad_cuota,'$codigo_comercio','$orden_compra_tienda'";
	
		$sp = 'sp_wp_pago_transaccion_ws';
		
		if($archivo = fopen($nombre_archivo, "a"))
		{
			fwrite($archivo, $param. "\n");
			fclose($archivo);
		}
		
		if(!$dbc->EXECUTE_SP($sp,$param)){ 
			$urlRedirection = $url_fracaso;
			$data = "sp_wp_pago_transaccion_ws ERROR";
			$sp_bitacora = 'spw_bitacora_transaccion';
			$param = "$orden_compra,'$fase','Error sp_wp_pago_transaccion_ws' $param";
			$dbc->EXECUTE_SP($sp_bitacora,$param);
			break;
		}else{ 
			//- validar orden de compra
			$sql = "SELECT COUNT(*) CANT
					FROM WP_TRANSACCION
					WHERE COD_WP_TRANSACCION = $orden_compra
					AND EXITO = 'S'";
			$dbc->query($sql);
			$result = $dbc->get_row();
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
			$dbc->query($sql);
			$result = $dbc->get_row();
			$monto_guardado = $result['MONTO_PESOS'];
			$token_guardado = $result['TOKEN'];
			$token_guardado = trim($token_guardado);
			$token = trim($token);
			if($cant > 0){//COMPROBAR QUE NO ESTE DUPLICADA
					$urlRedirection = $url_fracaso; 
					$data = "DUPLICADA";
					$sp_bitacora = 'spw_bitacora_transaccion';
					$param = "$orden_compra,'$fase','DUPLICADA'";
					$dbc->EXECUTE_SP($sp_bitacora,$param);
					break;
			}else if($monto_guardado != $monto_pago){ 
			 	$urlRedirection = $url_fracaso; 
			 	$data = "RECHAZADO";
			 	
			 	$sp_bitacora = 'spw_bitacora_transaccion';
				$param = "$orden_compra,'$fase','MONTOS DIFERENTES $monto_guardado VS $monto_pago'";
				$dbc->EXECUTE_SP($sp_bitacora,$param);
				break;
			}else if($token_guardado != $token){ 
				$data = "RECHAZADO";
				$urlRedirection = $url_fracaso; 
				
				$sp_bitacora = 'spw_bitacora_transaccion';
				$param = "$orden_compra,'$fase','TOKENS DIFERENTES'";
				$dbc->EXECUTE_SP($sp_bitacora,$param);
				break;
			}else{
				//SE INFORMA A TRANSBANK QUE SE HIZO UNA TRANSACCION
				$webpayService = new WebService();
				$acknowledgeTransaction = new acknowledgeTransaction();
				$acknowledgeTransaction->tokenInput = $token;
				$acknowledgeTransactionResponse = $webpayService->acknowledgeTransaction($acknowledgeTransaction);
				
				if($respuesta_pago != '0'){	
					$data = "RECHAZADO RESPUESTA NO ES 0";
					$urlRedirection = $url_fracaso; 
					$sp_bitacora = 'spw_bitacora_transaccion';
					$param = "$orden_compra,'$fase','RESPUESTA DE PAGO NO FUE 0'";
					$dbc->EXECUTE_SP($sp_bitacora,$param);
					break;
				}else if($respuesta_pago == '0'){ 
					//si todas las validaciones OK
					$urlRedirection = $transactionResultOutput->urlRedirection;
					/* SI LA TRANSACCION RESULTO EXITOSA SE ACTUALIZA LA TABLA WP_TRANSACCION */
					$sp = "spw_web_pay_pago";
					$param = "$orden_compra, '1'";
					if(!$dbc->EXECUTE_SP($sp,$param)){ 
						$urlRedirection = $url_fracaso;
						$data = "spw_web_pay_pago error";
						$sp_bitacora = 'spw_bitacora_transaccion';
						$param = "$orden_compra,'$fase','ERROR spw_web_pay_pago'";
						$dbc->EXECUTE_SP($sp_bitacora,$param);
						break;
					}else{
						$data = "ACEPTADO";
						$sp_bitacora = 'spw_bitacora_transaccion';
						$param = "$orden_compra,'$fase','TRANSICION EXITOSA'";
						$dbc->EXECUTE_SP($sp_bitacora,$param);
						$action = "CONFIRMACION";
						break;
					}	
				} 
			}
		}
	break;	
	case "CONFIRMACION":
		$action = "CONFIRMACION";
		$data = "ERROR CONFIRMACION";
		$token = "";
		$TBK_ORDEN_COMPRA = $_REQUEST['TBK_ORDEN_COMPRA'];
		
		$token = $_POST['token_ws'];
		//$token = $_SESSION["TBK_TOKEN"];
		
		if ($token == ''){
			$urlRedirection = $url_fracaso; 
			break;
		} 

		/*
		session_start();

        $TBK_BD = $_SESSION["TBK_HELEN_USACH"];
		$datos_bd = base64url_decode($TBK_BD);
		$privatekey = openssl_get_privatekey('file://clave.pem');
		if(!openssl_private_decrypt($datos_bd, $param_descripter, $privatekey)) {
			$error = 2; //echo "Error DESENcriptando datos <br>";
			$param_descripter = "ERROR_DESENCRIPTADO ";
			$urlRedirection = $url_fracaso;
			break;
		}
		$bd_array = explode('&', $param_descripter);
		$bd_type = $bd_array[0];
		$bd_host = $bd_array[1];
		$bd_name = $bd_array[2];
		$bd_user = $bd_array[3];
		$bd_pass = $bd_array[4];
		
		$dbc   = new database($bd_type,$bd_host,$bd_name,$bd_user,$bd_pass); 
        */
		$dbc   = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
        
		$sql="select EXITO from WP_TRANSACCION where TOKEN = '$token'";
		$dbc->query($sql);
		$result = $dbc->get_row();
		$resultado = $result['EXITO'];
		
		if($token == '')
			$urlRedirection = $url_fracaso;
		else if($resultado != 'S')	
			$urlRedirection = $url_fracaso;
		else if($resultado == 'S'){	
			$data = "Correcto++  ";
			$urlRedirection = "https://sishelen.usach.cl/helen_usach/trunk/appl/webpay_usach/web_pay/exito.php";
			break;	
		}	
	}
		
//$recarga_automatica = "";
//SI SE QUIERE VER EL REQUEST Y EL RESPONSE
 /*	print_r($wsInitTransactionInput);
	print_r($initTransactionResponse); 
 */ 
//OnLoad="window.document.forms['imput'].submit();"
//OnLoad=<?=$recarga_automatica*/
//<input type="submit" value="OK">
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title></title>
</head>
<body OnLoad=<?php echo $recarga_automatica?>>

<form action="<?php echo $urlRedirection?>" method="post" id="imput" TARGET="_self">
<p><input type="hidden" name="token_ws" size="60" value="<?php echo $token?>" /></p>
<p><input type="hidden" name="TBK_ORDEN_COMPRA" size="60" value="<?php echo $TBK_ORDEN_COMPRA?>" /></p>
<p><input type="hidden" name="data" size="60" value="<?php echo $data?>" /></p>
<p><input type="hidden" name="action" size="60" value="<?php echo $action?>" /></p>

</form>

</body>
</html>