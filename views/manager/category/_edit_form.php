<?php

use app\forms\Category\CreateCategoryForm;
use yii\bootstrap5\ActiveForm;

/** @var CreateCategoryForm $formModel */
?>

<?php $form = ActiveForm::begin([
        'action' => ['/category/update'],
        'method' => 'post',
]); ?>

<?= $form->field($formModel, 'id')->hiddenInput()->label(false) ?>
<?= $form->field($formModel, 'name') ?>
<button class="btn btn-outline-success" type="submit">Добавить</button>
<?php $form = ActiveForm::end(); ?>
