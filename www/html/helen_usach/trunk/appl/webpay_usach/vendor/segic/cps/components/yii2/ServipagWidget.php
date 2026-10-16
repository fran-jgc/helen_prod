<?php

namespace segic\cps\components\yii2;

use Yii;
use yii\helpers\Url;
use segic\cps\components\CommonCall;

/**
 * Description of WebPayWidget
 *
 * @author gilles
 */
class ServipagWidget extends PagoWidget
{

    public function run()
    {
        $urlError = Url::to([Yii::$app->controller->id . '/errorpay'], true);
        $url = Url::to([$this->ajaxDefaultAction]);

        $this->generateButton(CommonCall::TIPO_SERVIPAG);
        $this->_view->registerJs($this->javascriptForm('https://www.servipag.com/BotonPago/BotonPago/Pagar'));
        $this->_view->registerJs($this->javascriptClickButton($url, $urlError));
    }
}
