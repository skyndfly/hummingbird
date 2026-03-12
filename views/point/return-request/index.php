<?php

/** @var array<int, array<string, mixed>> $requests */
/** @var string $title */
/** @var \yii\data\Pagination $pagination */
/** @var string|null $status */
/** @var int|null $id */
/** @var string|null $date */
/** @var array<string, string> $statusLabels */

use yii\widgets\LinkPager;

$this->title = $title;
?>

<section>
    <h2><?= htmlspecialchars($title) ?></h2>
    <hr>

    <form class="row g-2 align-items-end mb-3" method="get">
        <div class="col-sm-6 col-md-3">
            <label class="form-label" for="id-filter">Номер (ID)</label>
            <input
                class="form-control"
                id="id-filter"
                name="id"
                inputmode="numeric"
                pattern="\d*"
                value="<?= $id !== null ? htmlspecialchars((string) $id) : '' ?>"
            >
        </div>
        <div class="col-sm-6 col-md-3">
            <label class="form-label" for="date-filter">Дата</label>
            <input
                class="form-control"
                id="date-filter"
                name="date"
                type="date"
                value="<?= $date !== null ? htmlspecialchars($date) : '' ?>"
            >
        </div>
        <div class="col-sm-6 col-md-3">
            <label class="form-label" for="status-filter">Статус</label>
            <select class="form-select" id="status-filter" name="status">
                <option value="">Все</option>
                <?php foreach ($statusLabels as $key => $label): ?>
                    <option value="<?= htmlspecialchars($key) ?>" <?= $status === $key ? 'selected' : '' ?>>
                        <?= htmlspecialchars($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary" type="submit">Показать</button>
        </div>
    </form>

    <?php if (!empty($requests)): ?>
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead>
                <tr>
                    <th>Номер (ID)</th>
                    <th>Телефон</th>
                    <th>Тип</th>
                    <th>Статус</th>
                    <th>QR код</th>
                    <th>Создана</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($requests as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars((string) ($row['id'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string) ($row['phone'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string) ($row['return_type'] ?? '')) ?></td>
                        <td>
                            <?php
                                $status = (string) ($row['status'] ?? '');
                                $statusLabel = match ($status) {
                                    'delivered' => 'Доставлен на пункт',
                                    'qr_uploaded' => 'QR код загружен',
                                    'canceled' => 'отмена',
                                    default => $status,
                                };
                                $statusClass = match ($status) {
                                    'delivered' => 'bg-warning text-dark',
                                    'qr_uploaded' => 'bg-success',
                                    'canceled' => 'bg-danger',
                                    default => 'bg-secondary',
                                };
                            ?>
                            <span class="badge <?= $statusClass ?>">
                                <?= htmlspecialchars($statusLabel) ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($row['qr_code_file'])): ?>
                                <a href="/<?= htmlspecialchars((string) $row['qr_code_file']) ?>" target="_blank">Открыть</a>
                            <?php else: ?>
                                <span class="text-muted">Нет</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars((string) ($row['created_at'] ?? '')) ?></td>
                        <td class="text-nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="/point-returns/<?= (int) $row['id'] ?>">Просмотр</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-muted">Нет возвратов для отображения.</div>
    <?php endif; ?>

    <?php if ($pagination->pageCount > 1): ?>
        <div class="mt-3">
            <?= LinkPager::widget(['pagination' => $pagination]) ?>
        </div>
    <?php endif; ?>
</section>
