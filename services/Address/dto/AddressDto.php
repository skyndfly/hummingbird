<?php

namespace app\services\Address\dto;

class AddressDto
{
    public function __construct(
        public int $id,
        public int $companyId,
        public string $address,
        public ?string $companyName = null,
        public ?string $companyBotKey = null,
        public ?string $deletedAt = null,
    ) {
    }

    /**
     * @param array{
     *     id: int,
     *     company_id: int,
     *     address: string,
     *     company_name?: string|null,
     *     company_bot_key?: string|null,
     *     deleted_at?: string|null,
     * } $record
     */
    public static function fromDbRecord(array $record): self
    {
        return new self(
            id: $record['id'],
            companyId: $record['company_id'],
            address: $record['address'],
            companyName: $record['company_name'] ?? null,
            companyBotKey: $record['company_bot_key'] ?? null,
            deletedAt: $record['deleted_at'] ?? null,
        );
    }
}
