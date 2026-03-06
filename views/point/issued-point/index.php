<?php

use app\services\Address\dto\AddressDto;

/** @var AddressDto[] $addresses */
/** @var array<int, array{await:int, pending:int}> $counts */

$this->title = 'Пункты выдачи';
?>

<section>
    <h2>Пункты выдачи</h2>
    <hr>
    <?php if (empty($addresses)): ?>
        Нет доступных адресов.
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($addresses as $address): ?>
                <?php
                    $awaitCount = $counts[$address->id]['await'] ?? 0;
                    $pendingCount = $counts[$address->id]['pending'] ?? 0;
                ?>
                <a class="list-group-item list-group-item-action" href="/issued-point/address/<?= $address->id ?>">
                    <strong><?= $address->companyName ?? 'Компания' ?></strong>
                    <span class="text-muted">— <?= $address->address ?></span>
                    <?php if ($awaitCount > 0): ?>
                        <span class="badge text-bg-success ms-2">Ожидают: <?= $awaitCount ?></span>
                    <?php endif; ?>
                    <?php if ($pendingCount > 0): ?>
                        <span class="badge text-bg-dark ms-1">Отложено: <?= $pendingCount ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
