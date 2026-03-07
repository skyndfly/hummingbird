<?php

use app\forms\Address\CreateAddressForm;
use yii\bootstrap5\ActiveForm;

/** @var CreateAddressForm $formModel */
/** @var array<int, string> $companies */

$this->title = 'Адреса';
?>

<section>
    <h2>Адреса</h2>
    <hr>
    <h4>Добавить</h4>
    <?php $form = ActiveForm::begin([
        'action' => ['/owner-address/store'],
        'method' => 'post',
    ]); ?>

    <?= $form->field($formModel, 'companyId')->dropDownList($companies, ['prompt' => 'Выберите компанию']) ?>
    <?= $form->field($formModel, 'address') ?>
    <button class="btn btn-outline-success" type="submit">Добавить</button>
    <?php ActiveForm::end(); ?>
</section>
