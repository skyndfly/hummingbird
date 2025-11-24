<?php

use app\forms\Category\CreateCategoryForm;

///** @var CreateCategoryForm $formModel */
/** @var string $grid */
?>

<section>
    <h2>Службы доставки</h2>
    <hr>
    <h4>Добавить</h4>
    <!--    --><?php //= $this->render(view: '_create_form', params: ['formModel' => $formModel]) ?>
    <hr>
    <h4>Список</h4>
    <?= $grid ?>
</section>
