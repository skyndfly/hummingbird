<?php

/** @var array<string, mixed> $request */
/** @var array<string, string> $statusLabels */

$this->title = 'Заявка на возврат';

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
    <h2>Заявка на возврат #<?= htmlspecialchars((string) ($request['id'] ?? '')) ?></h2>
    <hr>

    <div class="mb-3">
        <a class="btn btn-outline-secondary" href="/return-request">К списку</a>
        <a class="btn btn-outline-primary" href="/return-request/<?= (int) $request['id'] ?>/edit">Редактировать</a>
        <?php if (($request['status'] ?? '') === \app\repositories\ReturnRequest\enums\ReturnRequestStatusEnum::ACCEPTED->value): ?>
            <form method="post" action="/return-request/<?= (int) $request['id'] ?>/road" class="d-inline">
                <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                <button class="btn btn-outline-warning" type="submit">Отгрузить</button>
            </form>
        <?php endif; ?>
        <?php if (($request['status'] ?? '') === \app\repositories\ReturnRequest\enums\ReturnRequestStatusEnum::ROAD->value): ?>
            <form method="post" action="/return-request/<?= (int) $request['id'] ?>/delivered" class="d-inline">
                <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                <button class="btn btn-outline-success" type="submit">Доставлен на пункт</button>
            </form>
        <?php endif; ?>
        <?php if (
            ($request['status'] ?? '') === \app\repositories\ReturnRequest\enums\ReturnRequestStatusEnum::CANCELED->value
            && (Yii::$app->user->can('owner') || Yii::$app->user->can('point'))
        ): ?>
            <form method="post" action="/return-request/<?= (int) $request['id'] ?>/returning" class="d-inline">
                <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                <button class="btn btn-outline-warning" type="submit">Товар едет обратно</button>
            </form>
        <?php endif; ?>
        <?php if (
            ($request['status'] ?? '') === \app\repositories\ReturnRequest\enums\ReturnRequestStatusEnum::RETURNING->value
            && (Yii::$app->user->can('owner') || Yii::$app->user->can('point'))
        ): ?>
            <form method="post" action="/return-request/<?= (int) $request['id'] ?>/accepted" class="d-inline">
                <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                <button class="btn btn-outline-info" type="submit">Принят в 108к</button>
            </form>
        <?php endif; ?>
        <?php if (
            ($request['status'] ?? '') === \app\repositories\ReturnRequest\enums\ReturnRequestStatusEnum::ACCEPTED_RETURN->value
            && (Yii::$app->user->can('owner') || Yii::$app->user->can('point'))
        ): ?>
            <form method="post" action="/return-request/<?= (int) $request['id'] ?>/return-client" class="d-inline">
                <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                <button class="btn btn-outline-success" type="submit">Вернули клиенту</button>
            </form>
        <?php endif; ?>
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
            <div class="text-muted">Статус</div>
            <span class="badge <?= status_badge_class((string) ($request['status'] ?? '')) ?>">
                <?= htmlspecialchars((string) ($statusLabels[$request['status']] ?? $request['status'])) ?>
            </span>
        </div>
    </div>

    <?php if (!empty($request['cancel_reason'])): ?>
        <div class="mt-3 p-3 rounded" style="background:#f8d7da;color:#842029;">
            <strong>Причина отмены:</strong>
            <?= htmlspecialchars((string) $request['cancel_reason']) ?>
        </div>
    <?php endif; ?>

    <div class="row g-3 mt-2">
        <div class="col-12 col-md-6">
            <div class="text-muted mb-1">Фото 1</div>
            <?php if (!empty($request['photo_one'])): ?>
                <a href="/<?= htmlspecialchars((string) $request['photo_one']) ?>" target="_blank">
                    <img src="/<?= htmlspecialchars((string) $request['photo_one']) ?>" alt="Фото 1" style="max-width: 100%; border-radius: 8px;">
                </a>
            <?php else: ?>
                <div class="text-muted">Нет</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-3 mt-2">
        <div class="col-12 col-md-6">
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

    <div class="row g-3 mt-2">
        <div class="col-12 col-md-4">
            <div class="text-muted">Тип</div>
            <div class="fw-bold"><?= htmlspecialchars((string) ($request['return_type'] ?? '')) ?></div>
        </div>
        <div class="col-12 col-md-4">
            <div class="text-muted">Создана</div>
            <div class="fw-bold"><?= htmlspecialchars((string) ($request['created_at'] ?? '')) ?></div>
        </div>
        <div class="col-12 col-md-4">
            <div class="text-muted">Обновлена</div>
            <div class="fw-bold"><?= htmlspecialchars((string) ($request['updated_at'] ?? '')) ?></div>
        </div>
    </div>
</section>
