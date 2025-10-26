<?php


use app\filters\Code\CodeFilter;
use app\forms\Code\CreateCodeForm;
use app\repositories\Category\dto\CategoryDto;
use app\repositories\Code\enums\CodeStatusEnum;
use app\ui\gridTable\Code\AllCodeGridTable;

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
    <?php if (!empty($codes)): ?>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Место хранения</th>
                <th scope="col">Количество</th>
                <th scope="col">Статус</th>
                <th scope="col">Цена</th>
                <th scope="col">Комментарии</th>
                <th scope="col">Стоимость</th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($codes as $code => $data): ?>
                <tr>
                <td rowspan="<?= count($data['rows']) + 1 ?>"><?= $code ?></td>
                <?php $first = true; ?>
                <?php foreach ($data['rows'] as $row): ?>
                    <?php if (!$first): ?>
                        <tr>
                    <?php endif; ?>
                    <td><?= $row['category_name'] ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td>
                        <span class="badge <?= AllCodeGridTable::mapBadge(CodeStatusEnum::from($row['status'])) ?>">
                            <?= $row['status'] ?>
                        </span>
                    </td>
                    <td><?= $row['price'] / 100 ?> рублей</td>
                    <td><?= $row['comments'] ?></td>
                    <?php if ($first): ?>
                        <td rowspan="<?= count($data['rows']) + 1 ?>"></td>
                    <?php endif; ?>
                        </tr>
                    <?php $first = false; ?>
                <?php endforeach; ?>
                <tr>
                    <td colspan="6" style="text-align: right; background-color: rgba(1,133,0,0.62); color: #fff;"><strong>Итоговая
                            цена к оплате:</strong></td>
                    <td style="text-align: right; background-color: rgba(1,133,0,0.62); color: #fff;">
                        <strong><?= $data['unpaid_total'] / 100 ?> рублей</strong></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="6" ></td>
                    <td style="text-align: ">
                        <button class="btn btn-primary">Выдать</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <h1>Введите код для поиска</h1>
    <?php endif; ?>

</section>
