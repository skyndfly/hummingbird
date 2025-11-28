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

<?= $form->field($formModel, 'image')->fileInput() ?>

<div class="form-group">
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>
