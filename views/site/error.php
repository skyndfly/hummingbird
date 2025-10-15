<?php

/** @var yii\web\View $this */
/** @var string $name */
/** @var string $message */
/** @var Exception $exception */

use yii\helpers\Html;

$this->title = 'Произошла ошибка';
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        Извините, произошла ошибка. Сфотографируйте или скопируйте текст ошибки.
    </p>
    <p>
        Не забудьте рассказать об этом Артуру.
    </p>

</div>
