<?php

use app\services\UploadCode\enums\UploadedCodeCompanyKeyEnum;

?>

<div class="list-group-item">
    <div class="text-primary mb-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box2-fill"
             viewBox="0 0 16 16">
            <path d="M3.75 0a1 1 0 0 0-.8.4L.1 4.2a.5.5 0 0 0-.1.3V15a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V4.5a.5.5 0 0 0-.1-.3L13.05.4a1 1 0 0 0-.8-.4zM15 4.667V5H1v-.333L1.5 4h6V1h1v3h6z"/>
        </svg>
        Пункт выдачи товаров
    </div>
    <div class="ps-3">
        <div class="pt-2 pb-2 d-flex align-items-center gap-2 list-group-item border-start-0 border-end-0">
            <img class="" style="width: 16px;" src="/img/wb.svg" alt="">
            <a href="/issued-point/wb" class="d-flex align-items-center gap-1">
                <?= UploadedCodeCompanyKeyEnum::WB->label() ?>
            </a>
        </div>
        <div class="pt-2 pb-2 d-flex align-items-center gap-2 list-group-item border-start-0 border-end-0">
            <a href="/issued-point/ozon" class="d-flex align-items-center gap-1">
                <img style="width: 16px;" src="/img/ozon.svg" alt="">
                <?= UploadedCodeCompanyKeyEnum::OZON->label() ?>
            </a>
        </div>
    </div>
</div>

