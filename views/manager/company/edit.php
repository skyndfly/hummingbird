<?php

use app\forms\Company\EditCompanyForm;
use yii\bootstrap5\ActiveForm;

/** @var EditCompanyForm $formModel */
/** @var string $grid */
?>
<section>
    <h2>Службы доставки</h2>
    <hr>
    <h4>Редактировать - <?= $formModel->name ?></h4>
    <?php $form = ActiveForm::begin([
            'action' => ['/company/update'],
            'method' => 'post',
    ]); ?>

    <?= $form->field($formModel, 'id')->hiddenInput()->label(false) ?>
    <?= $form->field($formModel, 'name') ?>
    <?= $form->field($formModel, 'botKey') ?>
    <button class="btn btn-outline-success" type="submit">Добавить</button>
    <?php $form = ActiveForm::end(); ?>
</section>
