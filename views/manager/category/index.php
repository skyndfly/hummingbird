<?php

use app\forms\Category\CreateCategoryForm;


/** @var CreateCategoryForm $formModel */


?>

<section>
    <h2>Места хранения</h2>
    <hr>
    <h4>Добавить</h4>
    <?= $this->render(view:'_create_form', params: ['formModel' => $formModel]) ?>
    <hr>
</section>
