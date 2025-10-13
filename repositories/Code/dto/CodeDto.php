<?php

namespace app\repositories\Code\dto;

class CodeDto
{
    public function __construct(
        public int $code,
        public int $id,
        public int $price,
        public string $place,
        public string $comment,
        public string $createdAt,
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
        );
    }
}