<?php

namespace frontend\assets\quest;

use yii\bootstrap\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;

class QuestAsset extends AssetBundle
{
    public $basePath = '@webroot/quest/front';
    public $baseUrl = '@web/quest/front';
    public $css = [
        'css/quest.css',
    ];
    public $js = [
        'js/quest.js',
    ];
    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class,
        JqueryAsset::class,
    ];
}
