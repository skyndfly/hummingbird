<?php

use app\services\Address\dto\AddressDto;
use app\services\UploadCode\enums\UploadedCodeStatusEnum;

/** @var AddressDto[] $addresses */
/** @var array<int, array<string, int>> $counts */
/** @var bool $isOwner */

$this->title = 'Пункты выдачи';
$statusClasses = [
    UploadedCodeStatusEnum::AWAIT->value => 'text-bg-success',
    UploadedCodeStatusEnum::PENDING->value => 'text-bg-dark',
    UploadedCodeStatusEnum::ISSUED->value => 'text-bg-primary',
    UploadedCodeStatusEnum::NOT_PAID->value => 'text-bg-danger',
    UploadedCodeStatusEnum::OUTDATED->value => 'text-bg-warning',
];
?>

<section>
    <h2>Пункты выдачи</h2>
    <hr>
    <?php if (empty($addresses)): ?>
        Нет доступных адресов.
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($addresses as $address): ?>
                <a class="list-group-item list-group-item-action" href="/issued-point/address/<?= $address->id ?>">
                    <strong><?= $address->companyName ?? 'Компания' ?></strong>
                    <span class="text-muted">— <?= $address->address ?></span>
                    <?php if ($isOwner): ?>
                        <?php foreach (UploadedCodeStatusEnum::cases() as $status): ?>
                            <?php $count = $counts[$address->id][$status->value] ?? 0; ?>
                            <span class="badge <?= $statusClasses[$status->value] ?? 'text-bg-secondary' ?> ms-1">
                                <?= $status->label() ?>: <?= $count ?>
                            </span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php $awaitCount = $counts[$address->id]['await'] ?? 0; ?>
                        <?php $pendingCount = $counts[$address->id]['pending'] ?? 0; ?>
                        <?php if ($awaitCount > 0): ?>
                            <span class="badge text-bg-success ms-2">Ожидают: <?= $awaitCount ?></span>
                        <?php endif; ?>
                        <?php if ($pendingCount > 0): ?>
                            <span class="badge text-bg-dark ms-1">Отложено: <?= $pendingCount ?></span>
                        <?php endif; ?>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
