<?php

namespace segic\cps;

use yii\web\AssetBundle;
define('sourceAssetpath', __DIR__ . '/assets');

class CpsAsset extends AssetBundle
{
    public $sourcePath = sourceAssetpath;
    public $css = [
        'css/ticket.css',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
    ];
}
