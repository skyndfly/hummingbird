<?php

/** @var yii\web\View $this */

/** @var CreateCodeForm $formModel */

use app\forms\Code\CreateCodeForm;
use yii\bootstrap5\ActiveForm;


$this->title = 'КолибриCRM';
?>
<section>
    <h2>Все кода</h2>
    <?php $form = ActiveForm::begin([
            'method' => 'post',
            'action' => ['manager/add-code/store'],
    ]); ?>
    <div class="row">
        <div class="col"><?= $form->field($formModel, 'code')->textInput() ?></div>
        <div class="col"><?= $form->field($formModel, 'price')->input('number') ?></div>
    </div>
    <div class="row">
        <div class="col"><?= $form->field($formModel, 'place')->textInput() ?></div>
        <div class="col"><?= $form->field($formModel, 'comment')->textInput() ?></div>
    </div>
    <button class="btn btn-outline-success" type="submit">Добавить</button>
    <?php $form = ActiveForm::end(); ?>

    <h2 class="mt-5">Список кодов</h2>
    <?php if (isset($grid)): ?>
        <?= $grid ?>
    <?php endif; ?>
</section>
