<?php

namespace app\services\Company\dto;

use JetBrains\PhpStorm\ArrayShape;

class CompanyDto
{
    public function __construct(
        public int $id,
        public ?string $name,
        public string $commissionStrategy = 'marketplace',
        public ?string $botKey = null,
    )
    {
    }

    /**
     * @param array{
     *     id: int,
     *     name: string,
     *     commission_strategy: string,
     *     bot_key: ?string
     * } $data
     */
    public static function fromDbRecord(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            commissionStrategy: $data['commission_strategy'],
            botKey: $data['bot_key'],
        );
    }

    /**
     * @return  array{
     *     id: int,
     *     name: null|string,
     *     commissionStrategy: string,
     *     botKey: null|string
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'commissionStrategy' => $this->commissionStrategy,
            'botKey' => $this->botKey,
        ];
    }
}