CPS-client
==========

Configuración Composer
----------------------

    {
        "repositories": [{
            [...]
            {
                "type": "vcs",
                "url": "http://composer:2ZJ3axPvqU5Pp2N2pa89@gitlab.segic.cl/segic/cps-client.git"
            }
        ],

        "require": {
            [...]
            "segic/cps": "dev-master"
        },
    
        [...]
    }

Configuración Yii 2.x
=====================

Configuración WebApp (web.php)
-----------------------------


    'modules' => [
        [...]
        'cps' => [
            'class' => 'segic\cps\Module'
            'serverURL' => 'https://webpay-server.usach.cl/api', // Opcional : Define URL del servidor
            // 'modelSaveTransaction' => 'app\models\Pagosweb', // Opcional : Define model para graber transacciones en la BDD
        ],
    ]

Note : Podemos configurar un debug del package (opcional)

    'log' => [
        'targets' => [
            [...]
            [
                'class' => 'yii\log\FileTarget',
                'categories' => ['segic\cps\*'],
                'logFile' => '@runtime/logs/pago_api.log',
                'levels' => ['trace', 'error'],
            ],
        ],
    ]

Configuración controller
------------------------

Note : controller usado por el formulario de pago

    class XxxxController extends \yii\base\Controller {

        [...]

        public function behaviors() {
            return [
                [...]
                'PaySystemFilter' => [
                    'class' => \segic\cps\components\yii2\PaySystemFilter::className(),
                    'only' => ['formpay', 'initpay', 'resultpay', 'errorpay'],
                ],
            ];
        }

        [...]

        public function actions() {
            return [
                'formpay' => [
                    'class' => 'segic\cps\components\yii2\WebpayAction',
                ],
                'initpay' => [
                    'class' => 'segic\cps\components\yii2\WebpayAction',
                ],
                'errorpay' => [
                    'class' => 'segic\cps\components\yii2\WebpayAction',
                ]
            ];
        }
    }

Widget Usage
------------

Note 1 : Agregar el widget donde quiere ver un botón de pago.
   
    use segic\cps\components\yii2\WebPayWidget;

    <?=
        WebPayWidget::widget([
            'storeId' => 1, // StoreID : Numero de tienda del servidor de pago SEGIC
            'fieldId' => 'pago-field', // FieldID : ID del campo con el monto del pago.
            'formType' => 'frame', // FormType : frame : integrado a la pagina corriente en una iframe | blank : nueva pagina para el pago | _self : adentro de la misma pagina
            'frameName' => 'xxxx', // FrameName : Name del frame de la pagina que recibe la interfaz de pago (Funciona solo si FormType = frame, Opcional)
            'size' => 'small', // Tamaño del botón : small | large
        ]);
    ?>

Configuración Yii 1.x
=====================

Configuración WebApp (main.php)
-----------------------------

    'aliases' => [
        [...]
        'segic' => 'ven.segic',
        [...]
    ],

    'components' => [
        [...]
        'cps' => [
            'class' => 'segic.cps.CpsClient',
            'serverURL' => 'https://webpay-server.usach.cl/api', // Opcional : Define URL del servidor
            // 'modelSaveTransaction' => 'Pagosweb', // Opcional : Define model para graber transacciones en la BDD
        ],
    ]

Configuración controller
------------------------

Note : controller usado por el formulario de pago

    class XxxxController extends CController {

        [...]

        public function actions() {
            return [
                'initpay' => 'application.vendor.segic.cps.components.yii1.WebpayAction',
                'formpay' => 'application.vendor.segic.cps.components.yii1.WebpayAction',
                'errorpay' => 'application.vendor.segic.cps.components.yii1.WebpayAction'
            ];
        }
    }

Widget Usage
------------

Note 1 : Agregar el widget donde quiere ver un botón de pago.

    <?php
        $this->widget('application.vendor.segic.cps.components.yii1.WebPayWidget', [
            'storeId' => 1, // StoreID : Numero de tienda del servidor de pago SEGIC
            'fieldId' => 'monto_pago', // FieldID : ID del campo con el monto del pago.
            'formType' => 'blank', // FormType : frame : integrado a la pagina corriente en una iframe | blank : nueva pagina para el pago | _self : adentro de la misma pagina
            'frameName' => 'xxxx', // FrameName : Name del frame de la pagina que recibe la interfaz de pago (Funciona solo si FormType = frame, Opcional)
            'size' => 'large', // Tamaño del botón : small | large
        ]);
    ?>

Required Server
---------------

    sudo apt-get install php5-curl

function API
------------

### Init

URL prod : https://webpay-server.usach.cl/api/webpay/default/initialisation

Tipo : POST
Basic Authentication : user/password
Header : "Content-Type" : "application/xml"

Request : XML 
```
<cps>
    <storeId>1</storeId>
    <monto>10000</monto>
    <urlFinal>https://xxxx.usach.cl/es/admision/wizard/resultpay</urlFinal>
</cps>
```

Result : XML
```
<?xml version="1.0" encoding="UTF-8"?>
<response>
    <token>e4450582c727931e6745e75eb3f37f741cdabdbd3bac263e0900283fd62a48e1</token>
    <url>https://webpay3gint.transbank.cl/webpayserver/initTransaction</url>
    <idTransaction>306819</idTransaction>
    <monto>10000</monto>
</response>
```

> **Note:** El resultado del API permite contruir un formulario para empezar un pago WebPay.

Formulario : HTML
```
<form action="https://webpay3gint.transbank.cl/webpayserver/initTransaction" method="post">
 <input type="hidden" name="token_ws" value="e4450582c727931e6745e75eb3f37f741cdabdbd3bac263e0900283fd62a48e1">
 <input type="submit" value="pago">
</form>
```

### Result

URL : https://webpay-server.usach.cl/api/webpay/default/get-transaction-detail

Tipo : POST

Dato : XML (Eg: "```<cps><store_id>1</store_id><numTransaction>306199</numTransaction></cps>```")

Result : XML 
```
<?xml version="1.0" encoding="UTF-8"?>
<response>
 <detail>
  <transactionResultOutput>
    <accountingDate>0411</accountingDate>
    <buyOrder>306199</buyOrder>
    <cardDetail>
      <cardDetail>
        <cardNumber>6623</cardNumber>
        <cardExpirationDate>
        </cardExpirationDate>
      </cardDetail>
    </cardDetail>
    <detailOutput>
      <wsTransactionDetailOutput>
        <authorizationCode>1213</authorizationCode>
        <paymentTypeCode>VN</paymentTypeCode>
        <responseCode>0</responseCode>
        <sharesNumber>0</sharesNumber>
        <amount>2700</amount>
        <commerceCode>597020000541</commerceCode>
        <buyOrder>306199</buyOrder>
      </wsTransactionDetailOutput>
    </detailOutput>
    <sessionId>wp_58ecfee4eb6bb7.30980807</sessionId>
    <transactionDate>2017-04-11T12:06:16.853-04:00</transactionDate>
    <urlRedirection>https://webpay3gint.transbank.cl/filtroUnificado/voucher.cgi</urlRedirection>
    <VCI>TSY</VCI>
  </transactionResultOutput>
 </detail>
</response>
```