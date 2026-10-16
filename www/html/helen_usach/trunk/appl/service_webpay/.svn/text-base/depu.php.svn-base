<?php
require_once(dirname(__FILE__)."/../web_service/class_ws_helen.php");

$ws_helen = new wakeUp_helen();



//INSERT,227211,,8368,,0904,2019-09-04T12:43:54.941-04:00,TSN,https://webpay.integrasystem.cl/webpay_ws_usach/fracaso.php,000000,VD,-1,3,null,0,597032909078,227211

$operacion = 'INSERT';
$orden_compra = '227211';
$sessionId = 'asdas';
$numeroTarjeta = '8368';
$expiracion_tarjeta = 'asda';
$fecha_autorizacion = '0904';
$fecha_hora_autorizacion = '2019-09-04T12:43:54.941-04:00';
$resultado_autenticacion = 'TSN';
$urlRedirection = 'https://webpay.integrasystem.cl/webpay_ws_usach/fracaso.php';
$codigo_autorizacion = '000000';
$tipo_transaccion = 'VD';
$respuesta_pago = '-1';
$monto_pago = '3';
$null = 'null';
$cantidad_cuota = '0';
$codigo_comercio = '597032909078';
$orden_compra_tienda = '227211';


$result = $ws_helen->pago_transaccion($operacion
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
											,$orden_compra_tienda);
											
print_r($result);										

?>