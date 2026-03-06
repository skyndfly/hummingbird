<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use app\forms\UploadedCode\ManualUploadForm;
use app\services\UploadCode\enums\UploadedCodeCompanyKeyEnum;

/** @var ManualUploadForm $formModel */
/** @var UploadedCodeCompanyKeyEnum $companyKey */
?>
<?php $form = ActiveForm::begin([
        'action' => '/owner-point/add-code',
        'method' => 'post',
        'options' => ['enctype' => 'multipart/form-data']
]); ?>

<?= $form->field($formModel, 'note')->textInput(['maxlength' => true]) ?>

<?= $form->field($formModel, 'companyName')->hiddenInput(['value' => $companyKey->value])->label(false) ?>
<?= $form->field($formModel, 'addressId')->hiddenInput()->label(false) ?>

<?= $form->field($formModel, 'image')->fileInput() ?>

<div class="mt-2">
    <div id="imagePreview" style="display: none; border: 2px dashed #e2e8f0; border-radius: 12px; padding: 10px; background: #f8fafc; text-align: center;">
        <button type="button" id="clearImageBtn" style="position: absolute; right: 16px; margin-top: -6px; width: 28px; height: 28px; border-radius: 50%; border: none; background: #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.15); font-size: 18px; line-height: 28px; cursor: pointer;">×</button>
        <img id="previewImg" alt="Предпросмотр" style="max-width: 100%; border-radius: 10px;">
    </div>
</div>

<div class="form-group">
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>

<script>
    const imageInput = document.getElementById('manualuploadform-image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const clearImageBtn = document.getElementById('clearImageBtn');

    imageInput.addEventListener('change', function () {
        const file = this.files && this.files[0];
        if (!file) {
            imagePreview.style.display = 'none';
            previewImg.src = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function (e) {
            previewImg.src = e.target.result;
            imagePreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    });

    clearImageBtn.addEventListener('click', function () {
        imageInput.value = '';
        imagePreview.style.display = 'none';
        previewImg.src = '';
    });
</script>
