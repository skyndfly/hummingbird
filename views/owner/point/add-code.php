<?php

use app\forms\UploadedCode\ManualUploadForm;
use app\services\UploadCode\enums\UploadedCodeCompanyKeyEnum;


$this->title = 'Добавить код WB';

/** @var ManualUploadForm $formModel */
/** @var UploadedCodeCompanyKeyEnum $companyKey */
?>
<section>
    <h2>Добавить код <?=$companyKey->label()?></h2>
    <hr>
   <?= $this->render('_create-form', [
           'formModel' => $formModel,
           'companyKey' => $companyKey
   ]) ?>
</section>
