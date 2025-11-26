<?php

use app\filters\User\ManagerFilter;

/** @var string $grid */
/** @var ManagerFilter $filterModel */

$this->title = 'Менеджеры';
?>

<section>
    <h2>Менеджеры</h2>
    <a href="/operations-on-manager/create" class="btn btn-outline-success mb-2">Добавить менеджера</a>
    <a href="/operations-on-manager/create-point" class="btn btn-outline-success mb-2">Добавить пункт выдачи</a>

    <?= $grid ?>
    <?= $this->render('_search', ['model' => $filterModel]) ?>

</section>