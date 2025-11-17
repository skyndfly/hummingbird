<?php

namespace app\services\CommissionConfig\dto;

use app\services\CommissionConfig\enums\CommissionTypeEnum;

class CommissionConfigDto
{
    public function __construct(
        public ?int $id = null,
        public string $strategy,
        public ?int $fromAmount = null,
        public ?int $toAmount = null,
        public CommissionTypeEnum $type,
        public int $value,
    )
    {
    }

    /**
     * @param array{
     *     id: int,
     *     strategy: string,
     *     from_amount: int,
     *     to_amount: int,
     *     type: string,
     *     value: int,
     * } $record
     */
    public static function fromDbRecord(array $record): self
    {
        return new self(
            id: $record['id'],
            strategy: $record['strategy'],
            fromAmount: $record['from_amount'],
            toAmount: $record['to_amount'],
            type: CommissionTypeEnum::from($record['type']),
            value: $record['value'],
        );
    }
}