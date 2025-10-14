<?php

use app\forms\Category\CreateCategoryForm;
use app\ui\gridTable\GridFactory;


/** @var CreateCategoryForm $formModel */
/** @var string $grid */


?>

<section>
    <h2>Места хранения</h2>
    <hr>
    <h4>Добавить</h4>
    <?= $this->render(view:'_create_form', params: ['formModel' => $formModel]) ?>
    <hr>
    <h4>Список</h4>
    <?= $grid ?>
</section>
