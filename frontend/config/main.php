<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager'   => [
            'enablePrettyUrl'     => true,
            'showScriptName'      => false,
            'enableStrictParsing' => true,
            'rules'               => [
                '/'                                                            => 'site/index',
                'sitemap.xml'                                                  => 'site/sitemap',
                'user/<account:[\w-]+>'                                        => 'account/view',
                'list/<alias:[\w-]+>'                                          => 'list/view',
                'lists/<action:[\w-]+>'                                        => 'list/<action>',
                'lists/<action:[\w-]+>/<id:[\d+]>'                             => 'list/<action>',
                'POST vote/add'                                                => 'vote/add',
                'vote/<action:[\w-]+>/<entity:[\w-]+>/<id:[\d+]>/<vote:[\d+]>' => 'vote/<action>',
                '<controller:[\w-]+>/<action:[\w-]+>/<id:[\d+]>'               => '<controller>/<action>',
                '<controller:[\w-]+>/<action:[\w-]+>'                          => '<controller>/<action>',
            ],
        ],
    ],
    'params' => $params,
];
