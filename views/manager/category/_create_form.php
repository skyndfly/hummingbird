<?php
use app\forms\Category\CreateCategoryForm;
use yii\bootstrap5\ActiveForm;

/** @var CreateCategoryForm $formModel */
?>

<?php $form = ActiveForm::begin([
    'action' => ['category/store'],
    'method' => 'post',
]); ?>

<?= $form->field($formModel, 'name') ?>
<button class="btn btn-outline-success" type="submit">Добавить</button>
<?php $form = ActiveForm::end(); ?>
