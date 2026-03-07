<?php

use app\forms\PublicCheckForm;
use app\services\UploadCode\dto\UploadedCodeDto;
use app\services\UploadCode\enums\UploadedCodeStatusEnum;
use yii\helpers\Html;

/** @var PublicCheckForm $formModel */
/** @var UploadedCodeDto[] $results */

function status_class(UploadedCodeStatusEnum $status): string
{
    return match ($status) {
        UploadedCodeStatusEnum::AWAIT => 'status-new',
        UploadedCodeStatusEnum::ISSUED => 'status-issued',
        UploadedCodeStatusEnum::PENDING => 'status-pending',
        UploadedCodeStatusEnum::NOT_PAID => 'status-pending',
        UploadedCodeStatusEnum::OUTDATED => 'status-not-found',
    };
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Проверка кода | КолибриCRM</title>
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

        .input-wrapper {
            position: relative;
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

        .status-issued {
            background: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-not-found {
            background: #fee2e2;
            color: #991b1b;
        }

        .code-info {
            text-align: left;
            margin-top: 15px;
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

        .error-message {
            color: var(--error);
            font-size: 14px;
            margin-top: 8px;
            display: none;
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
        <h1>Проверка кода</h1>
        <p class="subtitle">Введите номер телефона, который указывали при загрузке</p>
        <a class="back-link" href="/public-upload">Вернуться к загрузке кода</a>

        <form method="post">
            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
            <div class="form-group">
                <label for="phoneInput">Номер телефона</label>
                <div class="input-wrapper">
                    <input
                        type="text"
                        id="phoneInput"
                        name="PublicCheckForm[phone]"
                        placeholder="+7..."
                        value="<?= Html::encode($formModel->phone) ?>"
                        required
                    >
                </div>
            </div>

            <button type="submit" class="btn">Проверить</button>
        </form>

        <?php if ($formModel->hasErrors('phone')): ?>
            <div class="result status-not-found">
                <?= Html::encode($formModel->getFirstError('phone')) ?>
            </div>
        <?php elseif (Yii::$app->request->isPost): ?>
            <?php if (empty($results)): ?>
                <div class="result status-not-found">
                    Код по этому номеру телефона не найден
                </div>
            <?php else: ?>
                <?php foreach ($results as $result): ?>
                    <?php $status = UploadedCodeStatusEnum::from($result->status->value); ?>
                    <div class="result">
                        <div class="status-badge <?= status_class($status) ?>"><?= Html::encode($status->label()) ?></div>
                        <h3>Статус кода</h3>
                        <div class="code-info">
                            <div class="info-row">
                                <span>Служба доставки:</span>
                                <strong><?= Html::encode($result->companyName ?? $result->companyKey) ?></strong>
                            </div>
                            <div class="info-row">
                                <span>Пункт выдачи:</span>
                                <strong><?= Html::encode($result->address ?? 'Не указан') ?></strong>
                            </div>
                            <div class="info-row">
                                <span>Дата загрузки:</span>
                                <strong><?= Html::encode($result->createdAt) ?></strong>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
