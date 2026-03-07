<?php

/** @var int $chatId */
/** @var array<int, array{id:int, chat_id:int, text:string, created_at:string, owner_user_id:int|null}> $messages */

$this->title = 'Чат с пользователем';
?>

<section>
    <h2>Чат с пользователем</h2>
    <hr>
    <div class="mb-3">
        <a class="btn btn-outline-secondary" href="/owner-chat">Назад к списку</a>
    </div>

    <div class="card">
        <div class="card-header">
            Chat ID: <?= htmlspecialchars((string) $chatId) ?>
        </div>
        <div class="card-body">
            <?php if (empty($messages)): ?>
                <div class="text-muted">Нет сообщений.</div>
            <?php else: ?>
                <?php foreach (array_reverse($messages) as $msg): ?>
                    <div class="mb-3">
                        <div class="small text-muted"><?= htmlspecialchars((string) $msg['created_at']) ?></div>
                        <div><?= nl2br(htmlspecialchars((string) $msg['text'])) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <form method="post" action="/owner-chat/send" class="mt-4">
        <input type="hidden" name="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
        <input type="hidden" name="chatId" value="<?= htmlspecialchars((string) $chatId) ?>">
        <div class="mb-3">
            <label class="form-label">Сообщение</label>
            <textarea class="form-control" name="text" rows="4" required></textarea>
        </div>
        <button class="btn btn-outline-success" type="submit">Отправить</button>
    </form>
</section>
