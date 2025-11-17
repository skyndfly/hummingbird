<?php

namespace app\services\Company\dto;

class CompanyDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $commissionStrategy,
    )
    {
    }

    /**
     * @param array{
     *     id: int,
     *     name: string,
     *     commission_strategy: string
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            commissionStrategy: $data['commission_strategy'],
        );
    }
}