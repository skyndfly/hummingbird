<?php


use app\forms\Code\IssuedCodeForm;
use app\repositories\Code\dto\CodeDto;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var CodeDto $model */
/** @var IssuedCodeForm $formModel */

?>

    <div>
        <div class="row">
            <div class="col-4">
                <strong class="text-body-tertiary">Код:</strong> <?= $model->code ?>
            </div>
            <div class="col-4 ">
                <strong class="text-body-tertiary">Количество:</strong> <?= $model->quantity ?>
            </div>
            <div class="col-4 ">
                <strong class="text-body-tertiary">Место хранения: <?= $model->category->name ?></strong>
            </div>
        </div>


    </div>
    <hr>
    <div>
        <?php $form = ActiveForm::begin([
                'action' => ['manager/code/change-status'],
                'method' => 'post',
        ]); ?>

        <?= $form->field($formModel, 'id[]')->hiddenInput(['value' => $model->id])->label(false) ?>
        <?= $form->field($formModel, 'code')->hiddenInput(['value' => $model->code])->label(false) ?>
        <?= $form->field($formModel, 'status')->radioList(
                $formModel->getStatusOptionsForManager(),
                [
                        'item' => function ($index, $label, $name, $checked, $value) {
                            $id = "status-{$value}";
                            return "
            <div class='form-check mb-2'>
                <input class='form-check-input' type='radio' name='{$name}' id='{$id}' value='{$value}' " . ($checked ? 'checked' : '') . ">
                <label class='form-check-label fw-semibold' for='{$id}'>{$label}</label>
            </div>
            ";
                        },
                        'encode' => false,
                ]
        ) ?>
        <hr>
        <div class="form-group mt-3">
            <?= Html::submitButton('Изменить статус', ['class' => 'btn btn-primary']) ?>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
        </div>
    </div>


<?php ActiveForm::end(); ?>