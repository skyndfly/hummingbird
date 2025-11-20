<?php

use app\forms\Category\EditCategoryForm;

/** @var EditCategoryForm $formModel */
/** @var string $grid */
?>
<section>
    <h2>Места хранения</h2>
    <hr>
    <h4>Редактировать - <?= $formModel->name ?></h4>
    <?= $this->render(view: '_edit_form', params: ['formModel' => $formModel]) ?>
</section>
