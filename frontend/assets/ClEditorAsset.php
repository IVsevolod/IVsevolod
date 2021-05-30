<?php

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;


class ClEditorAsset extends AssetBundle
{
    public $sourcePath = '@webroot/widgets/clEditor';
    public $baseUrl = '@web/widgets/clEditor';
    public $css = [
        'jquery.cleditor.css'
    ];
    public $js = [
        'jquery.cleditor.min.js',
    ];
    public $depends = [
        AppAsset::class,
        JqueryAsset::class,
    ];

    public function init()
    {
        parent::init();
    }
}
