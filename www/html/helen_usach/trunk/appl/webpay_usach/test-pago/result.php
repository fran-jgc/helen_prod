<?php
require 'vendor/autoload.php';
use segic\cps\components\CommonCall;

$api = new CommonCall('http://webpay-server-test.usach.cl/api');
$transactionId = filter_input(INPUT_GET, 'idTransaction');
$result = $api->result($transactionId, $error);
?>

<html>
    <head>
        <meta charset="UTF-8">
        <title>Result</title>
    </head>
    <body style="text-align: center;">
        <?php if (!empty($error)): ?>
            <h2>¡Transacción Fracasada!</h2>
            <br>
            <p><b>N° de transacción fracasada <?= $transactionId ?></b></p>
        <?php else: ?>
            <h2>Resultado Pago :</h2>
            <br>
            <p><?php echo var_dump($result); ?></p>
        <?php endif; ?>
    </body>
</html>


