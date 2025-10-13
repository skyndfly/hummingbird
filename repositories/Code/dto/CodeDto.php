<?php

namespace app\repositories\Code\dto;

use app\repositories\Code\enums\CodeStatusEnum;

class CodeDto
{
    public function __construct(
        public int $code,
        public int $id,
        public int $price,
        public string $place,
        public string $comment,
        public string $createdAt,
        public CodeStatusEnum $status
    )
    {
    }

    /**
     * @param array{
     *     code: int,
     *     id: int,
     *     price: int,
     *     place: string,
     *     comment: string,
     *     created_at: string,
     *     status: string,
     * } $record
     */
    public static function fromDbRecord(array $record): self
    {
        return new self(
            code: $record['code'],
            id: $record['id'],
            price: $record['price'],
            place: $record['place'],
            comment: $record['comment'],
            createdAt: $record['created_at'],
            status: CodeStatusEnum::from($record['status']),
        );
    }
}