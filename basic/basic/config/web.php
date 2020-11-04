<?php

//$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'defaultRoute' => '/site/index',


    'name' => 'Бобра Доставка',

    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [


        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'zrAb17HyoQrYi4Hzoo5x6qMR1BLIF3TK',
            // отключаем ибо POST API
            'enableCsrfValidation' => false,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'v1/site/index',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
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
        'db' => $db,


        'urlManager' => [

            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                //'<action:\w+>' => 'site/<action>',

                //http://basic-yii2.loc/profile/xxxxxx
                //'<controller:(profile)>/<id:\w+>' => '<controller>/index',

                //http://basic-yii2.loc/ajax/profile-post/1111
                //['pattern' => '/ajax/profile-post/<id:\w+>', 'route' => 'ajax/profile-post'],
                //'<controller:(profile)>/<id:\w+>' => '<controller>/index',
                //'<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>?id=<id>',
                //'<controller:\w+>/<id:\w+>' => '<controller>/<action>',
            ],
        ],

    ],

    'modules' => [
        'v1' => [
            'class' => 'app\modules\v1\Module',
            'defaultRoute' => 'cities'
        ],

        'v2' => [
            'class' => 'app\modules\v2\Module',
            'defaultRoute' => 'cities'
        ],

    ],


    //'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
