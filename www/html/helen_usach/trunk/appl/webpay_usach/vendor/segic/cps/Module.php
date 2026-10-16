<?php

namespace segic\cps;

use yii;
use yii\helpers\VarDumper;
use segic\cps\components\CommonCall;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'vendor\segic\cps\controllers';
    public $formView = '@vendor/segic/cps/views/yii2/_webpay';
    public $resultView = '@vendor/segic/cps/views/yii2/_result';
    public $contactoMail = '';

    public $serverURL = 'https://webpay-server.usach.cl/api';
    public $slugInitWebpay = '/webpay/initialisation';
    public $slugInitServipag = '/servipag/initialisation';
    public $slugInitGetnet = '/getnet/initialisation';
    public $slugResult = '/default/get-transaction-detail';

    //webpay/default/get-transaction-detail

    public $modelSaveTransaction = '';
    private $_commonCall;


    public function init()
    {
        parent::init();

        $option = [
            "slugInitWebpay" => $this->slugInitWebpay,
            "slugInitServipag" => $this->slugInitServipag,
            "slugInitGetnet" => $this->slugInitGetnet,
            "slugResult" => $this->slugResult,
        ];

        $this->serverURL = (substr($this->serverURL, -1) == "/") ? substr($this->serverURL, 0, -1) : $this->serverURL;
        $this->_commonCall = new CommonCall($this->serverURL, $option);
    }

    /**
     * Call common function for save session to DB
     * @param type $result
     */
    public function saveSessionAsDB($result)
    {
        if (!empty($this->modelSaveTransaction)) {
            $this->_commonCall->saveSessionAsDB($this->modelSaveTransaction, $result);
        }
    }

    public function initialisation($tipo, $monto, $storeId, $urlFinal)
    {

        yii::info('Tipo Pago                                 : ' . VarDumper::dumpAsString($tipo), __METHOD__);
        yii::info('Parametro entrada initialisation monto    : ' . VarDumper::dumpAsString($monto), __METHOD__);
        yii::info('Parametro entrada initialisation storeId  : ' . VarDumper::dumpAsString($storeId), __METHOD__);
        yii::info('Parametro entrada initialisation urlFinal : ' . VarDumper::dumpAsString($urlFinal), __METHOD__);

        $error = null;

        if ($tipo == CommonCall::TIPO_SERVIPAG) {
            $data = $this->_commonCall->initialisationServipag($monto, $storeId, $urlFinal, $error);
        }
        if ($tipo == CommonCall::TIPO_WEBPAY) {
            $data = $this->_commonCall->initialisationWebPay($monto, $storeId, $urlFinal, $error);
        }
        if ($tipo == CommonCall::TIPO_GETNET) {
            $data = $this->_commonCall->initialisationGetNet($monto, $storeId, $urlFinal, $error);
        }

        if (!empty($error)) {
            yii::error('Error initialisation tipo ' . VarDumper::dumpAsString($tipo) . '  : ' . VarDumper::dumpAsString($error), __METHOD__);
        }

        return $data;
    }

    public function result($transactionId)
    {

        yii::info('Parametro entrada result transactionId : ' . VarDumper::dumpAsString($transactionId), __METHOD__);

        $error = null;
        $data = $this->_commonCall->result($transactionId, $error);

        if (!empty($error)) {
            yii::error('Error result WebPay : ' . VarDumper::dumpAsString($error), __METHOD__);
        } else {
            return $data;
        }
    }
}
