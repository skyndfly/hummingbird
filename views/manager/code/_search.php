<?php

use app\filters\Code\CodeFilter;
use app\repositories\Category\dto\CategoryDto;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $filterModel CodeFilter */
/** @var CategoryDto[] $categories */

$form = ActiveForm::begin([
    'action' => ['create'],
    'method' => 'get',
]); ?>
    <hr>
    <div class="manager-search">
        <div class="row">
            <div class="col-12 col-md-4"><?= $form->field($filterModel, 'code')->textInput() ?></div>
            <div class="col-12 col-md-4">
                <?= $form->field($filterModel, 'categoryId')->dropDownList(
                        $categories,
                        ['prompt' => 'Выберите категорию...']
                ) ?>
            </div>
            <div class="col-12 col-md-4"><?= $form->field($filterModel, 'date')->input('date') ?></div>
        </div>
        <div class="form-group">
            <?= Html::submitButton('Искать', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Очистить', ['create'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>