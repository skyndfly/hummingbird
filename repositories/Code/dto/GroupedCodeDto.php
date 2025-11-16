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
        public int $companyId,
        public string $categoryName,
        public string $companyName,
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
     *     company_id: int,
     *     category_name: string,
     *     company_name: string,
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
            companyId: $record['company_id'],
            categoryName: $record['category_name'],
            companyName: $record['company_name'],
            unpaidTotal: $record['unpaid_total'],
        );
    }
}