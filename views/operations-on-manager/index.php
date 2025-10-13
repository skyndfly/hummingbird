<?php

use app\filters\User\ManagerFilter;

/** @var string $grid */
/** @var ManagerFilter $filterModel */

$this->title = 'Менеджеры по продажам';
?>

<section>
    <h2>Менеджеры по продажам</h2>
    <a href="/operations-on-manager/create" class="btn btn-outline-success mb-2">Добавить менеджера</a>

    <?= $grid ?>
    <?= $this->render('_search', ['model' => $filterModel]) ?>

</section>