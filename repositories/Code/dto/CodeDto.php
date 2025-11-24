<?php

namespace app\repositories\Code\dto;

use app\repositories\Category\dto\CategoryDto;
use app\repositories\Code\enums\CodeStatusEnum;
use app\services\Company\dto\CompanyDto;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class CodeDto
{
    public function __construct(
        public string $code,
        public int $id,
        public int $price,
        public int $userId,
        public int $quantity,
        public CategoryDto $category,
        public CompanyDto $company,
        public ?string $comment,
        public CodeStatusEnum $status,
        public ?string $createdAt = null
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
     *     company_id: int,
     *     company_name: string,
     *     company_commission_strategy: string,
     *     comment: ?string,
     *     created_at: string,
     *     status: string,
     * } $record
     */
    public static function fromDbRecord(array $record): self
    {
        $category = new CategoryDto(id: $record['category_id'],name: $record['category_name']);
        $company = new CompanyDto(
            id: $record['company_id'],
            name: $record['company_name'],
            commissionStrategy: $record['company_commission_strategy']
        );
        return new self(
            code: $record['code'],
            id: $record['id'],
            price: $record['price'],
            userId: $record['user_id'],
            quantity: $record['quantity'],
            category: $category,
            company: $company,
            comment: $record['comment'],
            status: CodeStatusEnum::from($record['status']),
            createdAt: $record['created_at'],
        );
    }

    #[ArrayShape([
        'code' => "string",
        'id' => "int",
        'price' => "int",
        'userId' => "int",
        'quantity' => "int",
        'category' => "array",
        'company' => "array",
        'comment' => "string|null",
        'status' => "string",
        'createdAt' => "string|null",
        'companyId' => "int",
        'categoryId' => "int"
    ])]
    //TODO разобраться как описывать массивы через атрибуты
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'id' => $this->id,
            'price' => $this->price,
            'userId' => $this->userId,
            'quantity' => $this->quantity,
            'category' => $this->category->toArray(),
            'company' => $this->company->toArray(),
            'comment' => $this->comment,
            'createdAt' => $this->createdAt,
            'status' => $this->status->value,
            'companyId' => $this->company->id,
            'categoryId' => $this->category->id,
        ];
    }
}