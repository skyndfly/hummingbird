<?php

declare(strict_types=1);

use yii\console\Application;

// подгружаем Composer
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';
// создаём минимальное приложение (чтобы был Yii::$app)
new Application([
    'id' => 'phpstan',
    'basePath' => __DIR__,
]);
