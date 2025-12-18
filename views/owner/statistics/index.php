<?php
/** @var int $allWbCount */
/** @var int $allOzonCount */
/** @var int $issuedOzonCount */
/** @var int $issuedWbCount */
/** @var int $awaitWbCount */
/** @var int $awaitOzonCount */
/** @var int $notPaidWbCount */
/** @var int $notPaidOzonCount */
/** @var int $outdatedWbCount */
/** @var int $outdatedOzonCount */
/** @var int $totalCodes */
/** @var int $totalAmount */

/** @var StockStatisticsDto $statistics */

use app\services\Code\dto\StockStatisticsDto;

$this->title = 'Статистика';
?>
<section>
    <h2 class="mb-3">Статистика</h2>
    <hr>
    <h3>Отправлено с бота</h3>
    <div>
        <div class="d-flex  gap-2 mb-3">
            <div class="card w-25">
                <div class="card-body">
                    <p class="card-text">
                        Общее количество <strong>принятых</strong> сегодня кодов <strong>WB</strong>
                    </p>
                    <h3><?= $allWbCount ?></h3>
                </div>
            </div>
            <div class="card w-25">
                <div class="card-body">
                    <p class="card-text">
                        Общее количество <strong>принятых</strong> сегодня кодов <strong>Ozon</strong>
                    </p>
                    <h3><?= $allOzonCount ?></h3>
                </div>
            </div>

        </div>
        <div class="d-flex gap-2 mb-3">
            <div class="card w-25">
                <div class="card-body">
                    <p class="card-text">
                        Общее количество <strong>выданных</strong> сегодня кодов <strong>WB</strong>
                    </p>
                    <h3><?= $issuedWbCount ?></h3>
                </div>
            </div>
            <div class="card w-25">
                <div class="card-body">
                    <p class="card-text">
                        Общее количество <strong>выданных</strong> сегодня кодов <strong>Ozon</strong>
                    </p>
                    <h3><?= $issuedOzonCount ?></h3>
                </div>
            </div>
            <div class="card w-25">
                <div class="card-body">
                    <p class="card-text">
                        Общее количество <strong>ожидающих выдачи</strong> сегодня кодов <strong>WB</strong>
                    </p>
                    <h3><?= $awaitWbCount ?></h3>
                </div>
            </div>
            <div class="card w-25">
                <div class="card-body">
                    <p class="card-text">
                        Общее количество <strong>ожидающих выдачи</strong> сегодня кодов <strong>Ozon</strong>
                    </p>
                    <h3><?= $awaitOzonCount ?></h3>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2 mb3">
            <div class="card w-25 bg-warning">
                <div class="card-body">
                    <p class="card-text">
                        Общее количество <strong>не оплаченных</strong> сегодня кодов <strong>WB</strong>
                    </p>
                    <h3><?= $notPaidWbCount ?></h3>
                </div>
            </div>
            <div class="card w-25  bg-warning">
                <div class="card-body">
                    <p class="card-text">
                        Общее количество <strong>не оплаченных</strong> сегодня кодов <strong>Ozon</strong>
                    </p>
                    <h3><?= $notPaidOzonCount ?></h3>
                </div>
            </div>
            <div class="card w-25 bg-danger text-white">
                <div class="card-body">
                    <p class="card-text">
                        Общее количество <strong>просроченных</strong> сегодня кодов <strong>WB</strong>
                    </p>
                    <h3><?= $outdatedWbCount ?></h3>
                </div>
            </div>
            <div class="card w-25 bg-danger text-white">
                <div class="card-body">
                    <p class="card-text">
                        Общее количество <strong>просроченных</strong> сегодня кодов <strong>Ozon</strong>
                    </p>
                    <h3><?= $outdatedOzonCount ?></h3>
                </div>
            </div>
        </div>
    </div>

    <hr>
    <h3>Внесено в 108</h3>
    <div>
        <div class="d-flex gap-2 mb-3">
            <div class="card w-25">
                <div class="card-body">
                    <p class="card-text">
                        Общее количество <strong>выданных</strong> сегодня кодов
                    </p>
                    <h3><?= $totalCodes ?></h3>
                </div>
            </div>
            <div class="card w-25  bg-success text-white">
                <div class="card-body">
                    <p class="card-text">
                        Сегодня заработано
                    </p>
                    <h3><?= $totalAmount / 100 ?> ₽</h3>
                </div>
            </div>
            <div class="card w-25">
                <div class="card-body">
                    <p class="card-text">
                        Можно заработать
                    </p>
                    <h3><?= $statistics->totalCommission / 100 ?> ₽</h3>
                </div>
            </div>
            <div class="card w-25">
                <div class="card-body">
                    <p class="card-text">
                        <strong>Не выданных кодов</strong> находится на складе
                    </p>
                    <h3><?= $statistics->uniqueCodeCount ?> </h3>
                </div>
            </div>
        </div>
    </div>
</section>
