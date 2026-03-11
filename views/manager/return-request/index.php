<?php

/** @var array<int, array<string, mixed>> $requests */
/** @var array<string, string> $statusLabels */
/** @var string|null $number */
/** @var string|null $phone */
/** @var string|null $status */

$this->title = 'Возвраты';

function status_badge_class(string $status): string
{
    return match ($status) {
        'created' => 'bg-primary',
        'accepted' => 'bg-info',
        'road' => 'bg-warning',
        'delivered' => 'bg-warning',
        'qr_uploaded' => 'bg-success',
        'returning' => 'bg-warning',
        'accepted_return' => 'bg-info',
        'return_client' => 'bg-success',
        'canceled' => 'bg-danger',
        default => 'bg-secondary',
    };
}
?>

<section>
    <h2>Заявки на возврат</h2>
    <hr>
    <div class="mb-3">
        <a class="btn btn-outline-success" href="/return-request/create">Новая заявка</a>
    </div>

    <form class="row g-3 mb-3" method="get" action="/return-request">
        <div class="col-12 col-md-3">
            <label class="form-label">Номер заявки (ID)</label>
            <input type="text" class="form-control" name="number" value="<?= htmlspecialchars((string) $number) ?>">
        </div>
        <div class="col-12 col-md-3">
            <label class="form-label">Телефон</label>
            <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars((string) $phone) ?>">
        </div>
        <div class="col-12 col-md-3">
            <label class="form-label">Статус</label>
            <select class="form-select" name="status">
                <option value="">Все статусы</option>
                <?php foreach ($statusLabels as $key => $label): ?>
                    <option value="<?= htmlspecialchars((string) $key) ?>" <?= $status === $key ? 'selected' : '' ?>>
                        <?= htmlspecialchars((string) $label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12 col-md-2 d-flex align-items-end">
            <button class="btn btn-outline-primary" type="submit">Найти</button>
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
                    <th>Фото 1</th>
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
                            <span class="badge <?= status_badge_class((string) ($row['status'] ?? '')) ?>">
                                <?= htmlspecialchars((string) ($statusLabels[$row['status']] ?? $row['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($row['photo_one'])): ?>
                                <a href="/<?= htmlspecialchars((string) $row['photo_one']) ?>" target="_blank">Открыть</a>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars((string) ($row['created_at'] ?? '')) ?></td>
                        <td class="text-nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="/return-request/<?= (int) $row['id'] ?>">Просмотр</a>
                            <a class="btn btn-sm btn-outline-secondary" href="/return-request/<?= (int) $row['id'] ?>/edit">Редактировать</a>
                            <form method="post" action="/return-request/<?= (int) $row['id'] ?>/delete" class="d-inline">
                                <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                                <button class="btn btn-sm btn-outline-danger" type="submit" onclick="return confirm('Удалить заявку?')">Удалить</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-muted">Заявок пока нет.</div>
    <?php endif; ?>
</section>
