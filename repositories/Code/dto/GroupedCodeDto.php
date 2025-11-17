<?php

namespace app\repositories\Code\dto;

use app\repositories\Code\enums\CodeStatusEnum;
use app\services\Company\dto\CompanyDto;

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
        public CompanyDto $company,
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
     *     company_name: string,
     *     company_commission_strategy: string,
     *     company_id: int,
     *     unpaid_total: int,
     *     status: string,
     *
     * } $record
     */
    public static function fromDbRecord(array $record): GroupedCodeDto
    {
        $company = new CompanyDto(
            id: $record['company_id'],
            name: $record['company_name'],
            commissionStrategy: $record['company_commission_strategy'],
        );
        return new self(
            id: $record['id'],
            code: $record['code'],
            status: CodeStatusEnum::tryFrom($record['status']),
            price: $record['price'],
            comments: $record['comments'],
            quantity: $record['quantity'],
            categoryId: $record['category_id'],
            company: $company,
            categoryName: $record['category_name'],
            unpaidTotal: $record['unpaid_total'],
        );
    }
}