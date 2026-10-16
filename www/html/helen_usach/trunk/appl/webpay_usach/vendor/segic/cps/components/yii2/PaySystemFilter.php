<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace segic\cps\components\yii2;

use Yii;
use yii\base\ActionFilter;

/**
 * Description of PaySystemFilter
 *
 * @author gilles
 */
class PaySystemFilter extends ActionFilter {

    //put your code here

    public function beforeAction($action) {
        if (in_array(Yii::$app->controller->action->id, $this->only)) {
            Yii::$app->controller->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

}
