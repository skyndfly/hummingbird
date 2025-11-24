<?php

use app\filters\Code\CodeFilter;
use app\forms\Code\CreateCodeForm;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var CreateCodeForm $formModel */
/** @var array<int, string> $categories */
/** @var array<int, string> $companies */
/** @var CodeFilter $filterModel */

$this->title = 'КолибриCRM';

?>
<section>
    <h2>Добавить код</h2>
    <hr>
    <?php $form = ActiveForm::begin([
            'method' => 'post',
            'action' => ['code/update'],
    ]); ?>
    <?= $form->field($formModel, 'id')->hiddenInput()->label(false) ?>
    <?= $form->field($formModel, 'status')->hiddenInput()->label(false) ?>
    <div class="row">

        <div class="col-12 col-md-3"><?= $form->field($formModel, 'code')->textInput() ?></div>
        <div class="col-12 col-md-3"><?= $form->field($formModel, 'price')->input('number', ['value' => $formModel->price /100]) ?></div>

    </div>
    <?= $form->field($formModel, 'companyId')->radioList(
            $companies,
            [
                    'item' => function ($index, $label, $name, $checked, $value) {
                        return "
                <div class='col-6 mb-2'>
                    <div class='form-check'>
                        <input 
                            class='form-check-input' 
                            type='radio' 
                            name='{$name}' 
                            id='company{$value}' 
                            value='{$value}'
                            " . ($checked ? 'checked' : '') . "
                        >
                        <label class='form-check-label' for='company{$value}'>
                            {$label}
                        </label>
                    </div>
                </div>
            ";
                    },
                    'class' => 'row', // чтобы radioList стал row
            ]
    ) ?>
    <div class="row">
        <div class="col-12 col-md-3">
            <?= $form->field($formModel, 'categoryId')->dropDownList(
                    $categories,
                    ['prompt' => 'Выберите место хранения...']
            ) ?>
        </div>
        <div class="col-12 col-md-3"><?= $form->field($formModel, 'quantity')->input('number') ?></div>
        <div class="col-12 col-md-3"><?= $form->field($formModel, 'comment')->textarea() ?></div>
    </div>
    <button class="btn btn-outline-success" type="submit">Добавить</button>
    <?php $form = ActiveForm::end(); ?>



</section>
