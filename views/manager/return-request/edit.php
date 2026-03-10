<?php

use app\forms\ReturnRequest\EditReturnRequestForm;
use yii\bootstrap5\ActiveForm;

/** @var EditReturnRequestForm $formModel */
/** @var array<string, mixed> $request */

$this->title = 'Редактировать возврат';
?>

<section>
    <h2>Редактировать возврат #<?= htmlspecialchars((string) ($request['id'] ?? '')) ?></h2>
    <hr>
    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'action' => ['/return-request/' . (int) $request['id'] . '/update'],
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>
    <div class="row g-3">
        <div class="col-12 col-md-4">
            <?= $form->field($formModel, 'phone')->textInput(['placeholder' => '+7...']) ?>
        </div>
        <div class="col-12 col-md-4">
            <?= $form->field($formModel, 'photoOne')->fileInput([
                'accept' => 'image/*',
                'id' => 'return-edit-photo-one',
            ]) ?>
            <?php if (!empty($request['photo_one'])): ?>
                <div class="border rounded p-2 bg-light mt-2 text-center">
                    <img id="return-edit-preview-one-img" src="/<?= htmlspecialchars((string) $request['photo_one']) ?>" alt="Фото 1" style="max-width: 100%; border-radius: 8px;">
                </div>
            <?php else: ?>
                <div class="text-muted mt-2">Нет фото</div>
            <?php endif; ?>
        </div>
        <div class="col-12 col-md-4">
            <?= $form->field($formModel, 'qrCode')->fileInput([
                'accept' => 'image/*',
                'id' => 'return-edit-qr-code',
            ]) ?>
            <div class="border rounded p-2 bg-light mt-2 text-center" id="return-edit-qr-preview" style="<?= empty($request['qr_code_file']) ? 'display:none;' : '' ?>">
                <img id="return-edit-preview-qr-img" src="<?= !empty($request['qr_code_file']) ? '/' . htmlspecialchars((string) $request['qr_code_file']) : '' ?>" alt="QR код" style="max-width: 100%; border-radius: 8px;">
            </div>
            <?php if (empty($request['qr_code_file'])): ?>
                <div class="text-muted mt-2" id="return-edit-qr-empty">Нет QR кода</div>
            <?php endif; ?>
        </div>
    </div>
    <div class="mt-3 d-flex gap-2">
        <button class="btn btn-outline-success" type="submit">Сохранить</button>
        <a class="btn btn-outline-secondary" href="/return-request/<?= (int) $request['id'] ?>">К просмотру</a>
        <a class="btn btn-outline-secondary" href="/return-request">К списку</a>
    </div>
    <?php ActiveForm::end(); ?>
</section>

<script>
    (function () {
        function bindPreview(inputId, imgId, previewId, emptyId) {
            const input = document.getElementById(inputId);
            const img = document.getElementById(imgId);
            const initialSrc = img ? img.getAttribute('src') : '';
            const preview = previewId ? document.getElementById(previewId) : null;
            const empty = emptyId ? document.getElementById(emptyId) : null;
            if (!input || !img) {
                return;
            }
            input.addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (!file) {
                    if (preview) {
                        preview.style.display = initialSrc ? '' : 'none';
                    }
                    if (empty) {
                        empty.style.display = initialSrc ? 'none' : '';
                    }
                    if (initialSrc) {
                        img.src = initialSrc;
                    } else {
                        img.removeAttribute('src');
                    }
                    return;
                }
                if (preview) {
                    preview.style.display = '';
                }
                if (empty) {
                    empty.style.display = 'none';
                }
                const reader = new FileReader();
                reader.onload = function (e) {
                    img.src = e.target && e.target.result ? e.target.result : '';
                };
                reader.readAsDataURL(file);
            });
        }

        bindPreview('return-edit-photo-one', 'return-edit-preview-one-img', null, null);
        bindPreview('return-edit-qr-code', 'return-edit-preview-qr-img', 'return-edit-qr-preview', 'return-edit-qr-empty');
    })();
</script>
