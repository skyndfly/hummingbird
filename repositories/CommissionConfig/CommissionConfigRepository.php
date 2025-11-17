<?php

namespace app\repositories\CommissionConfig;

use app\repositories\BaseRepository;
use app\services\CommissionConfig\dto\CommissionConfigDto;

class CommissionConfigRepository extends BaseRepository
{
    public const string TABLE_NAME = 'commision_config';

    /**
     * Используется для marketplace (диапазоны)
     */
    public function findForAmount(string $strategy, int $amount): ?CommissionConfigDto
    {
        $record = $this->getQuery()
            ->from(self::TABLE_NAME)
            ->where(['strategy' => $strategy])
            ->andWhere(['<=', 'from_amount', $amount])
            ->andWhere([
                'or',
                ['>=', 'to_amount', $amount],
                ['to_amount' => null],
            ])
            ->one();
        if ($record === false) {
            return null;
        }
        return CommissionConfigDto::fromDbRecord($record);
    }

    public function findForFixed(string $strategy): ?CommissionConfigDto
    {
        $record = $this->getQuery()
            ->from(self::TABLE_NAME)
            ->where(['strategy' => $strategy])
            ->one();
        if ($record === false) {
            return null;
        }
        return CommissionConfigDto::fromDbRecord($record);
    }
}