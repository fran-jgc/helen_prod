<?php 
require_once(dirname(__FILE__)."/../../web_service/class_ws_helen.php");

$TBK_ORDEN_COMPRA		= $_POST['TBK_ORDEN_COMPRA'];
$cod_wp_transaccion = $TBK_ORDEN_COMPRA;


$ws_helen = new wakeUp_helen();
$result = $ws_helen->datos_exito($cod_wp_transaccion);

$tbk_rut = $result[0]['RUT'];
$tbk_monto_pesos = $result[0]['MONTO_PESOS'];
$tbk_cod_wp_transaccion = $result[0]['COD_WP_TRANSACCION'];
$tbk_fecha_transaccion = $result[0]['FECHA'];
$tbk_codigo_autorizacion = $result[0]['TBK_CODIGO_AUTORIZACION'];
$tbk_final_numero_tarjeta = $result[0]['TBK_FINAL_NUMERO_TARJETA'];
$tbk_numero_cuotas = $result[0]['TBK_NUMERO_CUOTAS'];
$tbk_tipo_pago = $result[0]['TBK_TIPO_PAGO'];
$tbk_orden_compra = $result[0]['TBK_ORDEN_COMPRA'];
$orden_compra = $tbk_rut."_".$tbk_monto_pesos."_".$tbk_cod_wp_transaccion;

$data ="APROBADO|$tbk_codigo_autorizacion|$tbk_fecha_transaccion|$tbk_final_numero_tarjeta|$tbk_numero_cuotas|$tbk_tipo_pago|$orden_compra";

$url  = '../webpay_cupon.php?data='.$data;
header ('Location: '.$url);
?>