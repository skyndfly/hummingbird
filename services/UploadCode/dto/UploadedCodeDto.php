<?php

namespace app\services\UploadCode\dto;

use app\services\UploadCode\enums\UploadedCodeStatusEnum;

class UploadedCodeDto
{
    public function __construct(
        public string $fileName,
        public string $companyKey,
        public UploadedCodeStatusEnum $status,
        public string $chatId,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
        public ?int $id = null,
    ) {
    }

    /**
     * @param array{
     *     id: int|null,
     *     company_key: string,
     *     file_name: string,
     *     status: string,
     *     chat_id: string,
     *     created_at: string|null,
     *     updated_at: string|null,
     * } $record
     */
    public static function fromDbRecord(array $record): UploadedCodeDto
    {
        return new self(
            fileName: $record['file_name'],
            companyKey: $record['company_key'],
            status: UploadedCodeStatusEnum::from($record['status']),
            chatId: $record['chat_id'],
            createdAt: $record['created_at'],
            updatedAt: $record['updated_at'],
            id: $record['id'],
        );
    }
}