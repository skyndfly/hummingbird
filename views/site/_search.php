<?php

use app\filters\User\ManagerFilter;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ManagerFilter */

$form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
]); ?>
    <hr>
    <h4>Фильтр</h4>
    <div class="manager-search">
        <?= $form->field($model, 'username')->textInput(['placeholder' => 'Логин']) ?>
        <?= $form->field($model, 'fio')->textInput(['placeholder' => 'ФИО']) ?>
        <div class="form-group">
            <?= Html::submitButton('Искать', ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton('Сбросить', ['class' => 'btn btn-default']) ?>
        </div>
    </div>

<?php ActiveForm::end(); ?>