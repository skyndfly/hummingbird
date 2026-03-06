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
/** @var int $allCount */
/** @var int $awaitCount */

$this->title = 'Пункт выдачи ' . ($address->companyName ?? '') . ' — ' . $address->address;
?>

<section>
    <h2>Пункт выдачи товаров</h2>
    <div class="text-muted"><?= $address->companyName ?? 'Компания' ?> — <?= $address->address ?></div>
    <hr>
    <?php if ($pendingCount > 0): ?>
        <p>
            Количество отложенных кодов: <strong><?= $pendingCount ?></strong> <br>
            <a href="/issued-point/address/<?= $address->id ?>/pending">Перейти к отложенным кодам</a>
        </p>
    <?php endif; ?>
    <?php if ($code === null): ?>
        <?php if ($pendingCount > 0): ?>
            <div class="alert alert-danger">
                Обратите внимание у вас есть отложенные кода.
            </div>
        <?php else: ?>
            На сегодня все коды выданы 💪
        <?php endif; ?>
    <?php else: ?>
        <div>
            <div class="">
                Дата отправки: <strong><?= new DateTimeImmutable($code->createdAt)->format('d.m.Y H:i') ?></strong>
            </div>
            <div class="">
                Всего добавленных кодов: <strong><?= $allCount ?></strong>
            </div>
            <div class="">
                Ожидает выдачи: <strong><?= $awaitCount ?></strong>
            </div>
        </div>

        <div style="margin-bottom: 100px" class="d-md-none"></div>

        <div class="d-flex align-items-center justify-content-around mb-3 flex-wrap">
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
                <?= $form->field($formModel, 'status')->hiddenInput(['value' => UploadedCodeStatusEnum::PENDING->value])->label(false) ?>
                <?= $form->field($formModel, 'chatId')->hiddenInput()->label(false) ?>
                <button type="submit" class="btn btn-dark">
                    <?= UploadedCodeStatusEnum::PENDING->label() ?>
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
