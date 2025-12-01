<?php

use app\forms\UploadedCode\IssuedCodeForm;
use app\services\UploadCode\dto\UploadedCodeDto;
use app\services\UploadCode\enums\UploadedCodeCompanyKeyEnum;
use app\services\UploadCode\enums\UploadedCodeStatusEnum;
use yii\bootstrap5\ActiveForm;

/** @var ?UploadedCodeDto $code */
/** @var IssuedCodeForm $formModel */
/** @var int $pendingCount */

$this->title = 'Пункт выдачи ' . UploadedCodeCompanyKeyEnum::OZON->label();
?>

<section>
    <h2>Пункт выдачи товаров - <?= UploadedCodeCompanyKeyEnum::OZON->label() ?></h2>
    <hr>
    <?php if ($pendingCount > 0): ?>
        <p>
            Количество отложенных кодов: <strong><?=$pendingCount?></strong> <br>
            <a href="/issued-point/ozon/pending">Перейти к отложенным кодам</a>
        </p>
    <?php endif; ?>
    <?php if ($code === null): ?>
        <?php if ($pendingCount > 0): ?>
            <div class="alert alert-danger">
                Обратите внимание у вас есть отложенные кода
            </div>
        <?php else: ?>
            На сегодня все коды выданы 💪
        <?php endif; ?>
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
