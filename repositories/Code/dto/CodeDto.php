<?php

namespace app\repositories\Code\dto;

use app\repositories\Category\dto\CategoryDto;
use app\repositories\Code\enums\CodeStatusEnum;
use GuzzleHttp\Psr7\Response;

class CodeDto
{
    public function __construct(
        public string $code,
        public int $id,
        public int $price,
        public int $userId,
        public int $quantity,
        public CategoryDto $category,
        public ?string $comment,
        public string $createdAt,
        public CodeStatusEnum $status
    )
    {
    }

    /**
     * @param array{
     *     code: string,
     *     id: int,
     *     price: int,
     *     quantity: int,
     *     category_id: int,
     *     user_id: int,
     *     category_name: string,
     *     comment: ?string,
     *     created_at: string,
     *     status: string,
     * } $record
     */
    public static function fromDbRecord(array $record): self
    {

        $category = new CategoryDto(id: $record['category_id'],name: $record['category_name']);
        return new self(
            code: $record['code'],
            id: $record['id'],
            price: $record['price'],
            userId: $record['user_id'],
            quantity: $record['quantity'],
            category: $category,
            comment: $record['comment'],
            createdAt: $record['created_at'],
            status: CodeStatusEnum::from($record['status']),
        );
    }
}