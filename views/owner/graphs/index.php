<?php

use app\services\Address\dto\AddressDto;
use app\services\UploadCode\enums\UploadedCodeStatusEnum;
use yii\helpers\Html;

/** @var AddressDto[] $addresses */
/** @var int|null $addressId */
/** @var string $start */
/** @var string $end */
/** @var string[] $statusLabels */
/** @var int[] $statusValues */
/** @var int $totalCount */
/** @var array<string, int> $counts */

$this->title = 'Графики';
$statusClasses = [
    UploadedCodeStatusEnum::AWAIT->value => 'text-bg-success',
    UploadedCodeStatusEnum::PENDING->value => 'text-bg-dark',
    UploadedCodeStatusEnum::ISSUED->value => 'text-bg-primary',
    UploadedCodeStatusEnum::NOT_PAID->value => 'text-bg-danger',
    UploadedCodeStatusEnum::OUTDATED->value => 'text-bg-warning',
];
?>

<section>
    <h2>Графики</h2>
    <hr>

    <form class="row g-3 mb-4" method="get" action="/owner-graphs">
        <div class="col-md-6">
            <label class="form-label">Адрес</label>
            <select class="form-select" name="addressId" required>
                <?php foreach ($addresses as $address): ?>
                    <option value="<?= $address->id ?>" <?= $addressId === $address->id ? 'selected' : '' ?>>
                        <?= Html::encode(($address->companyName ?? 'Компания') . ' — ' . $address->address) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Дата с</label>
            <input type="date" class="form-control" name="start" value="<?= Html::encode($start) ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Дата по</label>
            <input type="date" class="form-control" name="end" value="<?= Html::encode($end) ?>" required>
        </div>
        <div class="col-12">
            <button class="btn btn-primary" type="submit">Показать</button>
        </div>
    </form>

    <?php if (empty($addresses)): ?>
        Нет доступных адресов.
    <?php else: ?>
        <div class="mb-3">
            <span class="badge text-bg-secondary">Всего: <?= $totalCount ?></span>
            <?php foreach (UploadedCodeStatusEnum::cases() as $status): ?>
                <?php $value = $counts[$status->value] ?? 0; ?>
                <span class="badge <?= $statusClasses[$status->value] ?? 'text-bg-secondary' ?> ms-1">
                    <?= $status->label() ?>: <?= $value ?>
                </span>
            <?php endforeach; ?>
        </div>
        <canvas id="statusChart" height="120"></canvas>
    <?php endif; ?>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    const labels = <?= json_encode($statusLabels, JSON_UNESCAPED_UNICODE) ?>;
    const values = <?= json_encode($statusValues, JSON_UNESCAPED_UNICODE) ?>;

    const ctx = document.getElementById('statusChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Количество',
                    data: values,
                    backgroundColor: [
                        '#22c55e',
                        '#111827',
                        '#3b82f6',
                        '#ef4444',
                        '#f59e0b'
                    ],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
</script>
