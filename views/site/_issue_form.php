<?php

use app\forms\Code\IssuedCodeForm;
use app\repositories\Code\dto\GroupedCodeDto;
use app\repositories\Code\dto\GroupedCodeList;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var GroupedCodeDto $model */
/** @var IssuedCodeForm $formModel */
/** @var GroupedCodeList $data */
/** @var int $totalQuantity */
/** @var int $totalPrice */
/** @var array $storages */
/** @var int[] $ids */

?>

<div>

    <strong class="text-body-tertiary">Код:</strong> <?= $model->code ?>
    <br>
    <strong class="text-body-tertiary">Общее количество:</strong> <?= $totalQuantity ?>
    <p class="text-body-tertiary fw-light">
        <span class="text-danger">*</span>
        В общее количество входят позиции только со статусом "Новый".
    </p>
    <strong class="text-body-tertiary">Места хранения:</strong>
    <ul>
        <?php foreach ($storages as $storage): ?>
            <li>
                <?= $storage ?>
            </li>
        <?php endforeach; ?>
    </ul>

</div>
<hr>
<div>
    <?php $form = ActiveForm::begin([
            'action' => ['manager/code/issued'],
            'method' => 'post',
    ]); ?>

    <?php foreach ($ids as $id): ?>
        <?= Html::hiddenInput('IssuedCodeForm[id][]', $id) ?>
    <?php endforeach; ?>
    <?= $form->field($formModel, 'code')->hiddenInput(['value' => $model->code])->label(false) ?>
    <?= $form->field($formModel, 'status')->radioList(
            $formModel->getStatusOptionsForIssuedForm(),
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
    <h3 class="text-success fw-normal">К оплате: <?= $totalPrice ?> ₽</h3>
    <hr>
    <div class="form-group mt-3">
        <?= Html::submitButton('Выдать', ['class' => 'btn btn-primary']) ?>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
    </div>
</div>

<p class="text-body-tertiary fw-light">
    <span class="text-danger">*</span>
    Если какая-то часть кода не найдена, вернитесь назад и переведите товар в статус "Не найден".
</p>

<?php ActiveForm::end(); ?>