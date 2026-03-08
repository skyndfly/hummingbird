<?php

use app\forms\ReturnRequest\CreateReturnRequestForm;
use yii\bootstrap5\ActiveForm;

/** @var CreateReturnRequestForm $formModel */

$this->title = 'Новая заявка на возврат';
?>

<section>
    <h2>Новая заявка на возврат</h2>
    <hr>
    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'action' => ['/return-request/store'],
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>
    <div class="row g-3">
        <div class="col-12 col-md-4">
            <?= $form->field($formModel, 'phone')->textInput(['placeholder' => '+7...']) ?>
        </div>
        <div class="col-12 col-md-4">
            <?= $form->field($formModel, 'returnType')->dropDownList([
                'wb' => 'Вайлдберис',
                'ozon' => 'Озон',
            ]) ?>
        </div>
        <div class="col-12 col-md-4">
            <?= $form->field($formModel, 'photoOne')->fileInput([
                'accept' => 'image/*',
                'id' => 'return-photo-one',
            ]) ?>
            <div class="border rounded p-2 bg-light mt-2 text-center" id="return-preview-one" style="display: none;">
                <img id="return-preview-one-img" alt="Предпросмотр фото 1" style="max-width: 100%; border-radius: 8px;">
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="return-preview-one-clear">Очистить</button>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <?= $form->field($formModel, 'photoTwo')->fileInput([
                'accept' => 'image/*',
                'id' => 'return-photo-two',
            ]) ?>
            <div class="border rounded p-2 bg-light mt-2 text-center" id="return-preview-two" style="display: none;">
                <img id="return-preview-two-img" alt="Предпросмотр фото 2" style="max-width: 100%; border-radius: 8px;">
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="return-preview-two-clear">Очистить</button>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-3 d-flex gap-2">
        <button class="btn btn-outline-success" type="submit">Создать заявку</button>
        <a class="btn btn-outline-secondary" href="/return-request">К списку</a>
    </div>
    <?php ActiveForm::end(); ?>
</section>

<script>
    (function () {
        function bindPreview(inputId, previewId, imgId, clearId) {
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            const img = document.getElementById(imgId);
            const clearBtn = document.getElementById(clearId);

            if (!input || !preview || !img || !clearBtn) {
                return;
            }

            input.addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (!file) {
                    preview.style.display = 'none';
                    img.removeAttribute('src');
                    return;
                }
                const reader = new FileReader();
                reader.onload = function (e) {
                    img.src = e.target && e.target.result ? e.target.result : '';
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            });

            clearBtn.addEventListener('click', function () {
                input.value = '';
                preview.style.display = 'none';
                img.removeAttribute('src');
            });
        }

        bindPreview('return-photo-one', 'return-preview-one', 'return-preview-one-img', 'return-preview-one-clear');
        bindPreview('return-photo-two', 'return-preview-two', 'return-preview-two-img', 'return-preview-two-clear');
    })();
</script>
