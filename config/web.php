<?php

use app\auth\UserIdentity;
use yii\symfonymailer\Mailer;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'xlIBwLqW8p4amvkcdOkZnzGw1BnO1fGi',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => UserIdentity::class,
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/app.log',
                    'maxFileSize' => 1024 * 10, // 10 MB
                    'maxLogFiles' => 10,
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'GET operations-on-manager' => 'operations-on-manager/index',
                'GET operations-on-manager/create' => 'operations-on-manager/create',
                'POST operations-on-manager/create' => 'operations-on-manager/store',

                'GET users' => 'users/index',
                'POST users/impersonate' => 'users/login-as-user',
                'POST site/return-to-user' => 'site/return-to-user',

                'GET code/create' => 'code/create',
                'GET manager/search' => 'code/search',
                'POST manager/add-code/store' => 'code/store',
                'POST manager/code/issued' => 'code/issued',

                'GET category' => 'category/index',
                'POST category/store' => 'category/store',
            ],
        ],

    ],
    'controllerMap' => [
        'operations-on-manager' => [
            'class' => app\controllers\Owner\OperationsOnManagerController::class,
        ],
        'users' => [
            'class' => app\controllers\Owner\UsersController::class,
        ],
        'code' => [
            'class' => app\controllers\Manager\CodeController::class,
        ],
        'category' => [
            'class' => app\controllers\Manager\CategoryController::class,
        ]
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
