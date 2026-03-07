<?php

use app\forms\BotSettings\BotSettingsForm;
use yii\bootstrap5\ActiveForm;

/** @var BotSettingsForm $formModel */

$this->title = 'Настройки бота';
?>

<section>
    <h2>Настройки бота</h2>
    <hr>
    <p class="text-muted">Укажите час, после которого прием кодов закрыт. Формат: целый час (например, 12).</p>
    <?php $form = ActiveForm::begin([
        'action' => ['/owner-bot-settings/update'],
        'method' => 'post',
    ]); ?>

    <?= $form->field($formModel, 'cutoffHour')->input('number', [
        'min' => 0,
        'max' => 23,
        'step' => 1,
    ])->hint('Например: 16 означает закрытие с 16:00 до 23:59') ?>

    <button class="btn btn-outline-success" type="submit">Сохранить</button>
    <?php ActiveForm::end(); ?>
</section>
