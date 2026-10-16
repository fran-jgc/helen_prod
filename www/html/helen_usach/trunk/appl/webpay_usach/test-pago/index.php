<?php
require 'vendor/autoload.php';

use segic\cps\components\CommonCall;
use segic\cps\components\php\BasePagoWidget;

$pagoGetNet = new BasePagoWidget();
$pagoGetNet->init('http://webpay-server-test.usach.cl/api');
$pagoGetNet->type = CommonCall::TIPO_GETNET;
$pagoGetNet->storeId = '7'; // StoreID : Numero de tienda del servidor de pago SEGIC
$pagoGetNet->fieldId = "total"; // FieldID : ID del campo con el monto del pago.
$pagoGetNet->formType = '_self'; // FormType : frame : integrado a la pagina corriente en una iframe | _blank : nueva pagina para el pago | _self : adentro de la misma pagina
$pagoGetNet->size = 'small'; // Tamaño del botón : small | large

$pagoTransbank = new BasePagoWidget();
$pagoTransbank->init('http://webpay-server-test.usach.cl/api');
$pagoTransbank->type = CommonCall::TIPO_WEBPAY;
$pagoTransbank->storeId = '5'; // StoreID : Numero de tienda del servidor de pago SEGIC
$pagoTransbank->fieldId = "total"; // FieldID : ID del campo con el monto del pago.
$pagoTransbank->formType = '_self'; // FormType : frame : integrado a la pagina corriente en una iframe | _blank : nueva pagina para el pago | _self : adentro de la misma pagina
$pagoTransbank->size = 'small'; // Tamaño del botón : small | large

$pagoServipag = new BasePagoWidget();
$pagoServipag->init('http://webpay-server-test.usach.cl/api');
$pagoServipag->type = CommonCall::TIPO_SERVIPAG;
$pagoServipag->storeId = '2'; // StoreID : Numero de tienda del servidor de pago SEGIC
$pagoServipag->fieldId = "total"; // FieldID : ID del campo con el monto del pago.
$pagoServipag->formType = '_self'; // FormType : frame : integrado a la pagina corriente en una iframe | _blank : nueva pagina para el pago | _self : adentro de la misma pagina
$pagoServipag->size = 'small'; // Tamaño del botón : small | large

?>
<html>
    <head>
        <meta charset="UTF-8">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <title>Test de Pago Usach</title>
    </head>
    <body style="text-align: center;">
        <div style="width:800px; margin:0 auto;margin-top: 30px;">
            DEMO PAGO WEB
            <br />
            <br />
            <form>
                <input type="text" name="total" id="total" value="1000">
            </form>
            <br />
            <?= $pagoTransbank->generateButton(); ?>
            <?= $pagoGetNet->generateButton(); ?>
            <?= $pagoServipag->generateButton(); ?>
        </div>
    </body>
    <script>
        jQuery(function ($) {
            <?= $pagoTransbank->javascriptForm(); ?>
            <?= $pagoTransbank->javascriptClickButton(); ?>
            <?= $pagoGetNet->javascriptForm(); ?>
            <?= $pagoGetNet->javascriptClickButton(); ?>
            <?php echo $pagoServipag->javascriptForm('https://ww4.servipag.com/BotonPago/BotonPago/Pagar'); ?>
            <?= $pagoServipag->javascriptClickButton(); ?>
        });
    </script>
</html>
