<?php

/** @var array<string, mixed> $request */

$this->title = 'Возврат (QR)';

function status_badge_class(string $status): string
{
    return match ($status) {
        'created' => 'bg-primary',
        'accepted' => 'bg-info',
        'road' => 'bg-warning',
        'delivered' => 'bg-warning',
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
                        'road' => 'В пути',
                        'delivered' => 'Доставлен на пункт',
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
                <form method="post" action="/point-returns/<?= (int) $request['id'] ?>/delivered">
                    <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                    <button class="btn btn-outline-warning" type="submit">Обновить</button>
                </form>
                <form method="post" action="/point-returns/<?= (int) $request['id'] ?>/cancel" class="cancel-form">
                    <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                    <input type="hidden" name="cancelReason" value="">
                    <button class="btn btn-outline-danger" type="button" id="cancel-open-btn">Возврат не принят</button>
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

<script>
    (function () {
        const openBtn = document.getElementById('cancel-open-btn');
        const form = document.querySelector('.cancel-form');

        if (!openBtn || !form) {
            return;
        }

        const modal = document.createElement('div');
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        modal.style.position = 'fixed';
        modal.style.inset = '0';
        modal.style.background = 'rgba(0, 0, 0, 0.5)';
        modal.style.display = 'none';
        modal.style.alignItems = 'center';
        modal.style.justifyContent = 'center';
        modal.style.zIndex = '1050';

        modal.innerHTML = `
            <div style="background:#fff;border-radius:12px;max-width:420px;width:90%;padding:16px;box-shadow:0 10px 30px rgba(0,0,0,0.2);">
                <div style="font-weight:600;margin-bottom:8px;">Причина отмены</div>
                <input type="text" id="cancel-reason-input" class="form-control" placeholder="Введите причину">
                <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:12px;">
                    <button type="button" class="btn btn-outline-secondary" id="cancel-close-btn">Отмена</button>
                    <button type="button" class="btn btn-outline-danger" id="cancel-submit-btn">Подтвердить</button>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        const closeBtn = modal.querySelector('#cancel-close-btn');
        const submitBtn = modal.querySelector('#cancel-submit-btn');
        const input = modal.querySelector('#cancel-reason-input');

        function openModal() {
            modal.style.display = 'flex';
            if (input) {
                input.value = '';
                input.focus();
            }
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        openBtn.addEventListener('click', openModal);
        closeBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeModal();
            }
        });
        submitBtn.addEventListener('click', function () {
            const reason = input && input.value ? input.value.trim() : '';
            if (!reason) {
                if (input) {
                    input.focus();
                }
                return;
            }
            const hidden = form.querySelector('input[name="cancelReason"]');
            if (hidden) {
                hidden.value = reason;
            }
            form.submit();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && modal.style.display === 'flex') {
                closeModal();
            }
        });
    })();
</script>
