<?php

use app\forms\UploadedCode\IssuedCodeForm;
use app\services\Address\dto\AddressDto;
use app\services\UploadCode\dto\UploadedCodeDto;
use app\services\UploadCode\enums\UploadedCodeStatusEnum;
use yii\bootstrap5\ActiveForm;

/** @var ?UploadedCodeDto $code */
/** @var IssuedCodeForm $formModel */
/** @var int $pendingCount */
/** @var AddressDto $address */

$this->title = 'Отложенные коды — ' . ($address->companyName ?? '') . ' — ' . $address->address;
?>

<section>

    <h2>Отложенные коды</h2>
    <div class="text-muted"><?= $address->companyName ?? 'Компания' ?> — <?= $address->address ?></div>

    <hr>
    Количество отложенных кодов: <strong><?= $pendingCount ?></strong> <br>
    <?php if ($code === null): ?>
        Пока нет отложенных кодов 💪
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
        <div class="row justify-content-center">
            <div class="col-12 col-md-4">
                <img class="w-100" src="/<?= $code->fileName ?>">
            </div>
        </div>
    <?php endif; ?>
</section>
