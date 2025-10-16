
<?php

use app\forms\Code\IssuedCodeForm;
use app\repositories\Code\dto\CodeDto;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var CodeDto $model */
/** @var IssuedCodeForm $formModel */
?>

<strong>Код:</strong> <?= $model->code ?><br>
<strong>Место хранения:</strong> <?= $model->category->name ?><br>
<strong>Количество:</strong> <?= $model->quantity ?>
<?php $form = ActiveForm::begin([
    'action' => ['manager/code/issued'],
    'method' => 'post',
]); ?>

<?= $form->field($formModel, 'id')->hiddenInput(['value' => $model->id])->label(false) ?>
<?= $form->field($formModel, 'code')->hiddenInput(['value' => $model->code])->label(false) ?>
<?= $form->field($formModel, 'comment')->textInput(['value' => $model->comment]) ?>
<?= $form->field($formModel, 'status')->radioList($formModel->getStatusOptions(), [
        'item' => function($index, $label, $name, $checked, $value) {
            $id = "status-{$value}";
            $checked = $checked ? 'checked' : '';
            return "
            <div class='form-check'>
                <input class='form-check-input' type='radio' name='{$name}' id='{$id}' value='{$value}' {$checked}>
                <label class='form-check-label' for='{$id}'>{$label}</label>
            </div>
        ";
        }
]) ?>

<div class="form-group mt-3">
    <?= Html::submitButton('Выдать', ['class' => 'btn btn-primary']) ?>
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
</div>

<?php ActiveForm::end(); ?>