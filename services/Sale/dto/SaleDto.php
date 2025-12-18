<?php

namespace app\services\Sale\dto;

use DateTimeImmutable;
use JsonSerializable;

final class SaleDto
{
    public function __construct(
        public int $amount,
        public string $code,
        public array $itemIds,
        public int $quantity,
        public int $userId,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
        public ?int $id = null
    ) {
    }

    public static function fromDbRecord(array $data): self
    {
        return new self(
            amount: $data['amount'],
            code: $data['code'],
            itemIds: json_decode($data['item_ids'], true),
            quantity: $data['quantity'],
            userId: $data['userId'],
            createdAt: new DateTimeImmutable($data['createdAt']),
            updatedAt: new DateTimeImmutable($data['updatedAt']),
            id: $data['id']
        );
    }
}