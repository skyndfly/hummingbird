<?php

use app\forms\Address\EditAddressForm;
use yii\bootstrap5\ActiveForm;

/** @var EditAddressForm $formModel */
/** @var array<int, string> $companies */

$this->title = 'Адреса';
?>

<section>
    <h2>Адреса</h2>
    <hr>
    <h4>Редактировать</h4>
    <?php $form = ActiveForm::begin([
        'action' => ['/owner-address/update'],
        'method' => 'post',
    ]); ?>

    <?= $form->field($formModel, 'id')->hiddenInput()->label(false) ?>
    <?= $form->field($formModel, 'companyId')->dropDownList($companies, ['prompt' => 'Выберите компанию']) ?>
    <?= $form->field($formModel, 'address') ?>
    <button class="btn btn-outline-success" type="submit">Сохранить</button>
    <?php ActiveForm::end(); ?>
</section>
