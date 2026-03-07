<?php

use app\forms\User\ChangePasswordForm;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var ChangePasswordForm $model */

$this->title = 'Смена пароля';
?>

<section>
    <h2>Смена пароля</h2>
    <hr>
    <?php $form = ActiveForm::begin([
        'action' => ['/site/change-password'],
        'method' => 'post',
    ]); ?>

    <?= $form->field($model, 'oldPassword')->passwordInput() ?>
    <?= $form->field($model, 'newPassword')->passwordInput() ?>
    <?= $form->field($model, 'newPasswordRepeat')->passwordInput() ?>

    <button class="btn btn-outline-success" type="submit">Сменить пароль</button>
    <?php ActiveForm::end(); ?>
</section>
