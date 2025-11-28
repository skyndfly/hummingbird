<?php

use app\services\UploadCode\enums\UploadedCodeCompanyKeyEnum;

?>
<li class="list-group-item">
    <a href="/operations-on-manager" class="d-flex align-items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people-fill"
             viewBox="0 0 16 16">
            <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
        </svg>
        Менеджеры
    </a>
</li>
<li class="list-group-item d-flex gap-2">
    <a href="/statistics" class="d-flex align-items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pie-chart-fill"
             viewBox="0 0 16 16">
            <path d="M15.985 8.5H8.207l-5.5 5.5a8 8 0 0 0 13.277-5.5zM2 13.292A8 8 0 0 1 7.5.015v7.778zM8.5.015V7.5h7.485A8 8 0 0 0 8.5.015"/>
        </svg>
        Статистика
    </a>
</li>
<li class="list-group-item">
    <a href="/company" class="d-flex align-items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-buildings-fill"
             viewBox="0 0 16 16">
            <path d="M15 .5a.5.5 0 0 0-.724-.447l-8 4A.5.5 0 0 0 6 4.5v3.14L.342 9.526A.5.5 0 0 0 0 10v5.5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5V14h1v1.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5zM2 11h1v1H2zm2 0h1v1H4zm-1 2v1H2v-1zm1 0h1v1H4zm9-10v1h-1V3zM8 5h1v1H8zm1 2v1H8V7zM8 9h1v1H8zm2 0h1v1h-1zm-1 2v1H8v-1zm1 0h1v1h-1zm3-2v1h-1V9zm-1 2h1v1h-1zm-2-4h1v1h-1zm3 0v1h-1V7zm-2-2v1h-1V5zm1 0h1v1h-1z"/>
        </svg>
        Службы доставки
    </a>
</li>
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
        <div class="pt-2 ps-5 pb-2 d-flex align-items-center gap-2 list-group-item border-start-0 border-end-0">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle-fill" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z"/>
            </svg>
            <a href="/owner-point/wb/add-code" class="d-flex align-items-center gap-1">
                Добавить код WB
            </a>
        </div>
        <div class="pt-2 pb-2 d-flex align-items-center gap-2 list-group-item border-start-0 border-end-0">
            <a href="/issued-point/ozon" class="d-flex align-items-center gap-1">
                <img style="width: 16px;" src="/img/ozon.svg" alt="">
                <?= UploadedCodeCompanyKeyEnum::OZON->label() ?>
            </a>
        </div>
        <div class="pt-2 ps-5 pb-2 d-flex align-items-center gap-2 list-group-item border-start-0 border-end-0">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle-fill" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z"/>
            </svg>
            <a href="/owner-point/ozon/add-code" class="d-flex align-items-center gap-1">
                Добавить код Ozon
            </a>
        </div>
    </div>
</div>

