<?php

namespace app\repositories\Code\dto;

use app\repositories\Code\enums\CodeStatusEnum;

final readonly class GroupedCodeDto
{
    public function __construct(
        public int $id,
        public string $code,
        public CodeStatusEnum $status,
        public int $price,
        public ?string $comments,
        public int $quantity,
        public int $categoryId,
        public string $categoryName,
        public int $unpaidTotal,
    )
    {
    }

    /**
     * @param array{
     *     id: int,
     *     code: string,
     *     price: int,
     *     comments: ?string,
     *     quantity: int,
     *     category_id: int,
     *     category_name: string,
     *     unpaid_total: int,
     *     status: string,
     *
     * } $record
     */
    public static function fromDbRecord(array $record): GroupedCodeDto
    {
        return new self(
            id: $record['id'],
            code: $record['code'],
            status: CodeStatusEnum::tryFrom($record['status']),
            price: $record['price'],
            comments: $record['comments'],
            quantity: $record['quantity'],
            categoryId: $record['category_id'],
            categoryName: $record['category_name'],
            unpaidTotal: $record['unpaid_total'],
        );
    }
}