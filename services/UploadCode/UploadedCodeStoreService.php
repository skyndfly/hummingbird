<?php

namespace app\services\UploadCode;

use app\repositories\UploadedCode\UploadedCodeRepository;
use app\services\UploadCode\dto\UploadedCodeDto;
use Yii;
use yii\web\UploadedFile;

class UploadedCodeStoreService
{
    public const string PATH = 'uploads/codes/';

    public function __construct(
        private UploadedCodeRepository $uploadedCodeRepository
    ) {
        // Создаем директорию при инициализации сервиса
        $this->ensureDirectoryExists();
    }

    public function execute(UploadedCodeDto $dto, UploadedFile $file): void
    {
        $dto->fileName = self::PATH . $dto->fileName;
        $this->uploadedCodeRepository->create($dto);
        $file->saveAs($dto->fileName);
    }

    private function ensureDirectoryExists(): void
    {
        $path = Yii::getAlias('@webroot/' . self::PATH);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }
}