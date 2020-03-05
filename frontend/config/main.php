<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id'                  => 'app-frontend',
    'basePath'            => dirname(__DIR__),
    'bootstrap'           => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components'          => [
        'user'         => [
            'identityClass'   => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'log'          => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'  => 'yii\log\FileTarget',
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
                'library/view/<alias:[\w-]+>'                                  => 'library/view',
                'library/view/<alias:[\w-]+>/<page:[\d+]>'                     => 'library/view',
                'lists/<action:[\w-]+>'                                        => 'list/<action>',
                'lists/<action:[\w-]+>/<id:[\d+]>'                             => 'list/<action>',
                'library/index/<id:[\d+]>/<sort:[\w-]+>'                       => 'library/index',
                'library/index/<id:[\d+]>'                                     => 'library/index',
                'library/index/<sort:[\w-]+>'                                  => 'library/index',
                'POST vote/add'                                                => 'vote/add',
                'vote/<action:[\w-]+>/<entity:[\w-]+>/<id:[\d+]>/<vote:[\d+]>' => 'vote/<action>',
                '<controller:[\w-]+>/<action:[\w-]+>/<id:[\d+]>'               => '<controller>/<action>',
                '<controller:[\w-]+>/<action:[\w-]+>'                          => '<controller>/<action>',
                'games/<controller:[\w-]+>/<action:[\w-]+>/<alias:[\w-]+>'     => 'games/<controller>/<action>',
                'games/<controller:[\w-]+>/<action:[\w-]+>'                    => 'games/<controller>/<action>',
            ],
        ],
        'vkapi'        => [
            'class' => 'common\components\VkontakteComponent',
        ],
    ],
    'params'              => $params,
];
