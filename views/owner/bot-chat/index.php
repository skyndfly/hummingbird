<?php

/** @var array<int, array{id:int, username:string|null, phone:string|null, name:string|null}> $users */
/** @var string|null $phone */
/** @var string|null $chatId */
/** @var int $total */
/** @var int $page */
/** @var int $pageSize */

$this->title = 'Чат с пользователями';
$totalPages = $pageSize > 0 ? (int) ceil($total / $pageSize) : 1;
?>

<section>
    <h2>Чат с пользователями</h2>
    <hr>

    <div class="row g-3 mb-2">
        <div class="col-md-8">
            <form class="row g-3" method="get" action="/owner-chat">
                <div class="col-md-6">
                    <label class="form-label">Телефон</label>
                    <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars((string) $phone) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Chat ID</label>
                    <input type="text" class="form-control" name="chatId" value="<?= htmlspecialchars((string) $chatId) ?>">
                </div>
                <div class="col-12">
                    <button class="btn btn-primary" type="submit">Найти</button>
                </div>
            </form>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <form method="post" action="/owner-chat/sync">
                <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                <button class="btn btn-outline-secondary" type="submit">Обновить пользователей</button>
            </form>
        </div>
    </div>

    <?php if (!empty($users)): ?>
        <div class="table-responsive mb-4">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th>Chat ID</th>
                        <th>Username</th>
                        <th>Телефон</th>
                        <th>Имя</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars((string) $user['id']) ?></td>
                            <td><?= htmlspecialchars((string) ($user['username'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($user['phone'] ?? '')) ?></td>
                            <td><?= htmlspecialchars((string) ($user['name'] ?? '')) ?></td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary" href="/owner-chat/<?= htmlspecialchars((string) $user['id']) ?>">
                                    Написать
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($totalPages > 1): ?>
            <?php
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);
                if ($page <= 3) {
                    $end = min($totalPages, 5);
                }
                if ($page >= $totalPages - 2) {
                    $start = max(1, $totalPages - 4);
                }
            ?>
            <nav>
                <ul class="pagination flex-wrap">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(['phone' => $phone, 'chatId' => $chatId, 'page' => max(1, $page - 1)]) ?>">Назад</a>
                    </li>
                    <?php if ($start > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?= http_build_query(['phone' => $phone, 'chatId' => $chatId, 'page' => 1]) ?>">1</a>
                        </li>
                        <?php if ($start > 2): ?>
                            <li class="page-item disabled"><span class="page-link">…</span></li>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php for ($p = $start; $p <= $end; $p++): ?>
                        <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(['phone' => $phone, 'chatId' => $chatId, 'page' => $p]) ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($end < $totalPages): ?>
                        <?php if ($end < $totalPages - 1): ?>
                            <li class="page-item disabled"><span class="page-link">…</span></li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?= http_build_query(['phone' => $phone, 'chatId' => $chatId, 'page' => $totalPages]) ?>"><?= $totalPages ?></a>
                        </li>
                    <?php endif; ?>
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(['phone' => $phone, 'chatId' => $chatId, 'page' => min($totalPages, $page + 1)]) ?>">Вперед</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>

</section>
