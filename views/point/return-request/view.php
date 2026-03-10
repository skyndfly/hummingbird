<?php

/** @var array<string, mixed> $request */

$this->title = 'Возврат (QR)';

function status_badge_class(string $status): string
{
    return match ($status) {
        'created' => 'bg-primary',
        'accepted' => 'bg-info',
        'qr_uploaded' => 'bg-success',
        'completed' => 'bg-success',
        'canceled' => 'bg-danger',
        default => 'bg-secondary',
    };
}
?>

<section>
    <h2>Возврат #<?= htmlspecialchars((string) ($request['id'] ?? '')) ?></h2>
    <hr>
    <div class="mb-3">
        <a class="btn btn-outline-secondary" href="/point-returns/wb">К списку WB</a>
        <a class="btn btn-outline-secondary" href="/point-returns/ozon">К списку OZON</a>
    </div>

    <div class="row g-3">
        <div class="col-12 col-md-4">
            <div class="text-muted">Номер (ID)</div>
            <div class="fw-bold"><?= htmlspecialchars((string) ($request['id'] ?? '')) ?></div>
        </div>
        <div class="col-12 col-md-4">
            <div class="text-muted">Телефон</div>
            <div class="fw-bold"><?= htmlspecialchars((string) ($request['phone'] ?? '')) ?></div>
        </div>
        <div class="col-12 col-md-4">
            <div class="text-muted">Создана</div>
            <div class="fw-bold"><?= htmlspecialchars((string) ($request['created_at'] ?? '')) ?></div>
        </div>
    </div>

    <div class="row g-3 mt-2">
        <div class="col-12 col-md-4">
            <div class="text-muted">Тип</div>
            <div class="fw-bold"><?= htmlspecialchars((string) ($request['return_type'] ?? '')) ?></div>
        </div>
    </div>

    <div class="row g-3 mt-2">
        <div class="col-12 col-md-4">
            <div class="text-muted">Статус</div>
            <span class="badge <?= status_badge_class((string) ($request['status'] ?? '')) ?>">
                <?php
                    $statusLabel = match ((string) ($request['status'] ?? '')) {
                        'created' => 'создана',
                        'accepted' => 'принято в 108к',
                        'qr_uploaded' => 'QR код загружен',
                        'completed' => 'выполнена',
                        'canceled' => 'отмена',
                        default => (string) ($request['status'] ?? ''),
                    };
                ?>
                <?= htmlspecialchars($statusLabel) ?>
            </span>
        </div>
    </div>

    <?php if (($request['status'] ?? '') === 'qr_uploaded'): ?>
        <div class="row g-3 mt-3">
            <div class="col-12 d-flex gap-2">
                <form method="post" action="/point-returns/<?= (int) $request['id'] ?>/complete">
                    <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                    <button class="btn btn-outline-success" type="submit">Выполнена</button>
                </form>
                <form method="post" action="/point-returns/<?= (int) $request['id'] ?>/cancel">
                    <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                    <button class="btn btn-outline-danger" type="submit" onclick="return confirm('Отменить возврат?')">Возврат не принят</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <div class="row g-3 mt-2">
        <div class="col-12 col-md-4">
            <div class="text-muted mb-1">Фото 1</div>
            <?php if (!empty($request['photo_one'])): ?>
                <a href="/<?= htmlspecialchars((string) $request['photo_one']) ?>" target="_blank">
                    <img src="/<?= htmlspecialchars((string) $request['photo_one']) ?>" alt="Фото 1" style="max-width: 100%; border-radius: 8px;">
                </a>
            <?php else: ?>
                <div class="text-muted">Нет</div>
            <?php endif; ?>
        </div>
        <div class="col-12 col-md-4">
            <div class="text-muted mb-1">QR код</div>
            <?php if (!empty($request['qr_code_file'])): ?>
                <a href="/<?= htmlspecialchars((string) $request['qr_code_file']) ?>" target="_blank">
                    <img src="/<?= htmlspecialchars((string) $request['qr_code_file']) ?>" alt="QR код" style="max-width: 100%; border-radius: 8px;">
                </a>
            <?php else: ?>
                <div class="text-muted">Нет</div>
            <?php endif; ?>
        </div>
    </div>
</section>
