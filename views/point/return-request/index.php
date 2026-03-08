<?php

/** @var array<int, array<string, mixed>> $requests */
/** @var string $title */

$this->title = $title;
?>

<section>
    <h2><?= htmlspecialchars($title) ?> с QR кодом за сегодня</h2>
    <hr>

    <?php if (!empty($requests)): ?>
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead>
                <tr>
                    <th>Номер (ID)</th>
                    <th>Телефон</th>
                    <th>Тип</th>
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
        <div class="text-muted">За сегодня возвратов с QR кодом нет.</div>
    <?php endif; ?>
</section>
