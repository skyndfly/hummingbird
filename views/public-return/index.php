<?php

use app\forms\PublicReturnCheckForm;
use yii\helpers\Html;

/** @var PublicReturnCheckForm $formModel */
/** @var array<string, mixed>|null $request */
/** @var array<string, string> $statusLabels */
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Проверка возврата | КолибриCRM</title>
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #f8fafc;
            --text: #1e293b;
            --text-light: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --error: #ef4444;
            --border: #e2e8f0;
            --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            color: var(--text);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 480px;
            width: 100%;
        }

        .card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: var(--shadow);
            text-align: center;
        }

        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            font-weight: bold;
        }

        h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .subtitle {
            color: var(--text-light);
            margin-bottom: 30px;
            font-size: 16px;
        }

        .back-link {
            display: inline-block;
            margin: 10px 0 20px;
            padding: 10px 14px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            color: var(--primary);
            border: 2px dashed var(--primary);
            background: #eef2ff;
        }

        .form-group {
            margin-bottom: 25px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text);
        }

        input[type="text"] {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid var(--border);
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: var(--secondary);
        }

        input[type="text"]:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .btn {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.4);
        }

        .result {
            margin-top: 30px;
            padding: 25px;
            border-radius: 12px;
            background: var(--secondary);
            text-align: left;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .status-new {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .status-not-found {
            background: #fee2e2;
            color: #991b1b;
            text-align: center;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid var(--border);
        }

        .info-row:last-child {
            border-bottom: none;
        }

        @media (max-width: 480px) {
            .card {
                padding: 30px 20px;
            }

            h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="logo">K</div>
        <h1>Проверка возврата</h1>
        <p class="subtitle">Введите номер возврата, который вам сообщили</p>
        <a class="back-link" href="/public-upload">Вернуться к загрузке кода</a>

        <?php if (Yii::$app->session->hasFlash('success')): ?>
            <div class="result" style="background:#dcfce7;color:#166534;text-align:center;">
                <?= Html::encode(Yii::$app->session->getFlash('success')) ?>
            </div>
        <?php endif; ?>
        <?php if (Yii::$app->session->hasFlash('error')): ?>
            <div class="result status-not-found">
                <?= Html::encode(Yii::$app->session->getFlash('error')) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
            <div class="form-group">
                <label for="returnIdInput">Номер возврата</label>
                <input
                    type="text"
                    id="returnIdInput"
                    name="PublicReturnCheckForm[returnId]"
                    placeholder="Например 123"
                    value="<?= Html::encode($formModel->returnId) ?>"
                    required
                >
            </div>
            <div class="form-group">
                <label for="returnPhoneInput">Номер телефона</label>
                <input
                    type="text"
                    id="returnPhoneInput"
                    name="PublicReturnCheckForm[phone]"
                    placeholder="+7..."
                    value="<?= Html::encode($formModel->phone) ?>"
                    required
                >
            </div>

            <button type="submit" class="btn">Проверить</button>
        </form>

        <?php if ($formModel->hasErrors('returnId') || $formModel->hasErrors('phone')): ?>
            <div class="result status-not-found">
                <?= Html::encode($formModel->getFirstError('returnId') ?: $formModel->getFirstError('phone')) ?>
            </div>
        <?php elseif (Yii::$app->request->isPost): ?>
            <?php if (empty($request)): ?>
                <div class="result status-not-found">
                    Возврат с таким номером не найден
                </div>
            <?php else: ?>
                <div class="result" style="text-align:center;">
                    <div class="status-badge status-new">
                        <?= Html::encode($statusLabels[$request['status']] ?? $request['status']) ?>
                    </div>
                    <h3>Статус кода</h3>
                    <div style="text-align:left;margin-top:15px;">
                        <div class="info-row">
                            <span>Номер возврата:</span>
                            <strong><?= Html::encode((string) $request['id']) ?></strong>
                        </div>
                        <div class="info-row">
                            <span>Тип возврата:</span>
                            <strong>
                                <?php
                                    $typeLabel = match ((string) ($request['return_type'] ?? '')) {
                                        'wb' => 'WB',
                                        'ozon' => 'OZON',
                                        default => (string) ($request['return_type'] ?? ''),
                                    };
                                ?>
                                <?= Html::encode($typeLabel) ?>
                            </strong>
                        </div>
                        <div class="info-row">
                            <span>Дата создания:</span>
                            <strong><?= Html::encode((string) (new DateTime($request['created_at'])->format('d-m-Y') ?? '')) ?></strong>
                        </div>
                    </div>
                </div>

                <?php if (in_array((string) ($request['status'] ?? ''), ['accepted', 'created'], true)): ?>
                    <div class="result" style="margin-top: 16px;">
                        <h3 style="margin-bottom: 12px;">Загрузить QR код</h3>
                        <form method="post" action="/public-return/upload" enctype="multipart/form-data">
                            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                            <input type="hidden" name="returnId" value="<?= Html::encode((string) $request['id']) ?>">
                            <input type="hidden" name="phone" value="<?= Html::encode($formModel->phone) ?>">
                            <div class="form-group">
                                <label for="qrImageInput">Изображение QR кода</label>
                                <input type="file" id="qrImageInput" name="qrImage" accept="image/*" required>
                                <div class="preview" id="qrPreview" style="display:none;margin-top:12px;border:2px dashed var(--border);border-radius:12px;padding:10px;background:var(--secondary);position:relative;">
                                    <button type="button" id="qrClearBtn" aria-label="Сбросить изображение" style="position:absolute;top:6px;right:6px;width:28px;height:28px;border-radius:50%;border:none;background:#fff;color:var(--text);box-shadow:0 4px 10px rgba(0,0,0,0.15);cursor:pointer;font-size:18px;line-height:28px;padding:0;">×</button>
                                    <img id="qrPreviewImg" alt="Предпросмотр QR" style="max-width:100%;border-radius:10px;">
                                </div>
                            </div>
                            <button type="submit" class="btn" id="qrUploadBtn">Загрузить QR код</button>
                        </form>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<script>
    (function () {
        const input = document.getElementById('qrImageInput');
        const preview = document.getElementById('qrPreview');
        const img = document.getElementById('qrPreviewImg');
        const clearBtn = document.getElementById('qrClearBtn');

        const uploadBtn = document.getElementById('qrUploadBtn');
        const uploadForm = uploadBtn ? uploadBtn.closest('form') : null;

        if (!input || !preview || !img || !clearBtn) {
            return;
        }

        input.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) {
                preview.style.display = 'none';
                img.removeAttribute('src');
                return;
            }
            const reader = new FileReader();
            reader.onload = function (e) {
                img.src = e.target && e.target.result ? e.target.result : '';
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });

        clearBtn.addEventListener('click', function () {
            input.value = '';
            preview.style.display = 'none';
            img.removeAttribute('src');
        });

        if (uploadForm && uploadBtn) {
            uploadForm.addEventListener('submit', function () {
                uploadBtn.disabled = true;
                uploadBtn.textContent = 'Загрузка...';
                uploadBtn.setAttribute('aria-busy', 'true');
            });
        }
    })();
</script>
</body>
</html>
