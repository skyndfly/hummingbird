<?php

namespace app\services\ReturnRequest;

use app\repositories\ReturnRequest\ReturnRequestRepository;
use app\repositories\ReturnRequest\enums\ReturnRequestStatusEnum;
use Exception;
use Yii;
use yii\web\UploadedFile;

class ReturnRequestStoreService
{
    public const string PATH = 'uploads/codes/returns/';

    public function __construct(private ReturnRequestRepository $repository)
    {
        $this->ensureDirectoryExists();
    }

    public function execute(string $phone, string $returnType, UploadedFile $photoOne, UploadedFile $photoTwo, int $createdBy): string
    {
        $photoOneData = $this->storePhoto($photoOne, 'rr1_');
        $photoTwoData = $this->storePhoto($photoTwo, 'rr2_');
        $relativePhotoOne = $photoOneData['relative'];
        $relativePhotoTwo = $photoTwoData['relative'];
        $absolutePhotoOne = $photoOneData['absolute'];
        $absolutePhotoTwo = $photoTwoData['absolute'];

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
            $id = $this->repository->create([
                'number' => null,
                'phone' => $phone,
                'return_type' => $returnType,
                'status' => ReturnRequestStatusEnum::CREATED->value,
                'photo_one' => $relativePhotoOne,
                'photo_two' => $relativePhotoTwo,
                'created_by' => $createdBy,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $transaction->commit();
            return (string) $id;
        } catch (Exception $e) {
            $transaction->rollBack();
            @unlink($absolutePhotoOne);
            @unlink($absolutePhotoTwo);
            throw $e;
        }
    }

    public function storePhoto(UploadedFile $file, string $prefix = 'rr_'): array
    {
        $this->ensureDirectoryExists();
        $fileName = uniqid($prefix, true) . '.' . $file->extension;
        $relative = self::PATH . $fileName;
        $absolute = Yii::getAlias('@webroot/' . $relative);
        $file->saveAs($absolute);
        return [
            'relative' => $relative,
            'absolute' => $absolute,
        ];
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
