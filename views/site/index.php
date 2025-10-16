<?php


use app\filters\Code\CodeFilter;
use app\forms\Code\CreateCodeForm;
use app\repositories\Category\dto\CategoryDto;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var CodeFilter $filterModel */
/** @var CreateCodeForm $formModel */
/** @var CategoryDto[] $categories */
$this->title = 'КолибриCRM';
?>
<section>
    <h2>Поиск кодов</h2>

    <?php if (isset($filterModel)): ?>
    <?= $this->render(view: '_search', params: ['filterModel' => $filterModel, 'categories' => $categories]); ?>
    <?php endif; ?>
    <hr>
    <?php if (isset($grid)): ?>
        <?= $grid ?>
    <?php endif; ?>

</section>
