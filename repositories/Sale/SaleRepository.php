<?php

namespace app\repositories\Sale;

use app\repositories\BaseRepository;
use app\services\Sale\dto\SaleDto;
use DateTimeImmutable;

class SaleRepository extends BaseRepository
{
    public const string TABLE_NAME = 'sale';

    public function create(SaleDto $dto): void
    {
        $this->getCommand()
            ->insert(
                table: self::TABLE_NAME,
                columns: [
                    'amount' => $dto->amount,
                    'code' => $dto->code,
                    'item_ids' => json_encode($dto->itemIds),
                    'quantity' => $dto->quantity,
                    'user_id' => $dto->userId,
                    'created_at' => $dto->createdAt->format('Y-m-d H:i:s'),
                    'update_at' => $dto->updatedAt->format('Y-m-d H:i:s'),
                ]
            )
            ->execute();
    }

    public function getIssuedStats(): bool|array
    {
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        return $this->getQuery()
            ->from(self::TABLE_NAME)
            ->select([
                'total_codes' => 'COUNT(*)',
                'total_amount' => 'SUM(amount)',
            ])
            ->Where(['>=', 'created_at', $todayStart])
            ->andWhere(['<=', 'created_at', $todayEnd])
            ->one();
    }

}