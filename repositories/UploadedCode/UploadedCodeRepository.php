<?php

namespace app\repositories\UploadedCode;

use app\repositories\BaseRepository;
use app\services\UploadCode\dto\UploadedCodeDto;

class UploadedCodeRepository extends BaseRepository
{
    public const string TABLE = 'uploaded_code';

    public function create(UploadedCodeDto $dto): void
    {
        $this->getCommand()
            ->insert(
                table: self::TABLE,
                columns: [
                    'company_key' => $dto->companyKey,
                    'file_name' => $dto->fileName,
                    'status' => $dto->status->value,
                    'chat_id' => $dto->chatId,
                    'created_at' => $this->getCurrentDate(),
                    'updated_at' => $this->getCurrentDate(),
                ]
            )
            ->execute();
    }
}