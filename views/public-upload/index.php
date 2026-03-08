<?php

use app\services\Address\dto\AddressDto;
use app\services\Company\dto\CompanyDto;
use yii\helpers\Html;

/** @var CompanyDto[] $companies */
/** @var AddressDto[] $addresses */

$addressMap = [];
foreach ($addresses as $address) {
    $addressMap[$address->companyId][] = [
        'id' => $address->id,
        'address' => $address->address,
    ];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отправка кода | КолибриCRM</title>
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
    body{
        margin: 0;
    }
    .public-upload {
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

    .public-upload .container {
        max-width: 520px;
        width: 100%;
    }

    .public-upload .card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: var(--shadow);
    }

    .public-upload .logo {
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

    .public-upload h1 {
        font-size: 26px;
        font-weight: 700;
        margin-bottom: 10px;
        text-align: center;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .public-upload .subtitle {
        color: var(--text-light);
        margin-bottom: 25px;
        font-size: 16px;
        text-align: center;
    }

    .public-upload .check-link {
        display: block;
        text-align: center;
        margin: 10px 0 25px;
        padding: 12px 16px;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        color: var(--primary);
        border: 2px dashed var(--primary);
        background: #eef2ff;
    }

    .public-upload .top-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-bottom: 10px;
    }

    .public-upload .login-link {
        font-size: 13px;
        color: var(--text-light);
        text-decoration: none;
        border: 1px solid var(--border);
        padding: 6px 10px;
        border-radius: 999px;
        background: var(--secondary);
    }

    .public-upload .login-link:hover {
        color: var(--text);
        border-color: var(--primary);
    }

    .public-upload .form-group {
        margin-bottom: 20px;
    }

    .public-upload label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: var(--text);
    }

    .public-upload select,
    .public-upload input[type="file"],
    .public-upload input[type="text"] {
        width: 100%;
        padding: 12px 14px;
        border: 2px solid var(--border);
        border-radius: 12px;
        font-size: 15px;
        background: var(--secondary);
        transition: all 0.3s ease;
    }

    .public-upload select:focus,
    .public-upload input[type="file"]:focus,
    .public-upload input[type="text"]:focus {
        outline: none;
        border-color: var(--primary);
        background: white;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .public-upload .btn {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        border: none;
        padding: 14px 24px;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
    }

    .public-upload .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.4);
    }

    .public-upload .preview {
        display: none;
        margin-top: 12px;
        border: 2px dashed var(--border);
        border-radius: 12px;
        padding: 10px;
        background: var(--secondary);
        text-align: center;
        position: relative;
    }

    .public-upload .preview img {
        max-width: 100%;
        border-radius: 10px;
    }

    .public-upload .preview .clear-btn {
        position: absolute;
        top: 6px;
        right: 6px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: none;
        background: #fff;
        color: var(--text);
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        cursor: pointer;
        font-size: 18px;
        line-height: 28px;
        padding: 0;
    }

    .public-upload .flash {
        padding: 12px 14px;
        border-radius: 12px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .public-upload .flash-success {
        background: #dcfce7;
        color: #166534;
    }

    .public-upload .flash-error {
        background: #fee2e2;
        color: #991b1b;
    }

    @media (max-width: 480px) {
        .public-upload .card {
            padding: 30px 20px;
        }
    }
    </style>
</head>
<body>
    <div class="public-upload">
        <div class="container">
            <div class="card">
                <div class="top-actions">
                    <?php if (!Yii::$app->user->isGuest && Yii::$app->user->can('owner')): ?>
                        <a class="login-link" href="/issued-point">Назад</a>
                    <?php endif; ?>
                    <a class="login-link" href="/site/login">Войти</a>
                </div>
                <div class="logo">K</div>
                <h1>Отправка кода</h1>
                <p class="subtitle">Выберите пункт выдачи и загрузите изображение кода</p>
                <a class="check-link" href="/public-check">Проверить статус по номеру телефона</a>
                <a class="check-link" href="/public-return">Проверить возврат по номеру заявки</a>

                <?php if (Yii::$app->session->hasFlash('success')): ?>
                    <div class="flash flash-success">
                        <?= Html::encode(Yii::$app->session->getFlash('success')) ?>
                    </div>
                <?php endif; ?>
                <?php if (Yii::$app->session->hasFlash('error')): ?>
                    <div class="flash flash-error">
                        <?= Html::encode(Yii::$app->session->getFlash('error')) ?>
                    </div>
                <?php endif; ?>

                <form id="publicUploadForm" action="/public-upload/store" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">

                    <div class="form-group">
                        <label for="companySelect">Служба доставки</label>
                        <select id="companySelect" name="PublicUploadForm[companyId]" required>
                            <option value="">Выберите компанию</option>
                            <?php foreach ($companies as $company): ?>
                                <option value="<?= $company->id ?>"><?= Html::encode($company->name ?? '') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="addressSelect">Адрес пункта</label>
                        <select id="addressSelect" name="PublicUploadForm[addressId]" required disabled>
                            <option value="">Сначала выберите компанию</option>
                        </select>
                    </div>

                <div class="form-group">
                    <label for="codeImage">Изображение кода</label>
                    <input id="codeImage" name="PublicUploadForm[image]" type="file" accept="image/*" required>
                    <div class="preview" id="imagePreview">
                        <button class="clear-btn" type="button" id="clearImageBtn" aria-label="Сбросить изображение">×</button>
                        <img id="previewImg" alt="Предпросмотр">
                    </div>
                </div>

                <div class="form-group">
                    <label for="phoneInput">Номер телефона</label>
                    <input id="phoneInput" name="PublicUploadForm[phone]" type="text" placeholder="+7..." required>
                </div>

                <button type="submit" class="btn">Отправить код</button>
            </form>
            </div>
        </div>
    </div>

<script>
    const addressMap = <?= json_encode($addressMap, JSON_UNESCAPED_UNICODE) ?>;
    const companySelect = document.getElementById('companySelect');
    const addressSelect = document.getElementById('addressSelect');
    const codeImage = document.getElementById('codeImage');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const clearImageBtn = document.getElementById('clearImageBtn');

    function resetAddresses() {
        addressSelect.innerHTML = '<option value="">Сначала выберите компанию</option>';
        addressSelect.disabled = true;
    }

    companySelect.addEventListener('change', function () {
        const companyId = this.value;
        if (!companyId || !addressMap[companyId]) {
            resetAddresses();
            return;
        }

        addressSelect.disabled = false;
        addressSelect.innerHTML = '<option value="">Выберите адрес</option>';
        addressMap[companyId].forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.address;
            addressSelect.appendChild(option);
        });
    });

    if (!companySelect.value) {
        resetAddresses();
    }

    codeImage.addEventListener('change', function () {
        const file = this.files && this.files[0];
        if (!file) {
            imagePreview.style.display = 'none';
            previewImg.src = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function (e) {
            previewImg.src = e.target.result;
            imagePreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    });

    clearImageBtn.addEventListener('click', function () {
        codeImage.value = '';
        imagePreview.style.display = 'none';
        previewImg.src = '';
    });
</script>
</body>
</html>
