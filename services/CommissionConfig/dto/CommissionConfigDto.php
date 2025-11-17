<?php

namespace app\services\CommissionConfig\dto;

use app\services\CommissionConfig\enums\CommissionTypeEnum;

class CommissionConfigDto
{
    public function __construct(
        public string $strategy,
        public CommissionTypeEnum $type,
        public int $value,
        public ?int $fromAmount = null,
        public ?int $toAmount = null,
        public ?int $id = null,
    ) {
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
            strategy: $record['strategy'],
            type: CommissionTypeEnum::from($record['type']),
            value: $record['value'],
            fromAmount: $record['from_amount'],
            toAmount: $record['to_amount'],
            id: $record['id'],
        );
    }
}