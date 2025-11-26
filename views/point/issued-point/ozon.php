<?php

use app\forms\UploadedCode\IssuedCodeForm;
use app\services\UploadCode\dto\UploadedCodeDto;
use app\services\UploadCode\enums\UploadedCodeCompanyKeyEnum;
use app\services\UploadCode\enums\UploadedCodeStatusEnum;
use yii\bootstrap5\ActiveForm;

/** @var ?UploadedCodeDto $code */
/** @var IssuedCodeForm $formModel */

?>

<section>
    <h2>Пункт выдачи товаров - <?= UploadedCodeCompanyKeyEnum::OZON->label() ?></h2>
    <hr>
    <?php if ($code === null): ?>
        На сегодня все коды выданы 💪
    <?php else: ?>
        <div>
            Дата отправки: <strong><?= new DateTimeImmutable($code->createdAt)->format('d.m.Y H:i') ?></strong>
        </div>
        <div class="d-flex align-items-center justify-content-around mb-3">
            <div>
                <?php $form = ActiveForm::begin([
                        'method' => 'post',
                        'action' => ['/issued-point/issued'],
                ]); ?>
                <?= $form->field($formModel, 'id')->hiddenInput()->label(false) ?>
                <?= $form->field($formModel, 'status')->hiddenInput(['value' => UploadedCodeStatusEnum::ISSUED->value])->label(false) ?>
                <?= $form->field($formModel, 'chatId')->hiddenInput()->label(false) ?>
                <button type="submit" class="btn btn-success">
                    <?= UploadedCodeStatusEnum::ISSUED->label() ?>
                </button>
                <?php ActiveForm::end(); ?>
            </div>

            <div>
                <?php $form = ActiveForm::begin([
                        'method' => 'post',
                        'action' => ['/issued-point/issued'],
                ]); ?>
                <?= $form->field($formModel, 'id')->hiddenInput()->label(false) ?>
                <?= $form->field($formModel, 'status')->hiddenInput(['value' => UploadedCodeStatusEnum::NOT_PAID->value])->label(false) ?>
                <?= $form->field($formModel, 'chatId')->hiddenInput()->label(false) ?>
                <button type="submit" class="btn btn-warning">
                    <?= UploadedCodeStatusEnum::NOT_PAID->label() ?>
                </button>
                <?php ActiveForm::end(); ?>
            </div>

            <div>
                <?php $form = ActiveForm::begin([
                        'method' => 'post',
                        'action' => ['/issued-point/issued'],
                ]); ?>
                <?= $form->field($formModel, 'id')->hiddenInput()->label(false) ?>
                <?= $form->field($formModel, 'status')->hiddenInput(['value' => UploadedCodeStatusEnum::OUTDATED->value])->label(false) ?>
                <?= $form->field($formModel, 'chatId')->hiddenInput()->label(false) ?>
                <button type="submit" class="btn btn-danger">
                    <?= UploadedCodeStatusEnum::OUTDATED->label() ?>
                </button>
                <?php ActiveForm::end(); ?>
            </div>

        </div>
        <div class="d-flex justify-content-center">
            <img src="/<?= $code->fileName ?>" class="w-100">
        </div>
    <?php endif; ?>
</section>
