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
        $relativePath = self::PATH . $dto->fileName;
        $dto->fileName = $relativePath;
        $this->uploadedCodeRepository->create($dto);
        $absolutePath = Yii::getAlias('@webroot/' . $relativePath);
        $file->saveAs($absolutePath);
    }

    private function ensureDirectoryExists(): void
    {
        $path = Yii::getAlias('@webroot/' . self::PATH);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        if (file_exists($path) && !is_writable($path)) {
            @chmod($path, 0777);
        }
    }
}
