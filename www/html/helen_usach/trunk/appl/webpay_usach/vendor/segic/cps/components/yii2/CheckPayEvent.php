<?php

namespace segic\cps\components\yii2;

use yii\base\Event;

class CheckPayEvent extends Event
{
    public $result;
    public $idTransaction;
}
