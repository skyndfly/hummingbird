<?php
/** @var CreateManagerForm $formModel */

use app\forms\User\CreateManagerForm;
use yii\bootstrap5\ActiveForm;
use yii\widgets\MaskedInput;

?>

<section>
    <h2>Добавить менеджера по продажам</h2>
    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'action' => ['operations-on-manager/create'],
    ]); ?>
    <?= $form->field($formModel, 'username')->textInput() ?>
    <?= $form->field($formModel, 'password')->textInput() ?>
    <?= $form->field($formModel, 'first_name')->textInput() ?>
    <?= $form->field($formModel, 'name')->textInput() ?>
    <?= $form->field($formModel, 'last_name')->textInput() ?>
    <?= $form->field($formModel, 'birth_day')->input('date') ?>
    <?= $form->field($formModel, 'status')->hiddenInput(['value' => 'point'])->label(false) ?>
    <?= $form->field($formModel, 'number_phone')->widget(MaskedInput::class, [
        'mask' => '+7999-999-9999',
    ]) ?>
    <button class="btn btn-outline-success" type="submit">Добавить</button>
    <?php $form = ActiveForm::end(); ?>
</section>
