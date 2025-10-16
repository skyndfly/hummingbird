<?php

use app\forms\Code\CreateCodeForm;
use app\repositories\Category\dto\CategoryDto;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var CreateCodeForm $formModel */
/** @var CategoryDto[] $categories */
$this->title = 'КолибриCRM';
?>
<section>
    <h2>Добавить код</h2>
    <hr>
    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'action' => ['manager/add-code/store'],
    ]); ?>
    <div class="row">
        <div class="col-12 col-md-3"><?= $form->field($formModel, 'code')->textInput() ?></div>
        <div class="col-12 col-md-3"><?= $form->field($formModel, 'price')->input('number') ?></div>
    </div>
    <div class="row">
        <div class="col-12 col-md-3">
            <?= $form->field($formModel, 'categoryId')->dropDownList(
                $categories,
                ['prompt' => 'Выберите категорию...']
            ) ?>
        </div>
        <div class="col-12 col-md-3"><?= $form->field($formModel, 'quantity')->input('number') ?></div>
        <div class="col-12 col-md-3"><?= $form->field($formModel, 'comment')->textInput() ?></div>
    </div>
    <button class="btn btn-outline-success" type="submit">Добавить</button>
    <?php $form = ActiveForm::end(); ?>

    <hr>
    <?php if (isset($grid)): ?>
        <?= $grid ?>
    <?php endif; ?>

</section>
