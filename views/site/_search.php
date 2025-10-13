<?php

use app\filters\Code\CodeFilter;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $filterModel CodeFilter */

$form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]); ?>
    <hr>
    <h4>Фильтр</h4>
    <div class="manager-search">
        <div class="row">
            <div class="col"><?= $form->field($filterModel, 'code')->textInput() ?></div>
            <div class="col"><?= $form->field($filterModel, 'place')->textInput() ?></div>
            <div class="col"><?= $form->field($filterModel, 'date')->input('date') ?></div>
        </div>
        <div class="form-group">
            <?= Html::submitButton('Искать', ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton('Сбросить', ['class' => 'btn btn-default']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>