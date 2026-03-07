<?php

namespace app\services\UploadCode;

use app\repositories\Address\AddressRepository;
use app\repositories\UploadedCode\UploadedCodeRepository;
use app\services\Disk\YandexDiskApi;
use app\services\UploadCode\dto\UploadedCodeDto;
use DateTimeImmutable;
use DateTimeZone;
use Throwable;
use Yii;
use yii\web\UploadedFile;

class UploadedCodeStoreService
{
    public const string PATH = 'uploads/codes/';

    public function __construct(
        private UploadedCodeRepository $uploadedCodeRepository,
        private AddressRepository $addressRepository
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
        $this->uploadToYandexDisk($dto, $absolutePath);
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

    private function uploadToYandexDisk(UploadedCodeDto $dto, string $absolutePath): void
    {
        $token = getenv('DISK_TOKEN');
        if ($token === false || $token === '') {
            return;
        }
        if ($dto->chatId !== null) {
            return;
        }
        if ($dto->addressId === null) {
            Yii::warning('Upload to Yandex Disk skipped: addressId missing', 'bot');
            return;
        }
        try {
            $address = $this->addressRepository->getById($dto->addressId);
            $companyName = $address->companyName ?? $dto->companyKey;

            $tz = new DateTimeZone('Europe/Moscow');
            $currentDate = new DateTimeImmutable('now', $tz);
            $folderSegments = [
                $currentDate->format('d-m-Y'),
                $companyName,
                $address->address,
            ];

            $disk = new YandexDiskApi($token);
            $disk->ensurePath($folderSegments);
            $remotePath = implode('/', $folderSegments) . '/' . basename($absolutePath);
            $disk->uploadFile($absolutePath, $remotePath);
        } catch (Throwable $e) {
            Yii::warning('Yandex Disk upload failed: ' . $e->getMessage(), 'bot');
        }
    }
}
