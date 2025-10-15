<?php


use app\filters\Code\CodeFilter;
use app\forms\Code\CreateCodeForm;
use app\repositories\Category\dto\CategoryDto;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var CodeFilter $filterModel */
/** @var CreateCodeForm $formModel */
/** @var CategoryDto[] $categories */
$this->title = 'КолибриCRM';
?>
<section>
    <h2>Все кода</h2>
    <hr>
    <h4>Добавить код</h4>
    <?php $form = ActiveForm::begin([
            'method' => 'post',
            'action' => ['manager/add-code/store'],
    ]); ?>
    <div class="row">
        <div class="col"><?= $form->field($formModel, 'code')->textInput() ?></div>
        <div class="col"><?= $form->field($formModel, 'price')->input('number') ?></div>
    </div>
    <div class="row">
        <div class="col">
            <?= $form->field($formModel, 'categoryId')->dropDownList(
                    $categories,
                    ['prompt' => 'Выберите категорию...']
            ) ?>
        </div>
        <div class="col"><?= $form->field($formModel, 'quantity')->input('number') ?></div>
        <div class="col"><?= $form->field($formModel, 'comment')->textInput() ?></div>
    </div>
    <button class="btn btn-outline-success" type="submit">Добавить</button>
    <?php $form = ActiveForm::end(); ?>

    <?php if (isset($filterModel)): ?>
    <?= $this->render(view: '_search', params: ['filterModel' => $filterModel, 'categories' => $categories]); ?>
    <?php endif; ?>
    <hr>
    <h2 class="mt-5">Список кодов</h2>
    <?php if (isset($grid)): ?>
        <?= $grid ?>
    <?php endif; ?>

</section>
