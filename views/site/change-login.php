<?php

use app\forms\User\ChangeLoginForm;
use yii\bootstrap5\ActiveForm;

/** @var ChangeLoginForm $model */

$this->title = 'Смена логина';
?>

<section>
    <h2>Смена логина</h2>
    <hr>
    <?php $form = ActiveForm::begin([
        'action' => ['/site/change-login'],
        'method' => 'post',
    ]); ?>

    <?= $form->field($model, 'oldUsername')->textInput() ?>
    <?= $form->field($model, 'newUsername')->textInput() ?>
    <?= $form->field($model, 'newUsernameRepeat')->textInput() ?>

    <button class="btn btn-outline-success" type="submit">Сменить логин</button>
    <?php ActiveForm::end(); ?>
</section>
