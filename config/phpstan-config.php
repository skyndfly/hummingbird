<?php

declare(strict_types=1);

return [
    // PHPStan only: used by this extension for behavior property/method type inference
//    'behaviors' => [
//        app\models\User::class => [
//            app\behaviors\SoftDeleteBehavior::class,
//            yii\behaviors\TimestampBehavior::class,
//        ],
//    ],
    'components' => [
        'db' => [
            'class' => yii\db\Connection::class,
            'dsn' => 'sqlite::memory:',
        ],
        'user' => [
            'class' => yii\web\User::class,
            'identityClass' => app\auth\UserIdentity::class
        ],
        // Add your custom components here
    ],
];