<?php


use app\filters\Code\CodeFilter;
use app\forms\Code\CreateCodeForm;
use app\forms\Code\IssuedCodeForm;
use app\repositories\Category\dto\CategoryDto;
use app\repositories\Code\dto\GroupedCodeList;
use app\ui\gridTable\Code\AllCodeGridTable;

/** @var yii\web\View $this */
/** @var CodeFilter $filterModel */
/** @var GroupedCodeList[] $codes */
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
                <td rowspan="<?= count($data->getRows()) + 1 ?>"><?= $code ?></td>
                <?php $first = true; ?>
                <?php
                foreach ($data->getRows() as $row): ?>
                    <?php $issuedHtml = $this->render('_issue_form', [
                        'model' => $row,
                        'formModel' => new IssuedCodeForm(),
                        'codeList' => $data->getIds(),
                        'totalQuantity' => $data->getTotalQuantity(),
                        'storages' => $data->getStorages(),
                        'ids' => $data->getIds(),
                        'totalPrice' => $data->getUnpaidTotal()
                    ]) ?>
                    <?php if (!$first): ?>
                        <tr>
                    <?php endif; ?>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <?= $row->categoryName ?>
                            <a href="/code/create?CodeFilter[code]=<?= $row->code ?>" class="link-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-pencil" viewBox="0 0 16 16">
                                    <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                                </svg>
                            </a>
                        </div>

                    </td>
                    <td><?= $row->quantity ?></td>
                    <td>
                        <span class="badge <?= AllCodeGridTable::mapBadge($row->status) ?>">
                            <?= $row->status->value ?>
                        </span>
                    </td>
                    <td><?= $row->price / 100 ?> рублей</td>
                    <td><?= $row->comments ?></td>
                    <?php if ($first): ?>
                        <td rowspan="<?= count($data->getRows()) + 1 ?>"></td>
                    <?php endif; ?>
                    </tr>
                    <?php $first = false; ?>
                <?php endforeach; ?>
                <tr>
                    <td colspan="6" style="text-align: right; background-color: rgba(1,133,0,0.62); color: #fff;">
                        <strong>Итого к оплате:</strong></td>
                    <td style="text-align: right; background-color: rgba(1,133,0,0.62); color: #fff;">
                        <strong><?= $data->getUnpaidTotal()  ?> рублей</strong></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="6"></td>
                    <td style="text-align: ">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop-<?=$code ?>">
                            Выдать
                        </button>
                        <div class="modal fade" id="staticBackdrop-<?=$code ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="staticBackdropLabel-<?=$code ?>">Выдать</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <?= $issuedHtml; ?>
                                    </div>
                                </div>
                            </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <h1 class="h3"><?= !empty($searchText) ? $searchText : 'Введите код для поиска 🔎' ?></h1>
    <?php endif; ?>

</section>
