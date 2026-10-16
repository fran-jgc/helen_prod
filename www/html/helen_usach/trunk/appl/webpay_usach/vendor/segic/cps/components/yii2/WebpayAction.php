<?php

namespace segic\cps\components\yii2;

use Yii;
use yii\base\Action;
use yii\helpers\Url;
use yii\web\Response;
use segic\cps\components\yii2\CheckPayEvent;

class WebpayAction extends Action
{
    const EVENT_CHECKPAY = 'checkpay';

    /**
     * Runs the action.
     */
    public function run()
    {

        if (strpos($this->getUniqueId(), 'initpay') !== false) {

            $storeId = Yii::$app->request->post('storeId');
            $monto = Yii::$app->request->post('monto');
            $type = Yii::$app->request->post('type');
            $urlFinal = Url::to(['/' . $this->controller->getUniqueId() . '/resultpay'], (YII_DEBUG ? true : 'https'));

            $result = Yii::$app->getModule('cps')->initialisation($type, $monto, $storeId, $urlFinal);
            $result['user'] = Yii::$app->user->id;

            Yii::$app->session->set('currentPago', $result);
            Yii::$app->getModule('cps')->saveSessionAsDB($result);

            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->data = $result;
        }

        if (strpos($this->getUniqueId(), 'formpay') !== false) {

            $url = Yii::$app->request->get('url');
            $token = Yii::$app->session->get('currentPago')['token'];

            return $this->controller->render(Yii::$app->getModule('cps')->formView, ['urlWebPay' => $url, 'token' => $token]);
        }

        if (strpos($this->getUniqueId(), 'resultpay') !== false) {

            $result = [];
            $idTransaction = Yii::$app->request->get('idTransaction');

            if (!empty($idTransaction) && is_numeric($idTransaction)) {
                $result = Yii::$app->getModule('cps')->result($idTransaction);
            }

            if (!empty($result)) {
                $event = new CheckPayEvent;
                $event->result = $result;
                $event->idTransaction = $idTransaction;
                $this->trigger(self::EVENT_CHECKPAY, $event);
            }

            return $this->controller->render(Yii::$app->getModule('cps')->resultView, ['idTransaction' => $idTransaction, 'result' => $result]);
        }
    }

    function getIp()
    {

        $ip = $_SERVER['REMOTE_ADDR'];

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        return $ip;
    }
}
