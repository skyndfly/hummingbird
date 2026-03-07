<?php

namespace app\repositories\OwnerMessage;

use app\repositories\BaseRepository;

class OwnerMessageRepository extends BaseRepository
{
    public const string TABLE = 'owner_messages';

    public function create(int $chatId, string $text, ?int $ownerUserId): void
    {
        $this->getCommand()->insert(
            table: self::TABLE,
            columns: [
                'owner_user_id' => $ownerUserId,
                'chat_id' => $chatId,
                'text' => $text,
                'created_at' => $this->getCurrentDate(),
            ]
        )->execute();
    }

    /**
     * @return array<int, array{id:int, chat_id:int, text:string, created_at:string, owner_user_id:int|null}>
     */
    public function getByChatId(int $chatId, int $limit = 200): array
    {
        return $this->getQuery()
            ->from(self::TABLE)
            ->where(['chat_id' => $chatId])
            ->orderBy(['id' => SORT_DESC])
            ->limit($limit)
            ->all();
    }
}
