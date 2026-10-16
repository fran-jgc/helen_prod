<?php
require 'vendor/autoload.php';
use segic\cps\components\CommonCall;

//$api = new CommonCall('https://webpay-server.usach.cl/api');
$api = new CommonCall('https://webpay-server.usach.cl/api');
$transactionId = filter_input(INPUT_GET, 'idTransaction');
$result = $api->result($transactionId, $error);

session_start();
$id_helen = $_SESSION['id_helen'];

$nombre_archivo = "confirmacion.txt";
	
$param = "entra con pago id=$id_helen";
/*
if($archivo = fopen($nombre_archivo, "a"))
{
	fwrite($archivo, $param. "\n");
	fclose($archivo);
}*/

if($result['success']==true){
    $estado = $result['estado'];
    $estado_transaccion = $result['detail']['details'][0]['responseCode'];
    if(($estado == 2)&&($estado_transaccion == 0)){ //aprobado
        $monto = $result['detail']['details'][0]['amount'];
        $voucher = $result['detail']['details'][0]['buyOrder'];
        $num_tarjeta = $result['detail']['cardNumber'];
        $cod_autorizacion = $result['detail']['details'][0]['authorizationCode'];
        $num_cuota = $result['detail']['details'][0]['installmentsNumber'];
        $estado = $result['detail']['details'][0]['responseCode'];
        $display_aprobado = "";
        $display_fracasado = "none";
        $estado_transaccion = 0;
    }
    else{
        $error_pago = $result['error'];
        $estado = $result['estado'];
        $display_aprobado = "none";
        $display_fracasado = "";
        $MENSAJE_TRANSACCION = "Su transacci&oacute;n no se ha podido efectuar con &eacute;xito, si el problema<br>persiste, comun&iacute;quese con su banco emisor";
        $estado_transaccion = 1;
    }
}
else{
    $error_pago = $result['error'];
    $estado = $result['estado'];
    $display_aprobado = "none";
    $display_fracasado = "";
    $MENSAJE_TRANSACCION = "Su transacci&oacute;n no se ha podido efectuar con &eacute;xito, si el problema<br>persiste, comun&iacute;quese con su banco emisor";
    $estado_transaccion = 1;
}


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-QGNNW6PP2C"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-QGNNW6PP2C');
</script>

<title></title>
</head>
<body OnLoad="window.document.forms['imput'].submit();">


<form action="respuesta_pago_cam.php" method="post" id="imput" TARGET="_self">
<p><input type="hidden" name="id_helen" size="60" value="<?php echo $id_helen?>" /></p>
<p><input type="hidden" name="monto" size="60" value="<?php echo $monto?>" /></p>
<p><input type="hidden" name="voucher" size="60" value="<?php echo $voucher?>" /></p>
<p><input type="hidden" name="num_tarjeta" size="60" value="<?php echo $num_tarjeta?>" /></p>
<p><input type="hidden" name="cod_autorizacion" size="60" value="<?php echo $cod_autorizacion?>" /></p>
<p><input type="hidden" name="num_cuota" size="60" value="<?php echo $num_cuota?>" /></p>
<p><input type="hidden" name="estado" size="60" value="<?php echo $estado?>" /></p>
<p><input type="hidden" name="estado_transaccion" size="60" value="<?php echo $estado_transaccion?>" /></p>

</form>

</body>
</html>